<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EventScoreRule;

class EventScoreRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EventScoreRule::create([
            'field' => 'status',
            'operator' => 'equals',
            'value' => 'failed',
            'points' => 70,
            'active' => true,
        ]);

        EventScoreRule::create([
            'field' => 'type',
            'operator' => 'equals',
            'value' => 'payment_transaction',
            'points' => 70,
            'active' => true,
        ]);
        EventScoreRule::create([
            'field' => 'normalized_data',
            'operator' => 'contains',
            'value' => 'interested_in',
            'points' => 70,
            'active' => true,
        ]);

        EventScoreRule::create([
            'field' => 'normalized_data',
            'operator' => 'contains',
            'value' => 'complaint',
            'points' => 70,
            'active' => true,
        ]);

        EventScoreRule::create([
            'field' => 'normalized_data',
            'operator' => 'contains',
            'value' => 'customer_support',
            'points' => 60,
            'active' => true,
        ]);

        EventScoreRule::create([
            'field' => 'status',
            'operator' => 'equals',
            'value' => 'succeeded',
            'points' => 10,
            'active' => true,
        ]);

        EventScoreRule::create([
            'field' => 'status',
            'operator' => 'equals',
            'value' => 'shipped',
            'points' => 20,
            'active' => true,
        ]);

        EventScoreRule::create([
            'field' => 'type',
            'operator' => 'equals',
            'value' => 'form_submission',
            'points' => 30,
            'active' => true,
        ]);

    }
}
