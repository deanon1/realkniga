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
        Schema::create('service_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('rating')->default(5); // 1-5 звезд
            $table->text('review_text');
            $table->text('filtered_text')->nullable(); // отфильтрованный текст
            $table->boolean('is_approved')->default(false);
            $table->boolean('contains_profanity')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'is_approved']);
            $table->index(['rating', 'is_approved']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_reviews');
    }
};
