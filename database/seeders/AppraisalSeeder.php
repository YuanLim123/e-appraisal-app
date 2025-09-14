<?php

namespace Database\Seeders;

use App\Models\Appraisal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppraisalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Appraisal::create([
            'appraiser_id' => 1,
            'appraisee_id' => 2,
        ])->approvers()->createMany([
            ['sequence' => 1, 'user_id' => 3],
            ['sequence' => 2, 'user_id' => 4],
        ]);

        Appraisal::create([
            'appraiser_id' => 3,
            'appraisee_id' => 4,
        ])->approvers()->createMany([
            ['sequence' => 3, 'user_id' => 5],
            ['sequence' => 4, 'user_id' => 6],
            ['sequence' => 1, 'user_id' => 1],
            ['sequence' => 2, 'user_id' => 2],
        ]);
    }
}
