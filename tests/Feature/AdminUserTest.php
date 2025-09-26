<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            PositionSeeder::class,
            RoleSeeder::class,
            DepartmentSeeder::class,
        ]);
    }

    public function test_public_user_cannot_access_adding_users(): void
    {
        $userData = User::factory()->make()->toArray();

        $response = $this->postJson('/api/v1/hr/users', $userData);

        $response->assertStatus(401);
    }

    public function test_non_hr_and_payoll_user_cannot_access_adding_users(): void
    {
        $nonHrUser = User::factory()->create();
        // reasign non hr and payroll department to user
        $nonHrUser->departments()->sync([1, 2]);
        $userData = User::factory()->make()->toArray();

        $response = $this->actingAs($nonHrUser)->postJson('/api/v1/hr/users', $userData);

        $response->assertStatus(403);
    }

    public function test_payroll_user_can_add_user_with_valid_data(): void
    {
        $payrollUser = User::factory()->create();

        $payrollUser->departments()->sync([21]); // id 21 is payroll department
        $userData = User::factory()->make()->toArray();
        $userData['password'] = 'Password123'; // since passsword is hidden in model, we need to add it manually

        $response = $this->actingAs($payrollUser)->postJson('/api/v1/hr/users', $userData);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'last_name' => $userData['last_name'],
            'employee_no' => $userData['employee_no'],
        ]);

        $this->assertDatabaseHas('users', [
            'employee_no' => $userData['employee_no'],
        ]);
    }

    public function test_payroll_user_cannot_add_user_with_invalid_data(): void
    {
        // create a user with specific email and employee no to test unique validation
        User::factory()->create([
            'email' => 'doe@gmail.com',
            'employee_no' => '003121',
        ]);

        $payrollUser = User::factory()->create();
        $payrollUser->departments()->sync([20, 21]); // id 20 is HR department, id 21 is payroll department

        $userData = User::factory()->make([
            'email' => 'doe@gmail.com', // duplicate email
            'employee_no' => '003121', // duplicate employee no
        ])->toArray();
        $userData['password'] = 'Password123';

        $response = $this->actingAs($payrollUser)->postJson('/api/v1/hr/users', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'employee_no']);
    }
}
