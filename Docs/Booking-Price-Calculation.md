# Booking Price Calculation Guide

## Overview

This document explains how the Goo-Taxi system calculates the final price for a taxi booking. The calculation follows a step-by-step process that considers distance, vehicle type, time, location, and special offers.

---

## Step-by-Step Calculation Process

### Step 1: Base Distance Price
**What it does:** Calculates the starting price based on how far you're traveling.

**How it works:**
- Takes the total distance of your trip (in kilometers)
- Multiplies it by the standard price per kilometer
- Example: If distance is 10 km and price per km is $2, the base price = $20

---

### Step 2: Van Surcharge (Optional)
**What it does:** Adds extra cost if you need a van for large luggage.

**How it works:**
- Only applies if you select "more than 4 passengers"
- Increases the base distance price by a percentage
- Example: If base price is $20 and van surcharge is 20%, the new price = $24

---

### Step 3: Time-Based Adjustments
**What it does:** Adds extra charges or adjustments based on when you're traveling.

The system looks for special pricing rules that match your booking date and time. If found, it applies:

**A. Start Price (Flat Fee)**
- A fixed amount added to the price regardless of distance
- Example: Add $5 start fee

**B. Percentage Adjustment**
- Increases or decreases the distance price by a percentage
- Example: Add 15% to the distance price

**C. Going Trip Charge (Per Kilometer)**
- Extra charge for each kilometer of the outbound trip
- Example: Add $0.50 per km for the going distance

**D. Return Trip Charge (Per Kilometer)**
- Extra charge for each kilometer of the return trip
- Example: Add $0.75 per km for the return distance

**How the system finds the right pricing:**
1. First, it checks if there's a time range rule active for your booking day and time
   - For example: "Monday to Friday, 8:00 AM to 6:00 PM"
   - If your booking matches, it uses those prices
   
2. If no time range matches, it uses location-based pricing
   - Finds the nearest special pricing zone for your pickup location (for going charges)
   - Finds the nearest special pricing zone for your drop-off location (for return charges)
   - Uses those location-specific prices instead

---

### Step 4: Offer Discount (Optional)
**What it does:** Applies a discount if you have a valid promotional offer code.

**How it works:**
- Checks if you entered a valid offer code
- Verifies the offer is active and not expired
- If valid, reduces the subtotal by the discount percentage
- Example: If subtotal is $50 and discount is 10%, final price = $45

---

## Final Price Breakdown

After all steps, the system provides you with:
- **Final Price:** The total amount you'll pay (rounded to 2 decimals)
- **Price Per Kilometer:** The base rate used
- **Van Surcharge Percentage:** If van was selected
- **Start Price:** Any flat fee added
- **Going Distance & Charges:** Details about the outbound trip
- **Return Distance & Charges:** Details about the return trip
- **Offer Discount:** Any discount percentage applied

---

## Example Calculation

Let's say you're booking a trip with these details:z
- Distance: 15 km
- Price per km: $2
- Extra Large Bags: Yes (20% van surcharge)
- Booking Time: Monday, 3:00 PM (matches time range with special pricing)
- Start Price: $5
- Percentage Adjustment: +10%
- Going Charge: $0.50 per km (going distance: 15 km)
- Return Charge: $0.75 per km (return distance: 10 km)
- Offer Discount: 15%

**Calculation:**
1. Base Distance Price: 15 km × $2 = **$30**
2. Van Surcharge: $30 × 1.20 = **$36**
3. Add Start Price: $36 + $5 = **$41**
4. Add Percentage: $36 × 10% = $3.60, so $41 + $3.60 = **$44.60**
5. Add Going Charge: 15 km × $0.50 = $7.50, so $44.60 + $7.50 = **$52.10**
6. Add Return Charge: 10 km × $0.75 = $7.50, so $52.10 + $7.50 = **$59.60**
7. Apply Discount: $59.60 × 0.85 (15% off) = **$50.66**

**Final Price: $50.66**

---

## Important Notes

- Prices are calculated in real-time based on your exact booking details
- The system always rounds the final price to 2 decimal places
- Time-based pricing rules take priority over location-based pricing
- Offers must be active and within their validity period to apply
- If no special time range matches your booking, location-based pricing is used as a fallback

---
