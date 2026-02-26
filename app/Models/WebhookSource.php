<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookSource extends Model
{
    /** @use HasFactory<\Database\Factories\WebhookSourceFactory> */
    use HasFactory;

    protected $fillable = [
        'project_id',
        'source_key',
        'source_type',
        'name',
        'signing_secret',
        'event_mappings',
        'is_active',
    ];

    protected $casts = [
        'event_mappings' => 'array',
        'is_active' => 'bool',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
