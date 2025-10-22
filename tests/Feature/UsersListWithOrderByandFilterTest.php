<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersListWithOrderByandFilterTest extends TestCase
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

    public function test_users_list_by_created_at_correctly(): void // sorting by created_at desc
    {
        $user = User::factory()->create(['created_at' => now()]);
        $earlierCreatedUser = User::factory()->create(['created_at' => now()->subDays(3)]);
        $laterCreatedUser = User::factory()->create(['created_at' => now()->addDays(10)]);

        $response = $this->actingAs($user)->getJson('/api/v1/users');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.employee_no', $laterCreatedUser->employee_no);
        $response->assertJsonPath('data.1.employee_no', $user->employee_no);
        $response->assertJsonPath('data.2.employee_no', $earlierCreatedUser->employee_no);
    }

    public function test_users_list_sorts_by_last_name_desc_correctly(): void
    {
        $alphaUser = User::factory()->create(['last_name' => 'Alpha']);
        $charlieUser = User::factory()->create(['last_name' => 'Charlie']);
        $bravoUser = User::factory()->create(['last_name' => 'Bravo']);

        $response = $this->actingAs($alphaUser)->getJson('/api/v1/users?sortBy=last_name&sortOrder=desc');

        $response->assertStatus(200);
        $response->assertJsonPath('data.0.employee_no', $charlieUser->employee_no);
        $response->assertJsonPath('data.1.employee_no', $bravoUser->employee_no);
        $response->assertJsonPath('data.2.employee_no', $alphaUser->employee_no);
    }

    public function test_users_list_filter_by_join_date_range_correctly(): void
    {
        $user = User::factory()->create(['join_at' => '2023-01-15']);
        $earlierJoinedUser = User::factory()->create(['join_at' => '2015-12-31']);
        $laterJoinedUser = User::factory()->create(['join_at' => '2025-02-01']);

        $endpoint = '/api/v1/users';

        $response = $this->actingAs($user)->getJson($endpoint.'?joinAfter=2016-01-01');
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['employee_no' => $user->employee_no]);
        $response->assertJsonMissing(['employee_no' => $earlierJoinedUser->employee_no]);

        $response = $this->actingAs($user)->getJson($endpoint.'?joinAfter=2026-01-01');
        $response->assertJsonCount(0, 'data');

        $response = $this->actingAs($user)->getJson($endpoint.'?joinBefore=2016-12-31');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['employee_no' => $earlierJoinedUser->employee_no]);
        $response->assertJsonMissing([
            'employee_no' => $user->employee_no,
            'employee_no' => $laterJoinedUser->employee_no,
        ]);

        $response = $this->actingAs($user)->getJson($endpoint.'?joinBefore=2013-12-31');
        $response->assertJsonCount(0, 'data');

        $response = $this->actingAs($user)->getJson($endpoint.'?joinAfter=2016-01-01&joinBefore=2024-12-31');
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['employee_no' => $user->employee_no]);
    }

    public function test_user_list_returns_validation_errors(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/users?sortBy=error_data');
        $response->assertStatus(422);

        $response = $this->actingAs($user)->getJson('/api/v1/users?sortOrder=test');
        $response->assertStatus(422);

        $response = $this->actingAs($user)->getJson('/api/v1/users?joinAfter=abc');
        $response->assertStatus(422);

        $response = $this->actingAs($user)->getJson('/api/v1/users?joinBefore=123');
        $response->assertStatus(422);
    }
}
