<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    protected $fillable = [
        'employee_id',
        'name',
        'start_date',
        'staffgroup_id',
        'vk',
        'factor_night',
        'factor_nef',
        'comment_id',
    ];

    /**
     * Get the comment for an episode.
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the staffgroup for an episode.
     */
    public function staffgroup(): BelongsTo
    {
        return $this->belongsTo(Staffgroup::class);
    }
}
