<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tavus_personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('persona_id')->unique();
            $table->string('persona_name');
            $table->text('system_prompt')->nullable();
            $table->text('context')->nullable();
            $table->json('layers')->nullable(); // Tavus persona layers configuration
            $table->string('default_replica_id')->nullable();
            $table->json('properties')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('persona_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tavus_personas');
    }
};
