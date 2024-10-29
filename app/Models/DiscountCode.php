<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DiscountCode extends Model
{
    use HasFactory;

    // Define the table name if it doesn't follow Laravel's naming convention
    protected $table = 'discount_codes';

    // Fillable properties
    protected $fillable = [
        'user_id',         // Foreign key to associate with users (if applicable)
        'code',
        'amount',
        'is_percentage',
        'valid_from',
        'valid_until',
        'usage_limit',
        'used_count',
    ];

    // Casts for easier access
    protected $casts = [
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_percentage' => 'boolean',
    ];

    // Method to check if the discount code is valid
    public function isValid(): bool
    {
        return $this->valid_from <= Carbon::now() && $this->valid_until >= Carbon::now();
    }

    // Method to check if the discount code can still be used based on the usage limit
    public function canBeUsed(): bool
    {
        return $this->usage_limit ? $this->used_count < $this->usage_limit : true;
    }

    // Method to apply discount to a given amount
    public function applyDiscount(float $amount): float
    {
        if (!$this->isValid() || !$this->canBeUsed()) {
            return $amount; // Return the original amount if the discount code is invalid or has reached its usage limit
        }

        $discountedAmount = $amount;

        if ($this->is_percentage) {
            $discountValue = ($this->amount / 100) * $amount; // Calculate percentage discount
            $discountedAmount -= $discountValue;
        } else {
            $discountedAmount -= $this->amount; // Flat amount discount
        }

        return max($discountedAmount, 0); // Ensure the amount doesn't go negative
    }

    // Optional: Relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
