<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientProgress extends Model
{
    protected $fillable = [
        'patient_id',
        'milestone_id',
        'status',
        'completed_at',
        'reviewed_at',
        'notes',
        'doctor_notes'
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProgramMilestone::class, 'milestone_id');
    }

    public function dailyLog(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PatientDailyLog::class, 'progress_id');
    }
}
