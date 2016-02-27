<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DueShift extends Model
{
    protected $fillable = [
        'staffgroup_id', 'year', 'nights', 'nefs'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the staffgroup for the entry.
     */
    public function staffgroup()
    {
        return $this->belongsTo('App\Staffgroup');
    }
}
