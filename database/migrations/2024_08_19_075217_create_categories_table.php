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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();  // Created at and Updated at fields
        });
        //insert data
        DB::table('categories')->insert([
            ['name' => 'Máy tính'],
            ['name' => 'Thức ăn'],
            ['name' => 'Nước uống'],
            ['name' => 'iphone'],
            ['name' => 'Bánh Ngọt']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
