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
            Schema::create('order_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id'); // Liên kết với đơn hàng
                $table->unsignedBigInteger('cart_item_id');
                // ->after('order_id');
                $table->unsignedBigInteger('product_id'); // Liên kết với sản phẩm
                $table->string('name'); // Tên sản phẩm
                $table->integer('quantity'); // Số lượng
                $table->decimal('price', 15, 2); // Giá sản phẩm
                $table->decimal('total_price', 15, 2); // Tổng giá tiền cho sản phẩm
                $table->timestamps();

                // Khóa ngoại
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
                $table->foreign('cart_item_id')->references('id')->on('cart_items');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('order_items');
        }
    };
