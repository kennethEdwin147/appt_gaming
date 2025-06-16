<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Mosaic HTML Demo - My Account</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="{{ asset('admin_theme/css/vendors/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin_theme/css/style.css') }}" rel="stylesheet">
    <script>
        if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
            document.querySelector('html').classList.remove('dark');
            document.querySelector('html').style.colorScheme = 'light';
        } else {
            document.querySelector('html').classList.add('dark');
            document.querySelector('html').style.colorScheme = 'dark';
        }
    </script>
</head>

<body
    class="font-inter antialiased bg-gray-100 dark:bg-gray-900 text-gray-600 dark:text-gray-400"
    :class="{ 'sidebar-expanded': sidebarExpanded }"
    x-data="{ sidebarOpen: false, sidebarExpanded: localStorage.getItem('sidebar-expanded') == 'true' }"
    x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebar-expanded', value))"
>

    <script>
        if (localStorage.getItem('sidebar-expanded') == 'true') {
            document.querySelector('body').classList.add('sidebar-expanded');
        } else {
            document.querySelector('body').classList.remove('sidebar-expanded');
        }
    </script>

    <!-- Page wrapper -->
    <div class="flex h-[100dvh] overflow-hidden">

        @include('creator::partials.sidebar')

        <!-- Content area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">

            @include('creator::partials.header')

            <main class="grow">
                <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-[96rem] mx-auto">

                    <!-- Page header -->
                    <div class="mb-8">

                        <!-- Title -->
                        <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Account Settings</h1>

                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-xs rounded-xl mb-8">
                        <div class="flex flex-col md:flex-row md:-mr-px">

                            <!-- Sidebar -->
                            <div class="flex flex-nowrap overflow-x-scroll no-scrollbar md:block md:overflow-auto px-3 py-6 border-b md:border-b-0 md:border-r border-gray-200 dark:border-gray-700/60 min-w-60 md:space-y-3">
                                <!-- Group 1 -->
                                <div>
                                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-3">Business settings</div>
                                    <ul class="flex flex-nowrap md:block mr-3 md:mr-0">
                                        <li class="mr-0.5 md:mr-0 md:mb-0.5">
                                            <a class="flex items-center px-2.5 py-2 rounded-lg whitespace-nowrap bg-linear-to-r from-violet-500/[0.12] dark:from-violet-500/[0.24] to-violet-500/[0.04]"  href="settings.html">
                                                <svg class="shrink-0 fill-current text-violet-400 mr-2" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M8 9a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm-5.143 7.91a1 1 0 1 1-1.714-1.033A7.996 7.996 0 0 1 8 10a7.996 7.996 0 0 1 6.857 3.877 1 1 0 1 1-1.714 1.032A5.996 5.996 0 0 0 8 12a5.996 5.996 0 0 0-5.143 2.91Z" />
                                                </svg>
                                                <span class="text-sm font-medium text-violet-500 dark:text-violet-400">My Account</span>
                                            </a>
                                        </li>
                                        <li class="mr-0.5 md:mr-0 md:mb-0.5">
                                            <a class="flex items-center px-2.5 py-2 rounded-lg whitespace-nowrap" href="notifications.html">
                                                <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 mr-2" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="m9 12.614 4.806 1.374a.15.15 0 0 0 .174-.21L8.133 2.082a.15.15 0 0 0-.268 0L2.02 13.777a.149.149 0 0 0 .174.21L7 12.614V9a1 1 0 1 1 2 0v3.614Zm-1 1.794-5.257 1.503c-1.798.514-3.35-1.355-2.513-3.028L6.076 1.188c.791-1.584 3.052-1.584 3.845 0l5.848 11.695c.836 1.672-.714 3.54-2.512 3.028L8 14.408Z" />
                                                </svg>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200">My Notifications</span>
                                            </a>
                                        </li>
                                        <li class="mr-0.5 md:mr-0 md:mb-0.5">
                                            <a class="flex items-center px-2.5 py-2 rounded-lg whitespace-nowrap" href="connected-apps.html">
                                                <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 mr-2" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M8 3.414V6a1 1 0 1 1-2 0V1a1 1 0 0 1 1-1h5a1 1 0 0 1 0 2H9.414l6.293 6.293a1 1 0 1 1-1.414 1.414L8 3.414Zm0 9.172V10a1 1 0 1 1 2 0v5a1 1 0 0 1-1 1H4a1 1 0 0 1 0-2h2.586L.293 7.707a1 1 0 0 1 1.414-1.414L8 12.586Z" />
                                                </svg>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200">Connected Apps</span>
                                            </a>
                                        </li>
                                        <li class="mr-0.5 md:mr-0 md:mb-0.5">
                                            <a class="flex items-center px-2.5 py-2 rounded-lg whitespace-nowrap" href="plans.html">
                                                <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 mr-2" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M5 9a1 1 0 1 1 0-2h6a1 1 0 0 1 0 2H5ZM1 4a1 1 0 1 1 0-2h14a1 1 0 0 1 0 2H1Zm0 10a1 1 0 0 1 0-2h14a1 1 0 0 1 0 2H1Z" />
                                                </svg>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200">Plans</span>
                                            </a>
                                        </li>
                                        <li class="mr-0.5 md:mr-0 md:mb-0.5">
                                            <a class="flex items-center px-2.5 py-2 rounded-lg whitespace-nowrap" href="billing.html">
                                                <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 mr-2" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M0 4a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v8a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V4Zm2 0v8a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Zm9 1a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2h6Zm0 4a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2h6Z" />
                                                </svg>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200">Billing & Invoices</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- Group 2 -->
                                <div>
                                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase mb-3">Experience</div>
                                    <ul class="flex flex-nowrap md:block mr-3 md:mr-0">
                                        <li class="mr-0.5 md:mr-0 md:mb-0.5">
                                            <a class="flex items-center px-2.5 py-2 rounded-lg whitespace-nowrap" href="feedback.html">
                                                <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 mr-2" width="16" height="16" viewBox="0 0 16 16">
                                                    <path d="M14.3.3c.4-.4 1-.4 1.4 0 .4.4.4 1 0 1.4l-8 8c-.2.2-.4.3-.7.3-.3 0-.5-.1-.7-.3-.4-.4-.4-1 0-1.4l8-8zM15 7c.6 0 1 .4 1 1 0 4.4-3.6 8-8 8s-8-3.6-8-8 3.6-8 8-8c.6 0 1 .4 1 1s-.4 1-1 1C4.7 2 2 4.7 2 8s2.7 6 6 6 6-2.7 6-6c0-.6.4-1 1-1z" />
                                                </svg>
                                                <span class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200">Give Feedback</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Panel -->
                            <div class="grow">

                                <!-- Panel body -->
                                <div class="p-6 space-y-6">
                                    <h2 class="text-2xl text-gray-800 dark:text-gray-100 font-bold mb-5">My Account</h2>

                                    <!-- Picture -->
                                    <section>
                                        <div class="flex items-center">
                                            <div class="mr-4">
                                                <img class="w-20 h-20 rounded-full" src="{{ asset('admin_theme/images/user-avatar-80.png') }}" width="80" height="80" alt="User upload" />
                                            </div>
                                            <button class="btn-sm dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">Change</button>
                                        </div>
                                    </section>

                                    <!-- Business Profile -->
                                    <section>
                                        <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Business Profile</h3>
                                        <div class="text-sm">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit.</div>
                                        <div class="sm:flex sm:items-center space-y-4 sm:space-y-0 sm:space-x-4 mt-5">
                                            <div class="sm:w-1/3">
                                                <label class="block text-sm font-medium mb-1" for="name">Business Name</label>
                                                <input id="name" class="form-input w-full" type="text" value="Acme Inc." />
                                            </div>
                                            <div class="sm:w-1/3">
                                                <label class="block text-sm font-medium mb-1" for="business-id">Business ID</label>
                                                <input id="business-id" class="form-input w-full" type="text" value="Kz4tSEqtUmA" />
                                            </div>
                                            <div class="sm:w-1/3">
                                                <label class="block text-sm font-medium mb-1" for="location">Location</label>
                                                <input id="location" class="form-input w-full" type="text" value="London, UK" />
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Email -->
                                    <section>
                                        <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Email</h3>
                                        <div class="text-sm">Excepteur sint occaecat cupidatat non proident sunt in culpa qui officia.</div>
                                        <div class="flex flex-wrap mt-5">
                                            <div class="mr-2">
                                                <label class="sr-only" for="email">Business email</label>
                                                <input id="email" class="form-input" type="email" value="admin@acmeinc.com" />
                                            </div>
                                            <button class="btn dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">Change</button>
                                        </div>
                                    </section>

                                    <!-- Password -->
                                    <section>
                                        <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Password</h3>
                                        <div class="text-sm">You can set a permanent password if you don't want to use temporary login codes.</div>
                                        <div class="mt-5">
                                            <button class="btn dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">Set New Password</button>
                                        </div>
                                    </section>

                                    <!-- Smart Sync -->
                                    <section>
                                        <h3 class="text-xl leading-snug text-gray-800 dark:text-gray-100 font-bold mb-1">Smart Sync update for Mac</h3>
                                        <div class="text-sm">With this update, online-only files will no longer appear to take up hard drive space.</div>
                                        <div class="flex items-center mt-5" x-data="{ checked: true }">
                                            <div class="form-switch">
                                                <input type="checkbox" id="toggle" class="sr-only" x-model="checked" />
                                                <label for="toggle">
                                                    <span class="bg-white shadow-xs" aria-hidden="true"></span>
                                                    <span class="sr-only">Enable smart sync</span>
                                                </label>
                                            </div>
                                            <div class="text-sm text-gray-400 dark:text-gray-500 italic ml-2" x-text="checked ? 'On' : 'Off'"></div>
                                        </div>
                                    </section>
                                </div>

                                <!-- Panel footer -->
                                <footer>
                                    <div class="flex flex-col px-6 py-5 border-t border-gray-200 dark:border-gray-700/60">
                                        <div class="flex self-end">
                                            <button class="btn dark:bg-gray-800 border-gray-200 dark:border-gray-700/60 hover:border-gray-300 dark:hover:border-gray-600 text-gray-800 dark:text-gray-300">Cancel</button>
                                            <button class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white ml-3">Save Changes</button>
                                        </div>
                                    </div>
                                </footer>

                            </div>

                        </div>
                    </div>

                </div>
            </main>

        </div>

    </div>

    <script src="{{ asset('admin_theme/js/vendors/alpinejs.min.js') }}" defer></script>
    <script src="{{ asset('admin_theme/js/main.js') }}"></script>

</body>

</html>
