<?php

namespace Database\Factories;

use App\Enums\MeetingPlatform;
use AppointmentApp\EventType\Models\EventType;
use AppointmentApp\Schedule\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventTypeFactory extends Factory
{
    protected $model = EventType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true) . ' Event',
            'description' => $this->faker->paragraph(),
            'default_duration' => $this->faker->randomElement([15, 30, 45, 60, 90, 120]),
            'default_price' => $this->faker->optional()->randomFloat(2, 10, 200),
            'default_max_participants' => $this->faker->optional()->numberBetween(1, 10),
            'meeting_platform' => $this->faker->randomElement(MeetingPlatform::cases())->value,
            'meeting_link' => $this->faker->url(),
            'creator_id' => User::factory()->creator(),
            'schedule_id' => null,
        ];
    }

    /**
     * Indicate that the event type has a custom meeting link.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withCustomLink()
    {
        return $this->state(function (array $attributes) {
            return [
                'meeting_platform' => MeetingPlatform::CUSTOM->value,
                'meeting_link' => $this->faker->url(),
            ];
        });
    }

    /**
     * Indicate that the event type is associated with a schedule.
     *
     * @param Schedule|null $schedule
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withSchedule(Schedule $schedule = null)
    {
        return $this->state(function (array $attributes) use ($schedule) {
            return [
                'schedule_id' => $schedule ? $schedule->id : Schedule::factory(),
            ];
        });
    }

    /**
     * Indicate that the event type is free.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function free()
    {
        return $this->state(function (array $attributes) {
            return [
                'default_price' => null,
            ];
        });
    }
}
