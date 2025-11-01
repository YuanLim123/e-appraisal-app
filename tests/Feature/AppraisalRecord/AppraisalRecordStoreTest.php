<?php

namespace Tests\Feature\AppraisalRecord;

use App\Enums\AppraisalRecordPurposeType;
use App\Enums\AppraisalRecordStatus;
use App\Exceptions\InvalidAppraisalSeasonException;
use App\Exceptions\InvalidRatingSumException;
use App\Exceptions\UserHasNoAppraisalCreatedException;
use App\Models\Season;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            SeasonSeeder::class,
        ]);

        $this->payrollDeparmentId = 21;
    }

    public function test_public_user_cannot_access_adding_appraisal_record(): void
    {
        $response = $this->postJson('/api/v1/appraisal-records', []);

        $response->assertStatus(401);
    }

    public function test_non_appraiser_cannot_access_adding_appraisal_record(): void
    {
        // create a appraisal first
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // create a non appraiser user
        $nonAppraiserUser = User::factory()->create();
        $nonAppraiserUser->departments()->sync([1]);

        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        // sign in as that non appraiser user and try to create appraisal record for the appraisee
        $response = $this->actingAs($nonAppraiserUser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // assert 403
        $response->assertStatus(403);
    }

    public function test_appraiser_can_create_normal_type_appraisal_record(): void
    {
        // create a appraisal first
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        // login as appraiser and call create appraisal record api for that appraisee
        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => AppraisalRecordStatus::CREATED->label(),
            'total' => $appraisalRecordInput['total'],
        ]);
        $response->assertJsonPath('data.appraiser.id', $appraiser->id);
        $response->assertJsonPath('data.appraisee.id', $appraisee->id);

        $this->assertDatabaseHas('appraisal_records', [
            'appraiser_id' => $appraiser->id,
            'appraisee_id' => $appraisee->id,
        ]);
    }

    public function test_appraiser_cannot_create_normal_type_appraisal_record_twice_for_appraisee_in_same_annual_or_special_season(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // create normal appraisal record input data with annual review season
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['purpose'] = AppraisalRecordPurposeType::ANNUAL_REVIEW->value;
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        // login as appraiser and submit
        $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        // try to submit again to same appraisee in same annual review season
        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The user already has an appraisal record in the selected season.',
        ]);
    }

    public function test_normal_type_appraisal_records_cannot_create_for_user_without_appraisal(): void
    {
        // here we do not create appraisal for the appraisee
        $appraiser = User::factory()->create();
        $appraisee = User::factory()->create();
        $appraisee->position_id = 2;
        $appraisee->save();

        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new UserHasNoAppraisalCreatedException)->getMessage(),
        ]);
    }

    public function test_appraiser_cannot_create_normal_type_appraisal_record_with_invalid_data(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // create normal appraisal record input data with invalid data
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['review_to'] = null;
        $appraisalRecordInput['purpose'] = 'dummy';
        $appraisalRecordInput['review_from'] = null;
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        // login as appraiser and call create appraisal record api for that appraisee with invalid data
        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['review_from', 'purpose', 'review_to']);
    }

    public function test_appraiser_can_create_supervision_type_appraisal_record(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // create supervision appraisal record input data
        $isSupervisionAppraisal = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionAppraisal);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'status' => AppraisalRecordStatus::CREATED->label(),
            'answer' => $appraisalRecordInput['section_one'],
        ]);
        $response->assertJsonPath('data.appraiser.id', $appraiser->id);
        $response->assertJsonPath('data.appraisee.id', $appraisee->id);

        $this->assertDatabaseHas('appraisal_records', [
            'appraiser_id' => $appraiser->id,
            'appraisee_id' => $appraisee->id,
        ]);
    }

    public function test_appraiser_cannot_create_supervision_type_appraisal_record_with_invalid_season_data(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // end the annual review season so we can validate the invalid season error
        $annualReviewSeason = Season::where('purpose', 'annual_review')->first();
        $annualReviewSeason->end_at = now();
        $annualReviewSeason->save();

        // create supervision appraisal record for that appraisee with invalid expired season data
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['purpose'] = AppraisalRecordPurposeType::ANNUAL_REVIEW->value;
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new InvalidAppraisalSeasonException)->getMessage(),
        ]);
    }

    public function test_appraiser_cannot_create_supervision_type_appraisal_record_with_invalid_rating_sum(): void
    {
        $isAppraiseeHighPosition = true;
        $appraisal = $this->createAppraisal($isAppraiseeHighPosition);
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // create supervision appraisal record with invalid section one rating sum which exceeds 100
        $isSupervisionAppraisal = true;
        $appraisalRecordInput = $this->createAppraisalRecordInputData($isSupervisionAppraisal);
        $appraisalRecordInput['appraisee_id'] = $appraisee->id;
        $appraisalRecordInput['section_one'] = [
            [
                'goal' => 'goal 1',
                'result' => 'result 1',
                'rating' => 100,
            ],
            [
                'goal' => 'goal 2',
                'result' => 'result 2',
                'rating' => 20,
            ],
        ];

        $response = $this->actingAs($appraiser)->postJson("/api/v1/appraisal-records", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new InvalidRatingSumException)->getMessage(),
        ]);
    }
}
