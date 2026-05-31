<?php

namespace Database\Seeders;

use App\Models\FollowUpMilestone;
use Illuminate\Database\Seeder;

class FollowUpMilestoneSeeder extends Seeder
{
    public function run(): void
    {
        $milestones = [
            ['days_before_due' => 15, 'label' => '15 days before due', 'is_active' => true, 'daily_after_due' => false],
            ['days_before_due' => 7,  'label' => '7 days before due',  'is_active' => true, 'daily_after_due' => false],
            ['days_before_due' => 3,  'label' => '3 days before due',  'is_active' => true, 'daily_after_due' => false],
            ['days_before_due' => 1,  'label' => '1 day before due',   'is_active' => true, 'daily_after_due' => false],
            ['days_before_due' => 0,  'label' => 'Daily after due',    'is_active' => true, 'daily_after_due' => true],
        ];
        foreach ($milestones as $m) {
            FollowUpMilestone::create($m);
        }
    }
}
