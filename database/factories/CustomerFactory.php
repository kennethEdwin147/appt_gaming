<?php

namespace Database\Factories;

use App\Models\User;
use AppointmentApp\Customer\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AppointmentApp\Customer\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => $this->faker->optional()->phoneNumber(),
            'phone_verified_at' => $this->faker->optional()->dateTime(),
            'date_of_birth' => $this->faker->optional()->date('Y-m-d', '-18 years'),
            'timezone' => $this->faker->randomElement(['Europe/Paris', 'Europe/London', 'America/New_York']),
            'language' => $this->faker->randomElement(['fr', 'en', 'es']),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'last_activity_at' => $this->faker->optional()->dateTime(),
        ];
    }

    /**
     * Indicate that the customer is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the customer is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the customer has a verified phone.
     */
    public function phoneVerified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone' => $this->faker->phoneNumber(),
            'phone_verified_at' => now(),
        ]);
    }

    /**
     * Indicate that the customer has recent activity.
     */
    public function recentActivity(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_activity_at' => now()->subMinutes(rand(1, 60)),
        ]);
    }

    /**
     * Create a customer with a specific user role.
     */
    public function withCustomerUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory()->create([
                'role' => 'customer',
                'email_verified_at' => now(),
            ])->id,
        ]);
    }
}
