<?php

namespace Tests\Feature\AppraisalRecord;

use App\Mail\AppraisalRecordPendingReviewMail;
use App\Enums\AppraisalRecordStatus;
use App\Notifications\AppraisalRecordSubmitted;
use App\Exceptions\AgreementRequiredException;
use App\Exceptions\RecordAlreadySubmitException;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
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
        // case 1: submit normal appraisal record
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

        // case 2: submit supervision appraisal record
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
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

    public function test_database_has_correct_data_after_submitting_normal_appraisal_record(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisalRecord = $this->createUnsubmittedAppraisalRecord($appraisal);
        $appraisalRecord->update([
            'employee_agreed_at' => now(),
            'supervisor_agreed_at' => now(),
        ]);

        $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $this->assertDatabaseHas('appraisal_records', [
            'id' => $appraisalRecord->id,
            'status' => AppraisalRecordStatus::SUBMITTED->value,
            'current_step' => 1,
            'current_approver_id' => $appraisal->approvers->first()->user_id,
        ]);
    }

    public function test_cannot_submit_appraisal_record_if_employee_agreed_or_supervisor_agreed_is_missing(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisalRecord = $this->createUnsubmittedAppraisalRecord($appraisal);

        // case 1: both employee_agreed_at and supervisor_agreed_at are null
        $response = $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new AgreementRequiredException())->getMessage(),
        ]);

        // case 2: only employee_agreed_at is set
        $appraisalRecord->update([
            'employee_agreed_at' => now(),
            'supervisor_agreed_at' => null,
        ]);

        $response = $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new AgreementRequiredException())->getMessage(),
        ]);

        // case 3: only supervisor_agreed_at is set
        $appraisalRecord->update([
            'employee_agreed_at' => null,
            'supervisor_agreed_at' => now(),
        ]);

        $response = $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new AgreementRequiredException())->getMessage(),
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

    public function test_review_pending_email_sent_to_queue_after_submitting_appraisal_record(): void
    {
        Mail::fake();
        $appraisal = $this->createAppraisal();
        $appraisalRecord = $this->createUnsubmittedAppraisalRecord($appraisal);
        $appraisalRecord->update([
            'employee_agreed_at' => now(),
            'supervisor_agreed_at' => now(),
        ]);

        $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

        Mail::assertQueued(AppraisalRecordPendingReviewMail::class);
    }

    // public function test_database_notification_sent_to_queue_after_submitting_appraisal_record(): void
    // {
    //     Queue::fake();

    //     $appraisal = $this->createAppraisal();
    //     $appraisalRecord = $this->createUnsubmittedAppraisalRecord($appraisal);
    //     $appraisalRecord->update([
    //         'employee_agreed_at' => now(),
    //         'supervisor_agreed_at' => now(),
    //     ]);

    //     $this->actingAs($appraisalRecord->appraiser)->postJson("api/v1/users/{$appraisalRecord->appraisee_id}/appraisal-records/{$appraisalRecord->id}/submissions");

    //     Queue::assertPushed(AppraisalRecordSubmitted::class);
    // }
}
