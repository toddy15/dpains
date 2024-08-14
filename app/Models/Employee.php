<?php

namespace App\Models;

use Database\Factories\EmployeeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /** @use HasFactory<EmployeeFactory> */
    use HasFactory;

    protected $fillable = ['email', 'hash', 'bu_start'];

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
    protected function name(): Attribute
    {
        return Attribute::get(
            function () {
                $last_episode = $this->episodes()
                    ->latest('start_date')
                    ->firstOrFail();

                return $last_episode->name;
            }
        );
    }
}
