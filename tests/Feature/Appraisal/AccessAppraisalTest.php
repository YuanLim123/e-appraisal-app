<?php

namespace Tests\Feature\Appraisal;

use App\Models\Appraisal;
use App\Models\User;
use Database\Seeders\AppraisalSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessAppraisalTest extends TestCase
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
            AppraisalSeeder::class,
        ]);

        $this->payrollDeparmentId = 21;
    }

    public function test_public_user_cannot_access_appraisal(): void
    {
        $appraisal = Appraisal::first();

        $response = $this->getJson("/api/v1/appraisals/{$appraisal->id}");

        $response->assertStatus(401);
    }

    public function test_non_hr_and_payroll_user_cannot_access_appraisal(): void
    {
        $nonHrUser = User::factory()->create();

        // reasign non hr and payroll department to user
        $nonHrUser->departments()->sync([1, 2]);
        $appraisal = Appraisal::first();

        $response = $this->actingAs($nonHrUser)->getJson("/api/v1/appraisals/{$appraisal->id}");

        $response->assertStatus(403);
    }

    public function test_appraiser_can_access_own_appraisal(): void
    {
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);

        $appraisee = User::factory()->create();
        $appraiser = User::factory()->create();
        $approver1 = User::factory()->create();
        $approver2 = User::factory()->create();

        $appraisalInput = [
            'appraisee_id' => $appraisee->id,
            'appraiser_id' => $appraiser->id,
            'approvers' => [
                ['user_id' => $approver1->id, 'sequence' => 1],
                ['user_id' => $approver2->id, 'sequence' => 2],
            ],
        ];

        // Create appraisal first
        $createResponse = $this->actingAs($payrollUser)->postJson("/api/v1/hr/appraisals", $appraisalInput);
        $createResponse->assertStatus(201);

        // Access appraisal as appraiser
        $appraisal = Appraisal::query()
            ->where('appraisee_id', $appraisee->id)
            ->where('appraiser_id', $appraiser->id)
            ->first();

        $response = $this->actingAs($appraiser)->getJson("/api/v1/appraisals/{$appraisal->id}");
        $response->assertStatus(200);
        $response->assertJsonPath('data.appraiser.id', $appraiser->id);
        $response->assertJsonPath('data.appraisee.id', $appraisee->id);
    }

    public function test_hr_or_payroll_user_can_access_any_appraisal(): void
    {
        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([$this->payrollDeparmentId]);
        $appraisal = Appraisal::first();

        $response = $this->actingAs($payrollUser)->getJson("/api/v1/appraisals/{$appraisal->id}");

        $response->assertStatus(200);
    }
}
