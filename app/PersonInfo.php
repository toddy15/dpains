<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonInfo extends Model
{
    protected $fillable = [
      'number', 'hash'
    ];
}
