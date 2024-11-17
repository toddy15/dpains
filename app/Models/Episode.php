<?php

namespace App\Models;

use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Episode extends Model
{
    /** @use HasFactory<EpisodeFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'name',
        'start_date',
        'staffgroup_id',
        'vk',
        'factor_night',
        'factor_nef',
        'comment_id',
    ];

    protected function year(): Attribute
    {
        return new Attribute(
            get: fn () => explode('-', (string) $this->start_date)[0]
        );
    }

    protected function month(): Attribute
    {
        return new Attribute(
            get: fn () => explode('-', (string) $this->start_date)[1]
        );
    }

    /**
     * Get the comment for an episode.
     *
     * @return BelongsTo<Comment, $this>
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }

    /**
     * Get the staffgroup for an episode.
     *
     * @return BelongsTo<Staffgroup, $this>
     */
    public function staffgroup(): BelongsTo
    {
        return $this->belongsTo(Staffgroup::class);
    }
}
