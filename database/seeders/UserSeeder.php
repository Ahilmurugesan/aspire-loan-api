<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Admin User
        User::factory()->create([
            'name'     => 'Admin User',
            'email'    => 'admin@mail.com',
            'is_admin' => 1
        ]);

        // Non admin users
        User::factory(10)->create();
    }
}
