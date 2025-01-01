<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Mas Ikyy',
            'email'  => 'masikyy@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        \App\Models\User::create([
            'name' => 'Rizky Darmawan',
            'email' => 'rizkyd@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        \App\Models\User::create([
            'name' => 'Joko Widodo',
            'email' => 'jokowi@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        \App\Models\User::create([
            'name' => 'Prabowo Subianto',
            'email' => 'prabowo@gmail.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
