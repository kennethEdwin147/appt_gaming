<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }} - Vérification d'email</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('admin_theme/css/vendors/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin_theme/style.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-inter antialiased bg-gray-100 text-gray-600">

    <script>
        if (localStorage.getItem('sidebar-expanded') == 'true') {
            document.querySelector('body').classList.add('sidebar-expanded');
        } else {
            document.querySelector('body').classList.remove('sidebar-expanded');
        }
    </script>

    <main class="bg-white">

        <div class="relative flex">

            <!-- Content -->
            <div class="w-full md:w-1/2">

                <div class="min-h-[100dvh] h-full flex flex-col after:flex-1">

                    <!-- Header -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                            <!-- Logo -->
                            <a class="block" href="{{ url('/') }}">
                                <svg class="fill-violet-500" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
                                    <path d="M31.956 14.8C31.372 6.92 25.08.628 17.2.044V5.76a9.04 9.04 0 0 0 9.04 9.04h5.716ZM14.8 26.24v5.716C6.92 31.372.63 25.08.044 17.2H5.76a9.04 9.04 0 0 1 9.04 9.04Zm11.44-9.04h5.716c-.584 7.88-6.876 14.172-14.756 14.756V26.24a9.04 9.04 0 0 1 9.04-9.04ZM.044 14.8C.63 6.92 6.92.628 14.8.044V5.76a9.04 9.04 0 0 1-9.04 9.04H.044Z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="max-w-sm mx-auto w-full px-4 py-8">

                        <h1 class="text-3xl text-gray-800 font-bold mb-6">{{ __('Vérifiez votre adresse e-mail') }}</h1>
                        
                        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                            <div class="flex justify-center mb-4">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-violet-100 text-violet-500 mb-4">
                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                        <path d="M16 0c8.837 0 16 7.163 16 16s-7.163 16-16 16S0 24.837 0 16 7.163 0 16 0zm0 2C8.268 2 2 8.268 2 16s6.268 14 14 14 14-6.268 14-14S23.732 2 16 2zm-1.194 7h2.388v2.388h-2.388V9zm0 4.774h2.388v10.048h-2.388V13.774z"></path>
                                    </svg>
                                </div>
                            </div>
                            
                            <p class="text-gray-600 mb-6 text-center">
                                {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer ?') }}
                            </p>
                            
                            @if (session('status') == 'verification-link-sent')
                                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 text-center" data-testid="resend-confirmation">
                                    <div class="flex justify-center mb-2">
                                        <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <p class="font-medium">{{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail.') }}</p>
                                    <p class="text-sm mt-2">{{ __('Veuillez vérifier votre boîte de réception (et éventuellement vos spams).') }}</p>
                                </div>
                            @else
                                <p class="text-gray-600 mb-6 text-center">
                                    {{ __('Si vous n\'avez pas reçu l\'e-mail, nous vous en enverrons volontiers un autre.') }}
                                </p>
                                
                                <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                                    @csrf
                                    <button type="submit" class="btn bg-violet-500 hover:bg-violet-600 text-white w-full" data-testid="resend-button">
                                        {{ __('Renvoyer l\'e-mail de vérification') }}
                                    </button>
                                </form>
                            @endif
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn border-gray-300 hover:border-gray-400 text-gray-600 w-full">
                                    {{ __('Se déconnecter') }}
                                </button>
                            </form>
                        </div>
                        
                        <div class="text-sm text-center text-gray-600">
                            {{ __('Besoin d\'aide ?') }} <a class="font-medium text-violet-500 hover:text-violet-600" href="{{ url('/') }}">{{ __('Contactez-nous') }}</a>
                        </div>

                    </div>

                </div>

            </div>

            <!-- Image -->
            <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2" aria-hidden="true">
                <img class="object-cover object-center w-full h-full" src="{{ asset('admin_theme/images/auth-image.jpg') }}" width="760" height="1024" alt="Authentication image" />
            </div>

        </div>

    </main>

    <script src="{{ asset('admin_theme/js/vendors/alpinejs.min.js') }}" defer></script>
    <script src="{{ asset('admin_theme/js/main.js') }}"></script>
</body>

</html>
