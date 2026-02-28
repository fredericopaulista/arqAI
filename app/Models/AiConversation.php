<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    protected $fillable = ['project_id', 'role', 'message'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
