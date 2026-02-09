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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecture_page_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes');
            $table->string('zoom_meeting_id')->nullable();
            $table->text('zoom_join_url')->nullable(); // For participants
            $table->text('zoom_start_url')->nullable(); // For host (hidden from students)
            $table->string('zoom_passcode')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
