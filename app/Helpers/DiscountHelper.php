<?php
namespace App\Helpers;

class DiscountHelper
{
    public function applyDiscount($discountCode, $totalAmount)
{
    // Find the discount code
    $discount = DiscountCode::where('code', $discountCode)
        ->where(function ($query) {
            $query->whereNull('valid_from')->orWhere('valid_from', '<=', now());
        })
        ->where(function ($query) {
            $query->whereNull('valid_until')->orWhere('valid_until', '>=', now());
        })
        ->first();

    if (!$discount) {
        throw new \Exception('Invalid or expired discount code');
    }

    // Check usage limit
    if ($discount->usage_limit && $discount->used_count >= $discount->usage_limit) {
        throw new \Exception('This discount code has reached its usage limit');
    }

    // Calculate the discount
    $discountedAmount = $totalAmount;

    if ($discount->is_percentage) {
        $discountValue = ($discount->amount / 100) * $totalAmount;
        $discountedAmount -= $discountValue;
    } else {
        $discountedAmount -= $discount->amount;
    }

    // Update the used count for the discount code
    $discount->increment('used_count');

    return max($discountedAmount, 0); // Ensure the total doesn't go negative
}
}
