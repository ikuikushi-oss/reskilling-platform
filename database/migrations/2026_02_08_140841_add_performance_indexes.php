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
        Schema::table('companies', function (Blueprint $table) {
            $table->index('status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            // company_id is likely a foreign key and might already be indexed, but explicit index is good if not.
            // checking if index exists is hard in migration without raw SQL, but standard practice is safe enough usually.
            // If strictly needed we can check, but for now assuming it might need one or just skip if FK constraint created it (FK usually create index).
            // Let's stick to non-FK columns or columns used in WHERE.
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->index('scheduled_at');
        });

        Schema::table('meeting_logs', function (Blueprint $table) {
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
        });

        Schema::table('meetings', function (Blueprint $table) {
            $table->dropIndex(['scheduled_at']);
        });

        Schema::table('meeting_logs', function (Blueprint $table) {
            $table->dropIndex(['started_at']);
        });
    }
};
