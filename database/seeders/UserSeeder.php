<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Abitzu Owner',
            'email'    => 'admin@abitzu.com',
            'password' => Hash::make('ChangeMeOnFirstLogin!'),
            'role'     => 'owner',
            'is_active' => true,
        ]);

        $agents = [
            ['name' => 'Priya Nair',   'email' => 'priya@abitzu.com',  'role' => 'agent'],
            ['name' => 'Rohan Mehta',  'email' => 'rohan@abitzu.com',  'role' => 'agent'],
            ['name' => 'Aisha Khan',   'email' => 'aisha@abitzu.com',  'role' => 'agent'],
            ['name' => 'Vikram Rao',   'email' => 'vikram@abitzu.com', 'role' => 'agent'],
        ];

        foreach ($agents as $a) {
            User::create(array_merge($a, ['password' => Hash::make('password'), 'is_active' => true]));
        }
    }
}
