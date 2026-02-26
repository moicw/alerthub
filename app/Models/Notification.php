<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Events\NotificationCreated;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'project_id',
        'subscriber_id',
        'alert_rule_id',
        'channel',
        'subject',
        'body',
        'status',
        'payload',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($notification) {
            if (empty($notification->uuid)) {
                $notification->uuid = (string) Str::uuid();
            }
        });

        static::created(function ($notification) {
            event(new NotificationCreated($notification));
        });
    }

    public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function alertRule()
    {
        return $this->belongsTo(AlertRule::class);
    }
}
