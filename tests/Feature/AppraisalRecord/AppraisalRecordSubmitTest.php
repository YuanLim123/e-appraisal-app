<?php

namespace Tests\Feature\AppraisalRecord;

use App\Models\User;
use App\Models\AppraisalRecord;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\SeasonSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;

class AppraisalRecordSubmitTest extends TestCase
{
    use RefreshDatabase;

    private int $payrollDeparmentId;
    private User $payrollUser;
    private User $appraisee;
    private User $appraiser;
    private User $approver1;
    private User $approver2;
    private array $appraisalInput;

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

        $this->payrollUser = User::factory()->create();
        $this->payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $this->appraisee = User::factory()->create();
        $this->appraisee->position_id = 2;
        $this->appraisee->save();
        $this->appraiser = User::factory()->create();
        $this->approver1 = User::factory()->create();
        $this->approver2 = User::factory()->create();

        $this->appraisalInput = [
            'appraiser_id' => $this->appraiser->id,
            'approvers' => [
                ['user_id' => $this->approver1->id, 'sequence' => 1],
                ['user_id' => $this->approver2->id, 'sequence' => 2],
            ],
        ];

        // Create appraisal
        $this->actingAs($this->payrollUser)->postJson("/api/v1/hr/users/{$this->appraisee->id}/appraisals", $this->appraisalInput);

    }

    public function test_public_user_cannot_access_submitting_appraisal_record(): void
    {

        // Create appraisal record
        $appraisalRecordInput = AppraisalRecord::factory()->make()->toArray();
        $this->actingAs($this->appraiser)->postJson("/api/v1/users/{$this->appraisee->id}/appraisal-records", $appraisalRecordInput);

        // Get the created appraisal record
        $appraisalRecordId = AppraisalRecord::latest()->first()->id;

        // attempt to submit appraisal record as public user
        $response = $this->postJson("/api/v1/users/{$this->appraisee->id}/appraisal-records/{$appraisalRecordId}/submissions");
        // assert 403
        $response->assertStatus(403);
    }
}
