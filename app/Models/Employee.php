<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'email', 'hash', 'bu_start',
    ];

    /**
     * Get the episodes for the employee.
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class);
    }

    /**
     * Return the name of the last episode
     */
    public function getNameAttribute()
    {
        $last_episode = $this->episodes()->latest('start_date')->first();

        return $last_episode->name;
    }
}
