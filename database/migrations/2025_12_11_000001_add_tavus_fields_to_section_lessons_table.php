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
        Schema::table('section_lessons', function (Blueprint $table) {
            $table->string('tavus_conversation_id')->nullable()->after('summary');
            $table->string('tavus_replica_id')->nullable()->after('tavus_conversation_id');
            $table->text('tavus_api_key')->nullable()->after('tavus_replica_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('section_lessons', function (Blueprint $table) {
            $table->dropColumn(['tavus_conversation_id', 'tavus_replica_id', 'tavus_api_key']);
        });
    }
};
