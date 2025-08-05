<?php

namespace Database\Factories;

use App\Models\Tenant;
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'tenant_id' => Tenant::factory(),
            'role' => 'member',
            'phone' => fake()->optional()->phoneNumber(),
            'date_of_birth' => fake()->optional()->date('Y-m-d', '-13 years'),
            'membership_type' => fake()->randomElement(['standard', 'student', 'teen_1', 'teen_2']),
            'membership_expires_at' => fake()->date('Y-m-d', '+1 year'),
            'is_active' => true,
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
     * Indicate that the user is a super admin.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => null,
            'role' => 'super_admin',
            'membership_type' => null,
            'membership_expires_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a tenant admin.
     */
    public function tenantAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'tenant_admin',
            'membership_type' => null,
            'membership_expires_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an instructor.
     */
    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'instructor',
            'membership_type' => 'free',
            'membership_expires_at' => null,
        ]);
    }

    /**
     * Indicate that the user is a member.
     */
    public function member(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
        ]);
    }

    /**
     * Indicate that the user has a student membership.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'member',
            'membership_type' => 'student',
        ]);
    }
}
