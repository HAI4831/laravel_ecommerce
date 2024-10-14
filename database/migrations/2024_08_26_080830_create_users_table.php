<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('role')->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

        // Insert default users
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'address' => 'HN',
                'phone' => '035346355',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'user',
                'email' => 'user@example.com',
                'password' => Hash::make('user'),
                'role' => 'user',
                'address' => 'HN',
                'phone' => '0355346448',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nguyễn Văn Hải',
                'email' => 'nvh@gmail.com',
                'password' => Hash::make('abCD@1234'),
                'address' => 'NB',
                'phone' => '0373194245',
                'role' => 'user',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nguyen Van Hai',
                // 'name' => 'Nguyễn Văn Hải',
                'email' => 'nvhai227@gmail.com',
                'password' => Hash::make('abCD@1234'),
                'address' => 'NB',
                'phone' => '0373194245',
                'role' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nguyễn Văn Hải',
                'email' => 'existingUser@gmail.com',
                'password' => Hash::make('abCD@1234'),
                'address' => 'NB',
                'phone' => '0373194245',
                'role' => 'admin',
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
        Schema::dropIfExists('users');
    }
};
