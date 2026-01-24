<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\BookingData;
use App\Data\BookingPriceData;
use App\Enums\OfferStatus;
use App\Models\BasePrice;
use App\Models\Offer;
use App\Models\TimeRange;
use Carbon\Carbon;

/**
 * Calculates the final booking price based on distance, time ranges, location, and offers.
 *
 * Price calculation follows this order:
 * 1. Base distance price (distance × price per km)
 * 2. Van surcharge (if extra large bags)
 * 3. Time range adjustments (flat start price + percentage + per-km charges)
 * 4. Offer discount (applied to subtotal)
 */
final readonly class CalculateBookingPriceAction
{
    public function __construct(
        private ReturnNearestAdditionalPriceAction $nearestAdditionalPriceAction
    ) {}

    /**
     * Calculate the final booking price.
     *
     * @param  BookingData  $data  Booking information including distances, locations, and offer
     * @return BookingPriceData Complete price breakdown
     */
    public function handle(BookingData $data): BookingPriceData
    {
        $basePrice = $this->getBasePriceConfiguration();

        $baseDistancePrice = $this->calculateBaseDistancePrice($basePrice, $data);
        $distancePriceWithVan = $this->applyVanSurcharge($baseDistancePrice, $basePrice, $data);
        $timeRangeAdjustments = $this->getActiveTimeRangeAdjustments($data);

        $subtotal = $this->calculateSubtotal(
            $distancePriceWithVan,
            $timeRangeAdjustments,
            $data
        );

        $offerDiscountRate = $this->getOfferDiscountRate($data->offerId, $data);
        $finalPrice = $this->applyOfferDiscount($subtotal, $offerDiscountRate);

        return $this->buildPriceData(
            $finalPrice,
            $basePrice,
            $timeRangeAdjustments,
            $data,
            $offerDiscountRate
        );
    }

    /**
     * Retrieve the base pricing configuration.
     */
    private function getBasePriceConfiguration(): BasePrice
    {
        return BasePrice::query()->firstOrFail();
    }

    /**
     * Calculate the base price based on distance and price per kilometer.
     */
    private function calculateBaseDistancePrice(BasePrice $basePrice, BookingData $data): float
    {
        return $basePrice->price_per_km * $data->distance;
    }

    /**
     * Apply van surcharge if booking includes extra large bags.
     */
    private function applyVanSurcharge(
        float $baseDistancePrice,
        BasePrice $basePrice,
        BookingData $data
    ): float {
        if (! $data->extraLargeBags) {
            return $baseDistancePrice;
        }

        $surchargeMultiplier = 1 + ($basePrice->van_price_percentage / 100);

        return $baseDistancePrice * $surchargeMultiplier;
    }

    /**
     * Calculate the subtotal including all adjustments before offer discount.
     */
    private function calculateSubtotal(
        float $distancePriceWithVan,
        array $timeRangeAdjustments,
        BookingData $data
    ): float {
        $subtotal = $distancePriceWithVan;

        // Add flat start price
        $subtotal += $timeRangeAdjustments['startPrice'];

        // Apply percentage adjustment to base distance price only
        $percentageAdjustment = $distancePriceWithVan * ($timeRangeAdjustments['pricePercentage'] / 100);
        $subtotal += $percentageAdjustment;

        // Add distance-based charges for going and return trips
        $goingCharge = $timeRangeAdjustments['priceOfGoingPerKm'] * $data->goingDistance;
        $returnCharge = $timeRangeAdjustments['returnPricePerKm'] * $data->returnDistance;
        $subtotal += $goingCharge + $returnCharge;

        return $subtotal;
    }

    /**
     * Apply offer discount to the subtotal if a valid discount exists.
     */
    private function applyOfferDiscount(float $subtotal, float $offerDiscountRate): float
    {
        if ($offerDiscountRate <= 0) {
            return $subtotal;
        }

        $discountMultiplier = 1 - ($offerDiscountRate / 100);

        return $subtotal * $discountMultiplier;
    }

    /**
     * Build the final price data object with all breakdown components.
     */
    private function buildPriceData(
        float $finalPrice,
        BasePrice $basePrice,
        array $timeRangeAdjustments,
        BookingData $data,
        float $offerDiscountRate
    ): BookingPriceData {
        return BookingPriceData::from([
            'finalPrice' => round($finalPrice, 2),
            'pricePerKm' => $basePrice->price_per_km,
            'vanPricePercentage' => $basePrice->van_price_percentage,
            'startPrice' => $timeRangeAdjustments['startPrice'],
            'priceOfGoingPerKm' => $timeRangeAdjustments['priceOfGoingPerKm'],
            'goingDistance' => $data->goingDistance,
            'returnPricePerKm' => $timeRangeAdjustments['returnPricePerKm'],
            'returnDistance' => $data->returnDistance,
            'offerDiscountRate' => $offerDiscountRate,
        ]);
    }

    /**
     * Get active time range adjustments based on current day and time.
     * Falls back to coordinate-based pricing if no active time range exists.
     *
     * @return array{pricePercentage: float, startPrice: float, priceOfGoingPerKm: float, returnPricePerKm: float}
     */
    private function getActiveTimeRangeAdjustments(BookingData $data): array
    {
        $activeRange = $this->findActiveTimeRange($data);

        if ($activeRange !== null) {
            return $this->extractTimeRangeAdjustments($activeRange, $data);
        }

        return $this->getAdditionalPricingFromCoordinates($data);
    }

    /**
     * Find the currently active time range based on day and time.
     */
    private function findActiveTimeRange(BookingData $data): ?TimeRange
    {
        $bookingDateTime = $this->getBookingDateTime($data);
        $currentDay = $bookingDateTime->format('D');

        return TimeRange::query()
            ->whereNotNull('days')
            ->whereJsonContains('days', $currentDay)
            ->whereTime('from_time', '<=', $bookingDateTime)
            ->whereTime('to_time', '>=', $bookingDateTime)
            ->first();
    }

    /**
     * Get the booking date/time from data or use current time as fallback.
     */
    private function getBookingDateTime(BookingData $data): Carbon
    {
        if ($data->date !== null && $data->time !== null) {
            return Carbon::parse($data->date.' '.$data->time);
        }

        return Carbon::now();
    }

    /**
     * Extract pricing adjustments from a time range model.
     *
     * @return array{pricePercentage: float, startPrice: float, priceOfGoingPerKm: float, returnPricePerKm: float}
     */
    private function extractTimeRangeAdjustments(TimeRange $timeRange, BookingData $data): array
    {
        $additionalPriceData = $this->getAdditionalPricingFromCoordinates($data);

        return [
            'pricePercentage' => $timeRange->price_percentage ?? $additionalPriceData['pricePercentage'],
            'startPrice' => $timeRange->start_price ?? $additionalPriceData['startPrice'],
            'priceOfGoingPerKm' => $timeRange->price_of_going_per_km ?? $additionalPriceData['priceOfGoingPerKm'],
            'returnPricePerKm' => $timeRange->return_price_per_km ?? $additionalPriceData['returnPricePerKm'],
        ];
    }

    /**
     * Get pricing from nearest additional price locations based on coordinates.
     * Used as fallback when no active time range exists.
     *
     * @return array{pricePercentage: float, startPrice: float, priceOfGoingPerKm: float, returnPricePerKm: float}
     */
    private function getAdditionalPricingFromCoordinates(BookingData $data): array
    {
        $startingAdditionalPrice = null;
        $endingAdditionalPrice = null;

        if ($data->startingLat !== null && $data->startingLng !== null) {
            $startingAdditionalPrice = $this->nearestAdditionalPriceAction->handle(
                $data->startingLat,
                $data->startingLng
            );
        }

        if ($data->endingLat !== null && $data->endingLng !== null) {
            $endingAdditionalPrice = $this->nearestAdditionalPriceAction->handle(
                $data->endingLat,
                $data->endingLng
            );
        }

        return [
            'pricePercentage' => 0,
            'startPrice' => $startingAdditionalPrice?->start_price ?? 0,
            'priceOfGoingPerKm' => $startingAdditionalPrice?->price_of_going_per_km ?? 0,
            'returnPricePerKm' => $endingAdditionalPrice?->return_price_per_km ?? 0,
        ];
    }

    /**
     * Get the discount rate from a valid, active offer.
     * Returns 0 if offer doesn't exist or is invalid/expired.
     */
    private function getOfferDiscountRate(?int $offerId, BookingData $data): float
    {
        if ($offerId === null) {
            return 0;
        }

        $offer = $this->findValidOffer($offerId);

        return (float) ($offer?->discount_rate ?? 0);
    }

    /**
     * Find a valid, active offer by ID.
     */
    private function findValidOffer(int $offerId): ?Offer
    {
        return Offer::query()
            ->where('id', $offerId)
            ->where('status', OfferStatus::Active)
            ->whereColumn('uses', '<', 'number_of_times_used')
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->first();
    }
}
