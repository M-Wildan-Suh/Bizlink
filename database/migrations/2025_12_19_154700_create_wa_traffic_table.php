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
        Schema::create('wa_traffic', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('article_show_id');
            $table->foreign('article_show_id')->references('id')->on('article_shows')->onUpdate('cascade')->onDelete('cascade');

            $table->integer('access')->default(1);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_traffic');
    }
};
