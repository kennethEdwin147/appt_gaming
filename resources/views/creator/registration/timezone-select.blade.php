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
  <title>{{ config('app.name', 'Laravel') }} - Sélection du fuseau horaire</title>
</head>

<body>

  <!-- navbar -->
  <nav id="mainNav" class="navbar navbar-expand-lg navbar-sticky navbar-dark">
    <div class="container">
      <a href="{{ url('/') }}" class="navbar-brand"><img src="{{ asset('auth_theme/assets/images/logo/logo-light.svg') }}" alt="Logo"></a>
    </div>
  </nav>

  <section class="bg-black overflow-hidden">
    <div class="py-15 py-xl-20 d-flex flex-column container level-3 min-vh-100">
      <div class="row align-items-center justify-content-center my-auto">
        <div class="col-md-10 col-lg-8 col-xl-6">

          <div class="card">
            <div class="card-header bg-white text-center pb-0">
              <h5 class="fs-4">{{ __('Sélectionnez votre fuseau horaire') }}</h5>
              <p class="lead fs-8 mb-4">{{ __('Pour vous offrir la meilleure expérience, nous avons besoin de connaître votre fuseau horaire.') }}</p>
            </div>
            <div class="card-body bg-white">
              @if (session('success'))
                <div class="alert alert-success" role="alert">
                  {{ session('success') }}
                </div>
              @endif

              <form method="POST" action="{{ route('creator.timezone.save', ['user_id' => $user->id]) }}">
                @csrf
                <div class="form-floating mb-3">
                  <select id="timezone" class="form-select @error('timezone') is-invalid @enderror" name="timezone" required>
                    <option value="" disabled selected>{{ __('Sélectionnez votre fuseau horaire') }}</option>
                    @foreach(\App\Enums\Timezone::getTimezonesByRegion() as $region => $timezones)
                      <optgroup label="{{ $region }}">
                        @foreach($timezones as $value => $label)
                          <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                      </optgroup>
                    @endforeach
                  </select>
                  <label for="timezone">{{ __('Fuseau horaire') }}</label>
                  @error('timezone')
                    <span class="invalid-feedback" role="alert">
                      <strong>{{ $message }}</strong>
                    </span>
                  @enderror
                </div>
                <div class="d-grid mb-3">
                  <button type="submit" class="btn btn-lg btn-primary">{{ __('Continuer') }}</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <figure class="background background-overlay" style="background-color: #9a6496 ">
    </figure>
  </section>

  <!-- javascript -->
  <script src="{{ asset('auth_theme/assets/js/vendor.bundle.js') }}"></script>
  <script src="{{ asset('auth_theme/assets/js/index.bundle.js') }}"></script>
  <script src="{{ asset('js/timezone-helper.js') }}"></script>
  <script>
    // Configuration du token CSRF pour les requêtes AJAX
    document.addEventListener('DOMContentLoaded', function() {
      // Récupérer le token CSRF depuis la balise meta
      let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

      // Ajouter le token à toutes les requêtes AJAX
      let headers = new Headers();
      headers.append('X-CSRF-TOKEN', token);
    });
  </script>
</body>

</html>