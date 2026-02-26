<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertRule extends Model
{
    /** @use HasFactory<\Database\Factories\AlertRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'source_type',
        'event_type',
        'conditions',
        'action',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'bool',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
