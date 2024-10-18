<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * @var User $echo
         */
        $echo = User::query()->find(1);
        
        /**
         * @var User $george
         */
        $george = User::query()->find(2);
        
        // Assign the user role to Echo.
        $echo->assignRole('user');
        
        // Assign the admin role to George.
        $george->assignRole('admin');
    }
}
