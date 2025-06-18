<!-- Exemple de template setup créateur avec data-testid pour les tests Cypress -->
<div data-testid="setup-wizard" class="setup-wizard">
    <h1>Configurez votre profil de créateur</h1>
    
    <form method="POST" action="{{ route('creator.setup.store') }}" data-testid="setup-form">
        @csrf
        
        <div>
            <label for="platform-name">Nom de votre plateforme</label>
            <input 
                id="platform-name" 
                name="platform_name" 
                type="text" 
                data-testid="platform-name-input"
                aria-label="Nom de votre plateforme"
                aria-required="true"
                value="{{ old('platform_name') }}"
                class="@error('platform_name') border-red-500 @enderror"
                required 
            />
        </div>
        
        <div>
            <label for="bio">Biographie</label>
            <textarea 
                id="bio" 
                name="bio" 
                data-testid="bio-input"
                aria-label="Biographie"
                aria-required="true"
                class="@error('bio') border-red-500 @enderror"
                required
            >{{ old('bio') }}</textarea>
        </div>
        
        <div>
            <label for="platform-url">URL de votre plateforme</label>
            <input 
                id="platform-url" 
                name="platform_url" 
                type="url" 
                data-testid="platform-url-input"
                aria-label="URL de votre plateforme"
                aria-required="true"
                value="{{ old('platform_url') }}"
                class="@error('platform_url') border-red-500 @enderror"
                required 
            />
        </div>
        
        <div>
            <label for="type">Type de contenu</label>
            <select 
                id="type" 
                name="type" 
                data-testid="type-select"
                aria-label="Type de contenu"
                aria-required="true"
                class="@error('type') border-red-500 @enderror"
                required
            >
                <option value="">Sélectionnez un type</option>
                <option value="Gaming" {{ old('type') == 'Gaming' ? 'selected' : '' }}>Gaming</option>
                <option value="Fitness" {{ old('type') == 'Fitness' ? 'selected' : '' }}>Fitness</option>
                <option value="Education" {{ old('type') == 'Education' ? 'selected' : '' }}>Education</option>
                <option value="Art" {{ old('type') == 'Art' ? 'selected' : '' }}>Art</option>
                <option value="Music" {{ old('type') == 'Music' ? 'selected' : '' }}>Music</option>
                <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Autre</option>
            </select>
        </div>
        
        <div>
            <label for="timezone">Fuseau horaire</label>
            <select 
                id="timezone" 
                name="timezone" 
                data-testid="timezone-select"
                aria-label="Fuseau horaire"
                aria-required="true"
                class="@error('timezone') border-red-500 @enderror"
                required
            >
                <option value="">Sélectionnez un fuseau horaire</option>
                <option value="America/New_York" {{ old('timezone') == 'America/New_York' ? 'selected' : '' }}>Eastern Time (ET)</option>
                <option value="America/Chicago" {{ old('timezone') == 'America/Chicago' ? 'selected' : '' }}>Central Time (CT)</option>
                <option value="America/Denver" {{ old('timezone') == 'America/Denver' ? 'selected' : '' }}>Mountain Time (MT)</option>
                <option value="America/Los_Angeles" {{ old('timezone') == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time (PT)</option>
                <option value="America/Toronto" {{ old('timezone') == 'America/Toronto' ? 'selected' : '' }}>Eastern Time - Toronto</option>
                <option value="Europe/London" {{ old('timezone') == 'Europe/London' ? 'selected' : '' }}>London</option>
                <option value="Europe/Paris" {{ old('timezone') == 'Europe/Paris' ? 'selected' : '' }}>Paris</option>
                <option value="Asia/Tokyo" {{ old('timezone') == 'Asia/Tokyo' ? 'selected' : '' }}>Tokyo</option>
                <option value="Australia/Sydney" {{ old('timezone') == 'Australia/Sydney' ? 'selected' : '' }}>Sydney</option>
            </select>
        </div>
        
        @if ($errors->any())
            <div data-testid="validation-error" class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <button type="submit" data-testid="complete-setup-button">
            Terminer la configuration
        </button>
    </form>
</div>
