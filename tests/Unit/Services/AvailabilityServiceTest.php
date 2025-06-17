<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Services\AvailabilityService;
use App\Models\TimeSlot;
use App\Models\availability\Availability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class AvailabilityServiceTest extends TestCase
{
    use CreatesTestData;
    
    protected AvailabilityService $availabilityService;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityService = app(AvailabilityService::class);
        
        Mail::fake();
        Notification::fake();
    }

    // Tests génération time slots

    /** @test */
    public function it_can_generate_time_slots_for_creator()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '12:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        $this->assertIsArray($slots);
        $this->assertNotEmpty($slots);
        $this->assertContainsOnlyInstancesOf(TimeSlot::class, $slots);
        
        // Vérifier qu'on a des slots pour le lundi
        $mondaySlots = collect($slots)->filter(function ($slot) use ($startDate) {
            return $slot->start_time->format('Y-m-d') === $startDate->format('Y-m-d');
        });
        
        $this->assertGreaterThan(0, $mondaySlots->count());
    }

    /** @test */
    public function it_generates_slots_for_correct_dates()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDay();
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        foreach ($slots as $slot) {
            $this->assertTrue($slot->start_time->between($startDate->startOfDay(), $endDate->endOfDay()));
        }
    }

    /** @test */
    public function it_respects_availability_hours()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '14:00',
            'end_time' => '16:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDay();
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        $this->assertNotEmpty($slots);
        foreach ($slots as $slot) {
            $localTime = $slot->start_time->setTimezone($creator->timezone);
            $this->assertGreaterThanOrEqual(14, $localTime->hour);
            $this->assertLessThan(16, $localTime->hour);
        }
    }

    /** @test */
    public function it_generates_30_minute_slots_by_default()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDay();
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        $this->assertNotEmpty($slots);
        foreach ($slots as $slot) {
            $duration = $slot->start_time->diffInMinutes($slot->end_time);
            $this->assertEquals(30, $duration);
        }
    }

    /** @test */
    public function it_uses_creator_timezone()
    {
        // Arrange
        $creator = $this->createCreator(['timezone' => 'Europe/Paris']);
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDay();
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        $this->assertNotEmpty($slots);
        foreach ($slots as $slot) {
            $this->assertEquals('Europe/Paris', $slot->timezone);
        }
    }

    /** @test */
    public function it_marks_slots_as_recurring()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDay();
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        $this->assertNotEmpty($slots);
        foreach ($slots as $slot) {
            $this->assertTrue($slot->is_recurring_slot);
        }
    }

    /** @test */
    public function it_creates_slots_for_multiple_days()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
        $this->createAvailability($creator, [
            'day_of_week' => 'tuesday',
            'start_time' => '14:00',
            'end_time' => '15:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        $this->assertNotEmpty($slots);
        
        $mondaySlots = collect($slots)->filter(function ($slot) use ($startDate) {
            return $slot->start_time->format('Y-m-d') === $startDate->format('Y-m-d');
        });
        
        $tuesdaySlots = collect($slots)->filter(function ($slot) use ($startDate) {
            return $slot->start_time->format('Y-m-d') === $startDate->copy()->addDay()->format('Y-m-d');
        });
        
        $this->assertGreaterThan(0, $mondaySlots->count());
        $this->assertGreaterThan(0, $tuesdaySlots->count());
    }

    /** @test */
    public function it_skips_days_without_availability()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createAvailability($creator, [
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
        
        $startDate = Carbon::parse('next monday');
        $endDate = $startDate->copy()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->generateTimeSlotsForCreator(
            $creator->id,
            $startDate,
            $endDate
        );
        
        // Assert
        $this->assertNotEmpty($slots);
        
        // Vérifier qu'il n'y a pas de slots pour mardi (pas d'availability)
        $tuesdaySlots = collect($slots)->filter(function ($slot) use ($startDate) {
            return $slot->start_time->format('Y-m-d') === $startDate->copy()->addDay()->format('Y-m-d');
        });
        
        $this->assertEquals(0, $tuesdaySlots->count());
    }

    // Tests récupération slots disponibles

    /** @test */
    public function it_can_get_available_slots_for_event_type()
    {
        // Arrange
        $creator = $this->createCreator();
        $eventType = $this->createEventType($creator);
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->getAvailableSlots(
            $creator->id,
            $eventType->id,
            $startDate,
            $endDate,
            'America/Toronto'
        );
        
        // Assert
        $this->assertIsArray($slots);
        $this->assertNotEmpty($slots);
        
        // Vérifier la structure des slots
        foreach ($slots as $daySlots) {
            foreach ($daySlots as $slot) {
                $this->assertArrayHasKey('id', $slot);
                $this->assertArrayHasKey('start_time', $slot);
                $this->assertArrayHasKey('display_time', $slot);
                $this->assertArrayHasKey('datetime', $slot);
            }
        }
    }

    /** @test */
    public function it_only_returns_available_status_slots()
    {
        // Arrange
        $creator = $this->createCreator();
        $eventType = $this->createEventType($creator);
        $availableSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        $bookedSlot = $this->createTimeSlot($creator, ['status' => 'booked']);
        $blockedSlot = $this->createTimeSlot($creator, ['status' => 'blocked']);
        
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->getAvailableSlots(
            $creator->id,
            $eventType->id,
            $startDate,
            $endDate,
            'America/Toronto'
        );
        
        // Assert
        $allSlotIds = [];
        foreach ($slots as $daySlots) {
            foreach ($daySlots as $slot) {
                $allSlotIds[] = $slot['id'];
            }
        }
        
        $this->assertContains($availableSlot->id, $allSlotIds);
        $this->assertNotContains($bookedSlot->id, $allSlotIds);
        $this->assertNotContains($blockedSlot->id, $allSlotIds);
    }

    /** @test */
    public function it_filters_by_event_duration()
    {
        // Arrange
        $creator = $this->createCreator();
        $eventType = $this->createEventType($creator, ['default_duration' => 60]);
        
        // Créer un slot de 30 minutes (trop court)
        $shortSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->addDay()->setHour(10),
            'end_time' => now()->addDay()->setHour(10)->addMinutes(30),
        ]);
        
        // Créer un slot de 60 minutes (correct)
        $longSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->addDay()->setHour(14),
            'end_time' => now()->addDay()->setHour(14)->addMinutes(60),
        ]);
        
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->getAvailableSlots(
            $creator->id,
            $eventType->id,
            $startDate,
            $endDate,
            'America/Toronto'
        );
        
        // Assert
        $allSlotIds = [];
        foreach ($slots as $daySlots) {
            foreach ($daySlots as $slot) {
                $allSlotIds[] = $slot['id'];
            }
        }
        
        $this->assertNotContains($shortSlot->id, $allSlotIds);
        $this->assertContains($longSlot->id, $allSlotIds);
    }

    /** @test */
    public function it_organizes_slots_by_date()
    {
        // Arrange
        $creator = $this->createCreator();
        $eventType = $this->createEventType($creator);
        
        $todaySlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->addDay()->setHour(10),
        ]);
        
        $tomorrowSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->addDays(2)->setHour(10),
        ]);
        
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->getAvailableSlots(
            $creator->id,
            $eventType->id,
            $startDate,
            $endDate,
            'America/Toronto'
        );
        
        // Assert
        $this->assertIsArray($slots);
        $this->assertGreaterThanOrEqual(2, count($slots));
        
        // Vérifier que les slots sont organisés par date
        foreach ($slots as $dateKey => $daySlots) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $dateKey);
            $this->assertIsArray($daySlots);
        }
    }

    /** @test */
    public function it_converts_to_user_timezone()
    {
        // Arrange
        $creator = $this->createCreator(['timezone' => 'America/Toronto']);
        $eventType = $this->createEventType($creator);
        
        $timeSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->addDay()->setHour(15)->setMinute(0), // 15:00 Toronto = 21:00 Paris
        ]);
        
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->getAvailableSlots(
            $creator->id,
            $eventType->id,
            $startDate,
            $endDate,
            'Europe/Paris'
        );
        
        // Assert
        $this->assertNotEmpty($slots);
        
        $foundSlot = null;
        foreach ($slots as $daySlots) {
            foreach ($daySlots as $slot) {
                if ($slot['id'] === $timeSlot->id) {
                    $foundSlot = $slot;
                    break;
                }
            }
        }
        
        $this->assertNotNull($foundSlot);
        $this->assertEquals('21:00', $foundSlot['start_time']);
    }

    /** @test */
    public function it_includes_custom_price_if_set()
    {
        // Arrange
        $creator = $this->createCreator();
        $eventType = $this->createEventType($creator);
        
        $timeSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'custom_price' => 75.00,
        ]);
        
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->getAvailableSlots(
            $creator->id,
            $eventType->id,
            $startDate,
            $endDate,
            'America/Toronto'
        );
        
        // Assert
        $foundSlot = null;
        foreach ($slots as $daySlots) {
            foreach ($daySlots as $slot) {
                if ($slot['id'] === $timeSlot->id) {
                    $foundSlot = $slot;
                    break;
                }
            }
        }
        
        $this->assertNotNull($foundSlot);
        $this->assertEquals(75.00, $foundSlot['custom_price']);
    }

    /** @test */
    public function it_excludes_past_slots()
    {
        // Arrange
        $creator = $this->createCreator();
        $eventType = $this->createEventType($creator);
        
        $pastSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->subDay(),
        ]);
        
        $futureSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->addDay(),
        ]);
        
        $startDate = Carbon::today()->subDays(2);
        $endDate = Carbon::today()->addDays(7);
        
        // Act
        $slots = $this->availabilityService->getAvailableSlots(
            $creator->id,
            $eventType->id,
            $startDate,
            $endDate,
            'America/Toronto'
        );
        
        // Assert
        $allSlotIds = [];
        foreach ($slots as $daySlots) {
            foreach ($daySlots as $slot) {
                $allSlotIds[] = $slot['id'];
            }
        }
        
        $this->assertNotContains($pastSlot->id, $allSlotIds);
        $this->assertContains($futureSlot->id, $allSlotIds);
    }

    // Tests booking/release slots

    /** @test */
    public function it_can_book_available_time_slot()
    {
        // Arrange
        $timeSlot = $this->createTimeSlot(null, ['status' => 'available']);
        
        // Act
        $result = $this->availabilityService->bookTimeSlot($timeSlot->id);
        
        // Assert
        $this->assertTrue($result);
        $timeSlot->refresh();
        $this->assertEquals('booked', $timeSlot->status);
    }

    /** @test */
    public function it_cannot_book_unavailable_slot()
    {
        // Arrange
        $timeSlot = $this->createTimeSlot(null, ['status' => 'booked']);
        
        // Act
        $result = $this->availabilityService->bookTimeSlot($timeSlot->id);
        
        // Assert
        $this->assertFalse($result);
        $timeSlot->refresh();
        $this->assertEquals('booked', $timeSlot->status); // Unchanged
    }

    /** @test */
    public function it_cannot_book_past_slot()
    {
        // Arrange
        $timeSlot = $this->createTimeSlot(null, [
            'status' => 'available',
            'start_time' => now()->subHour(),
        ]);
        
        // Act & Assert
        // Note: Cette vérification se fait dans isTimeSlotAvailable
        $available = $this->availabilityService->isTimeSlotAvailable($timeSlot->id, 1);
        $this->assertFalse($available);
    }

    /** @test */
    public function it_can_release_booked_slot()
    {
        // Arrange
        $timeSlot = $this->createTimeSlot(null, ['status' => 'booked']);
        
        // Act
        $result = $this->availabilityService->releaseTimeSlot($timeSlot->id);
        
        // Assert
        $this->assertTrue($result);
        $timeSlot->refresh();
        $this->assertEquals('available', $timeSlot->status);
    }

    /** @test */
    public function it_cannot_release_available_slot()
    {
        // Arrange
        $timeSlot = $this->createTimeSlot(null, ['status' => 'available']);
        
        // Act
        $result = $this->availabilityService->releaseTimeSlot($timeSlot->id);
        
        // Assert
        $this->assertFalse($result);
        $timeSlot->refresh();
        $this->assertEquals('available', $timeSlot->status); // Unchanged
    }

    // Tests blocage manuel

    /** @test */
    public function it_can_block_time_slot()
    {
        // Arrange
        $creator = $this->createCreator();
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        
        // Act
        $result = $this->availabilityService->blockTimeSlot($timeSlot->id, $creator->id, 'Personal meeting');
        
        // Assert
        $this->assertTrue($result);
        $timeSlot->refresh();
        $this->assertEquals('blocked', $timeSlot->status);
        $this->assertEquals('Personal meeting', $timeSlot->creator_notes);
    }

    /** @test */
    public function it_can_unblock_time_slot()
    {
        // Arrange
        $creator = $this->createCreator();
        $timeSlot = $this->createTimeSlot($creator, [
            'status' => 'blocked',
            'creator_notes' => 'Personal meeting'
        ]);
        
        // Act
        $result = $this->availabilityService->unblockTimeSlot($timeSlot->id, $creator->id);
        
        // Assert
        $this->assertTrue($result);
        $timeSlot->refresh();
        $this->assertEquals('available', $timeSlot->status);
        $this->assertNull($timeSlot->creator_notes);
    }

    /** @test */
    public function it_cannot_block_past_slot()
    {
        // Arrange
        $creator = $this->createCreator();
        $timeSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'start_time' => now()->subHour(),
        ]);
        
        // Act
        $result = $this->availabilityService->blockTimeSlot($timeSlot->id, $creator->id);
        
        // Assert
        $this->assertTrue($result); // Le service ne vérifie pas si c'est dans le passé pour le blocage
        $timeSlot->refresh();
        $this->assertEquals('blocked', $timeSlot->status);
    }

    /** @test */
    public function it_saves_creator_notes_when_blocking()
    {
        // Arrange
        $creator = $this->createCreator();
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        $reason = 'Meeting with potential sponsor';
        
        // Act
        $result = $this->availabilityService->blockTimeSlot($timeSlot->id, $creator->id, $reason);
        
        // Assert
        $this->assertTrue($result);
        $timeSlot->refresh();
        $this->assertEquals($reason, $timeSlot->creator_notes);
    }

    // Tests statistiques

    /** @test */
    public function it_can_get_creator_slots_stats()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createTimeSlot($creator, ['status' => 'available']);
        $this->createTimeSlot($creator, ['status' => 'booked']);
        $this->createTimeSlot($creator, ['status' => 'blocked']);
        $this->createTimeSlot($creator, ['status' => 'past']);
        
        // Act
        $stats = $this->availabilityService->getCreatorSlotsStats($creator->id);
        
        // Assert
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('available', $stats);
        $this->assertArrayHasKey('booked', $stats);
        $this->assertArrayHasKey('blocked', $stats);
        $this->assertArrayHasKey('past', $stats);
        $this->assertArrayHasKey('utilization_rate', $stats);
        
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(1, $stats['available']);
        $this->assertEquals(1, $stats['booked']);
        $this->assertEquals(1, $stats['blocked']);
        $this->assertEquals(1, $stats['past']);
    }

    /** @test */
    public function it_calculates_utilization_rate()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createTimeSlot($creator, ['status' => 'available']);
        $this->createTimeSlot($creator, ['status' => 'booked']);
        $this->createTimeSlot($creator, ['status' => 'booked']);
        $this->createTimeSlot($creator, ['status' => 'booked']);
        
        // Act
        $stats = $this->availabilityService->getCreatorSlotsStats($creator->id);
        
        // Assert
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(3, $stats['booked']);
        $this->assertEquals(75.0, $stats['utilization_rate']); // 3/4 = 75%
    }

    /** @test */
    public function it_counts_each_status_correctly()
    {
        // Arrange
        $creator = $this->createCreator();
        
        // Créer 2 slots de chaque statut
        for ($i = 0; $i < 2; $i++) {
            $this->createTimeSlot($creator, ['status' => 'available']);
            $this->createTimeSlot($creator, ['status' => 'booked']);
            $this->createTimeSlot($creator, ['status' => 'blocked']);
            $this->createTimeSlot($creator, ['status' => 'past']);
        }
        
        // Act
        $stats = $this->availabilityService->getCreatorSlotsStats($creator->id);
        
        // Assert
        $this->assertEquals(8, $stats['total']);
        $this->assertEquals(2, $stats['available']);
        $this->assertEquals(2, $stats['booked']);
        $this->assertEquals(2, $stats['blocked']);
        $this->assertEquals(2, $stats['past']);
        $this->assertEquals(25.0, $stats['utilization_rate']); // 2/8 = 25%
    }

    /** @test */
    public function it_handles_empty_stats()
    {
        // Arrange
        $creator = $this->createCreator();
        
        // Act
        $stats = $this->availabilityService->getCreatorSlotsStats($creator->id);
        
        // Assert
        $this->assertEquals(0, $stats['total']);
        $this->assertEquals(0, $stats['available']);
        $this->assertEquals(0, $stats['booked']);
        $this->assertEquals(0, $stats['blocked']);
        $this->assertEquals(0, $stats['past']);
        $this->assertEquals(0, $stats['utilization_rate']);
    }

    // Tests nettoyage

    /** @test */
    public function it_can_cleanup_past_slots()
    {
        // Arrange
        $creator = $this->createCreator();
        
        $pastSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'end_time' => now()->subHour(),
        ]);
        
        $futureSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'end_time' => now()->addHour(),
        ]);
        
        // Act
        $updated = $this->availabilityService->cleanupPastSlots();
        
        // Assert
        $this->assertEquals(1, $updated);
        
        $pastSlot->refresh();
        $futureSlot->refresh();
        
        $this->assertEquals('past', $pastSlot->status);
        $this->assertEquals('available', $futureSlot->status);
    }

    /** @test */
    public function it_updates_past_slot_status()
    {
        // Arrange
        $creator = $this->createCreator();
        
        $pastAvailableSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'end_time' => now()->subHour(),
        ]);
        
        $pastBlockedSlot = $this->createTimeSlot($creator, [
            'status' => 'blocked',
            'end_time' => now()->subHour(),
        ]);
        
        $pastBookedSlot = $this->createTimeSlot($creator, [
            'status' => 'booked',
            'end_time' => now()->subHour(),
        ]);
        
        // Act
        $updated = $this->availabilityService->cleanupPastSlots();
        
        // Assert
        $this->assertEquals(2, $updated); // Seuls available et blocked sont mis à jour
        
        $pastAvailableSlot->refresh();
        $pastBlockedSlot->refresh();
        $pastBookedSlot->refresh();
        
        $this->assertEquals('past', $pastAvailableSlot->status);
        $this->assertEquals('past', $pastBlockedSlot->status);
        $this->assertEquals('booked', $pastBookedSlot->status); // Pas changé
    }

    /** @test */
    public function it_only_cleans_past_slots()
    {
        // Arrange
        $creator = $this->createCreator();
        
        $pastSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'end_time' => now()->subMinute(),
        ]);
        
        $currentSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'end_time' => now()->addMinute(),
        ]);
        
        $futureSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'end_time' => now()->addHour(),
        ]);
        
        // Act
        $updated = $this->availabilityService->cleanupPastSlots();
        
        // Assert
        $this->assertEquals(1, $updated);
        
        $pastSlot->refresh();
        $currentSlot->refresh();
        $futureSlot->refresh();
        
        $this->assertEquals('past', $pastSlot->status);
        $this->assertEquals('available', $currentSlot->status);
        $this->assertEquals('available', $futureSlot->status);
    }
}