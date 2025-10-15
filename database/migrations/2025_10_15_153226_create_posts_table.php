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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false);
            $table->string('tldr');
            $table->text('content')->nullable(false);
            $table->text('image_path')->nullable(false);
            $table->foreignId('author_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->dateTime('published_at');
            $table->boolean('status')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
