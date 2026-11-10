<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'event_id',
        'full_name',
        'email',
        'phone_number',
        'institution',
        'position',
        'attendance_status',
        'qr_code_token',
        'is_checked_in',
        'checked_in_at',
        'checked_in_by',
        'checkin_method',
        'email_sent',
        'email_sent_at',
        'mailketing_message_id',
        'ip_address',
        'user_agent',
        'attended_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_checked_in' => 'boolean',
            'checked_in_at' => 'datetime',
            'email_sent' => 'boolean',
            'email_sent_at' => 'datetime',
            'attended_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Attendance $attendance) {
            if (empty($attendance->uuid)) {
                $attendance->uuid = (string) Str::uuid();
            }

            if (empty($attendance->qr_code_token)) {
                $attendance->qr_code_token = 'EVT-'.strtoupper(Str::random(12));
            }

            if (empty($attendance->attended_at)) {
                $attendance->attended_at = now();
            }
        });
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->attendance_status) {
            'hadir' => 'Hadir',
            'tidak_hadir' => 'Tidak Hadir',
            'hadir_terlambat' => 'Hadir Terlambat',
            default => ucfirst(str_replace('_', ' ', (string) $this->attendance_status)),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->attendance_status) {
            'hadir' => 'emerald',
            'tidak_hadir' => 'rose',
            'hadir_terlambat' => 'amber',
            default => 'slate',
        };
    }
}
