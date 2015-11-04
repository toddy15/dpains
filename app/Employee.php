<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'email', 'hash'
    ];

    /**
     * Return the name of the last episode
     */
    public function name()
    {
        $episode = Episode::where('employee_id', $this->id)
            ->orderBy('start_date', 'DESC')
            ->first();
        return $episode->name;
    }

}
