<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create a admin user
        User::factory()->count(20)->create();

        User::factory()->create([
            'first_name' => 'Zhi',
            'last_name' => 'Yuan',
            'email' => 'admin@example.com',
            'employee_no' => '003121',
        ])->departments()->sync([3, 21]);
    }
}
