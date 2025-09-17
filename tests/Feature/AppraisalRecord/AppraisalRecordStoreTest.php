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

    public function test_non_appraiser_cannot_access_adding_appraisal_record(): void
    {
        // create appraisee, approser, approver
        // create appraisal for that appraisee
        // login as hr to create appraisal
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisee = User::factory()->create();
        $appraisee->position_id = 2;
        $appraisee->save();
        $appraiser = User::factory()->create();
        $approver1 = User::factory()->create();
        $approver2 = User::factory()->create();

        $appraisalInput = [
            'appraiser_id' => $appraiser->id,
            'approvers' => [
                ['user_id' => $approver1->id, 'sequence' => 1],
                ['user_id' => $approver2->id, 'sequence' => 2],
            ],
        ];

        // Create appraisal first
        $createResponse = $this->actingAs($payrollUser)->postJson("/api/v1/admin/users/{$appraisee->id}/appraisals", $appraisalInput);
        $createResponse->assertStatus(201);

        // create one more user
        $nonAppraiserUser = User::factory()->create();
        $nonAppraiserUser->departments()->sync([1]);
        // sign in as that user

        $appraisalRecordInput = [
            "review_from" => "2024-09-17",
            "review_to" => "2025-09-17",
            "purpose" => "annual_review",
            "total" => 85,
        ];

        $response = $this->actingAs($nonAppraiserUser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);
        // call create appraisal record api for that appraisee

        // assert 403
        $response->assertStatus(403);
    }

    public function test_appraiser_can_add_appraisal_record(): void
    {
        // create appraisee, approser, approver
        // create appraisal for that appraisee
        // login as hr to create appraisal

        // login as appraiser
        // call create appraisal record api for that appraisee

        // assert 201
        // assert json
        // assert database has that record
    }
}
