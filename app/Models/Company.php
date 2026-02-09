<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'status', 'contract_start_date', 'business_description', 'teacher_id'];

    protected $casts = [
        'contract_start_date' => 'date',
    ];

    // Status Constants
    const STATUS_FREE_TRIAL = 'free_trial';
    const STATUS_ACTIVE = 'active';
    const STATUS_FINISHED = 'finished';

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_FREE_TRIAL => '無料研修中',
            self::STATUS_ACTIVE => '研修中',
            self::STATUS_FINISHED => '修了済',
            default => '不明',
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            self::STATUS_FREE_TRIAL => 'bg-amber-100 text-amber-800',
            self::STATUS_ACTIVE => 'bg-emerald-100 text-emerald-800',
            self::STATUS_FINISHED => 'bg-slate-100 text-slate-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Teacher assigned to this company (1:1)
    public function teacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    // Teachers assigned to this company (Legacy/Many-to-Many - keeping for safety but not using for new logic)
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user');
    }

    // Students belonging to this company
    public function students(): HasMany
    {
        return $this->hasMany(User::class)->where('role', User::ROLE_STUDENT);
    }
}
