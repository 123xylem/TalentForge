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
            //Get owner through listing
            $table->foreignId('listing_id')->constrained('listings');
            $table->foreignId('user_id')->constrained('users');
            $table->string('cv')->nullable();
            $table->string('cover_letter')->nullable();
            //User unread or read. Then user applied. Then employer shortlisted or rejected.
            $table->enum('status', ['unread', 'read', 'applied', 'shortlisted', 'rejected'])->default('unread');
            //TODO $table->json('screening_answers')->nullable(); For JSON based [Screening questions, required answers]
            $table->timestamps();
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
