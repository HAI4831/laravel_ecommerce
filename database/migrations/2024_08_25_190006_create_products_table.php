<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Import DB facade

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->string('image')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->date('manufacture_date'); // Date of manufacture
            $table->date('expiry_date')->nullable(); // Expiry date
            $table->timestamps();
        });

        // Insert data into `products` table
        DB::table('products')->insert([
            [
                'name' => 'Dell Vostro',
                'description' => 'Máy tính xách tay Dell Vostro với cấu hình mạnh mẽ.',
                'price' => 15000000,
                'quantity' => 9,
                'image' => 'dell_vostro.png',
                'category_id' => 1, // Assuming 'Máy tính' has id 1
                'manufacture_date' => '2023-01-01', // Add manufacture date
                'expiry_date' => '2025-01-01', // Add expiry date
            ],
            [
                'name' => 'Dell Inspiron',
                'description' => 'Máy tính xách tay Dell Inspiron với thiết kế thời trang.',
                'price' => 12000000,
                'quantity' => 7,
                'image' => 'dell_inspiron.png',
                'category_id' => 1,
                'manufacture_date' => '2023-01-01',
                'expiry_date' => '2025-01-01',
            ],
            [
                'name' => 'Inspiron 3',
                'description' => 'Máy tính xách tay Inspiron 3, nhẹ và tiện dụng cho công việc.',
                'price' => 10000000,
                'quantity' => 4,
                'image' => 'inspiron_3.png',
                'category_id' => 1,
                'manufacture_date' => '2023-01-01',
                'expiry_date' => '2025-01-01',
            ],
            [
                'name' => 'iPhone 16',
                'description' => 'iPhone 16 mới nhất với nhiều tính năng ưu việt.',
                'price' => 12000000,
                'quantity' => 12,
                'image' => 'iphone_16.png',
                'category_id' => 4, // Assuming 'iphone' has id 4
                'manufacture_date' => '2023-01-01',
                'expiry_date' => '2025-01-01',
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'iPhone 15 Pro Max, flagship với camera và hiệu suất tuyệt vời.',
                'price' => 15000000,
                'quantity' => 7,
                'image' => 'iphone_15_pro_max.png',
                'category_id' => 4,
                'manufacture_date' => '2023-01-01',
                'expiry_date' => '2025-01-01',
            ],
            [
                'name' => 'iPhone 14 Pro',
                'description' => 'iPhone 14 Pro, một lựa chọn tốt cho những ai yêu thích công nghệ.',
                'price' => 11000000,
                'quantity' => 23,
                'image' => 'iphone_14_pro.png',
                'category_id' => 4,
                'manufacture_date' => '2023-01-01',
                'expiry_date' => '2025-01-01',
            ],
            [
                'name' => 'Bánh sinh nhật',
                'description' => 'Bánh sinh nhật cho các buổi tiệc.',
                'price' => 249000,
                'quantity' => 230,
                'image' => 'birthday_cake.png',
                'manufacture_date' => '2003-07-22', // Fixed date format
                'expiry_date' => '2003-09-22', // Fixed date format
                'category_id' => 5,
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
