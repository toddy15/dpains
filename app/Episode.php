<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'employee_id', 'name', 'start_date', 'staffgroup_id', 'vk', 'factor_night', 'factor_nef', 'comment_id',
    ];

    /**
     * Get the comment for an episode.
     */
    public function comment()
    {
        return $this->belongsTo('App\Comment');
    }

    /**
     * Get the staffgroup for an episode.
     */
    public function staffgroup()
    {
        return $this->belongsTo('App\Staffgroup');
    }
}
