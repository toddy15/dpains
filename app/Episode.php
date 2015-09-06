<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'number', 'name', 'start_date', 'vk', 'factor_night', 'factor_nef', 'comment_id'
    ];

    /**
     * Get the comment for an episode.
     */
    public function comment()
    {
        return $this->belongsTo('App\Comment');
    }
}
