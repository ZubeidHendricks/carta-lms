<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tavus_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('conversation_id')->unique();
            $table->string('conversation_name')->nullable();
            $table->string('replica_id');
            $table->string('persona_id')->nullable();
            $table->text('conversational_context')->nullable();
            $table->text('custom_greeting')->nullable();
            $table->string('status')->default('active'); // active, ended, failed
            $table->integer('duration')->nullable(); // in seconds
            $table->json('properties')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
            $table->index('replica_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tavus_conversations');
    }
};
