<?php

namespace Database\Factories;

use App\Enums\Timezone;
use AppointmentApp\Creator\Models\Creator;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CreatorFactory extends Factory
{
    protected $model = Creator::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->creator(),
            'bio' => $this->faker->paragraph(),
            'platform_name' => $this->faker->optional()->userName(),
            'platform_url' => $this->faker->optional()->url(),
            'type' => $this->faker->randomElement(['content_creator', 'coach', 'consultant', 'teacher', 'artist']),
            'timezone' => $this->faker->randomElement(array_column(Timezone::cases(), 'value')),
            'platform_commission_rate' => $this->faker->randomFloat(2, 0.01, 0.15),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure()
    {
        return $this->afterMaking(function (Creator $creator) {
            // Ensure the user has the creator role
            if ($creator->user && $creator->user->role !== 'creator') {
                $creator->user->update(['role' => 'creator']);
            }
        });
    }

    /**
     * Indicate that the creator has a specific timezone.
     *
     * @param string $timezone
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTimezone(string $timezone)
    {
        return $this->state(function (array $attributes) use ($timezone) {
            return [
                'timezone' => $timezone,
            ];
        });
    }
}
