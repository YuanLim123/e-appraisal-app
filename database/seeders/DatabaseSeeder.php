<?php

namespace Database\Seeders;

use App\Models\User;

use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AppraisalControlSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PositionSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            AppraisalControlSeeder::class,
        ]);
    }
}
