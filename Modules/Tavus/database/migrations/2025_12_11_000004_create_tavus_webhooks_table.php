<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tavus_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('event_type'); // replica.trained, video.generated, conversation.started, etc.
            $table->string('resource_id'); // replica_id, video_id, conversation_id
            $table->string('resource_type'); // replica, video, conversation
            $table->json('payload');
            $table->boolean('processed')->default(false);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index('event_type');
            $table->index('resource_id');
            $table->index('processed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tavus_webhooks');
    }
};
