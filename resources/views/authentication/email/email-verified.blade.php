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
  <title>{{ config('app.name', 'Laravel') }} - {{ __('Email vérifié') }}</title>
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
              <div class="icon icon-lg icon-green mb-3">
                <i class="bi bi-check-circle"></i>
              </div>
              <h5 class="fs-4 mb-1">{{ __('Email vérifié avec succès !') }}</h5>
              <p class="small text-secondary">{{ __('Votre adresse e-mail a été vérifiée avec succès. Vous pouvez maintenant accéder à toutes les fonctionnalités de notre plateforme.') }}</p>
            </div>
            <div class="card-body bg-white text-center">
              <div class="d-grid mb-2">
                @if(auth()->user() && auth()->user()->role === 'creator')
                  <a href="{{ route('creator.dashboard') }}" class="btn btn-lg btn-primary">{{ __('Accéder à mon espace créateur') }}</a>
                @else
                  <a href="{{ route('home') }}" class="btn btn-lg btn-primary">{{ __('Accéder à mon compte') }}</a>
                @endif
              </div>
            </div>
            <div class="card-footer bg-opaque-black inverted text-center">
              <p class="text-secondary">
                {{ __('Besoin d\'aide ?') }} <a href="{{ url('/contact') }}" class="underline">{{ __('Contactez-nous') }}</a>
              </p>
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