<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LearnerSeeder extends Seeder
{
    /**
     * Two demo learners so candidates can test login and per-user progress.
     * Both use the password "password".
     */
    public function run(): void
    {
        foreach ([
            ['Amara Perera', 'amara@example.com'],
            ['Nuwan Fernando', 'nuwan@example.com'],
        ] as [$name, $email]) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => $name, 'password' => Hash::make('password')]
            );
        }
    }
}
