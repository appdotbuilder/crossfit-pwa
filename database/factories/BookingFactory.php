<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\CrossfitClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\App\Models\Booking>
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->member(),
            'class_id' => CrossfitClass::factory(),
            'status' => fake()->randomElement(['confirmed', 'waiting_list', 'cancelled']),
            'booking_type' => fake()->randomElement(['membership', 'drop_in', 'day_pass']),
            'amount_paid' => fake()->optional(0.3)->randomFloat(2, 15, 40), // 30% chance of having paid amount
            'stripe_payment_intent_id' => fake()->optional(0.3)->uuid(),
            'is_refundable' => true,
        ];
    }

    /**
     * Indicate that the booking is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
        ]);
    }

    /**
     * Indicate that the booking is on waiting list.
     */
    public function waitingList(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'waiting_list',
        ]);
    }

    /**
     * Indicate that the booking is a drop-in.
     */
    public function dropIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_type' => 'drop_in',
            'amount_paid' => fake()->randomFloat(2, 15, 40),
            'stripe_payment_intent_id' => fake()->uuid(),
        ]);
    }
}