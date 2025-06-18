<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;
use App\Models\creator\Creator;
use Illuminate\Support\Facades\Auth;

class CreatorSetupControllerTest extends TestCase
{
    /** @test */
    public function it_shows_timezone_selection()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => null]);

        // Act
        $response = $this->get('/creator/setup/timezone');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('authentication.creator-setup.timezone');
        $response->assertViewHas('timezones');
    }

    /** @test */
    public function it_can_save_timezone()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => null]);
        $timezoneData = ['timezone' => 'America/Toronto'];

        // Act
        $response = $this->post('/creator/setup/timezone', $timezoneData);

        // Assert
        $this->assertDatabaseHas('creators', [
            'id' => $creator->id,
            'timezone' => 'America/Toronto'
        ]);
        $response->assertRedirect('/creator/setup/profile');
        $response->assertSessionHas('success', 'Fuseau horaire sauvegardé !');
    }

    /** @test */
    public function it_redirects_to_profile_after_timezone()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => null]);

        // Act
        $response = $this->post('/creator/setup/timezone', ['timezone' => 'Europe/Paris']);

        // Assert
        $response->assertRedirect('/creator/setup/profile');
    }

    /** @test */
    public function it_skips_timezone_if_already_set()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);

        // Act
        $response = $this->get('/creator/setup/timezone');

        // Assert
        $response->assertRedirect('/creator/setup/profile');
    }

    /** @test */
    public function it_shows_profile_setup()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);

        // Act
        $response = $this->get('/creator/setup/profile');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('authentication.creator-setup.profile');
        $response->assertViewHas('creator', $creator);
    }

    /** @test */
    public function it_requires_timezone_before_profile()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => null]);

        // Act
        $response = $this->get('/creator/setup/profile');

        // Assert
        $response->assertRedirect('/creator/setup/timezone');
    }

    /** @test */
    public function it_can_save_profile()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);
        $profileData = $this->getCreatorSetupData();

        // Act
        $response = $this->post('/creator/setup/profile', $profileData);

        // Assert
        $this->assertDatabaseHas('creators', [
            'id' => $creator->id,
            'bio' => $profileData['bio'],
            'main_game' => $profileData['main_game'],
            'rank_info' => $profileData['rank_info'],
            'default_hourly_rate' => $profileData['default_hourly_rate'],
        ]);
        $response->assertRedirect('/creator/dashboard');
    }

    /** @test */
    public function it_validates_profile_fields()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);
        $invalidData = [
            'bio' => '', // required
            'default_hourly_rate' => 'invalid', // must be numeric
        ];

        // Act
        $response = $this->post('/creator/setup/profile', $invalidData);

        // Assert
        $response->assertSessionHasErrors(['bio', 'default_hourly_rate']);
    }

    /** @test */
    public function it_marks_setup_as_completed()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);
        $profileData = $this->getCreatorSetupData();

        // Act
        $response = $this->post('/creator/setup/profile', $profileData);

        // Assert
        $creator->refresh();
        $this->assertNotNull($creator->setup_completed_at);
    }

    /** @test */
    public function it_redirects_to_dashboard_after_completion()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);
        $profileData = $this->getCreatorSetupData();

        // Act
        $response = $this->post('/creator/setup/profile', $profileData);

        // Assert
        $response->assertRedirect('/creator/dashboard');
        $response->assertSessionHas('success', 'Profil créateur configuré avec succès !');
    }

    /** @test */
    public function it_requires_authentication()
    {
        // Act
        $response = $this->get('/creator/setup/timezone');

        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function it_requires_creator_role()
    {
        // Arrange
        $customer = $this->actingAsCustomer();

        // Act
        $response = $this->get('/creator/setup/timezone');

        // Assert
        $response->assertStatus(403); // or redirect to appropriate page
    }

    /** @test */
    public function it_validates_timezone_field()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => null]);

        // Act
        $response = $this->post('/creator/setup/timezone', []);

        // Assert
        $response->assertSessionHasErrors(['timezone']);
    }

    /** @test */
    public function it_validates_bio_max_length()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);
        $longBio = str_repeat('a', 501); // exceeds max length of 500

        // Act
        $response = $this->post('/creator/setup/profile', [
            'bio' => $longBio,
            'default_hourly_rate' => 50.00,
        ]);

        // Assert
        $response->assertSessionHasErrors(['bio']);
    }

    /** @test */
    public function it_validates_hourly_rate_range()
    {
        // Arrange
        $creator = $this->actingAsCreator(['timezone' => 'America/Toronto']);

        // Test minimum
        $response = $this->post('/creator/setup/profile', [
            'bio' => 'Test bio',
            'default_hourly_rate' => 2.00, // below minimum of 5
        ]);
        $response->assertSessionHasErrors(['default_hourly_rate']);

        // Test maximum
        $response = $this->post('/creator/setup/profile', [
            'bio' => 'Test bio',
            'default_hourly_rate' => 600.00, // above maximum of 500
        ]);
        $response->assertSessionHasErrors(['default_hourly_rate']);
    }
}