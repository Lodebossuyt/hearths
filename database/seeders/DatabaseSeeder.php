<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed four Players (password "password") so a full table can be tried locally.
     */
    public function run(): void
    {
        foreach (['Bramble', 'Moss', 'Thistle', 'Wren'] as $name) {
            User::factory()->create([
                'name' => $name,
                'email' => strtolower($name).'@hearths.test',
            ]);
        }
    }
}
