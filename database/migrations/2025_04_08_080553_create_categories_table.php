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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('parent_id')->unsigned()->nullable();
            $table->string('slug')->unique();
            $table->foreignId('parent_id')
                ->nullable()  // Top-level categories have no parent
                ->constrained('categories')  // Look only in categories table
                ->onDelete('set null');  // If parent is deleted, children become top-level
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
