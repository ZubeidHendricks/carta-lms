<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tavus_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('video_id')->unique();
            $table->string('video_name');
            $table->string('replica_id');
            $table->text('script')->nullable();
            $table->text('background_source_url')->nullable();
            $table->string('status')->default('pending'); // pending, processing, ready, failed
            $table->text('download_url')->nullable();
            $table->text('hosted_url')->nullable();
            $table->string('local_path')->nullable();
            $table->integer('duration')->nullable(); // in seconds
            $table->json('variables')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('course_id');
            $table->index('status');
            $table->index('replica_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tavus_videos');
    }
};
