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
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('account_balance')->default(0);
            $table->string('account_currency')->default('UGX');
            $table->string('role')->default('user');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_wallet_active')->default(false);
            $table->boolean('show_wallet_balance')->default(false);
            $table->string('pin')->nullable();
            $table->string('device_token')->nullable();
            $table->boolean('is_user_verified')->default(false);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
