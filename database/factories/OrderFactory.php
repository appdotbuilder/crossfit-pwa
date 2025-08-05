<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Order>
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['subscription', 'drop_in', 'day_pass']);
        $amount = match($type) {
            'subscription' => fake()->randomFloat(2, 80, 200),
            'drop_in' => fake()->randomFloat(2, 15, 40),
            'day_pass' => fake()->randomFloat(2, 30, 60),
            default => fake()->randomFloat(2, 15, 200),
        };

        return [
            'user_id' => User::factory()->member(),
            'stripe_payment_intent_id' => 'pi_' . fake()->unique()->regexify('[A-Za-z0-9]{24}'),
            'type' => $type,
            'amount' => $amount,
            'platform_fee' => $amount * 0.05, // 5% platform fee
            'status' => fake()->randomElement(['pending', 'completed', 'failed']),
            'metadata' => [
                'class_id' => fake()->optional()->numberBetween(1, 100),
                'membership_type' => fake()->optional()->randomElement(['standard', 'student']),
            ],
        ];
    }

    /**
     * Indicate that the order is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the order is for a drop-in.
     */
    public function dropIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'drop_in',
            'amount' => fake()->randomFloat(2, 15, 40),
        ]);
    }
}