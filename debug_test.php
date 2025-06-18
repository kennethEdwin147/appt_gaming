<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Models\creator\Creator;
use App\Models\availability\Availability;
use App\Services\AvailabilityService;
use Carbon\Carbon;

// Créer un créateur de test
$creator = Creator::create([
    'user_id' => 1, // Assumons qu'il y a un user avec ID 1
    'gaming_pseudo' => 'TestGamer123',
    'bio' => 'Test bio',
    'timezone' => 'America/Toronto',
    'confirmed_at' => now(),
]);

echo "Créateur créé avec ID: " . $creator->id . "\n";

// Créer une availability
$availability = Availability::create([
    'creator_id' => $creator->id,
    'day_of_week' => 'monday',
    'start_time' => '10:00',
    'end_time' => '12:00',
    'is_active' => true,
]);

echo "Availability créée: " . $availability->day_of_week . " " . $availability->start_time . "-" . $availability->end_time . "\n";

// Tester le service
$service = new AvailabilityService();
$startDate = Carbon::parse('next monday');
$endDate = $startDate->copy()->addDays(7);

echo "Dates: " . $startDate->format('Y-m-d (l)') . " to " . $endDate->format('Y-m-d') . "\n";
echo "Day of week recherché: " . strtolower($startDate->format('l')) . "\n";

$slots = $service->generateTimeSlotsForCreator($creator->id, $startDate, $endDate);

echo "Nombre de créneaux générés: " . count($slots) . "\n";

if (count($slots) > 0) {
    echo "Premier créneau: " . $slots[0]->start_time . " - " . $slots[0]->end_time . "\n";
}