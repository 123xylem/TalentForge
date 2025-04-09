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
        Schema::dropIfExists('listing_category');
        Schema::dropIfExists('user_skill');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
