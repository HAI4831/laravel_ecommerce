<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // $table->string('invoice_id')->unique(); // Mã hoá đơn
            $table->string('txn_ref')->unique(); // Mã giao dịch
            $table->unsignedBigInteger('user_id'); // Liên kết với người dùng
            $table->string('order_info')->nullable(); // Thông tin đơn hàng
            // $table->json('items');
            $table->decimal('amount', 15, 2); // Tổng số tiền
            $table->enum('status', ['pending', 'confirmed', 'paid', 'shipped', 'completed', 'canceled', 'failed'])->default('pending');
            $table->string('customer_name'); // Tên khách hàng
            $table->string('payment_method'); // Phương thức thanh toán
            $table->timestamp('payment_date')->nullable();//Ngày thanh toán
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
