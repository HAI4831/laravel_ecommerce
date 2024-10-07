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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            // Liên kết bình luận với người dùng
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Liên kết bình luận với sản phẩm
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
        });
        // Insert default users
        DB::table('comments')->insert([
            [
                'user_id'=> 1,
                'product_id' => 1,
                'comment' => "Đây là ví dụ comment1 cho sản phẩm 1 user1",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'=> 2,
                'product_id' => 1,
                'comment' => "Đây là ví dụ comment2 cho sản phẩm 1 user 2",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id'=> 2,
                'product_id' => 2,
                'comment' => "Đây là ví dụ comment cho sản phẩm 2",
                'created_at' => now(),
                'updated_at' => now(),            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
