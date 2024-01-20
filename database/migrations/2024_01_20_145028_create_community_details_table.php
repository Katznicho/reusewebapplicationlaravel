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
        Schema::create('community_details', function (Blueprint $table) {
            $table->id();
            $table->text("purpose");
            $table->string("location");
            $table->foreignId("community_category_id")->constrained();
            $table->foreignId("user_id")->constrained();
            $table->string("contact_person")->nullable();
            $table->string("contact_number")->nullable();
            $table->string("contact_person_email")->nullable();
            $table->string("contact_person_role")->nullable();
            $table->string("website")->nullable();
            $table->string("total_members")->nullable();
            $table->string("total_members_women")->nullable();
            $table->string("total_members_men")->nullable();
            $table->string("year_started")->nullable();
            $table->string("leader_name")->nullable();
            $table->string("leader_role")->nullable();
            $table->string("leader_email")->nullable();
            $table->string("leader_contact")->nullable();
            $table->string("images")->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_details');
    }
};
