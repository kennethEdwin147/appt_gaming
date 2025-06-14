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
  <title>{{ config('app.name', 'Laravel') }} - {{ __('Vérification d\'email') }}</title>
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
          {{-- TODO: Remettre quand le module Creator sera créé --}}
          {{-- <li class="nav-item">
            <a class="nav-link" href="{{ route('register.creator.form') }}">{{ __('Devenir Créateur') }}</a>
          </li> --}}
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
              <div class="icon icon-lg icon-primary mb-3">
                <i class="bi bi-envelope-check"></i>
              </div>
              <h5 class="fs-4 mb-1">{{ __('Vérifiez votre adresse e-mail') }}</h5>
              <p class="small text-secondary">
                {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ?') }}
                {{ __('Si vous n\'avez pas reçu l\'e-mail, nous vous en enverrons volontiers un autre.') }}
              </p>
            </div>
            <div class="card-body bg-white">
              @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success mb-4">
                  {{ __('Un nouveau lien de vérification a été envoyé à l\'adresse e-mail que vous avez fournie lors de votre inscription.') }}
                </div>
              @endif

              <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <div class="d-grid mb-3">
                  @if(auth()->user() && auth()->user()->role === 'creator')
                    <button type="submit" class="btn btn-lg btn-primary">
                      📧 {{ __('Renvoyer l\'email de confirmation créateur') }}
                    </button>
                  @else
                    <button type="submit" class="btn btn-lg btn-primary">{{ __('Renvoyer l\'e-mail de vérification') }}</button>
                  @endif
                </div>
              </form>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <div class="d-grid">
                  <button type="submit" class="btn btn-lg btn-outline-secondary">{{ __('Se déconnecter') }}</button>
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
