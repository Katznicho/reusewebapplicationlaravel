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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('type');//Donation, Product Order or Payment , Topup
            $table->string('amount');
            $table->string('phone_number')->nullable();
            $table->string("payment_mode");
            $table->string("payment_method")->nullable();
            $table->text('description')->nullable();
            $table->boolean("is_annyomous")->default(false);
            $table->string('reference');
            $table->string('status');
            $table->string("order_tracking_id")->nullable();
            $table->string("OrderNotificationType")->nullable();
            $table->foreignId("user_id")->nullable()->references("id")->on("users")->onDelete("cascade");
            $table->foreignId("product_id")->nullable()->references("id")->on("products")->onDelete("cascade");
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
