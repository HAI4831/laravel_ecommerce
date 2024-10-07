<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            // Liên kết rating với người dùng
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Liên kết rating với sản phẩm
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            // Đánh giá từ 1 đến 5
            $table->unsignedTinyInteger('rating'); 
            $table->timestamps();
        });
         // Insert default users
         DB::table('ratings')->insert([
            [
                'user_id'=> 1,
                'product_id' => 1,
                'rating' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],[
                'user_id'=> 2,
                'product_id' => 1,
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
            ,[
                'user_id'=> 2,
                'product_id' => 2,
                'rating' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
