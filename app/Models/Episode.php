<?php

namespace App\Models;

use Database\Factories\EpisodeFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Builder as QueryBuilder;

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

    /**
     * Scope a query to only include employees
     * with an active episode in the given month.
     *
     * @param  Builder<Episode>  $query
     */
    #[Scope]
    protected function inMonth(Builder $query, string $month): void
    {
        $query
            ->where('start_date', function (QueryBuilder $q) use ($month) {
                // With this complicated subquery we get the row with the
                // current data for the specified month.
                $q->from('episodes as e2')
                    ->selectRaw('MAX(`e2`.`start_date`)')
                    ->whereRaw('episodes.employee_id = e2.employee_id')
                    ->where('e2.start_date', '<=', $month);
            })
            // This filters out the episodes with "Vertragsende".
            ->whereDoesntHave('comment', function (Builder $q) {
                $q->where('comment', '=', 'Vertragsende');
            });
    }

    protected function year(): Attribute
    {
        return new Attribute(
            get: fn (): string => explode('-', (string) $this->start_date)[0]
        );
    }

    protected function month(): Attribute
    {
        return new Attribute(
            get: fn (): string => explode('-', (string) $this->start_date)[1]
        );
    }

    /**
     * Get the employee for an episode.
     *
     * @return BelongsTo<Employee, $this>
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
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
