<?php

namespace Database\Seeders;

use App\Models\AppraisalControl;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppraisalControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $control = AppraisalControl::create([
            'appraiser_id' => 1,
            'appraisee_id' => 2,
        ]);

        $control->approvers()->createMany([
            ['sequence' => 1, 'user_id' => 3],
            ['sequence' => 2, 'user_id' => 4],
        ]);
    }
}
