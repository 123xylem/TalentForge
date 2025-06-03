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
        Schema::create('listing_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('listing_id');
            $table->unsignedBigInteger('user_id');
            $table->string('cv')->nullable();
            $table->string('cover_letter')->nullable();
            //User unread or read. Then user applied. Then employer shortlisted or rejected.
            $table->enum('status', ['unread', 'read', 'applied', 'shortlisted', 'rejected'])->default('unread');
            //TODO $table->json('screening_answers')->nullable(); For JSON based [Screening questions, required answers]
            $table->timestamps();

            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_applications');
    }
};
