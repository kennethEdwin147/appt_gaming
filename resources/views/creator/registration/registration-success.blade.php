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
  <title>{{ config('app.name', 'Laravel') }} - Inscription Réussie</title>
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
          <a href="{{ route('login') }}" class="btn btn-outline-white rounded-pill ms-2">
            {{ __('Connexion') }}
          </a>
        </li>
      </ul>

      <!-- primary -->
      <div class="collapse navbar-collapse" id="navbar" data-bs-parent="#mainNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">{{ __('Connexion') }}</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <section class="bg-black overflow-hidden">
    <div class="py-15 py-xl-20 d-flex flex-column container level-3 min-vh-100">
      <div class="row align-items-center justify-content-center my-auto">
        <div class="col-md-10 col-lg-8 col-xl-6">

          <div class="card">
            <div class="card-header bg-white text-center pb-0">
              <div class="icon icon-lg icon-success mb-3">
                <i class="bi bi-check-circle-fill"></i>
              </div>
              <h5 class="fs-4 mb-1">🎉 {{ __('Compte créateur créé avec succès !') }}</h5>
              <p class="small text-secondary">
                {{ __('Félicitations ! Votre compte créateur a été créé avec succès.') }}
              </p>
            </div>
            <div class="card-body bg-white">
              
              @if (session('success'))
                <div class="alert alert-success mb-4">
                  {{ session('success') }}
                </div>
              @endif

              <div class="text-center mb-4">
                <div class="icon icon-lg icon-primary mb-3">
                  <i class="bi bi-envelope-check"></i>
                </div>
                <h6 class="mb-2">{{ __('Vérifiez votre email') }}</h6>
                <p class="text-muted small">
                  {{ __('Un email de confirmation a été envoyé à votre adresse') }}
                  @if(session('user_email'))
                    <strong>{{ session('user_email') }}</strong>.
                  @else
                    email.
                  @endif
                  <br>
                  {{ __('Cliquez sur le lien dans l\'email pour activer votre compte créateur.') }}
                </p>
              </div>

              <div class="alert alert-info">
                <h6 class="alert-heading">{{ __('Prochaines étapes :') }}</h6>
                <ol class="mb-0 small">
                  <li>{{ __('Vérifiez votre boîte email (et vos spams)') }}</li>
                  <li>{{ __('Cliquez sur le lien de confirmation dans l\'email') }}</li>
                  <li>{{ __('Connectez-vous à votre compte') }}</li>
                </ol>
              </div>

              <div class="d-grid mb-3">
                <a href="mailto:" class="btn btn-lg btn-primary">
                  📧 {{ __('Ouvrir ma boîte email') }}
                </a>
              </div>

              <div class="text-center">
                <p class="small text-muted">
                  {{ __('Vous n\'avez pas reçu l\'email ?') }}
                  <br>
                  <a href="{{ route('login') }}" class="text-decoration-none">
                    {{ __('Se connecter pour renvoyer l\'email') }}
                  </a>
                </p>
              </div>

            </div>
            <div class="card-footer bg-opaque-black inverted text-center">
              <p class="text-secondary small mb-0">
                {{ __('Besoin d\'aide ?') }} 
                <a href="mailto:support@example.com" class="text-white text-decoration-none">{{ __('Contactez le support') }}</a>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <figure class="background background-overlay" style="background-image: url('{{ asset('auth_theme/assets/images/pexels-s33.jpg') }}')">
    </figure>
  </section>

  <!-- javascript -->
  <script src="{{ asset('auth_theme/assets/js/vendor.bundle.js') }}"></script>
  <script src="{{ asset('auth_theme/assets/js/index.bundle.js') }}"></script>

  <script>
    // Message d'encouragement
    console.log('Compte créateur créé avec succès ! Vérifiez votre email pour continuer.');
  </script>

</body>

</html>
