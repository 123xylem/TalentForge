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
        Schema::table('listing_applications', function (Blueprint $table) {
            $table->dropColumn('status');

            $table->enum('status', ['unread', 'applied', 'shortlisted', 'rejected'])
                ->default('unread');
            //TODO $table->json('screening_answers')->nullable(); For JSON based [Screening questions, required answers]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listing_applications', function (Blueprint $table) {
            $table->dropColumn('status');

            $table->enum('status', ['unread', 'read', 'applied', 'shortlisted', 'rejected'])
                ->default('unread');
        });
    }
};
