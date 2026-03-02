<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EventScoreRule extends Model
{
    protected $table = 'event_scores_rules';
    
    protected $fillable = [
        'field',
        'operator',
        'value',
        'points',
        'active',
    ];

    /**
     * Calculate the score for a given event based on the active rules.
     */ 
    public function calculateScore(array $normalizedPayload): int
    {
        $score = 0;

        // Fetch active rules
        $rules = self::where('active', true)->get();

        foreach ($rules as $rule) {
            if ($this->matchesRule($normalizedPayload, $rule)) {
                $score += $rule->points;
            }
        }

        return $score;
    }

    /**
     * Check if the normalized payload matches the given rule.
     */
    private function matchesRule(array $normalizedPayload, $rule): bool
    {
        $fieldValue = $normalizedPayload[$rule->field] ?? null;

        return match ($rule->operator) {
            'equals'        => $fieldValue == $rule->value,
            'not_equals'    => $fieldValue != $rule->value,
            'contains'      => $this->valueContains($fieldValue, $rule->value),
            'not_contains'  => !$this->valueContains($fieldValue, $rule->value),
            'greater_than'  => is_numeric($fieldValue) && $fieldValue > $rule->value,
            'less_than'     => is_numeric($fieldValue) && $fieldValue < $rule->value,
            'in'            => in_array($fieldValue, explode(',', $rule->value)),
            default         => false,
        };
    }

    /**
     * Check if a value "contains" the given search term.
     * - For arrays: recursively checks if the search term exists as a key anywhere in the array.
     * - For strings: checks if the search term is a substring.
     */
    private function valueContains(mixed $fieldValue, string $search): bool
    {
        if (is_array($fieldValue)) {
            return $this->arrayHasKey($fieldValue, $search);
        }

        if (is_string($fieldValue)) {
            return str_contains($fieldValue, $search);
        }

        return false;
    }

    /**
     * Recursively check if an array has a key anywhere in its nested structure.
     */
    private function arrayHasKey(array $array, string $key): bool
    {
        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach ($array as $value) {
            if (is_array($value) && $this->arrayHasKey($value, $key)) {
                return true;
            }
        }

        return false;
    }

    public function getEventScoreRules(array $filters): Builder
    {
        $active = $filters['active'] ?? true;
        $points = $filters['points'] ?? null;

        return self::query()
            ->select([
                'id',
                'field',
                'operator',
                'value',
                'points',
                'active',
                'created_at',
                'updated_at',
            ])
            ->orderByDesc('created_at')
            ->when($active !== null, fn (Builder $q) => $q->where('active', (bool) $active))
            ->when(
                $points !== null && $points !== '',
                fn (Builder $q) => $q->where('points', '=', (int) $points)
            );
    }
}
