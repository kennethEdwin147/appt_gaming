<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use App\Models\availability\Availability;
use Illuminate\Support\Facades\Hash;

trait CreatesTestData
{
    /**
     * Create a user with specified role.
     */
    protected function createUser(string $role = 'customer', array $attributes = []): User
    {
        $defaults = [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'role' => $role,
            'email_verified_at' => now(),
        ];

        $finalAttributes = array_merge($defaults, $attributes);
        
        // Ensure email is always verified in tests
        if (!isset($finalAttributes['email_verified_at'])) {
            $finalAttributes['email_verified_at'] = now();
        }
        
        $user = User::create($finalAttributes);

        // Auto-create role-specific profile
        if ($role === 'creator') {
            $this->createCreatorProfile($user);
        } elseif ($role === 'customer') {
            $this->createCustomerProfile($user);
        }

        return $user;
    }

    /**
     * Create a creator with user profile.
     */
    protected function createCreator(array $attributes = []): Creator
    {
        // Créer un user sans auto-créer le profil
        $userAttributes = array_merge([
            'first_name' => 'Test',
            'last_name' => 'Creator',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'role' => 'creator',
            'email_verified_at' => now(),
        ], $attributes['user'] ?? []);

        $user = User::create($userAttributes);
        
        $defaults = [
            'user_id' => $user->id,
            'gaming_pseudo' => 'ProGamer' . rand(1000, 9999),
            'bio' => 'Expert gaming coach',
            'timezone' => 'America/Toronto',
            'confirmed_at' => now(),
        ];

        return Creator::create(array_merge($defaults, \Illuminate\Support\Arr::except($attributes, ['user'])));
    }

    /**
     * Create a customer with user profile.
     */
    protected function createCustomer(array $attributes = []): Customer
    {
        // Créer un user sans auto-créer le profil
        $userAttributes = array_merge([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ], $attributes['user'] ?? []);

        $user = User::create($userAttributes);
        
        $defaults = [
            'user_id' => $user->id,
            'timezone' => 'America/Toronto',
            'status' => 'active',
        ];

        return Customer::create(array_merge($defaults, \Illuminate\Support\Arr::except($attributes, ['user'])));
    }

    /**
     * Create an event type for a creator.
     */
    protected function createEventType(?Creator $creator = null, array $attributes = [])
    {
        if (!$creator) {
            $creator = $this->createCreator();
        }

        $defaults = [
            'creator_id' => $creator->id,
            'name' => 'Gaming Session',
            'description' => 'Professional gaming coaching session',
            'default_duration' => 60,
            'default_price' => 50.00,
            'default_max_participants' => 1,
            'meeting_platform' => 'discord',
            'meeting_link' => 'https://discord.gg/example',
            'session_type' => 'individual',
            'is_active' => true,
        ];

        // Manual include since autoloader has issues with dash in folder names
        require_once app_path('Models/event-type/EventType.php');
        $eventTypeClass = 'App\\Models\\EventType\\EventType';
        return $eventTypeClass::create(array_merge($defaults, $attributes));
    }

    /**
     * Create availability for a creator.
     */
    protected function createAvailability(?Creator $creator = null, array $attributes = []): Availability
    {
        if (!$creator) {
            $creator = $this->createCreator();
        }

        $defaults = [
            'creator_id' => $creator->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_active' => true,
        ];

        return Availability::create(array_merge($defaults, $attributes));
    }

    /**
     * Create time slot for a creator.
     */
    protected function createTimeSlot(?Creator $creator = null, array $attributes = [])
    {
        if (!$creator) {
            $creator = $this->createCreator();
        }

        // Use microseconds to ensure uniqueness
        $baseTime = now()->addDay()->setHour(10)->setMinute(0)->setSecond(0);
        $startTime = $baseTime->addMicroseconds(rand(0, 999999));
        
        $defaults = [
            'creator_id' => $creator->id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes(30),
            'timezone' => $creator->timezone ?? 'America/Toronto',
            'status' => 'available',
            'generated_for_date' => $startTime->format('Y-m-d'),
            'is_recurring_slot' => true,
        ];

        // Manual include since autoloader has issues with dash in folder names
        require_once app_path('Models/time-slot/TimeSlot.php');
        $timeSlotClass = 'App\\Models\\TimeSlot\\TimeSlot';
        return $timeSlotClass::create(array_merge($defaults, $attributes));
    }

    /**
     * Create reservation for testing.
     */
    protected function createReservation(?Creator $creator = null, ?Customer $customer = null, array $attributes = [])
    {
        if (!$creator) {
            $creator = $this->createCreator();
        }
        
        if (!$customer) {
            $customer = $this->createCustomer();
        }
        
        $eventType = $this->createEventType($creator);
        $timeSlot = $this->createTimeSlot($creator);
        
        $defaults = [
            'user_id' => $customer->user_id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'time_slot_id' => $timeSlot->id,
            'reserved_datetime' => $timeSlot->start_time,
            'timezone' => $creator->timezone ?? 'America/Toronto',
            'status' => 'pending',
            'price_paid' => $eventType->default_price,
            'payment_status' => 'pending',
            'participants_count' => 1,
        ];

        // Manual include since autoloader has issues with dash in folder names
        require_once app_path('Models/reservation/Reservation.php');
        $reservationClass = 'App\\Models\\Reservation\\Reservation';
        return $reservationClass::create(array_merge($defaults, $attributes));
    }

    /**
     * Authenticate as a creator.
     */
    protected function actingAsCreator(array $attributes = []): Creator
    {
        $creator = $this->createCreator($attributes);
        $this->actingAs($creator->user);
        return $creator;
    }

    /**
     * Authenticate as a customer.
     */
    protected function actingAsCustomer(array $attributes = []): Customer
    {
        $customer = $this->createCustomer($attributes);
        $this->actingAs($customer->user);
        return $customer;
    }

    /**
     * Create creator profile for a user.
     */
    private function createCreatorProfile(User $user): Creator
    {
        return Creator::create([
            'user_id' => $user->id,
            'gaming_pseudo' => 'ProGamer' . rand(1000, 9999),
            'timezone' => 'America/Toronto',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Create customer profile for a user.
     */
    private function createCustomerProfile(User $user): Customer
    {
        return Customer::create([
            'user_id' => $user->id,
            'timezone' => 'America/Toronto',
            'status' => 'active',
        ]);
    }

    /**
     * Get gaming creator data for testing.
     */
    protected function getGamingCreatorData(): array
    {
        return [
            'first_name' => 'ProGamer',
            'last_name' => 'Elite',
            'email' => 'progamer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'gaming_pseudo' => 'ProGamer2024',
        ];
    }

    /**
     * Get creator setup data for testing.
     */
    protected function getCreatorSetupData(): array
    {
        return [
            'timezone' => 'America/Toronto',
            'bio' => 'Coach Valorant expert avec 5 ans d\'expérience',
            'main_game' => 'Valorant',
            'rank_info' => 'Radiant',
            'default_hourly_rate' => 75.00,
        ];
    }
}