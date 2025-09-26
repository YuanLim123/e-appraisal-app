<?php

namespace Tests\Feature\Appraisal;

use App\Models\Appraisal;
use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppraisalTest extends TestCase
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

    public function test_public_user_cannot_access_adding_appraisal(): void
    {
        $response = $this->postJson('/api/v1/hr/users/1/appraisals', []);

        $response->assertStatus(401);
    }

    public function test_non_hr_and_payoll_user_cannot_access_adding_appraisal(): void
    {
        $nonHrUser = User::factory()->create();
        // reasign non hr and payroll department to user
        $nonHrUser->departments()->sync([1, 2]);
        $appraisalInput = [
            'appraiser_id' => 4,
            'approvers' => [
                ['user_id' => 5, 'sequence' => 1],
                ['user_id' => 6, 'sequence' => 2],
            ],
        ];

        $response = $this->actingAs($nonHrUser)->postJson('/api/v1/hr/users/3/appraisals', $appraisalInput);

        $response->assertStatus(403);
    }

    public function test_payroll_user_can_add_appraisal_with_valid_data(): void
    {
        $payrollUser = User::factory()->create();

        $payrollUser->departments()->sync([$this->payrollDeparmentId]); // id 21 is payroll department
        $appraisalInput = [
            'appraiser_id' => 4,
            'approvers' => [
                ['user_id' => 5, 'sequence' => 1],
                ['user_id' => 6, 'sequence' => 2],
            ],
        ];

        $response = $this->actingAs($payrollUser)->postJson('/api/v1/hr/users/3/appraisals', $appraisalInput);
        $response->assertStatus(201);
    }

    public function test_saves_appraisal_successfuly_with_valid_data(): void
    {
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisee = User::factory()->create();
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

        $response = $this->actingAs($payrollUser)->postJson("/api/v1/hr/users/{$appraisee->id}/appraisals", $appraisalInput);
        $response->assertStatus(201);
        $response->assertJsonCount(2, 'data.approvers');
        $response->assertJsonPath('data.appraisee.id', $appraisee->id);
        $response->assertJsonPath('data.appraiser.id', $appraiser->id);
        $response->assertJsonPath('data.approvers.0.user.id', $approver1->id);
        $response->assertJsonPath('data.approvers.1.user.id', $approver2->id);
    }

    public function test_saves_appraisal_successfuly_with_invalid_data(): void
    {
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisalInput = [
            'appraiser_id' => 'cde',
            'approvers' => [
                ['user_id' => null, 'sequence' => 1],
                ['user_id' => null, 'sequence' => 2],
            ],
        ];

        $response = $this->actingAs($payrollUser)->postJson('/api/v1/hr/users/3/appraisals', $appraisalInput);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['appraiser_id', 'approvers.0.user_id', 'approvers.1.user_id']);
    }

    public function test_cannot_add_appraisal_with_existing_appraisee(): void
    {
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisee = User::factory()->create();
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

        // first request should be successful
        $response1 = $this->actingAs($payrollUser)->postJson("/api/v1/hr/users/{$appraisee->id}/appraisals", $appraisalInput);
        $response1->assertStatus(201);

        // second request with same appraisee should fail
        $response2 = $this->actingAs($payrollUser)->postJson("/api/v1/hr/users/{$appraisee->id}/appraisals", $appraisalInput);
        $response2->assertStatus(422);
        $response2->assertJson([
            'message' => 'The selected user has already been registered as an appraisee.',
        ]);
    }

    public function test_updates_appraisal_successfuly_with_valid_data(): void
    {
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisee = User::factory()->create();
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
        $createResponse = $this->actingAs($payrollUser)->postJson("/api/v1/hr/users/{$appraisee->id}/appraisals", $appraisalInput);
        $createResponse->assertStatus(201);

        // Get the appraisal we just created
        $appraisal = Appraisal::where('appraisee_id', $appraisee->id)->first();

        // New data for update
        $newAppraiser = User::factory()->create();
        $newApprover1 = User::factory()->create();
        $newApprover2 = User::factory()->create();

        $updatedAppraisalInput = [
            'appraiser_id' => $newAppraiser->id,
            'approvers' => [
                ['user_id' => $newApprover1->id, 'sequence' => 1],
                ['user_id' => $newApprover2->id, 'sequence' => 2],
            ],
        ];

        // Update appraisal
        $updateResponse = $this->actingAs($payrollUser)->putJson("/api/v1/hr/users/{$appraisee->id}/appraisals/{$appraisal->id}", $updatedAppraisalInput);
        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonCount(2, 'data.approvers');
        $updateResponse->assertJsonPath('data.appraisee.id', $appraisee->id);
        $updateResponse->assertJsonPath('data.appraiser.id', $newAppraiser->id);
        $updateResponse->assertJsonPath('data.approvers.0.user.id', $newApprover1->id);
        $updateResponse->assertJsonPath('data.approvers.1.user.id', $newApprover2->id);
    }

    public function test_updates_appraisal_return_errors_with_invalid_data(): void
    {
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisee = User::factory()->create();
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
        $createResponse = $this->actingAs($payrollUser)->postJson("/api/v1/hr/users/{$appraisee->id}/appraisals", $appraisalInput);
        $createResponse->assertStatus(201);

        // Get the appraisal we just created
        $appraisal = Appraisal::where('appraisee_id', $appraisee->id)->first();

        // Invalid data for update
        $invalidAppraisalInput = [
            'appraiser_id' => '10000', // invalid appraiser_id
            'approvers' => [
                ['user_id' => null, 'sequence' => 1],
                ['user_id' => 1, 'sequence' => null],
            ],
        ];

        // Update appraisal with invalid data
        $updateResponse = $this->actingAs($payrollUser)->putJson("/api/v1/hr/users/{$appraisee->id}/appraisals/{$appraisal->id}", $invalidAppraisalInput);
        $updateResponse->assertStatus(422);
        $updateResponse->assertJsonValidationErrors(['appraiser_id', 'approvers.0.user_id', 'approvers.1.sequence']);
    }
}
