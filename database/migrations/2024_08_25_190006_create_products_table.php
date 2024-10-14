<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->timestamps();
        });
        // Insert dữ liệu vào bảng `products`
        DB::table('products')->insert([
            [
                'name' => 'Dell Vostro',
                'description' => 'Máy tính xách tay Dell Vostro với cấu hình mạnh mẽ.',
                'price' => 15000000,
                'quantity' => 9,
                'image' => 'dell_vostro.png',
                'category_id' => 1 // Assuming 'Máy tính' has id 1
            ],
            [
                'name' => 'Dell Inspiron',
                'description' => 'Máy tính xách tay Dell Inspiron với thiết kế thời trang.',
                'price' => 12000000,
                'quantity' => 7,
                'image' => 'dell_inspiron.png',
                'category_id' => 1
            ],
            [
                'name' => 'Inspiron 3',
                'description' => 'Máy tính xách tay Inspiron 3, nhẹ và tiện dụng cho công việc.',   
                'price' => 10000000,
                'quantity' => 4,
                'image' => 'inspiron_3.png',
                'category_id' => 1
            ],
            [
                'name' => 'iPhone 16',
                'description' => 'iPhone 16 mới nhất với nhiều tính năng ưu việt.',
                'price' => 12000000,
                'quantity' => 12,
                'image' => 'iphone_16.png',
                'category_id' => 4 // Assuming 'iphone' has id 4
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'iPhone 15 Pro Max, flagship với camera và hiệu suất tuyệt vời.',
                'price' => 15000000,
                'quantity' => 7,
                'image' => 'iphone_15_pro_max.png',
                'category_id' => 4
            ],
            [
                'name' => 'iPhone 14 Pro',
                'description' => 'iPhone 14 Pro, một lựa chọn tốt cho những ai yêu thích công nghệ.',
                'price' => 11000000,
                'quantity' => 23,
                'image' => 'iphone_14_pro.png',
                'category_id' => 4
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}
