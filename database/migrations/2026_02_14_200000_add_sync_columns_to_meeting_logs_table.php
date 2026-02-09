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
            $table->string('zoom_uuid')->nullable()->after('zoom_meeting_id');
            $table->string('host_email')->nullable()->after('zoom_uuid');
            // start_time already exists as 'started_at'
            $table->dateTime('end_time')->nullable()->after('started_at');
            $table->integer('duration_minutes')->nullable()->after('end_time'); // Actual duration

            $table->string('zoom_sync_status')->default('pending')->after('zoom_status'); // pending, synced, failed
            $table->dateTime('zoom_synced_at')->nullable()->after('zoom_sync_status');
            $table->text('zoom_sync_error')->nullable()->after('zoom_synced_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_logs', function (Blueprint $table) {
            $table->dropColumn([
                'zoom_uuid',
                'host_email',
                'end_time',
                'duration_minutes',
                'zoom_sync_status',
                'zoom_synced_at',
                'zoom_sync_error',
            ]);
        });
    }
};
