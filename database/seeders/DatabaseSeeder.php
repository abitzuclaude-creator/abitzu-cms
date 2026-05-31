<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Idempotent: skip if the database is already seeded (safe on redeploys).
        if (User::query()->exists()) {
            return;
        }

        $this->call([
            UserSeeder::class,
            BankAccountSeeder::class,
            WhatsappTemplateSeeder::class,
            FollowUpMilestoneSeeder::class,
            ClientSeeder::class,
        ]);
    }
}
