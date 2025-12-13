<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tavus_replicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('replica_id')->unique();
            $table->string('replica_name');
            $table->text('training_video_url')->nullable();
            $table->string('status')->default('pending'); // pending, training, ready, failed
            $table->text('callback_url')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('trained_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tavus_replicas');
    }
};
