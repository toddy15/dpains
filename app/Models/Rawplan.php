<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rawplan extends Model
{
    protected $fillable = [
        'month', 'people', 'shifts',
    ];
}
