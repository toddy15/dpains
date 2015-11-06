<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'email', 'hash'
    ];

    /**
     * Get the episodes for the employee.
     */
    public function episodes()
    {
        return $this->hasMany('App\Episode');
    }

    /**
     * Return the name of the last episode
     */
    public function getNameAttribute()
    {
        $last_episode = $this->episodes()->orderBy('start_date', 'DESC')->first();
        return $last_episode->name;
    }

}
