<?php

namespace Tests\Feature\AppraisalRecord;

use App\Enums\AppraisalRecordPurposeType;
use App\Exceptions\InvalidAppraisalSeasonException;
use App\Models\AppraisalRecord;
use App\Models\Season;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppraisalRecordUpdateTest extends TestCase
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

    public function test_public_user_cannot_access_updating_appraisal_record(): void
    {
        $response = $this->putJson('/api/v1/users/1/appraisal-records/1', []);

        $response->assertStatus(401);
    }

    public function test_non_appraiser_cannot_access_adding_appraisal_record(): void
    {
        // Create appraisal
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create normal appraisal record
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        // create a random non appraiser user
        $nonAppraiserUser = User::factory()->create();
        $nonAppraiserUser->departments()->sync([1]);

        // assume nonappraiseruser like to change the appraisal record date
        $appraisalRecordInput['review_from'] = '2023-01-01';
        // attempt to update appraisal record as non appraiser user
        $response = $this->actingAs($nonAppraiserUser)->putJson("/api/v1/users/{$appraisee->id}/appraisal-records/1", $appraisalRecordInput);

        $response->assertStatus(403);
    }

    public function test_appraisee_id_not_match_with_appraisal_record_appraisee_id_in_the_request_url_return_error(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create normal appraisal record
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        // create another appraisee with same position
        $anotherAppraisee = User::factory()->create();
        $anotherAppraisee->position_id = 2;
        $anotherAppraisee->save();

        $createdAppraisalRecordId = AppraisalRecord::latest()->first()->id;

        $appraisalRecordInput['review_from'] = '2023-01-01';
        // attempt to update appraisal record as appraiser but with different appraisee id in the url
        $response = $this->actingAs($appraiser)->putJson("/api/v1/users/{$anotherAppraisee->id}/appraisal-records/{$createdAppraisalRecordId}", $appraisalRecordInput);

        $response->assertStatus(403);
    }

    public function test_appraiser_can_update_normal_type_appraisal_record_with_valid_data(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create normal appraisal record
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;
        // Update the appraisal record data
        $appraisalRecordInput['review_from'] = '2022-02-02';
        $appraisalRecordInput['review_to'] = '2023-02-02';

        $response = $this->actingAs($appraiser)->putJson("/api/v1/users/{$appraisee->id}/appraisal-records/{$appraisalRecordId}", $appraisalRecordInput);
        $response->assertStatus(200);
    }

    public function test_appraiser_cannot_update_normal_type_appraisal_record_with_invalid_data(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create normal appraisal record
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // Update the appraisal record data with invalid data
        $appraisalRecordInput['review_from'] = 'dummy';
        $appraisalRecordInput['review_to'] = null;
        $appraisalRecordInput['purpose'] = 'invalid-purpose';

        $response = $this->actingAs($appraiser)->putJson("/api/v1/users/{$appraisee->id}/appraisal-records/{$appraisalRecordId}", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['review_from', 'review_to', 'purpose']);
    }

    public function test_appraiser_cannot_update_normal_type_appraisal_record_with_invalid_season_data(): void
    {
        $appraisal = $this->createAppraisal();
        $appraisee = $appraisal->appraisee;
        $appraiser = $appraisal->appraiser;

        // Create normal appraisal record
        $appraisalRecordInput = $this->createAppraisalRecordInputData();
        $appraisalRecordInput['purpose'] = AppraisalRecordPurposeType::ANNUAL_REVIEW->value;

        $this->actingAs($appraiser)->postJson("/api/v1/users/{$appraisee->id}/appraisal-records", $appraisalRecordInput);

        // end the annual review season so we can validate the invalid season error
        $annualReviewSeason = Season::where('purpose', 'annual_review')->first();
        $annualReviewSeason->end_at = now();
        $annualReviewSeason->save();

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // Update the appraisal record input with invalid purpose
        $appraisalRecordInput['purpose'] = AppraisalRecordPurposeType::ANNUAL_REVIEW->value;

        // Update the appraisal record with invalid season purpose
        $response = $this->actingAs($appraiser)->putJson("/api/v1/users/{$appraisee->id}/appraisal-records/{$appraisalRecordId}", $appraisalRecordInput);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => (new InvalidAppraisalSeasonException)->getMessage(),
        ]);
    }
}
