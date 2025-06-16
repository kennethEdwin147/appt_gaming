<?php

namespace Database\Factories;

use App\Models\availability\Availability;
use App\Models\creator\Creator;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityFactory extends Factory
{
    protected $model = Availability::class;

    public function definition()
    {
        $startTime = $this->faker->time('H:i');
        $endTime = date('H:i', strtotime($startTime) + $this->faker->numberBetween(1, 8) * 3600); // 1-8 hours later

        return [
            'creator_id' => Creator::factory(),
            'day_of_week' => $this->faker->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_active' => $this->faker->boolean(80), // 80% chance to be active
        ];
    }

    /**
     * Indicate that the availability is active.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Indicate that the availability is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    /**
     * Set a specific day of the week.
     *
     * @param string $day
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function forDay(string $day)
    {
        return $this->state(function (array $attributes) use ($day) {
            return [
                'day_of_week' => $day,
            ];
        });
    }

    /**
     * Set specific time range.
     *
     * @param string $start
     * @param string $end
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function timeRange(string $start, string $end)
    {
        return $this->state(function (array $attributes) use ($start, $end) {
            return [
                'start_time' => $start,
                'end_time' => $end,
            ];
        });
    }
}
