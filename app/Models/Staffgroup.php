<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staffgroup extends Model
{
    protected $fillable = ['staffgroup', 'weight'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
