<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rawplan extends Model
{
    protected $fillable = [
        'month', 'people', 'shifts',
    ];
}
