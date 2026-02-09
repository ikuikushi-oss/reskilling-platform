<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('meeting_logs', function (Blueprint $table) {
            $table->longText('transcript_text')->nullable()->after('memo');
            $table->enum('transcript_status', ['not_uploaded', 'ready', 'failed'])->default('not_uploaded')->after('transcript_text');
            $table->string('transcript_source')->default('youtube_caption')->after('transcript_status');
            $table->dateTime('transcript_uploaded_at')->nullable()->after('transcript_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_logs', function (Blueprint $table) {
            $table->dropColumn(['transcript_text', 'transcript_status', 'transcript_source', 'transcript_uploaded_at']);
        });
    }
};
