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
        Schema::create('meeting_log_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_log_id')->constrained()->cascadeOnDelete();
            $table->string('zoom_meeting_id')->nullable(); // Zoom's participant ID or uuid
            $table->string('participant_name')->nullable();
            $table->string('participant_email')->nullable();
            $table->dateTime('join_time')->nullable();
            $table->dateTime('leave_time')->nullable();
            $table->integer('attend_minutes')->default(0);
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_log_participants');
    }
};
