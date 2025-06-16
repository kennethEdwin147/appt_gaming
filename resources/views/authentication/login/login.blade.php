<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }} - Sign in</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('admin_theme/css/vendors/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin_theme/style.css') }}" rel="stylesheet">
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

                        <h1 class="text-3xl text-gray-800  font-bold mb-6">{{ __('Welcome back!') }}</h1>

                        @if (session('success'))
                            <div class="bg-green-100 text-green-600 px-3 py-2 rounded mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-100 text-red-600 px-3 py-2 rounded mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="email">{{ __('Email Address') }}</label>
                                    <input id="email" class="form-input w-full @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus />
                                    @error('email')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="password">{{ __('Password') }}</label>
                                    <input id="password" class="form-input w-full @error('password') border-red-500 @enderror" type="password" name="password" required autocomplete="current-password" />
                                    @error('password')
                                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-6">
                                <div class="mr-1">
                                    @if (Route::has('password.request'))
                                        <a class="text-sm underline hover:no-underline" href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                                    @endif
                                </div>
                                <button type="submit" class="btn bg-gray-900 text-gray-100 hover:bg-gray-800    ml-3">{{ __('Sign In') }}</button>
                            </div>
                        </form>
                        <!-- Footer -->
                        <div class="pt-5 mt-6 border-t border-gray-100 ">
                            <div class="text-sm">
                                {{ __('Don\'t you have an account?') }} <a class="font-medium text-violet-500 hover:text-violet-600 " href="{{ route('choose-role') }}">{{ __('Sign Up') }}</a>
                            </div>
                            <!-- Warning -->
                            <div class="mt-5">
                                <div class="bg-yellow-500/20 text-yellow-700 px-3 py-2 rounded-lg">
                                    <svg class="inline w-3 h-3 shrink-0 fill-current" viewBox="0 0 12 12">
                                        <path d="M10.28 1.28L3.989 7.575 1.695 5.28A1 1 0 00.28 6.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 1.28z" />
                                    </svg>
                                    <span class="text-sm">
                                        {{ __('To support you during the pandemic super pro features are free until March 31st.') }}
                                    </span>
                                </div>
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

    <script src="{{ asset('admin_theme/js/vendors/alpinejs.min.js') }}" defer></script>
    <script src="{{ asset('admin_theme/js/main.js') }}"></script>

</body>

</html>
