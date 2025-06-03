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
    Schema::dropIfExists('listing_skill');

    Schema::create('listing_skill', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('listing_id');
      $table->unsignedBigInteger('skill_id');
      $table->timestamps();

      $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
      $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('listing_skill');
  }
};
