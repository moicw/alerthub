<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;
    protected $fillable = [
        'organization_id',
        'name',
        'description',
         'uuid',
    ];

    
    public function getRouteKeyName()
    {
        return 'uuid';
    }
    protected static function booted()
    {
        static::creating(function ($project) {
            if (empty($project->uuid)) {
                $project->uuid = (string) Str::uuid();
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function subscribers()
    {
        return $this->hasMany(Subscriber::class);
    }

    public function alertRules()
    {
        return $this->hasMany(AlertRule::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function webhookSources()
    {
        return $this->hasMany(WebhookSource::class);
    }
}
