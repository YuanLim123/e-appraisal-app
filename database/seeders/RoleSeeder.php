<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Position G1
        Role::create([
            'position_id' => 1,
            'order' => 1,
            'name' => 'OPERATOR',
        ]);

        Role::create([
            'position_id' => 1,
            'order' => 2,
            'name' => 'QA INSPECTOR',
        ]);

        Role::create([
            'position_id' => 1,
            'order' => 2,
            'name' => 'CLEANER',

        ]);


        // Position G2
        Role::create([
            'position_id' => 2,
            'order' => 1,
            'name' => 'JUNIOR TECHNICIAN',

        ]);

        Role::create([
            'position_id' => 2,
            'order' => 1,
            'name' => 'STORE ASST',

        ]);

        Role::create([
            'position_id' => 2,
            'order' => 2,
            'name' => 'SENIOR OPERATOR',

        ]);

        Role::create([
            'position_id' => 2,
            'order' => 3,
            'name' => 'SENIOR INSPECTOR',

        ]);

        // Position G3
        Role::create([
            'position_id' => 3,
            'order' => 1,
            'name' => 'CLERK',

        ]);

        Role::create([
            'position_id' => 3,
            'order' => 2,
            'name' => 'LINE LEADER',

        ]);

        Role::create([
            'position_id' => 3,
            'order' => 2,
            'name' => 'STOREKEEPER',

        ]);

        Role::create([
            'position_id' => 3,
            'order' => 3,
            'name' => 'INDUSTRIAL TRAINEE',

        ]);

        Role::create([
            'position_id' => 3,
            'order' => 3,
            'name' => 'TECHNICIAN',

        ]);

        Role::create([
            'position_id' => 3,
            'order' => 3,
            'name' => 'DRIVER',

        ]);

        // Position G4
        Role::create([
            'position_id' => 4,
            'order' => 1,
            'name' => 'SENIOR TECHNICIAN',

        ]);

        Role::create([
            'position_id' => 4,
            'order' => 1,
            'name' => 'ASST SUPERVISOR',

        ]);

        Role::create([
            'position_id' => 4,
            'order' => 2,
            'name' => 'ASST SUPERVISOR',

        ]);

        Role::create([
            'position_id' => 4,
            'order' => 2,
            'name' => 'SENIOR CLERK',

        ]);

        Role::create([
            'position_id' => 4,
            'order' => 3,
            'name' => 'SENIOR CLERK',

        ]);

        Role::create([
            'position_id' => 4,
            'order' => 3,
            'name' => 'ASST OFFICER',

        ]);

        Role::create([
            'position_id' => 4,
            'order' => 4,
            'name' => 'ENGINEERING ASST',

        ]);

        Role::create([
            'position_id' => 4,
            'order' => 4,
            'name' => 'ACCOUNT ASST',

        ]);

        // Position G5
        Role::create([
            'position_id' => 5,
            'order' => 1,
            'name' => 'ASST ENGINEER',

        ]);

        Role::create([
            'position_id' => 5,
            'order' => 1,
            'name' => 'OFFICER',

        ]);

        Role::create([
            'position_id' => 5,
            'order' => 2,
            'name' => 'SUPERVISOR',

        ]);

        // Position G6
        Role::create([
            'position_id' => 6,
            'order' => 1,
            'name' => 'ENGINEER I',

        ]);

        Role::create([
            'position_id' => 6,
            'order' => 1,
            'name' => 'SENIOR OFFICER',

        ]);

        Role::create([
            'position_id' => 6,
            'order' => 2,
            'name' => 'SENIOR SUPERVISOR',

        ]);

        // Position G7
        Role::create([
            'position_id' => 7,
            'order' => 1,
            'name' => 'ENGINEER II',

        ]);

        Role::create([
            'position_id' => 7,
            'order' => 1,
            'name' => 'EXECUTIVE',

        ]);

        Role::create([
            'position_id' => 7,
            'order' => 2,
            'name' => 'ASST ACCOUNTANT',

        ]);

        // Position G8
        Role::create([
            'position_id' => 8,
            'order' => 1,
            'name' => 'SENIOR ENGINEER',

        ]);

        Role::create([
            'position_id' => 8,
            'order' => 1,
            'name' => 'SENIOR EXECUTIVE',

        ]);

        Role::create([
            'position_id' => 8,
            'order' => 2,
            'name' => 'SUPERINTENDENT',

        ]);

        // Position G9
        Role::create([
            'position_id' => 9,
            'order' => 1,
            'name' => 'ASST MANAGER',

        ]);

        Role::create([
            'position_id' => 9,
            'order' => 2,
            'name' => 'ACCOUNTANT',

        ]);

        // Position G10
        Role::create([
            'position_id' => 10,
            'order' => 1,
            'name' => 'MANAGER',

        ]);

        Role::create([
            'position_id' => 10,
            'order' => 2,
            'name' => 'SENIOR ACCOUNTANT',

        ]);

        // Position G11
        Role::create([
            'position_id' => 11,
            'order' => 1,
            'name' => 'SENIOR MANAGER',

        ]);

        // Position G12
        Role::create([
            'position_id' => 12,
            'order' => 1,
            'name' => 'GENERAL MANAGER',

        ]);

        Role::create([
            'position_id' => 12,
            'order' => 2,
            'name' => 'SENIOR DIRECTOR',

        ]);

        Role::create([
            'position_id' => 12,
            'order' => 3,
            'name' => 'DIRECTOR',

        ]);

        // Position G13
        Role::create([
            'position_id' => 13,
            'order' => 1,
            'name' => 'PRESIDENT',

        ]);

        Role::create([
            'position_id' => 13,
            'order' => 2,
            'name' => 'VICE PRESIDENT',

        ]);
    }
}
