<?php

namespace App\Models\PublicProfile;

use App\Models\creator\Creator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'creator_id',
        'slug',
    ];

    /**
     * Get the creator that owns the profile.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    /**
     * Scope for profiles with specific slug.
     */
    public function scopeBySlug($query, string $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope for profiles with active creators.
     */
    public function scopeActiveCreators($query)
    {
        return $query->whereHas('creator', function($q) {
            $q->active();
        });
    }

    /**
     * Scope for profiles with confirmed creators.
     */
    public function scopeConfirmedCreators($query)
    {
        return $query->whereHas('creator', function($q) {
            $q->confirmed();
        });
    }

    /**
     * Get the public URL for this profile.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('public.creator.profile', ['slug' => $this->slug]);
    }

    /**
     * Get creator's display name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->creator->display_name;
    }

    /**
     * Get creator's bio.
     */
    public function getBioAttribute(): ?string
    {
        return $this->creator->bio;
    }

    /**
     * Get creator's platform name.
     */
    public function getPlatformNameAttribute(): ?string
    {
        return $this->creator->platform_name;
    }

    /**
     * Get creator's platform URL.
     */
    public function getPlatformUrlAttribute(): ?string
    {
        return $this->creator->platform_url;
    }

    /**
     * Get creator's type.
     */
    public function getCreatorTypeAttribute(): ?string
    {
        return $this->creator->type;
    }

    /**
     * Get creator's timezone.
     */
    public function getTimezoneAttribute(): ?string
    {
        return $this->creator->timezone;
    }

    /**
     * Check if creator is available for booking.
     */
    public function isAvailableForBooking(): bool
    {
        return $this->creator->isConfirmed() && 
               $this->creator->eventTypes()->active()->exists();
    }

    /**
     * Get active event types for this creator.
     */
    public function getActiveEventTypes()
    {
        return $this->creator->eventTypes()->active()->get();
    }

    /**
     * Get creator's average rating.
     */
    public function getAverageRating(): ?float
    {
        // This would require a rating system
        return null;
    }

    /**
     * Get total completed sessions.
     */
    public function getTotalCompletedSessions(): int
    {
        return $this->creator->reservations()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get creator's total earnings (public safe version).
     */
    public function getPublicEarningsInfo(): array
    {
        $completedReservations = $this->creator->reservations()
            ->where('status', 'completed')
            ->count();
            
        return [
            'total_sessions' => $completedReservations,
            'experience_level' => $this->getExperienceLevel($completedReservations),
        ];
    }

    /**
     * Get experience level based on completed sessions.
     */
    private function getExperienceLevel(int $sessions): string
    {
        return match(true) {
            $sessions >= 100 => 'Expert',
            $sessions >= 50 => 'Expérimenté',
            $sessions >= 20 => 'Confirmé',
            $sessions >= 5 => 'Débutant confirmé',
            default => 'Nouveau',
        };
    }

    /**
     * Check if slug is available.
     */
    public static function isSlugAvailable(string $slug, ?int $excludeId = null): bool
    {
        $query = static::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return !$query->exists();
    }

    /**
     * Generate a unique slug from creator's name.
     */
    public static function generateSlugFromName(string $name, ?int $excludeId = null): string
    {
        $baseSlug = \Illuminate\Support\Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;
        
        while (!static::isSlugAvailable($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Get SEO meta tags for this profile.
     */
    public function getSeoMetaTags(): array
    {
        $creator = $this->creator;
        
        return [
            'title' => $creator->display_name . ' - Réservez une session',
            'description' => $creator->bio ? 
                \Illuminate\Support\Str::limit(strip_tags($creator->bio), 160) : 
                'Réservez une session avec ' . $creator->display_name,
            'keywords' => implode(', ', [
                $creator->display_name,
                $creator->type,
                'gaming',
                'coaching',
                'session',
                'réservation'
            ]),
            'canonical' => $this->public_url,
        ];
    }

    /**
     * Get structured data for this profile (JSON-LD).
     */
    public function getStructuredData(): array
    {
        $creator = $this->creator;
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $creator->display_name,
            'description' => $creator->bio,
            'url' => $this->public_url,
            'sameAs' => $creator->platform_url ? [$creator->platform_url] : [],
            'offers' => $this->getActiveEventTypes()->map(function($eventType) {
                return [
                    '@type' => 'Offer',
                    'name' => $eventType->name,
                    'description' => $eventType->description,
                    'price' => $eventType->default_price,
                    'priceCurrency' => 'CAD',
                ];
            })->toArray(),
        ];
    }
}