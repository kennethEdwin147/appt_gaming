<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use App\Models\availability\Availability;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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
        
        static $creatorCounter = 0;
        $creatorCounter++;
        
        $defaults = [
            'user_id' => $user->id,
            'gaming_pseudo' => 'ProGamer' . $creatorCounter . rand(100, 999),
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

        static $eventTypeCounter = 0;
        $eventTypeCounter++;
        
        $defaults = [
            'creator_id' => $creator->id,
            'name' => 'Gaming Session ' . $eventTypeCounter,
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

        static $globalCounter = 0;
        $globalCounter++;
        
        // Si start_time est fourni dans les attributs, l'utiliser
        if (isset($attributes['start_time'])) {
            $startTime = $attributes['start_time'];
            if (!$startTime instanceof Carbon) {
                $startTime = Carbon::parse($startTime);
            }
        } else {
            // Générer une heure unique basée sur un compteur global
            $baseTime = now()->addDays(2)->setHour(8)->setMinute(0)->setSecond(0)->setMicrosecond(0);
            
            // Espacement de 35 minutes entre chaque slot pour éviter les conflits
            $minuteOffset = $globalCounter * 35;
            $startTime = $baseTime->copy()->addMinutes($minuteOffset);
            
            // Si on dépasse 23h, passer au jour suivant
            if ($startTime->hour >= 23) {
                $daysToAdd = floor($minuteOffset / (16 * 60)); // 16h de travail par jour
                $startTime = $baseTime->copy()->addDays($daysToAdd)->addMinutes($minuteOffset % (16 * 60));
            }
        }
        
        $endTime = isset($attributes['end_time']) ? $attributes['end_time'] : $startTime->copy()->addMinutes(30);
        if (!$endTime instanceof Carbon) {
            $endTime = Carbon::parse($endTime);
        }
        
        $defaults = [
            'creator_id' => $creator->id,
            'start_time' => $startTime->utc(), // Stocker en UTC
            'end_time' => $endTime->utc(),
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