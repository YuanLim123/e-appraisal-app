<?php

namespace Tests\Feature\AppraisalRecord;

use App\Enums\AppraisalRecordPurposeType;
use App\Enums\AppraisalRecordStatus;
use App\Models\Appraisal;
use App\Models\AppraisalRecord;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertJson;

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
            SeasonSeeder::class,
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

        $appraisalRecordInput = AppraisalRecord::factory()->make()->toArray();

        $response = $this->actingAs($nonAppraiserUser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);
        // call create appraisal record api for that appraisee

        // assert 403
        $response->assertStatus(403);
    }

    public function test_appraiser_can_create_normal_type_appraisal_record(): void
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

        $createResponse = $this->actingAs($payrollUser)->postJson("/api/v1/admin/users/{$appraisee->id}/appraisals", $appraisalInput);
        $createResponse->assertStatus(201);
        // login as appraiser
        // call create appraisal record api for that appraisee
        $appraisalRecordInput = AppraisalRecord::factory()->make([
            'purpose' => AppraisalRecordPurposeType::ANNUAL_REVIEW
        ])->toArray();
        $response = $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);
        // assert 201
        $response->assertStatus(201);
        // assert json
        $response->assertJsonFragment([
            'status' => AppraisalRecordStatus::CREATED->value,
            'total' => $appraisalRecordInput['total'],
        ]);
        $response->assertJsonPath('data.appraiser.id', $appraiser->id);
        $response->assertJsonPath('data.appraisee.id', $appraisee->id);
        // assert database has that record

        $this->assertDatabaseHas('appraisal_records', [
            'appraiser_id' => $appraiser->id,
            'appraisee_id' => $appraisee->id,
        ]);
    }

    public function test_appraiser_cannot_create_appraisal_record_twice_for_appraisee_in_same_annual_or_special_season(): void
    {
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

        $this->actingAs($payrollUser)->postJson("/api/v1/admin/users/{$appraisee->id}/appraisals", $appraisalInput);

        // login as appraiser and submit
        $appraisalRecordInput = AppraisalRecord::factory()->make([
            'purpose' => AppraisalRecordPurposeType::ANNUAL_REVIEW
        ])->toArray();
        $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        // try to submit again to same appraisee in same season
        $response = $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The user already has an appraisal record in the selected season.'
        ]);
    }

    public function test_appraisal_records_cannot_create_for_user_without_appraisal(): void
    {
        $appraiser = User::factory()->create();
        $appraisee = User::factory()->create();
        $appraisee->position_id = 2;
        $appraisee->save();

        $appraisalRecordInput = AppraisalRecord::factory()->make([
            'purpose' => AppraisalRecordPurposeType::ANNUAL_REVIEW
        ])->toArray();

        $response = $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(403);

        $response->assertJson([
            'message' => 'The user does not have an appraisal. Please try again later.'
        ]);
    }

    public function test_appraiser_cannot_create_appraisal_record_with_invalid_data(): void
    {
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

        $this->actingAs($payrollUser)->postJson("/api/v1/admin/users/{$appraisee->id}/appraisals", $appraisalInput);

        // login as appraiser
        // call create appraisal record api for that appraisee with invalid data
        $appraisalRecordInput = AppraisalRecord::factory()->make([
            'review_to' => null,
            'purpose' => 'dummy',
            'review_from' => null,
        ])->toArray();
        $response = $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);
        // assert 422
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['review_from', 'purpose', 'review_to']);
    }

    public function test_appraiser_can_create_supervision_type_appraisal_record(): void
    {
        // create appraisee, approser, approver
        // create appraisal for that appraisee
        // login as hr to create appraisal
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisee = User::factory()->create();
        $appraisee->position_id = 6;
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

        $createResponse = $this->actingAs($payrollUser)->postJson("/api/v1/admin/users/{$appraisee->id}/appraisals", $appraisalInput);
        $createResponse->assertStatus(201);
        // login as appraiser
        // call create appraisal record api for that appraisee
        $appraisalRecordInput = AppraisalRecord::factory()->supervision()->make([
            'purpose' => AppraisalRecordPurposeType::ANNUAL_REVIEW
        ])->toArray();

        $response = $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);
        // assert 201
        $response->assertStatus(201);
        // assert json
        $response->assertJsonFragment([
            'status' => AppraisalRecordStatus::CREATED->value,
            'answer' => $appraisalRecordInput['performance'],
        ]);
        $response->assertJsonPath('data.appraiser.id', $appraiser->id);
        $response->assertJsonPath('data.appraisee.id', $appraisee->id);
        // assert database has that record

        $this->assertDatabaseHas('appraisal_records', [
            'appraiser_id' => $appraiser->id,
            'appraisee_id' => $appraisee->id,
        ]);
    }
}
