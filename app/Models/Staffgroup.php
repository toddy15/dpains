<?php

namespace App\Models;

use Database\Factories\StaffgroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staffgroup extends Model
{
    /** @use HasFactory<StaffgroupFactory> */
    use HasFactory;

    protected $fillable = ['staffgroup', 'weight'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
