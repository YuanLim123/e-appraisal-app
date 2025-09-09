<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\PositionSeeder;
use Database\Seeders\DepartmentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersListTest extends TestCase
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
    public function test_users_list_return_pagination(): void
    {
        User::factory(11)->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200);

        $response->assertJsonCount(10, 'data');
    }

    public function test_users_list_show_only_not_resigned_users(): void
    {
        $resignedUser = User::factory()->create(['resign_at' => now()]);
        $activeUser = User::factory()->create();

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['employee_no' => $activeUser->employee_no]);
        $response->assertJsonMissing(['employee_no' => $resignedUser->employee_no]);
    }
}
