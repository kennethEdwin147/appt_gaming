<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }} - Devenir Créateur</title>
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

    <main class="bg-white ">

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

                        <h1 class="text-3xl text-gray-800  font-bold mb-6">{{ __('Créer un compte Créateur') }}</h1>
                        <!-- Form -->
                        <form method="POST" action="{{ route('register.creator') }}" id="creatorRegisterForm">
                            @csrf
                            <div class="space-y-4">
                                <div class="space-y-4 sm:flex sm:space-y-0 sm:space-x-4">
                                    <div class="sm:w-1/2">
                                        <label class="block text-sm font-medium mb-1" for="first_name">{{ __('Prénom') }} <span class="text-red-500">*</span></label>
                                        <input id="first_name" class="form-input w-full @error('first_name') border-red-500 @enderror" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" />
                                        @error('first_name')
                                            <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="sm:w-1/2">
                                        <label class="block text-sm font-medium mb-1" for="last_name">{{ __('Nom') }} <span class="text-red-500">*</span></label>
                                        <input id="last_name" class="form-input w-full @error('last_name') border-red-500 @enderror" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" />
                                        @error('last_name')
                                            <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="email">{{ __('Adresse e-mail') }} <span class="text-red-500">*</span></label>
                                    <input id="email" class="form-input w-full @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
                                    @error('email')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="password">{{ __('Mot de passe') }} <span class="text-red-500">*</span></label>
                                    <input id="password" class="form-input w-full @error('password') border-red-500 @enderror" type="password" name="password" required autocomplete="new-password" />
                                    @error('password')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="password_confirmation">{{ __('Confirmer le mot de passe') }} <span class="text-red-500">*</span></label>
                                    <input id="password_confirmation" class="form-input w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-6">
                                <div class="mr-1">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" name="newsletter" />
                                        <span class="text-sm ml-2">{{ __('Recevoir les actualités par email.') }}</span>
                                    </label>
                                </div>
                                <button type="submit" class="btn bg-violet-500 hover:bg-violet-600 text-white ml-3 whitespace-nowrap" id="creatorSubmitBtn">
                                    <span id="creatorSubmitText">{{ __('C\'est parti !') }}</span>
                                    <span id="creatorLoadingSpinner" class="hidden">
                                        <svg class="animate-spin w-4 h-4 fill-current shrink-0 mr-1" viewBox="0 0 16 16">
                                            <path d="M8 16a8 8 0 1 1 8-8 7.91 7.91 0 0 1-1.754 5" />
                                        </svg>
                                        Création...
                                    </span>
                                </button>
                            </div>
                        </form>
                        <!-- Footer -->
                        <div class="pt-5 mt-6 border-t border-gray-100 ">
                            <div class="text-sm">
                                {{ __('Tu as dejà un compte ?') }} <a class="font-medium text-violet-500 hover:text-violet-600 " href="{{ route('login') }}">{{ __('Connectes toi') }}</a>
                            </div>
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

    <!-- Hidden trigger element for MicroModal -->
    <a href="#" data-micromodal-trigger="modal-creator-success" class="hidden"></a>

    <!-- Modal de succès pour la création de compte -->
    <div class="modal micromodal-slide" id="modal-creator-success" aria-hidden="true" inert>
        <div class="modal__overlay fixed inset-0 bg-gray-900 bg-opacity-70 z-50" data-micromodal-close>
            <div class="modal__container relative bg-white  rounded shadow-lg max-w-md w-full mx-4 p-6" role="dialog" aria-modal="true" aria-labelledby="modal-creator-success-title">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-500 mb-4">
                        <svg class="w-8 h-8 fill-current" viewBox="0 0 16 16">
                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z"></path>
                        </svg>
                    </div>
                    <h3 id="modal-creator-success-title" class="text-xl font-bold text-gray-800  mb-2">Compte créateur créé !</h3>
                    <div class="text-gray-600  mb-6">
                        🎉 Félicitations ! Votre compte créateur a été créé avec succès.
                        <br>
                        <span class="text-sm">Un email de confirmation a été envoyé.</span>
                        <br>
                        <span class="text-sm">Redirection vers la vérification email...</span>
                    </div>
                    <div class="flex justify-center">
                        <svg class="animate-spin w-5 h-5 fill-current text-violet-500" viewBox="0 0 16 16">
                            <path d="M8 16a8 8 0 1 1 8-8 7.91 7.91 0 0 1-1.754 5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin_theme/js/vendors/alpinejs.min.js') }}" defer></script>
    <script src="{{ asset('admin_theme/js/main.js') }}"></script>
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script type="module">
        // Configuration du token CSRF pour les requêtes AJAX
        document.addEventListener('DOMContentLoaded', function() {
            // Récupérer le token CSRF depuis la balise meta
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Ajouter le token à toutes les requêtes AJAX
            let headers = new Headers();
            headers.append('X-CSRF-TOKEN', token);

            // Pop-up de succès et loading pour créateur
            const form = document.getElementById('creatorRegisterForm');
            const submitBtn = document.getElementById('creatorSubmitBtn');
            const submitText = document.getElementById('creatorSubmitText');
            const loadingSpinner = document.getElementById('creatorLoadingSpinner');

            form.addEventListener('submit', function(e) {
                // Show loading state but allow normal form submission
                submitBtn.disabled = true;
                submitText.classList.add('hidden');
                loadingSpinner.classList.remove('hidden');
                
                // Don't prevent default - let the form submit normally
            });

        });
    </script>
</body>

</html>
