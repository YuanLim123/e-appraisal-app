<?php

namespace Tests\Feature\AppraisalRecord;

use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppraisalRecordSubmitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PositionSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
            UserSeeder::class,
            SeasonSeeder::class,
        ]);
    }

    public function test_public_user_cannot_access_submitting_normal_appraisal_record(): void
    {
        // create submitted normal appraisal record
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        // attempt to submit the appraisal record without authentication
        $response = $this->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $response->assertStatus(401);
    }
}
