<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    const STATUS_SUBMITTED = 'submitted';
    const STATUS_REVISION_REQUIRED = 'revision_required';
    const STATUS_PASSED = 'passed';

    protected $fillable = [
        'user_id',
        'lecture_page_id',
        'status',
        'teacher_comment',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lecturePage(): BelongsTo
    {
        return $this->belongsTo(LecturePage::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SubmissionItem::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
