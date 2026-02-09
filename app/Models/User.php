<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    const ROLE_ADMIN = 'admin';
    const ROLE_TEACHER = 'teacher';
    const ROLE_STUDENT = 'student';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeacher(): bool
    {
        return $this->role === self::ROLE_TEACHER;
    }

    public function isStudent(): bool
    {
        return $this->role === self::ROLE_STUDENT;
    }

    // Student belongs to a company
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Teacher works for many companies (1:1 now means Teacher hasMany Companies)
    public function assignedCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'teacher_id');
    }

    // Student has many submissions
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    // Teacher has one profile
    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TeacherProfile::class);
    }

    // Student: Meetings this user participates in
    public function participatingMeetings(): BelongsToMany
    {
        return $this->belongsToMany(Meeting::class, 'meeting_participants', 'student_id', 'meeting_id')
            ->withTimestamps();
    }
}
