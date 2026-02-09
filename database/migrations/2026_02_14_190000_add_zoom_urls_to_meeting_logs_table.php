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
            $table->text('zoom_join_url')->nullable()->after('zoom_meeting_id');
            $table->text('zoom_start_url')->nullable()->after('zoom_join_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('meeting_logs', function (Blueprint $table) {
            $table->dropColumn(['zoom_join_url', 'zoom_start_url']);
        });
    }
};
