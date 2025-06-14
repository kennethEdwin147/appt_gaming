<?php

namespace Database\Factories;

use App\Models\Creator;
use App\Models\EventType;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        $reservedDatetime = Carbon::now()->addDays(rand(1, 30))->setHour(rand(9, 17))->setMinute(0)->setSecond(0);

        // Créer un utilisateur et un créateur liés
        $user = User::factory()->create(['role' => 'user']);
        $creatorUser = User::factory()->create(['role' => 'creator']);
        $creator = Creator::factory()->create(['user_id' => $creatorUser->id]);

        // Créer un type d'événement lié au créateur
        $eventType = EventType::factory()->create([
            'creator_id' => $creator->id,
            'is_active' => true
        ]);

        return [
            'user_id' => $user->id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'availability_id' => null,
            'guest_first_name' => $this->faker->firstName(),
            'guest_last_name' => $this->faker->lastName(),
            'reserved_datetime' => $reservedDatetime,
            'timezone' => $this->faker->randomElement(['UTC', 'Europe/Paris', 'America/New_York', 'Asia/Tokyo']),
            'meeting_link' => $this->faker->optional(0.3)->url(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'rescheduled', 'completed']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'refunded', 'failed']),
            'payment_id' => $this->faker->optional(0.5)->uuid(),
        ];
    }

    /**
     * Indicate that the reservation is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Indicate that the reservation is confirmed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed',
            ];
        });
    }

    /**
     * Indicate that the reservation is cancelled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled',
            ];
        });
    }

    /**
     * Indicate that the reservation is rescheduled.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function rescheduled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rescheduled',
            ];
        });
    }

    /**
     * Indicate that the reservation is completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    /**
     * Indicate that the payment is pending.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function paymentPending()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'pending',
            ];
        });
    }

    /**
     * Indicate that the payment is paid.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'paid',
                'payment_id' => $this->faker->uuid(),
            ];
        });
    }
}
