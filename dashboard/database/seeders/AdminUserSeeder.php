<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@createlize.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('Createlize'),
                'is_admin' => true,
            ]
        );
    }


}
