<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'name', 'start_date', 'vk', 'factor_night', 'factor_nef'
    ];
}
