<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Add user_id field
            $table->string('code')->unique(); // Unique discount code
            $table->decimal('amount', 8, 2); // Discount amount or percentage
            $table->boolean('is_percentage')->default(false); // Indicates if discount is a percentage
            $table->date('valid_from')->nullable(); // Start date for the discount code
            $table->date('valid_until')->nullable(); // Expiry date for the discount code
            $table->integer('usage_limit')->nullable(); // Total times this code can be used
            $table->integer('used_count')->default(0); // Count of how many times the code has been used
            $table->timestamps();
        });

        // Insert default discount codes
        DB::table('discount_codes')->insert([
            [
                'code' => 'DISCOUNT11',               // The unique code
                'amount' => 11,                        // The discount amount (11%)
                'is_percentage' => true,               // This indicates it's a percentage discount
                'valid_from' => now(),                 // Set current date as the start date
                'valid_until' => now()->addMonths(6), // Valid for 6 months from now
                'usage_limit' => 100,                  // Limit the code to 100 uses
                'used_count' => 0                       // Initially set to 0 uses
            ],
            [
                'code' => 'DISCOUNT10',               // The unique code
                'amount' => 10,                        // The discount amount (10%)
                'is_percentage' => true,               // This indicates it's a percentage discount
                'valid_from' => now(),                 // Set current date as the start date
                'valid_until' => now()->addMonths(6), // Valid for 6 months from now
                'usage_limit' => 100,                  // Limit the code to 100 uses
                'used_count' => 0                       // Initially set to 0 uses
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_codes');
    }
};
