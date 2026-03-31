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
        Schema::create('traffic', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('article_show_id');
            $table->foreign('article_show_id')->references('id')->on('article_shows')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('article_id')->nullable();
            $table->foreign('article_id')->references('id')->on('articles')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('guardian_web_id')->nullable();
            $table->foreign('guardian_web_id')->references('id')->on('guardian_webs')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('access')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic');
    }
};
