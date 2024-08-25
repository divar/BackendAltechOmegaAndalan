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
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("title");
            $table->text("description")->nullable();
            $table->date("publish_date")->nullable();
            $table->uuid('author_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
            $table->index('title');
            $table->unique(['title', 'publish_date', 'author_id']);
            $table->fullText('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
