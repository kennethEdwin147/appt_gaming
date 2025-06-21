
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Mosaic HTML Demo - Onboarding 02</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="{{ asset('admin_theme/css/vendors/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin_theme/style.css') }}" rel="stylesheet">
</head>

<body class="font-inter antialiased bg-gray-100 text-gray-600">


    <main class="bg-white">

        <div class="relative flex">

            <!-- Content -->
            <div class="w-full md:w-1/2">

                <div class="min-h-[100dvh] h-full flex flex-col after:flex-1">

                    <div class="flex-1">

                        <!-- Header -->
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                            <!-- Logo -->
                             <div class="flex-1 max-w-sm mx-auto  w-full ">
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                            <!-- Logo -->
                            <a class="block" href="{{ url('/') }}">
                                <img src="{{ asset('images\logo\logo-dark.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                            <div class="text-sm">
                                Have an account? <a class="font-medium text-violet-500 hover:text-violet-600" data-testid="login-link" href="{{ route('login') }}">Sign In</a>
                            </div>
                        </div>

                        <!-- Progress indicator -->
                        <div class="px-4 pt-12 pb-8">
                            <div class="max-w-md mx-auto w-full">
                                <div class="relative">

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-4 py-8">
                        <div class="max-w-md mx-auto">
    
                            <h1 class="text-3xl text-gray-800 font-bold mb-6">Choisissez votre rôle</h1>
                            <!-- Form -->
                            <div data-testid="role-selector">
                                <form method="POST" action="{{ route('process-role-choice') }}">
                                    @csrf
                                    <div class="sm:flex space-y-3 sm:space-y-0 sm:space-x-4 mb-8">
                                        <label class="flex-1 relative block cursor-pointer" data-testid="role-creator">
                                            <input type="radio" name="role" value="creator" class="peer sr-only" checked />
                                            <div class="h-full text-center bg-white px-4 py-6 rounded-lg border border-gray-200 hover:border-gray-300 shadow-xs transition">
                                                <svg class="inline-flex fill-current text-violet-500 mt-2 mb-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10a9.955 9.955 0 0 1-2.003 6.005 2 2 0 0 0-1.382-1.115l-3.293-.732-.295-1.178A4.992 4.992 0 0 0 17 11v-1a5 5 0 0 0-10 0v1c0 1.626.776 3.07 1.977 3.983l-.294 1.175-3.293.732a1.999 1.999 0 0 0-1.384 1.119A9.956 9.956 0 0 1 2 12Zm3.61 7.693A9.96 9.96 0 0 0 12 22c2.431 0 4.66-.868 6.393-2.31l-.212-.847-4.5-1-.496-1.984a5.016 5.016 0 0 1-2.365 0l-.496 1.983-4.5 1-.213.85ZM12 7a3 3 0 0 0-3 3v1a3 3 0 1 0 6 0v-1a3 3 0 0 0-3-3Z" fill-rule="evenodd" />
                                                </svg>
                                                <div class="font-semibold text-gray-800 mb-1">Créateur</div>
                                                <div class="text-sm">Je veux offrir mes services et gérer mes événements.</div>
                                            </div>
                                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-violet-400 rounded-lg pointer-events-none" aria-hidden="true"></div>
                                        </label>
                                        <label class="flex-1 relative block cursor-pointer" data-testid="role-customer">
                                            <input type="radio" name="role" value="client" class="peer sr-only" />
                                            <div class="h-full text-center bg-white px-4 py-6 rounded-lg border border-gray-200 hover:border-gray-300 shadow-xs transition">
                                                <svg class="inline-flex fill-current text-violet-500 mt-2 mb-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                    <path d="M13 22V11a3 3 0 0 1 3-3h5a3 3 0 0 1 3 3v13H0V14a3 3 0 0 1 3-3h5a3 3 0 0 1 3 3v8h2Zm6-15h-2V3a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7H5V3a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v4ZM9 22v-8a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v8h7Zm13 0V11a1 1 0 0 0-1-1h-5a1 1 0 0 0-1 1v11h7Zm-5-8v-2h3v2h-3Zm0 3v-2h3v2h-3Zm0 3v-2h3v2h-3ZM4 20v-2h3v2H4Zm0-3v-2h3v2H4Z"/>
                                                </svg>
                                                <div class="font-semibold text-gray-800 mb-1">Client</div>
                                                <div class="text-sm">Je veux réserver des événements et services.</div>
                                            </div>
                                            <div class="absolute inset-0 border-2 border-transparent peer-checked:border-violet-400 rounded-lg pointer-events-none" aria-hidden="true"></div>
                                        </label>
                                    </div>
                                <div class="flex items-center justify-between space-x-6 mb-8">
                                    <div>
                                        <div class="font-medium text-gray-800 text-sm mb-1">💸 Lorem ipsum is place text commonly?</div>
                                        <div class="text-xs">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.</div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="form-switch">
                                            <input type="checkbox" id="switch" class="sr-only" checked />
                                            <label for="switch">
                                                <span class="bg-white shadow-xs" aria-hidden="true"></span>
                                                <span class="sr-only">Switch label</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                    <div class="flex items-center justify-between">
                                        <a class="text-sm underline hover:no-underline" href="{{ url('/') }}">&lt;- Retour</a>
                                        <button type="submit" data-testid="submit-button" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 ml-auto">Continuer -&gt;</button>
                                    </div>
                                </form>
                            </div>
    
                        </div>
                    </div>

                </div>

            </div>

            <!-- Image -->
            <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2" aria-hidden="true">
                <img class="object-cover object-center w-full h-full" src="{{ asset('images/login-pexels.jpg') }}" width="760" height="1024" alt="Onboarding" />
            </div>

        </div>

    </main>

    <script src="{{ asset('admin_theme/js/vendors/alpinejs.min.js') }}" defer></script>
    <script src="{{ asset('admin_theme/js/main.js') }}"></script>

</body>

</html>