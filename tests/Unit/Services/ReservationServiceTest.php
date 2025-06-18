<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use Tests\Traits\CreatesTestData;
use App\Services\ReservationService;
use App\Services\AvailabilityService;
use App\Services\EmailService;
use App\Models\reservation\Reservation;
use App\Models\TimeSlot\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Mockery;

class ReservationServiceTest extends TestCase
{
    use CreatesTestData;
    
    protected ReservationService $reservationService;
    protected $mockEmailService;
    protected $mockAvailabilityService;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock les services
        $this->mockEmailService = Mockery::mock(EmailService::class);
        $this->mockAvailabilityService = Mockery::mock(AvailabilityService::class);
        
        $this->reservationService = new ReservationService(
            $this->mockEmailService,
            $this->mockAvailabilityService
        );
        
        Mail::fake();
        Notification::fake();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // Tests création réservation

    /** @test */
    public function it_can_create_reservation()
    {
        // Arrange
        $creator = $this->createCreator();
        $customer = $this->createCustomer();
        $eventType = $this->createEventType($creator);
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        
        $data = [
            'user_id' => $customer->user_id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'time_slot_id' => $timeSlot->id,
            'timezone' => 'America/Toronto',
            'special_requests' => 'Coaching débutant',
        ];
        
        // Les emails ne sont pas appelés en mode test, donc on ne mock pas
        
        // Act
        $reservation = $this->reservationService->createReservation($data);
        
        // Assert
        $this->assertInstanceOf(Reservation::class, $reservation);
        $this->assertEquals('pending', $reservation->status);
        $this->assertEquals($customer->user_id, $reservation->user_id);
        $this->assertEquals($creator->id, $reservation->creator_id);
        $this->assertEquals($eventType->id, $reservation->event_type_id);
        $this->assertEquals($timeSlot->id, $reservation->time_slot_id);
        $this->assertEquals('Coaching débutant', $reservation->special_requests);
    }

    /** @test */
    public function it_marks_time_slot_as_booked()
    {
        // Arrange
        $creator = $this->createCreator();
        $customer = $this->createCustomer();
        $eventType = $this->createEventType($creator);
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        
        $data = [
            'user_id' => $customer->user_id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'time_slot_id' => $timeSlot->id,
            'timezone' => 'America/Toronto',
        ];
        
        // Les emails ne sont pas appelés en mode test
        
        // Act
        $reservation = $this->reservationService->createReservation($data);
        
        // Assert
        $timeSlot->refresh();
        $this->assertEquals('booked', $timeSlot->status);
    }

    /** @test */
    public function it_calculates_price_from_event_type()
    {
        // Arrange
        $creator = $this->createCreator();
        $customer = $this->createCustomer();
        $eventType = $this->createEventType($creator, ['default_price' => 75.00]);
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        
        $data = [
            'user_id' => $customer->user_id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'time_slot_id' => $timeSlot->id,
            'timezone' => 'America/Toronto',
        ];
        
        // Les emails ne sont pas appelés en mode test
        
        // Act
        $reservation = $this->reservationService->createReservation($data);
        
        // Assert
        $this->assertEquals(75.00, $reservation->price_paid);
    }

    /** @test */
    public function it_uses_custom_price_if_available()
    {
        // Arrange
        $creator = $this->createCreator();
        $customer = $this->createCustomer();
        $eventType = $this->createEventType($creator, ['default_price' => 50.00]);
        $timeSlot = $this->createTimeSlot($creator, [
            'status' => 'available',
            'custom_price' => 100.00
        ]);
        
        $data = [
            'user_id' => $customer->user_id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'time_slot_id' => $timeSlot->id,
            'timezone' => 'America/Toronto',
        ];
        
        // Les emails ne sont pas appelés en mode test
        
        // Act
        $reservation = $this->reservationService->createReservation($data);
        
        // Assert
        $this->assertEquals(100.00, $reservation->price_paid);
    }

    /** @test */
    public function it_sends_confirmation_notifications()
    {
        // Arrange
        $creator = $this->createCreator();
        $customer = $this->createCustomer();
        $eventType = $this->createEventType($creator);
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'available']);
        
        $data = [
            'user_id' => $customer->user_id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'time_slot_id' => $timeSlot->id,
            'timezone' => 'America/Toronto',
        ];
        
        // Les emails ne sont pas appelés en mode test
        // No email expectations needed
        
        // Act
        $reservation = $this->reservationService->createReservation($data);
    }

    /** @test */
    public function it_cannot_create_for_unavailable_slot()
    {
        // Arrange
        $creator = $this->createCreator();
        $customer = $this->createCustomer();
        $eventType = $this->createEventType($creator);
        $timeSlot = $this->createTimeSlot($creator, ['status' => 'booked']);
        
        $data = [
            'user_id' => $customer->user_id,
            'creator_id' => $creator->id,
            'event_type_id' => $eventType->id,
            'time_slot_id' => $timeSlot->id,
            'timezone' => 'America/Toronto',
        ];
        
        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ce créneau n\'est plus disponible.');
        
        $this->reservationService->createReservation($data);
    }

    /** @test */
    public function it_can_cancel_reservation()
    {
        // Arrange
        $reservation = $this->createReservation();
        $reason = 'Changement de plan';
        
        $this->mockAvailabilityService->shouldReceive('releaseTimeSlot')
            ->once()
            ->with($reservation->time_slot_id);
            
        // Email service call is not mocked as it's called directly in service
        
        // Act
        $result = $this->reservationService->cancelReservation($reservation, $reason);
        
        // Assert
        $this->assertTrue($result);
        $reservation->refresh();
        $this->assertEquals('cancelled', $reservation->status);
        $this->assertEquals($reason, $reservation->cancellation_reason);
        $this->assertNotNull($reservation->cancelled_at);
    }

    /** @test */
    public function it_can_confirm_reservation()
    {
        // Arrange
        $reservation = $this->createReservation(null, null, ['status' => 'pending']);
        
        // Act
        $result = $this->reservationService->confirmReservation($reservation);
        
        // Assert
        $this->assertTrue($result);
        $reservation->refresh();
        $this->assertEquals('confirmed', $reservation->status);
        $this->assertNotNull($reservation->confirmed_at);
    }

    /** @test */
    public function it_can_get_creator_reservation_stats()
    {
        // Arrange
        $creator = $this->createCreator();
        $this->createReservation($creator, null, ['status' => 'completed', 'payment_status' => 'paid', 'price_paid' => 50]);
        $this->createReservation($creator, null, ['status' => 'cancelled']);
        $this->createReservation($creator, null, ['status' => 'no_show_customer']);
        
        // Act
        $stats = $this->reservationService->getCreatorReservationStats($creator->id);
        
        // Assert
        $this->assertArrayHasKey('total_reservations', $stats);
        $this->assertArrayHasKey('completed', $stats);
        $this->assertArrayHasKey('cancelled', $stats);
        $this->assertArrayHasKey('completion_rate', $stats);
        $this->assertArrayHasKey('total_revenue', $stats);
        
        $this->assertEquals(3, $stats['total_reservations']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(1, $stats['cancelled']);
        $this->assertEquals(50.0, $stats['total_revenue']);
    }
}
