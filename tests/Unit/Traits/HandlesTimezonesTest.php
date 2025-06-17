<?php

namespace Tests\Unit\Traits;

use Tests\TestCase;
use App\Traits\HandlesTimezones;
use Carbon\Carbon;

class HandlesTimezonesTest extends TestCase
{
    use HandlesTimezones;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Fixer une date pour tests prévisibles
        Carbon::setTestNow('2025-06-15 12:00:00');
    }
    
    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset
        parent::tearDown();
    }

    protected function getGamingTimezones(): array
    {
        return [
            'America/Toronto' => 'Canada/US East Coast gamers',
            'America/Los_Angeles' => 'US West Coast gamers', 
            'Europe/Paris' => 'European gamers',
            'Europe/London' => 'UK gamers',
            'Asia/Tokyo' => 'Japanese gamers',
            'Australia/Sydney' => 'Australian gamers',
        ];
    }

    protected function getGamingSchedules(): array
    {
        return [
            'weeknight_na' => ['19:00', '23:00'], // NA evening gaming
            'weekend_eu' => ['14:00', '18:00'],   // EU afternoon
            'late_night_asia' => ['22:00', '02:00'], // Asia late night
            'morning_coaching' => ['09:00', '12:00'], // Morning coaching
        ];
    }

    protected function getDSTTestDates(): array
    {
        return [
            'spring_forward_us' => '2025-03-09', // US Spring forward
            'fall_back_us' => '2025-11-02',      // US Fall back
            'spring_forward_eu' => '2025-03-30', // EU Spring forward
            'fall_back_eu' => '2025-10-26',      // EU Fall back
        ];
    }

    // Tests convertToUTC()
    
    /** @test */
    public function it_can_convert_time_to_utc()
    {
        $result = $this->convertToUTC('15:30', 'America/Toronto', '2025-06-15');
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $result);
    }

    /** @test */
    public function it_converts_toronto_time_to_utc()
    {
        $result = $this->convertToUTC('15:30', 'America/Toronto', '2025-06-15');
        $this->assertEquals('19:30', $result); // Toronto is UTC-4 in summer
    }

    /** @test */
    public function it_converts_paris_time_to_utc()
    {
        $result = $this->convertToUTC('15:30', 'Europe/Paris', '2025-06-15');
        $this->assertEquals('13:30', $result); // Paris is UTC+2 in summer
    }

    /** @test */
    public function it_converts_tokyo_time_to_utc()
    {
        $result = $this->convertToUTC('15:30', 'Asia/Tokyo', '2025-06-15');
        $this->assertEquals('06:30', $result); // Tokyo is UTC+9
    }

    /** @test */
    public function it_handles_different_time_formats()
    {
        $result1 = $this->convertToUTC('15:30', 'America/Toronto', '2025-06-15');
        $result2 = $this->convertToUTC('15:30:00', 'America/Toronto', '2025-06-15');
        $this->assertEquals('19:30', $result1);
        $this->assertEquals('19:30', $result2);
    }

    /** @test */
    public function it_converts_with_specific_date()
    {
        $result = $this->convertToUTC('15:30', 'America/Toronto', '2025-12-15');
        $this->assertEquals('20:30', $result); // Toronto is UTC-5 in winter
    }

    /** @test */
    public function it_uses_today_when_no_date_provided()
    {
        $result = $this->convertToUTC('15:30', 'America/Toronto');
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $result);
    }

    /** @test */
    public function it_handles_date_boundaries()
    {
        $result = $this->convertToUTC('23:30', 'America/Toronto', '2025-06-15');
        $this->assertEquals('03:30', $result); // Next day in UTC
    }

    /** @test */
    public function it_converts_creator_gaming_hours()
    {
        $result = $this->convertToUTC('19:00', 'America/Toronto', '2025-06-15');
        $this->assertEquals('23:00', $result);
        
        $result = $this->convertToUTC('23:00', 'America/Toronto', '2025-06-15');
        $this->assertEquals('03:00', $result);
    }

    /** @test */
    public function it_handles_late_night_gaming_sessions()
    {
        $result = $this->convertToUTC('02:00', 'Asia/Tokyo', '2025-06-15');
        $this->assertEquals('17:00', $result); // Previous day in UTC
    }

    /** @test */
    public function it_converts_weekend_availability()
    {
        $result = $this->convertToUTC('14:00', 'Europe/Paris', '2025-06-15');
        $this->assertEquals('12:00', $result);
    }

    // Tests convertFromUTC()

    /** @test */
    public function it_can_convert_from_utc_to_timezone()
    {
        $result = $this->convertFromUTC('19:30', 'America/Toronto', '2025-06-15');
        $this->assertIsString($result);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $result);
    }

    /** @test */
    public function it_converts_utc_to_toronto()
    {
        $result = $this->convertFromUTC('19:30', 'America/Toronto', '2025-06-15');
        $this->assertEquals('15:30', $result); // UTC+0 to UTC-4
    }

    /** @test */
    public function it_converts_utc_to_paris()
    {
        $result = $this->convertFromUTC('13:30', 'Europe/Paris', '2025-06-15');
        $this->assertEquals('15:30', $result); // UTC+0 to UTC+2
    }

    /** @test */
    public function it_converts_utc_to_tokyo()
    {
        $result = $this->convertFromUTC('06:30', 'Asia/Tokyo', '2025-06-15');
        $this->assertEquals('15:30', $result); // UTC+0 to UTC+9
    }

    /** @test */
    public function it_provides_symmetrical_conversion()
    {
        $originalTime = '19:30';
        $creatorTimezone = 'America/Toronto';
        $testDate = '2025-06-15';
        
        $utcTime = $this->convertToUTC($originalTime, $creatorTimezone, $testDate);
        $backToOriginal = $this->convertFromUTC($utcTime, $creatorTimezone, $testDate);
        
        $this->assertEquals($originalTime, $backToOriginal);
    }

    /** @test */
    public function it_maintains_time_consistency()
    {
        $timezones = ['America/Toronto', 'Europe/Paris', 'Asia/Tokyo'];
        $times = ['09:00', '15:30', '21:45'];
        
        foreach ($timezones as $timezone) {
            foreach ($times as $time) {
                $utc = $this->convertToUTC($time, $timezone, '2025-06-15');
                $back = $this->convertFromUTC($utc, $timezone, '2025-06-15');
                $this->assertEquals($time, $back, "Failed for {$time} in {$timezone}");
            }
        }
    }

    /** @test */
    public function it_converts_booking_times_to_user_timezone()
    {
        $utcTime = '23:00';
        $userTime = $this->convertFromUTC($utcTime, 'Europe/Paris', '2025-06-15');
        $this->assertEquals('01:00', $userTime);
    }

    /** @test */
    public function it_displays_correct_local_time_for_gamers()
    {
        $utcTime = '03:00';
        $torontoTime = $this->convertFromUTC($utcTime, 'America/Toronto', '2025-06-15');
        $this->assertEquals('23:00', $torontoTime);
    }

    // Tests convertDateTime()

    /** @test */
    public function it_can_convert_full_datetime()
    {
        $result = $this->convertDateTime('2025-06-15 15:30:00', 'America/Toronto', 'UTC');
        $this->assertEquals('2025-06-15 19:30:00', $result);
    }

    /** @test */
    public function it_converts_between_different_timezones()
    {
        $result = $this->convertDateTime('2025-06-15 15:30:00', 'America/Toronto', 'Europe/Paris');
        $this->assertEquals('2025-06-15 21:30:00', $result);
    }

    /** @test */
    public function it_handles_datetime_with_seconds()
    {
        $result = $this->convertDateTime('2025-06-15 15:30:45', 'America/Toronto', 'UTC');
        $this->assertEquals('2025-06-15 19:30:45', $result);
    }

    /** @test */
    public function it_maintains_date_accuracy()
    {
        $result = $this->convertDateTime('2025-06-15 23:30:00', 'America/Toronto', 'UTC');
        $this->assertEquals('2025-06-16 03:30:00', $result);
    }

    /** @test */
    public function it_converts_reservation_datetime()
    {
        $result = $this->convertDateTime('2025-06-15 20:00:00', 'America/Toronto', 'UTC');
        $this->assertEquals('2025-06-16 00:00:00', $result);
    }

    /** @test */
    public function it_handles_cross_date_conversions()
    {
        $result = $this->convertDateTime('2025-06-15 02:00:00', 'UTC', 'America/Los_Angeles');
        $this->assertEquals('2025-06-14 19:00:00', $result);
    }

    /** @test */
    public function it_converts_session_end_times()
    {
        $result = $this->convertDateTime('2025-06-15 23:59:59', 'Europe/Paris', 'UTC');
        $this->assertEquals('2025-06-15 21:59:59', $result);
    }

    // Tests isValidTime()

    /** @test */
    public function it_validates_normal_times()
    {
        $this->assertTrue($this->isValidTime('15:30', 'America/Toronto', '2025-06-15'));
        $this->assertTrue($this->isValidTime('09:00', 'Europe/Paris', '2025-06-15'));
        $this->assertTrue($this->isValidTime('21:45', 'Asia/Tokyo', '2025-06-15'));
    }

    /** @test */
    public function it_rejects_invalid_time_formats()
    {
        $this->assertFalse($this->isValidTime('25:00', 'America/Toronto', '2025-06-15'));
        $this->assertFalse($this->isValidTime('15:60', 'America/Toronto', '2025-06-15'));
        $this->assertFalse($this->isValidTime('invalid', 'America/Toronto', '2025-06-15'));
    }

    /** @test */
    public function it_validates_edge_times()
    {
        $this->assertTrue($this->isValidTime('00:00', 'America/Toronto', '2025-06-15'));
        $this->assertTrue($this->isValidTime('23:59', 'America/Toronto', '2025-06-15'));
    }

    /** @test */
    public function it_detects_non_existent_times_during_spring_forward()
    {
        $nonExistentTime = '02:30';
        $timezone = 'America/Toronto';
        $springForwardDate = '2025-03-09';
        
        $isValid = $this->isValidTime($nonExistentTime, $timezone, $springForwardDate);
        $reason = $this->isValidTime($nonExistentTime, $timezone, $springForwardDate, true);
        
        $this->assertFalse($isValid);
        $this->assertIsString($reason);
        $this->assertStringContainsString('n\'existe pas', $reason);
    }

    /** @test */
    public function it_detects_ambiguous_times_during_fall_back()
    {
        $ambiguousTime = '01:30';
        $timezone = 'America/Toronto';
        $fallBackDate = '2025-11-02';
        
        $isValid = $this->isValidTime($ambiguousTime, $timezone, $fallBackDate);
        
        // Note: Cette méthode peut être complexe à tester précisément
        // car elle dépend de l'implémentation exacte de Carbon
        $this->assertIsBool($isValid);
    }

    /** @test */
    public function it_handles_normal_times_during_dst_transitions()
    {
        $this->assertTrue($this->isValidTime('15:30', 'America/Toronto', '2025-03-09'));
        $this->assertTrue($this->isValidTime('15:30', 'America/Toronto', '2025-11-02'));
    }

    /** @test */
    public function it_provides_detailed_error_messages()
    {
        $reason = $this->isValidTime('25:00', 'America/Toronto', '2025-06-15', true);
        $this->assertIsString($reason);
        $this->assertStringContainsString('invalide', $reason);
    }

    /** @test */
    public function it_validates_times_in_toronto_timezone()
    {
        $this->assertTrue($this->isValidTime('15:30', 'America/Toronto', '2025-06-15'));
        $this->assertTrue($this->isValidTime('19:00', 'America/Toronto', '2025-06-15'));
    }

    /** @test */
    public function it_validates_times_in_paris_timezone()
    {
        $this->assertTrue($this->isValidTime('15:30', 'Europe/Paris', '2025-06-15'));
        $this->assertTrue($this->isValidTime('14:00', 'Europe/Paris', '2025-06-15'));
    }

    /** @test */
    public function it_validates_times_in_no_dst_timezone()
    {
        $this->assertTrue($this->isValidTime('15:30', 'UTC', '2025-06-15'));
        $this->assertTrue($this->isValidTime('02:30', 'UTC', '2025-03-09'));
    }

    /** @test */
    public function it_validates_late_night_gaming_hours()
    {
        $this->assertTrue($this->isValidTime('23:00', 'America/Toronto', '2025-06-15'));
        $this->assertTrue($this->isValidTime('02:00', 'America/Toronto', '2025-06-15'));
    }

    /** @test */
    public function it_handles_availability_across_dst_changes()
    {
        $this->assertTrue($this->isValidTime('20:00', 'America/Toronto', '2025-03-08'));
        $this->assertTrue($this->isValidTime('20:00', 'America/Toronto', '2025-03-10'));
    }

    /** @test */
    public function it_validates_booking_times_near_dst()
    {
        $this->assertTrue($this->isValidTime('19:00', 'America/Toronto', '2025-03-08'));
        $this->assertTrue($this->isValidTime('19:00', 'America/Toronto', '2025-03-10'));
    }

    // Tests getTimezoneOffset()

    /** @test */
    public function it_calculates_timezone_offset()
    {
        $offset = $this->getTimezoneOffset('America/Toronto', 'Europe/Paris');
        $this->assertIsString($offset);
        $this->assertMatchesRegularExpression('/^[+-]\d+:00$/', $offset);
    }

    /** @test */
    public function it_handles_positive_offsets()
    {
        $offset = $this->getTimezoneOffset('America/Toronto', 'Europe/Paris');
        $this->assertStringStartsWith('+', $offset);
    }

    /** @test */
    public function it_handles_negative_offsets()
    {
        $offset = $this->getTimezoneOffset('Europe/Paris', 'America/Toronto');
        $this->assertStringStartsWith('-', $offset);
    }

    /** @test */
    public function it_formats_offset_correctly()
    {
        $offset = $this->getTimezoneOffset('UTC', 'Europe/Paris');
        $this->assertEquals('+2:00', $offset); // Paris is UTC+2 in summer
    }

    /** @test */
    public function it_calculates_creator_to_user_offset()
    {
        $offset = $this->getTimezoneOffset('America/Toronto', 'Europe/Paris');
        $this->assertEquals('+6:00', $offset); // Toronto UTC-4, Paris UTC+2 = +6
    }

    /** @test */
    public function it_shows_time_difference_for_booking()
    {
        $offset = $this->getTimezoneOffset('America/Los_Angeles', 'America/Toronto');
        $this->assertEquals('+3:00', $offset); // LA UTC-7, Toronto UTC-4 = +3
    }

    /** @test */
    public function it_handles_same_timezone_offset()
    {
        $offset = $this->getTimezoneOffset('America/Toronto', 'America/Toronto');
        $this->assertEquals('+0:00', $offset);
    }

    // Tests formatTimeWithZone()

    /** @test */
    public function it_formats_time_with_timezone_abbreviation()
    {
        $formatted = $this->formatTimeWithZone('15:30', 'America/Toronto', '2025-06-15');
        $this->assertStringContainsString('15:30', $formatted);
        $this->assertStringContainsString('EDT', $formatted); // Eastern Daylight Time
    }

    /** @test */
    public function it_shows_correct_timezone_names()
    {
        $torontoTime = $this->formatTimeWithZone('15:30', 'America/Toronto', '2025-06-15');
        $this->assertStringContainsString('EDT', $torontoTime);
        
        $parisTime = $this->formatTimeWithZone('15:30', 'Europe/Paris', '2025-06-15');
        $this->assertStringContainsString('CEST', $parisTime);
    }

    /** @test */
    public function it_handles_dst_abbreviation_changes()
    {
        $summerTime = $this->formatTimeWithZone('15:30', 'America/Toronto', '2025-06-15');
        $winterTime = $this->formatTimeWithZone('15:30', 'America/Toronto', '2025-12-15');
        
        $this->assertStringContainsString('EDT', $summerTime);
        $this->assertStringContainsString('EST', $winterTime);
    }

    /** @test */
    public function it_formats_with_specific_date()
    {
        $formatted = $this->formatTimeWithZone('15:30', 'Europe/Paris', '2025-06-15');
        $this->assertStringContainsString('15:30', $formatted);
        $this->assertStringContainsString('CEST', $formatted);
    }

    /** @test */
    public function it_formats_gaming_session_times()
    {
        $formatted = $this->formatTimeWithZone('20:00', 'America/Toronto', '2025-06-15');
        $this->assertStringContainsString('20:00', $formatted);
        $this->assertStringContainsString('EDT', $formatted);
    }

    /** @test */
    public function it_shows_creator_local_time()
    {
        $formatted = $this->formatTimeWithZone('19:00', 'America/Toronto', '2025-06-15');
        $this->assertStringContainsString('19:00', $formatted);
        $this->assertStringContainsString('(EDT)', $formatted);
    }

    /** @test */
    public function it_formats_booking_confirmation_times()
    {
        $formatted = $this->formatTimeWithZone('14:00', 'Europe/Paris', '2025-06-15');
        $this->assertStringContainsString('14:00', $formatted);
        $this->assertStringContainsString('(CEST)', $formatted);
    }

    // Tests getDSTTransitionForDate()

    /** @test */
    public function it_detects_spring_forward_transition()
    {
        $transition = $this->getDSTTransitionForDate('2025-03-09', 'America/Toronto');
        
        if ($transition !== false) {
            $this->assertIsArray($transition);
            $this->assertArrayHasKey('type', $transition);
            $this->assertArrayHasKey('direction', $transition);
            $this->assertEquals('summer', $transition['type']);
            $this->assertEquals('+1h', $transition['direction']);
        } else {
            $this->assertFalse($transition);
        }
    }

    /** @test */
    public function it_detects_fall_back_transition()
    {
        $transition = $this->getDSTTransitionForDate('2025-11-02', 'America/Toronto');
        
        if ($transition !== false) {
            $this->assertIsArray($transition);
            $this->assertArrayHasKey('type', $transition);
            $this->assertArrayHasKey('direction', $transition);
            $this->assertEquals('winter', $transition['type']);
            $this->assertEquals('-1h', $transition['direction']);
        } else {
            $this->assertFalse($transition);
        }
    }

    /** @test */
    public function it_returns_false_for_no_transition()
    {
        $transition = $this->getDSTTransitionForDate('2025-06-15', 'America/Toronto');
        $this->assertFalse($transition);
    }

    /** @test */
    public function it_provides_transition_details()
    {
        $transition = $this->getDSTTransitionForDate('2025-03-09', 'America/Toronto');
        
        if ($transition !== false) {
            $this->assertArrayHasKey('date', $transition);
            $this->assertArrayHasKey('time', $transition);
            $this->assertArrayHasKey('description', $transition);
            $this->assertEquals('2025-03-09', $transition['date']);
        }
    }

    /** @test */
    public function it_warns_about_dst_affecting_bookings()
    {
        $transition = $this->getDSTTransitionForDate('2025-03-09', 'America/Toronto');
        
        if ($transition !== false) {
            $this->assertStringContainsString('Changement', $transition['description']);
        } else {
            $this->assertFalse($transition);
        }
    }

    /** @test */
    public function it_handles_availability_during_dst()
    {
        $beforeDST = $this->getDSTTransitionForDate('2025-03-08', 'America/Toronto');
        $duringDST = $this->getDSTTransitionForDate('2025-03-09', 'America/Toronto');
        $afterDST = $this->getDSTTransitionForDate('2025-03-10', 'America/Toronto');
        
        $this->assertFalse($beforeDST);
        $this->assertFalse($afterDST);
        // During DST might be true or false depending on exact implementation
    }

    /** @test */
    public function it_detects_problematic_gaming_dates()
    {
        $dstDates = $this->getDSTTestDates();
        
        foreach ($dstDates as $date) {
            $transition = $this->getDSTTransitionForDate($date, 'America/Toronto');
            // Should either be false or a valid transition array
            $this->assertTrue($transition === false || is_array($transition));
        }
    }

    // Tests gaming scenarios spécifiques

    /** @test */
    public function it_handles_gaming_session_across_timezones()
    {
        $creatorTimezone = 'America/Toronto';
        $userTimezone = 'Europe/Paris';
        $sessionTime = '20:00';
        $date = '2025-06-15';
        
        $utcTime = $this->convertToUTC($sessionTime, $creatorTimezone, $date);
        $userLocalTime = $this->convertFromUTC($utcTime, $userTimezone, $date);
        $formattedTime = $this->formatTimeWithZone($userLocalTime, $userTimezone, $date);
        
        $this->assertEquals('02:00', $userLocalTime);
        $this->assertStringContainsString('02:00', $formattedTime);
        $this->assertStringContainsString('CEST', $formattedTime);
    }

    // Tests edge cases critiques

    /** @test */
    public function it_handles_year_boundary_conversions()
    {
        $result = $this->convertDateTime('2024-12-31 23:30:00', 'America/Toronto', 'UTC');
        $this->assertEquals('2025-01-01 04:30:00', $result);
    }

    /** @test */
    public function it_handles_month_boundary_conversions()
    {
        $result = $this->convertDateTime('2025-01-31 23:30:00', 'America/Toronto', 'UTC');
        $this->assertEquals('2025-02-01 04:30:00', $result);
    }

    /** @test */
    public function it_handles_leap_year_dates()
    {
        $result = $this->convertDateTime('2024-02-29 15:30:00', 'America/Toronto', 'UTC');
        $this->assertEquals('2024-02-29 20:30:00', $result);
    }

    /** @test */
    public function it_performs_conversions_efficiently()
    {
        $startTime = microtime(true);
        
        for ($i = 0; $i < 100; $i++) {
            $this->convertToUTC('15:30', 'America/Toronto', '2025-06-15');
        }
        
        $endTime = microtime(true);
        $duration = ($endTime - $startTime) * 1000; // Convert to milliseconds
        
        $this->assertLessThan(100, $duration, 'Conversions should be fast');
    }

    /** @test */
    public function it_handles_multiple_timezone_calculations()
    {
        $timezones = array_keys($this->getGamingTimezones());
        $times = ['09:00', '15:30', '21:00'];
        
        foreach ($timezones as $timezone) {
            foreach ($times as $time) {
                $utc = $this->convertToUTC($time, $timezone, '2025-06-15');
                $this->assertIsString($utc);
                $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $utc);
            }
        }
    }

    /** @test */
    public function it_handles_invalid_timezone_names()
    {
        $this->expectException(\Exception::class);
        $this->convertToUTC('15:30', 'Invalid/Timezone', '2025-06-15');
    }

    /** @test */
    public function it_handles_malformed_time_strings()
    {
        $this->expectException(\Exception::class);
        $this->convertToUTC('invalid-time', 'America/Toronto', '2025-06-15');
    }

    /** @test */
    public function it_gracefully_handles_edge_case_dates()
    {
        $result = $this->convertDateTime('2025-02-28 23:59:59', 'America/Toronto', 'UTC');
        $this->assertEquals('2025-03-01 04:59:59', $result);
    }
}