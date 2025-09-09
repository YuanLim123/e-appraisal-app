<?php

namespace Tests\Feature;

use App\Models\AppraisalControl;
use App\Models\User;
use Database\Seeders\AppraisalControlSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AppraisalControlsListTest extends TestCase
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
        ]);
    }

    public function test_appraisal_control_list_return_correct_appraisal_controls(): void
    {
        $appraiser = User::find(1);
        $appraisee = User::find(2);
        $approver1 = User::find(3);
        $approver2 = User::find(4);

        $control = AppraisalControl::create([
            'appraiser_id' => $appraiser->id,
            'appraisee_id' => $appraisee->id,
        ]);

        $control->approvers()->createMany([
            ['sequence' => 2, 'user_id' => $approver2->id],
            ['sequence' => 1, 'user_id' => $approver1->id],
        ]);

        $response = $this->getJson('/api/v1/appraisal-controls');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $control->id]);
    }


    public function test_appraiser_and_appraisee_and_approvers_are_displayed_correctly(): void
    {
        $appraiser = User::find(1);
        $appraisee = User::find(2);
        $approver1 = User::find(3);
        $approver2 = User::find(4);

        $control = AppraisalControl::create([
            'appraiser_id' => $appraiser->id,
            'appraisee_id' => $appraisee->id,
        ]);

        $control->approvers()->createMany([
            ['sequence' => 1, 'user_id' => $approver1->id],
            ['sequence' => 2, 'user_id' => $approver2->id],

        ]);

        $response = $this->getJson('/api/v1/appraisal-controls');
        
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.0.approvers');
        $response->assertJsonFragment(['id' => $appraiser->id]);
        $response->assertJsonFragment(['id' => $appraisee->id]);
        $response->assertJsonPath('data.0.approvers.0.user.id', $approver1->id);
        $response->assertJsonPath('data.0.approvers.1.user.id', $approver2->id);
    }
}
