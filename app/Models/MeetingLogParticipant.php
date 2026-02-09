<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingLogParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_log_id',
        'zoom_meeting_id',
        'participant_name',
        'participant_email',
        'join_time',
        'leave_time',
        'attend_minutes',
        'raw_payload',
    ];

    protected $casts = [
        'join_time' => 'datetime',
        'leave_time' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function meetingLog()
    {
        return $this->belongsTo(MeetingLog::class);
    }
}
