<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">

  <!-- Libs CSS -->
  <link rel="stylesheet" href="{{ asset('auth_theme/assets/css/libs.bundle.css') }}" />

  <!-- Main CSS -->
  <link rel="stylesheet" href="{{ asset('auth_theme/assets/css/index.bundle.css') }}" />

  <!-- Title -->
  <title>{{ config('app.name', 'Laravel') }} - {{ __('Réinitialisation du mot de passe') }}</title>
</head>

<body>

  <!-- navbar -->
  <nav id="mainNav" class="navbar navbar-expand-lg navbar-sticky navbar-dark">
    <div class="container">
      <a href="{{ url('/') }}" class="navbar-brand"><img src="{{ asset('auth_theme/assets/images/logo/logo-light.svg') }}" alt="Logo"></a>

      <!-- secondary -->
      <ul class="navbar-nav navbar-nav-secondary order-lg-3">
        <li class="nav-item d-lg-none">
          <a class="nav-link nav-icon" href="#" role="button" data-bs-toggle="collapse" data-bs-target="#navbar"
            aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="bi bi-list"></span>
          </a>
        </li>
        <li class="nav-item d-none d-lg-block">
          <a href="{{ route('register') }}" class="btn btn-outline-white rounded-pill ms-2">
            {{ __('Inscription') }}
          </a>
        </li>
      </ul>

      <!-- primary -->
      <div class="collapse navbar-collapse" id="navbar" data-bs-parent="#mainNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route('register.creator.form') }}">{{ __('Devenir Créateur') }}</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="bg-black overflow-hidden">
    <div class="py-15 py-xl-20 d-flex flex-column container level-3 min-vh-100">
      <div class="row align-items-center justify-content-center my-auto">
        <div class="col-md-10 col-lg-8 col-xl-5">

          <div class="card">
            <div class="card-header bg-white text-center pb-0">
              <h5 class="fs-4 mb-1">{{ __('Réinitialisation du mot de passe') }}</h5>
              <p class="small text-secondary">{{ __('Veuillez entrer votre nouveau mot de passe.') }}</p>
            </div>
            <div class="card-body bg-white">
              <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-floating mb-3">
                  <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" placeholder="nom@exemple.com" required autocomplete="email" autofocus readonly>
                  <label for="email">{{ __('Adresse e-mail') }}</label>
                  @error('email')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>

                <div class="form-floating mb-3">
                  <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="••••••••" required autocomplete="new-password">
                  <label for="password">{{ __('Nouveau mot de passe') }}</label>
                  @error('password')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>

                <div class="form-floating mb-3">
                  <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="••••••••" required autocomplete="new-password">
                  <label for="password-confirm">{{ __('Confirmer le mot de passe') }}</label>
                </div>

                <div class="d-grid mb-2">
                  <button type="submit" class="btn btn-lg btn-primary">{{ __('Réinitialiser le mot de passe') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <figure class="background background-overlay" style="background-color: #9a6496"></figure>
  </section>

  <!-- javascript -->
  <script src="{{ asset('auth_theme/assets/js/vendor.bundle.js') }}"></script>
  <script src="{{ asset('auth_theme/assets/js/index.bundle.js') }}"></script>
</body>

</html>