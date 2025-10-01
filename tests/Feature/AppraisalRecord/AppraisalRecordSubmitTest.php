<?php

namespace Tests\Feature\AppraisalRecord;

use App\Exceptions\RecordAlreadySubmitException;
use App\Models\User;
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

    public function test_non_appraiser_cannot_submit_normal_appraisal_record_for_appraisee(): void
    {
        $appraisalRecord = $this->createUnsubmittedAppraisalRecord();

        $nonAppraiser = User::factory()->create();

        $response = $this->actingAs($nonAppraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $response->assertStatus(403);
    }

    public function test_appraiser_can_submit_normal_appraisal_record_for_appraisee(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisalRecord = $this->createUnsubmittedAppraisalRecord($appraisal);
        $appraisalRecord->update([
            'employee_agreed_at' => now(),
            'supervisor_agreed_at' => now(),
        ]);

        $response = $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Appraisal record submitted successfully',
        ]);
    }

    public function test_submit_a_submitted_status_appraisal_record_return_error(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $response = $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new RecordAlreadySubmitException())->getMessage(),
        ]);

    }
}
