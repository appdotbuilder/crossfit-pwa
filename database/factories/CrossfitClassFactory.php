<?php

namespace Database\Factories;

use App\Models\CrossfitClass;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CrossfitClass>
 */
class CrossfitClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\CrossfitClass>
     */
    protected $model = CrossfitClass::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classNames = [
            'Morning WOD',
            'CrossFit Fundamentals',
            'Olympic Lifting',
            'MetCon Madness',
            'Teen CrossFit',
            'Open Gym',
            'Strength & Conditioning',
            'CrossFit Endurance',
        ];

        $descriptions = [
            'High-intensity workout combining cardio and strength training',
            'Perfect for beginners learning CrossFit movements',
            'Focus on Olympic lifting techniques and form',
            'Metabolic conditioning to push your limits',
            'CrossFit fundamentals designed for teenagers',
            'Self-directed training with coach supervision',
            'Build strength and improve conditioning',
            'Endurance-focused CrossFit workout',
        ];

        return [
            'tenant_id' => Tenant::factory(),
            'instructor_id' => User::factory()->instructor(),
            'name' => fake()->randomElement($classNames),
            'description' => fake()->randomElement($descriptions),
            'starts_at' => fake()->dateTimeBetween('now', '+2 weeks'),
            'duration_minutes' => fake()->randomElement([45, 60, 75, 90]),
            'max_participants' => fake()->numberBetween(8, 20),
            'teen_approved' => fake()->boolean(30), // 30% chance of being teen approved
            'drop_in_price' => fake()->randomFloat(2, 15, 40),
            'is_cancelled' => false,
        ];
    }

    /**
     * Indicate that the class is teen approved.
     */
    public function teenApproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'teen_approved' => true,
        ]);
    }

    /**
     * Indicate that the class is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_cancelled' => true,
        ]);
    }

    /**
     * Create an upcoming class.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => fake()->dateTimeBetween('now', '+1 week'),
        ]);
    }
}