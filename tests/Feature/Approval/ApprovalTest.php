<?php

namespace Tests\Feature\Approval;

use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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

        $response = $this->actingAs($firstApprover)->getJson("api/v1/appraisal-records/{$appraisalRecord->id}approvals");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $appraisalRecord->id,
        ]);
    }
}
