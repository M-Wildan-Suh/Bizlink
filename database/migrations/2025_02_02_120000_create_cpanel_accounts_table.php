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
        Schema::create('cpanel_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(2083);
            $table->string('username');
            $table->string('primary_domain')->nullable();
            $table->text('api_token');
            $table->boolean('use_ssl')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['host', 'username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cpanel_accounts');
    }
};
