<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DueShift extends Model
{
    protected $fillable = ['staffgroup_id', 'year', 'nights', 'nefs'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the staffgroup for the entry.
     */
    public function staffgroup(): BelongsTo
    {
        return $this->belongsTo(Staffgroup::class);
    }
}
