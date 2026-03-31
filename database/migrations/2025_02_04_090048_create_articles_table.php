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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('guardian_web_id')->nullable();
            $table->foreign('guardian_web_id')->references('id')->on('guardian_webs')->onUpdate('cascade')->onDelete('cascade');
            $table->text('judul');
            $table->longText('article');
            $table->string('article_type')->default('unique');
            $table->string('video_type');
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('no_telephone')->nullable();
            $table->string('no_whatsapp')->nullable();
            $table->boolean('schedule')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
