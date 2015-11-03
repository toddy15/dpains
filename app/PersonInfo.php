<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PersonInfo extends Model
{
    protected $fillable = [
        'number', 'email', 'hash'
    ];

    /**
     * Return all numbers of people
     */
    public static function numbers()
    {
        $numbers = [];
        $episodes = Episode::groupBy('number')->get();
        foreach ($episodes as $episode) {
            $numbers[$episode->number] = $episode->name;
        }
        return $numbers;
    }
}
