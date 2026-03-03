<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Event extends Model
{
    protected $table = 'events';
    
    protected $fillable = [
        'uuid',
        'external_id',
        'type',
        'source',
        'raw_payload',
        'normalized_payload',
        'occurred_at',
        'enrichment',
        'status',
        'score',
        'error',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'normalized_payload' => 'array',
        'enrichment' => 'array',
        'occurred_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function getEvents($filters): Builder
    {

        $from = Carbon::parse($filters['date_from'])->startOfDay()->format('Y-m-d H:i:s');
        $to   = Carbon::parse($filters['date_to'])->endOfDay()->format('Y-m-d H:i:s');
        $type = $filters['type'] ?? null;
        $status = $filters['status'] ?? null;
        
        $query = self::query()
                ->select([
                    'id',
                    'uuid',
                    'external_id',
                    'source',
                    'type',
                    'status',
                    'score',
                    'error',
                    'raw_payload',
                    'normalized_payload',
                    'enrichment',
                    'occurred_at',
                    'created_at',
                    'updated_at',
                ])
                ->when($status ?? null, fn (Builder $q, $status) => $q->where('status', $status))
                ->when($type ?? null, fn (Builder $q, $type) => $q->where('type', $type))
                ->when($from, fn (Builder $q) =>
                    $q->whereDate('created_at', '>=', $from)
                )
                ->when($to, fn (Builder $q) =>
                    $q->whereDate('created_at', '<=', $to)
                )
                ->orderByDesc('created_at');

        // dd($query->toSql(), $query->getBindings());

        return $query;
    }

    /**
     * Process the score for the event based on its normalized payload.
     * This method can be called after normalization to calculate and update the score of the event.
     *
     * The scoring logic can be implemented in the EventScoreRule class, which can analyze the normalized data and assign a score accordingly.
     * For example, if the event is a form submission with certain answers, we can assign a higher score to indicate a more promising lead.
     *
     * @return void
     */
    public function processScore(): void
    {
        $scoreRules =  new EventScoreRule();
        $score = $scoreRules->calculateScore($this->normalized_payload ?? []);
        $this->score = $score;
        $this->save();
    }

}
