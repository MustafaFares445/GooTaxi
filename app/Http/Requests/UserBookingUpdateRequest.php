<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class UserBookingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([BookingStatus::Cancelled->value]),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $booking = $this->route('booking');

            if (! $booking instanceof Booking) {
                return;
            }

            if (! in_array($booking->status->value, [BookingStatus::Pending->value, BookingStatus::Upcoming->value], true)) {
                $validator->errors()->add(
                    'status',
                    __('Booking can only be updated when it is in pending or upcoming status.')
                );
            }
        });
    }
}
