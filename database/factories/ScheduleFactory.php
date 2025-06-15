<?php

namespace Database\Factories;

use AppointmentApp\Schedule\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition()
    {
        return [
            'creator_id' => User::factory()->creator(),
            'name' => $this->faker->words(3, true) . ' Schedule',
            'effective_from' => $this->faker->optional()->date(),
            'effective_until' => $this->faker->optional()->date(),
        ];
    }

    /**
     * Set a specific date range for the schedule.
     *
     * @param string $from
     * @param string $until
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function dateRange(string $from, string $until)
    {
        return $this->state(function (array $attributes) use ($from, $until) {
            return [
                'effective_from' => $from,
                'effective_until' => $until,
            ];
        });
    }
}
