<?php

namespace Database\Seeders;

use App\Models\Season;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Season::create([
            'name' => 'Confirmation or Promotion',
            'purpose' => 'confirmation_or_promotion',
            'start_at' => '2024-06-01',
        ]);

        Season::create([
            'name' => '2024 Annual Review',
            'purpose' => 'annual_review',
            'start_at' => '2024-12-01',
        ]);
    }
}
