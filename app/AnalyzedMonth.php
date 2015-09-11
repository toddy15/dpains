<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnalyzedMonth extends Model
{
    protected $fillable = [
        'month', 'number', 'nights', 'nefs',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
