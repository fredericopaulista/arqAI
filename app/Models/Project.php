<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasTenant, \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'original_image_path',
        'generated_image_path',
        'briefing_json',
        'status',
        'generation_started_at',
        'generation_completed_at',
    ];

    protected $casts = [
        'briefing_json' => 'array',
        'generation_started_at' => 'datetime',
        'generation_completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversations()
    {
        return $this->hasMany(AiConversation::class);
    }
}
