<!-- Exemple de template login avec data-testid pour les tests Cypress -->
<form method="POST" action="{{ route('login') }}" data-testid="login-form">
    @csrf
    
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
        <input 
            type="checkbox" 
            name="remember" 
            id="remember"
            data-testid="remember-checkbox"
        />
        <label for="remember">Se souvenir de moi</label>
    </div>
    
    @if ($errors->any())
        <div data-testid="validation-error" class="error">
            {{ $errors->first() }}
        </div>
    @endif
    
    <button type="submit" data-testid="submit-button">
        Se connecter
    </button>
    
    <a href="{{ route('register') }}" data-testid="register-link">
        Créer un compte
    </a>
    
    <a href="{{ route('password.request') }}" data-testid="forgot-password-link">
        Mot de passe oublié ?
    </a>
</form>
