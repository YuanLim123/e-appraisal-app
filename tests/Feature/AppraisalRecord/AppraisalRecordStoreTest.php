<?php

namespace Tests\Feature\AppraisalRecord;

use App\Models\Appraisal;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppraisalRecordStoreTest extends TestCase
{
    use RefreshDatabase;
    private int $payrollDeparmentId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PositionSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
        ]);

        $this->payrollDeparmentId = 21;
    }

    public function test_public_user_cannot_access_adding_appraisal_record(): void
    {
        $response = $this->postJson('/api/v1/users/1/appraisal-records', []);

        $response->assertStatus(401);
    }
}
