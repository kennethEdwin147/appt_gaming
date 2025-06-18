<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Laravel') }}</title>
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

    <!-- Header -->
    <header class="sticky top-0 bg-white border-b border-gray-200 z-30">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 -mb-px">
                <!-- Logo -->
                <a class="block" href="{{ url('/') }}">
                    <svg class="fill-violet-500" xmlns="http://www.w3.org/2000/svg" width="32" height="32">
                        <path d="M31.956 14.8C31.372 6.92 25.08.628 17.2.044V5.76a9.04 9.04 0 0 0 9.04 9.04h5.716ZM14.8 26.24v5.716C6.92 31.372.63 25.08.044 17.2H5.76a9.04 9.04 0 0 1 9.04 9.04Zm11.44-9.04h5.716c-.584 7.88-6.876 14.172-14.756 14.756V26.24a9.04 9.04 0 0 1 9.04-9.04ZM.044 14.8C.63 6.92 6.92.628 14.8.044V5.76a9.04 9.04 0 0 1-9.04 9.04H.044Z" />
                    </svg>
                </a>

                <!-- User menu -->
                <div class="flex items-center">
                    @auth
                        <div class="relative inline-flex" x-data="{ open: false }">
                            <button
                                class="inline-flex justify-center items-center group"
                                aria-haspopup="true"
                                @click.prevent="open = !open"
                                :aria-expanded="open"
                            >
                                <div class="flex items-center truncate">
                                    <span class="truncate ml-2 text-sm font-medium group-hover:text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                                    <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400" viewBox="0 0 12 12">
                                        <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                    </svg>
                                </div>
                            </button>
                            <div
                                class="origin-top-right z-10 absolute top-full right-0 min-w-44 bg-white border border-gray-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"
                                @click.outside="open = false"
                                @keydown.escape.window="open = false"
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200 transform"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-out duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                x-cloak
                            >
                                <div class="pt-0.5 pb-2 px-3 mb-1 border-b border-gray-200">
                                    <div class="font-medium text-gray-800">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                                    <div class="text-xs text-gray-500 italic">{{ auth()->user()->email }}</div>
                                </div>
                                <ul>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="font-medium text-sm text-indigo-500 hover:text-indigo-600 flex items-center py-1 px-3">Se déconnecter</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                            <span>Se connecter</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main content -->
    <main>
        @yield('content')
    </main>

    <script src="{{ asset('admin_theme/js/vendors/alpinejs.min.js') }}" defer></script>
    <script src="{{ asset('admin_theme/js/main.js') }}"></script>
</body>

</html>
