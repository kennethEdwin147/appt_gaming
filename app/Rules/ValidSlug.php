<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidSlug implements ValidationRule
{
    protected $excludeId;

    public function __construct($excludeId = null)
    {
        $this->excludeId = $excludeId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifier la longueur
        if (strlen($value) < 3 || strlen($value) > 50) {
            $fail('L\'URL publique doit contenir entre 3 et 50 caractères.');
            return;
        }

        // Vérifier le format (lettres minuscules, chiffres, tirets)
        if (!preg_match('/^[a-z0-9\-]+$/', $value)) {
            $fail('L\'URL publique ne peut contenir que des lettres minuscules, des chiffres et des tirets.');
            return;
        }

        // Vérifier qu'elle n'est pas uniquement numérique
        if (preg_match('/^\d+$/', $value)) {
            $fail('L\'URL publique ne peut pas être uniquement numérique.');
            return;
        }

        // Vérifier qu'elle ne commence/finit pas par un tiret
        if (str_starts_with($value, '-') || str_ends_with($value, '-')) {
            $fail('L\'URL publique ne peut pas commencer ou finir par un tiret.');
            return;
        }

        // Vérifier qu'elle ne contient pas de tirets consécutifs
        if (str_contains($value, '--')) {
            $fail('L\'URL publique ne peut pas contenir de tirets consécutifs.');
            return;
        }

        // Liste des mots réservés
        $reservedWords = [
            'admin', 'api', 'www', 'mail', 'ftp', 'blog', 'shop', 'store', 'app',
            'test', 'dev', 'staging', 'prod', 'production', 'creator', 'user',
            'dashboard', 'login', 'register', 'logout', 'home', 'about', 'contact',
            'help', 'support', 'terms', 'privacy', 'policy', 'legal', 'docs',
            'documentation', 'public', 'private', 'static', 'assets', 'images',
            'css', 'js', 'fonts', 'uploads', 'downloads', 'files', 'media',
            'video', 'audio', 'stream', 'live', 'chat', 'forum', 'community',
            'news', 'events', 'calendar', 'booking', 'reservation', 'payment',
            'billing', 'account', 'profile', 'settings', 'config', 'configuration',
            'preferences', 'notifications', 'messages', 'inbox', 'outbox', 'sent',
            'draft', 'trash', 'spam', 'archive', 'search', 'filter', 'sort',
            'export', 'import', 'backup', 'restore', 'sync', 'update', 'upgrade',
            'install', 'uninstall', 'delete', 'remove', 'create', 'add', 'edit',
            'modify', 'change', 'save', 'cancel', 'submit', 'send', 'receive',
            'get', 'post', 'put', 'patch', 'head', 'options', 'connect', 'trace'
        ];

        if (in_array(strtolower($value), $reservedWords)) {
            $fail('Cette URL publique est réservée et ne peut pas être utilisée.');
            return;
        }

        // Vérifier l'unicité
        $query = \App\Models\CreatorProfile::where('slug', $value);
        if ($this->excludeId) {
            $query->where('id', '!=', $this->excludeId);
        }

        if ($query->exists()) {
            $fail('Cette URL publique est déjà utilisée par un autre créateur.');
            return;
        }
    }

    /**
     * Suggestions de slugs valides basées sur le nom
     */
    public static function generateSuggestions(string $name): array
    {
        $base = strtolower($name);
        $base = preg_replace('/[^a-z0-9\s]/', '', $base);
        $base = preg_replace('/\s+/', '-', trim($base));
        $base = substr($base, 0, 30); // Limiter à 30 caractères

        $suggestions = [];
        
        // Suggestion de base
        if (self::isValidSlug($base)) {
            $suggestions[] = $base;
        }

        // Avec suffixes
        $suffixes = ['pro', 'gaming', 'stream', '2024', 'official'];
        foreach ($suffixes as $suffix) {
            $suggestion = $base . '-' . $suffix;
            if (strlen($suggestion) <= 50 && self::isValidSlug($suggestion)) {
                $suggestions[] = $suggestion;
            }
        }

        return array_slice($suggestions, 0, 3); // Max 3 suggestions
    }

    private static function isValidSlug(string $slug): bool
    {
        return preg_match('/^[a-z0-9\-]+$/', $slug) && 
               !preg_match('/^\d+$/', $slug) &&
               strlen($slug) >= 3 && 
               strlen($slug) <= 50;
    }
}
