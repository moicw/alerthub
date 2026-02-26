<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;

    // ✅ Add all columns you want to allow mass assignment
    protected $fillable = [
        'project_id',
        'email',
        'external_id',
        'name',
        'notification_count',
        'last_notified_at',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_notified_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // Relationship with notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
