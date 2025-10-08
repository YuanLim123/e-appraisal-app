<?php

namespace Tests\Feature\Approval;

use App\Models\User;
use App\Notifications\AppraisalRecordRejected;
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

class RejectTest extends TestCase
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

    public function test_approver_can_reject_appraisal_record(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);
        $comment = [
            'comment' => 'not good',
            'date' => now(),
        ];

        $response = $this->actingAs($firstApprover)->deleteJson("api/v1/appraisal-records/{$appraisalRecord->id}/rejects", $comment);

        $response->assertStatus(200);

        $this->assertDatabaseHas('appraisal_records', [
            'id' => $appraisalRecord->id,
            'status' => 'rejected',
        ]);
    }

    public function test_not_current_approver_cannot_reject_appraisal_record(): void
    {
        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $secondAprover = User::find($appraisalRecord->approvers[1]->user_id);
        $comment = [
            'comment' => 'not good',
            'date' => now(),
        ];

        $response = $this->actingAs($secondAprover)->deleteJson("api/v1/appraisal-records/{$appraisalRecord->id}/rejects", $comment);

        $response->assertStatus(403);
    }

    public function test_notification_sent_to_appraiser_after_appraisal_record_is_rejected(): void
    {
        Notification::fake();

        $appraisalRecord = $this->createSubmittedAppraisalRecord();

        $firstApprover = User::find($appraisalRecord->current_approver_id);
        $appraiser = User::find($appraisalRecord->appraiser_id);

        $comment = [
            'comment' => 'not good',
            'date' => now(),
        ];

        $this->actingAs($firstApprover)->deleteJson("api/v1/appraisal-records/{$appraisalRecord->id}/rejects", $comment);

        Notification::assertSentTo($appraiser, AppraisalRecordRejected::class);
    }
}
