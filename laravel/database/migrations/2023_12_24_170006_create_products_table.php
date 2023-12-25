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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('price');
            $table->string('cover_image');
            $table->json('images');
            $table->string('pick_up_location');
            $table->string('weight');
            $table->boolean('is_delivery_available')->default(true);
            $table->boolean('is_delivery_fee_covered')->default(true);
            $table->boolean('is_delivery_set')->default(false);
            $table->boolean('is_donation')->default(true);
            $table->boolean('is_product_new')->default(true);
            $table->boolean('is_product_available_for_all')->default(false);
            $table->boolean('is_product_damaged')->default(false);
            $table->boolean('is_product_rejected')->default(false);
            $table->boolean('is_product_accepted')->default(false);
            $table->string('reason')->nullable();
            $table->string('damage_description')->nullable();
            $table->string('status');
            $table->softDeletes();
            $table->string('total_amount')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
