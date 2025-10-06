<?php

namespace Tests\Feature\AppraisalRecord;

use App\Exceptions\InvalidWeightAgeException;
use App\Mail\AppraisalPendingReviewMail;
use App\Mail\AppraisalRecordPendingReviewMail;
use App\Models\AppraisalRecord;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AppraisalRecordFeedbackStoreTest extends TestCase
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
            SeasonSeeder::class,
        ]);

        $this->payrollDeparmentId = 21;
    }

    public function test_public_user_cannot_access_storing_appraisal_record_feedback(): void
    {
        $response = $this->postJson('/api/v1/appraisal-records/1/feedbacks', []);

        $response->assertStatus(401);
    }

    public function test_non_appraiser_cannot_access_adding_appraisal_record(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create normal appraisal record
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // create one more user
        $nonAppraiserUser = User::factory()->create();
        $nonAppraiserUser->departments()->sync([1]);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // assume nonappraiseruser like to add feedback for the appraisal record
        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'expected_salary' => 6000,
        ];

        // attempt to store feedback as non appraiser user
        $response = $this->actingAs($nonAppraiserUser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks", $feedbackInput);

        $response->assertStatus(403);
    }

    public function test_appraiser_can_store_feedback_for_normal_type_appraisal_record_with_valid_data(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create normal appraisal record
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // assume appraiser like to add feedback for the appraisal record
        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'new_salary' => 6000,
        ];

        // update the appraisal record with new feedback
        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks", $feedbackInput);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'current_salary' => $feedbackInput['current_salary'],
            'new_salary' => $feedbackInput['new_salary'],
        ]);
    }

    public function test_appraiser_can_store_feedback_for_supervision_type_appraisal_record_with_valid_data(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create supervision appraisal record
        $isSupervisionType = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionType);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // assume appraiser like to add feedback for the appraisal record
        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'new_salary' => 6000,
            'goal_next' => [
                [
                    'objective' => 'objective 1',
                    'specificAction' => 'specific action 1',
                    'weightage' => 100,
                ],
            ],
        ];

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks", $feedbackInput);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'current_salary' => $feedbackInput['current_salary'],
            'new_salary' => $feedbackInput['new_salary'],
            'goal_next' => $feedbackInput['goal_next'],
        ]);
    }

    public function test_appraiser_cannot_store_feedback_for_supervision_type_appraisal_record_with_invalid_goal_next_data(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create supervision appraisal record
        $isSupervisionType = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionType);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // assume appraiser like to add feedback for the appraisal record
        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'new_salary' => 6000,
            // for supervision type record feedback, if objective is filled, then specificAction and weightage must be filled too
            // so we expected the validation error for specificAction and weightage
            'goal_next' => [
                [
                    'objective' => 'objective 1',
                ],
            ],
        ];

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks", $feedbackInput);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['goal_next.0.specificAction', 'goal_next.0.weightage']);
    }

    public function test_appraiser_cannot_store_feedback_for_supervision_type_appraisal_record_with_invalid_weight_age_data(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create supervision appraisal record
        $isSupervisionType = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionType);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // assume appraiser like to add feedback for the appraisal record
        // sum of weightage for all goals exceed 100 so we expected invalid weightage error message
        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'new_salary' => 6000,
            'goal_next' => [
                [
                    'objective' => 'objective 1',
                    'specificAction' => 'specific action 1',
                    'weightage' => 100,
                ],
                [
                    'objective' => 'objective 2',
                    'specificAction' => 'specific action 2',
                    'weightage' => 100,
                ],
            ],
        ];

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks", $feedbackInput);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new InvalidWeightAgeException)->getMessage(),
        ]);
    }

    public function test_appraisal_review_pending_email_sent_to_first_approver_push_to_queue_after_supervision_type_appraisal_record_feedback_is_saved_and_submitted(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create supervision appraisal record
        $isSupervisionType = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionType);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'new_salary' => 6000,
            'goal_next' => [
                [
                    'objective' => 'objective 1',
                    'specificAction' => 'specific action 1',
                    'weightage' => 100,
                ],
            ],
            'isEmployeeAgreed' => true,
            'isSupervisorAgreed' => true,
        ];

        Mail::fake();

        // attempt to store feedback and submit the appraisal record
        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks?isSubmit=true", $feedbackInput);

        // assert that AppraisalPendingReviewMail mailable push to queue
        Mail::assertQueued(AppraisalRecordPendingReviewMail::class);
    }

    public function test_supervision_type_appraisal_review_pending_email_not_push_to_queue_if_feedback_is_only_saved(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create supervision appraisal record
        $isSupervisionType = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionType);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'new_salary' => 6000,
            'goal_next' => [
                [
                    'objective' => 'objective 1',
                    'specificAction' => 'specific action 1',
                    'weightage' => 100,
                ],
            ],
        ];
        Mail::fake();

        // attempt to store feedback and submit the appraisal record
        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks", $feedbackInput);

        // assert that AppraisalPendingReviewMail mailable was sent
        Mail::assertNotQueued(AppraisalRecordPendingReviewMail::class);
    }

    public function test_superversion_type_appraisal_record_feedback_store_and_submit_return_error_if_employee_or_supervisor_did_not_agree(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create supervision appraisal record
        $isSupervisionType = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionType);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        $feedbackInput = [
            'appraisee_id' => $appraisee->id,
            'current_salary' => 5000,
            'new_salary' => 6000,
            'goal_next' => [
                [
                    'objective' => 'objective 1',
                    'specificAction' => 'specific action 1',
                    'weightage' => 100,
                ],
            ],
            // assume only supervisor agreed but employee not agreed
            'isSupervisorAgreed' => true,
            'isEmployeeAgreed' => false,
        ];

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records/{$appraisalRecordId}/feedbacks?isSubmit=true", $feedbackInput);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['isEmployeeAgreed']);
    }
}
