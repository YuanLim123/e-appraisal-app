<?php

namespace Tests\Feature\Approval;

use App\Notifications\AppraisalRecordCompleted;
use App\Mail\AppraisalRecordPendingReviewMail;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApprovalTest extends TestCase
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

    public function test_public_user_cannot_access_appraisal_record_for_approvals(): void
    {
        // case 1: public user try to approve normal appraisal record
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $response = $this->getJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals");

        $response->assertStatus(401);

        // case 2: public user try to approve supervision appraisal record
        $isSupervision = true;
        $appraisalRecord = $this->createSubmittedAppraisalRecord($isSupervision);

        $response = $this->getJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals");

        $response->assertStatus(401);
    }

    public function test_non_approver_cannot_access_appraisal_record_for_approvals(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $nonApprover = User::factory()->create();

        $response = $this->actingAs($nonApprover)->getJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals");

        $response->assertStatus(403);
    }

    public function test_approver_can_access_all_the_appraisal_records_that_currently_need_his_review(): void
    {
        // case 1: approver can see the appraisal records that currently need his review
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);

        $response = $this->actingAs($firstApprover)->getJson("api/v1/appraisal-records/approvals");

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $appraisalRecord->id,
        ]);

        // case 2: add one more appraisal record that require the same approver to review currently return count of 2
        $appraisalRecord2 = $this->createSubmittedAppraisalRecord();
        $appraisalRecord2->current_approver_id = $firstApprover->id;
        $appraisalRecord2->save();

        $response = $this->actingAs($firstApprover)->getJson("api/v1/appraisal-records/approvals");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
    }

    public function test_approver_can_access_specific_appraisal_record_that_currently_need_his_review(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);

        $response = $this->actingAs($firstApprover)->getJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $appraisalRecord->id,
        ]);
    }

    public function test_approver_can_approve_appraisal_record(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);
        $comment = [
            'comment' => 'test',
            'date' => now(),
        ];

        $response = $this->actingAs($firstApprover)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals", $comment);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Approved successfully',
        ]);
    }

    public function test_approver_cannot_approve_same_appraisal_record_twice(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);
        $comment = [
            'comment' => 'test',
            'date' => now(),
        ];

        $this->actingAs($firstApprover)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals", $comment);
        $response = $this->actingAs($firstApprover)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals", $comment);

        $response->assertStatus(403);
    }

    public function test_not_current_approver_cannot_approve_appraisal_record(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();
        $secondApprover = User::find($appraisalRecord->approvers[1]->user_id);
        $comment = [
            'comment' => 'test',
            'date' => now(),
        ];

        $response = $this->actingAs($secondApprover)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals", $comment);

        $response->assertStatus(403);
    }

    public function test_review_pending_email_sent_after_appraisal_record_is_approved(): void
    {
        Mail::fake();
        
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);
        $comment = [
            'comment' => 'not good',
            'date' => now(),
        ];

        $this->actingAs($firstApprover)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals", $comment);
        
        Mail::assertQueued(AppraisalRecordPendingReviewMail::class);
    }

    public function test_notification_sent_to_payrolls_after_appraisal_record_is_completed(): void
    {
        Notification::fake();
        
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);
        $secondApprover = User::find($appraisalRecord->approvers[1]->user_id);
        $payrolls = Department::where('name', 'PAYROLL')->first()->users;

        $comment = [
            'comment' => 'not good',
            'date' => now(),
        ];

        $this->actingAs($firstApprover)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals", $comment);
        $this->actingAs($secondApprover)->postJson("api/v1/appraisal-records/{$appraisalRecord->id}/approvals", $comment);

        Notification::assertSentTo($payrolls, AppraisalRecordCompleted::class);
    }
}
