<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->phoneNumber(),
            'office_phone' => fake()->phoneNumber(),
            'employee_no' => $this->faker->unique()->numerify('######'),
            'password' => static::$password ??= Hash::make('password'),
            'join_at' => fake()->date(),
            'is_login_enabled' => true,
            'is_appraiser' => true,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (User $user) {
            $user->username = 'Asj#'.$user->employee_no;
            $user->position_id = fake()->numberBetween(1, 12);
            $user->role_id = fake()->numberBetween(1, 42);
        })->afterCreating(function (User $user) {
            // Assign user to random departments
            $departments = Department::inRandomOrder()->take(rand(1, 2))->pluck('id');
            $user->departments()->attach($departments);
        });
    }
}
