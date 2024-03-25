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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('role');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        \App\Models\User::create([
            'name' => env('DB_USER_DEFAULT_NAME', "user_name"),
            'phone' => env('DB_USER_DEFAULT_PHONE',  "user_phone"),
            'role' => env('DB_USER_DEFAULT_ROLE',  "user_role"),
            'email' => env('DB_USER_DEFAULT_EMAIL', "user_email"),
            'password' => bcrypt(env('DB_USER_DEFAULT_PASSWORD',  "user_password")),
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
