<!-- Exemple de template register avec data-testid pour les tests Cypress -->
<form method="POST" action="{{ route('register') }}" data-testid="register-form">
    @csrf
    
    <!-- Sélection du rôle si nécessaire -->
    <div data-testid="role-selector">
        <div>
            <input 
                type="radio" 
                name="role" 
                id="role-customer" 
                value="customer"
                data-testid="role-customer"
                checked
            />
            <label for="role-customer">Client</label>
        </div>
        
        <div>
            <input 
                type="radio" 
                name="role" 
                id="role-creator" 
                value="creator"
                data-testid="role-creator"
            />
            <label for="role-creator">Créateur</label>
        </div>
    </div>
    
    <div>
        <label for="name">Nom</label>
        <input 
            id="name" 
            name="name" 
            type="text" 
            data-testid="name-input"
            aria-label="Nom complet"
            aria-required="true"
            value="{{ old('name') }}"
            class="@error('name') border-red-500 @enderror"
            required 
        />
    </div>
    
    <div>
        <label for="email">Email</label>
        <input 
            id="email" 
            name="email" 
            type="email" 
            data-testid="email-input"
            aria-label="Adresse email"
            aria-required="true"
            value="{{ old('email') }}"
            class="@error('email') border-red-500 @enderror"
            required 
        />
    </div>
    
    <div>
        <label for="password">Mot de passe</label>
        <input 
            id="password" 
            name="password" 
            type="password" 
            data-testid="password-input"
            aria-label="Mot de passe"
            aria-required="true"
            required 
        />
    </div>
    
    <div>
        <label for="password-confirmation">Confirmer le mot de passe</label>
        <input 
            id="password-confirmation" 
            name="password_confirmation" 
            type="password" 
            data-testid="password-confirmation-input"
            aria-label="Confirmer le mot de passe"
            aria-required="true"
            required 
        />
    </div>
    
    @if ($errors->any())
        <div data-testid="validation-error" class="error">
            {{ $errors->first() }}
        </div>
    @endif
    
    <button type="submit" data-testid="submit-button">
        S'inscrire
    </button>
    
    <div>
        <a href="{{ route('login') }}" data-testid="login-link">
            Déjà inscrit ? Se connecter
        </a>
    </div>
</form>
