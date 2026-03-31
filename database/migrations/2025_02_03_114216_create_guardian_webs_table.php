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
        Schema::create('guardian_webs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('url')->unique();

            $table->boolean('use_cpanel')->default(false);
            $table->foreignId('cpanel_account_id')
                ->nullable()
                ->constrained('cpanel_accounts')
                ->nullOnDelete();
            $table->string('cpanel_domain_type')->nullable();
            $table->timestamp('cpanel_domain_created_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_webs');
    }
};
