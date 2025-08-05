<?php

namespace Database\Factories;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Subscription>
     */
    protected $model = Subscription::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['standard', 'student', 'teen_1', 'teen_2']);
        $amount = match($type) {
            'standard' => 150.00,
            'student' => 120.00,
            'teen_1' => 80.00,
            'teen_2' => 120.00,
            default => 150.00,
        };

        $start = fake()->dateTimeBetween('-3 months', 'now');
        $end = (clone $start)->modify('+1 month');

        return [
            'user_id' => User::factory()->member(),
            'stripe_subscription_id' => 'sub_' . fake()->unique()->regexify('[A-Za-z0-9]{24}'),
            'type' => $type,
            'status' => fake()->randomElement(['active', 'cancelled', 'past_due']),
            'amount' => $amount,
            'current_period_start' => $start,
            'current_period_end' => $end,
            'cancelled_at' => null,
        ];
    }

    /**
     * Indicate that the subscription is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'cancelled_at' => null,
        ]);
    }

    /**
     * Indicate that the subscription is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancelled_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}