<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Mosaic HTML Demo - Campaigns</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="{{ asset('admin_theme/css/vendors/flatpickr.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin_theme/style.css') }}" rel="stylesheet">
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
                    <div class="sm:flex sm:justify-between sm:items-center mb-8">

                        <!-- Left: Title -->
                        <div class="mb-4 sm:mb-0">
                            <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold">Campaigns</h1>
                        </div>

                        <!-- Right: Actions -->
                        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">

                            <!-- Search form -->
                            <form class="relative">
                                <label for="action-search" class="sr-only">Search</label>
                                <input id="action-search" class="form-input pl-9 bg-white dark:bg-gray-800" type="search" placeholder="Search…" />
                                <button class="absolute inset-0 right-auto group" type="submit" aria-label="Search">
                                    <svg class="shrink-0 fill-current text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400 ml-3 mr-2" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                                        <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                                    </svg>
                                </button>
                            </form>

                            <!-- Filter button -->
                            <div class="relative inline-flex">
                                <button class="btn px-2.5 bg-white dark:bg-gray-800 border-gray-200 hover:border-gray-300 dark:border-gray-700/60 dark:hover:border-gray-600 text-gray-400 dark:text-gray-500">
                                    <span class="sr-only">Filter</span><wbr>
                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                                        <path d="M0 3a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1ZM3 8a1 1 0 0 1 1-1h8a1 1 0 1 1 0 2H4a1 1 0 0 1-1-1ZM7 12a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2H7Z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Create campaign button -->
                            <button class="btn bg-gray-900 text-gray-100 hover:bg-gray-800 dark:bg-gray-100 dark:text-gray-800 dark:hover:bg-white">
                                <svg class="fill-current shrink-0 xs:hidden" width="16" height="16" viewBox="0 0 16 16">
                                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                                </svg>
                                <span class="max-xs:sr-only">Create Campaign</span>
                            </button>

                        </div>

                    </div>

                    <!-- Cards -->
                    <div class="grid grid-cols-12 gap-6">

                        <!-- Card 1 -->
                        <div class="col-span-full sm:col-span-6 xl:col-span-4 bg-white dark:bg-gray-800 shadow-xs rounded-xl">
                            <div class="flex flex-col h-full p-5">
                                <header>
                                    <div class="flex items-center justify-between">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-red-500">
                                            <svg class="w-9 h-9 fill-current text-white" viewBox="0 0 36 36">
                                                <path d="M25 24H11a1 1 0 01-1-1v-5h2v4h12v-4h2v5a1 1 0 01-1 1zM14 13h8v2h-8z" />
                                            </svg>
                                        </div>
                                        <div class="flex shrink-0 -space-x-3 -ml-px">
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-01.jpg') }}" width="28" height="28" alt="User 01" />
                                            </a>
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-02.jpg') }}" width="28" height="28" alt="User 02" />
                                            </a>
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-03.jpg') }}" width="28" height="28" alt="User 03" />
                                            </a>
                                        </div>
                                    </div>
                                </header>
                                <div class="grow mt-2">
                                    <a class="inline-flex text-gray-800 dark:text-gray-100 hover:text-gray-900 dark:hover:text-white mb-1" href="#0">
                                        <h2 class="text-xl leading-snug font-semibold">Monitor progress in Real Time Value</h2>
                                    </a>
                                    <div class="text-sm">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.</div>
                                </div>
                                <footer class="mt-5">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Jan 20 <span class="text-gray-400 dark:text-gray-600">-&gt;</span> Jan 27</div>
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-xs inline-flex font-medium bg-green-500/20 text-green-700 rounded-full text-center px-2.5 py-1">One-Time</div>
                                        </div>
                                        <div>
                                            <a class="text-sm font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="#0">View -&gt;</a>
                                        </div>
                                    </div>
                                </footer>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="col-span-full sm:col-span-6 xl:col-span-4 bg-white dark:bg-gray-800 shadow-xs rounded-xl">
                            <div class="flex flex-col h-full p-5">
                                <header>
                                    <div class="flex items-center justify-between">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-green-500">
                                            <svg class="w-9 h-9 fill-current text-white" viewBox="0 0 36 36">
                                                <path d="M15 13v-3l-5 4 5 4v-3h8a1 1 0 000-2h-8zM21 21h-8a1 1 0 000 2h8v3l5-4-5-4v3z" />
                                            </svg>
                                        </div>
                                        <div class="flex shrink-0 -space-x-3 -ml-px">
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-04.jpg') }}" width="28" height="28" alt="User 04" />
                                            </a>
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-05.jpg') }}" width="28" height="28" alt="User 05" />
                                            </a>
                                        </div>
                                    </div>
                                </header>
                                <div class="grow mt-2">
                                    <a class="inline-flex text-gray-800 dark:text-gray-100 hover:text-gray-900 dark:hover:text-white mb-1" href="#0">
                                        <h2 class="text-xl leading-snug font-semibold">Monitor progress in Real Time Value</h2>
                                    </a>
                                    <div class="text-sm">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.</div>
                                </div>
                                <footer class="mt-5">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Jan 20 <span class="text-gray-400 dark:text-gray-600">-&gt;</span> Jan 27</div>
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-xs inline-flex font-medium bg-red-500/20 text-red-700 rounded-full text-center px-2.5 py-1">Off-Track</div>
                                        </div>
                                        <div>
                                            <a class="text-sm font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="#0">View -&gt;</a>
                                        </div>
                                    </div>
                                </footer>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="col-span-full sm:col-span-6 xl:col-span-4 bg-white dark:bg-gray-800 shadow-xs rounded-xl">
                            <div class="flex flex-col h-full p-5">
                                <header>
                                    <div class="flex items-center justify-between">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 bg-sky-500">
                                            <svg class="w-9 h-9 fill-current text-white" viewBox="0 0 36 36">
                                                <path d="M23 11v2.085c-2.841.401-4.41 2.462-5.8 4.315-1.449 1.932-2.7 3.6-5.2 3.6h-1v2h1c3.5 0 5.253-2.338 6.8-4.4 1.449-1.932 2.7-3.6 5.2-3.6h3l-4-4zM15.406 16.455c.066-.087.125-.162.194-.254.314-.419.656-.872 1.033-1.33C15.475 13.802 14.038 13 12 13h-1v2h1c1.471 0 2.505.586 3.406 1.455zM24 21c-1.471 0-2.505-.586-3.406-1.455-.066.087-.125.162-.194.254-.316.422-.656.873-1.028 1.328.959.878 2.108 1.573 3.628 1.788V25l4-4h-3z" />
                                            </svg>
                                        </div>
                                        <div class="flex shrink-0 -space-x-3 -ml-px">
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-07.jpg') }}" width="28" height="28" alt="User 07" />
                                            </a>
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-08.jpg') }}" width="28" height="28" alt="User 08" />
                                            </a>
                                            <a class="block" href="#0">
                                                <img class="rounded-full border-2 border-white dark:border-gray-800 box-content" src="{{ asset('admin_theme/images/user-28-09.jpg') }}" width="28" height="28" alt="User 09" />
                                            </a>
                                        </div>
                                    </div>
                                </header>
                                <div class="grow mt-2">
                                    <a class="inline-flex text-gray-800 dark:text-gray-100 hover:text-gray-900 dark:hover:text-white mb-1" href="#0">
                                        <h2 class="text-xl leading-snug font-semibold">Monitor progress in Real Time Value</h2>
                                    </a>
                                    <div class="text-sm">Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.</div>
                                </div>
                                <footer class="mt-5">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Jan 20 <span class="text-gray-400 dark:text-gray-600">-&gt;</span> Jan 27</div>
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-xs inline-flex font-medium bg-yellow-500/20 text-yellow-700 rounded-full text-center px-2.5 py-1">At Risk</div>
                                        </div>
                                        <div>
                                            <a class="text-sm font-medium text-violet-500 hover:text-violet-600 dark:hover:text-violet-400" href="#0">View -&gt;</a>
                                        </div>
                                    </div>
                                </footer>
                            </div>
                        </div>

                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        <div class="flex justify-center">
                            <nav class="flex" role="navigation" aria-label="Navigation">
                                <div class="mr-2">
                                    <span class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-gray-300 dark:text-gray-600">
                                        <span class="sr-only">Previous</span><wbr />
                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                                            <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                                        </svg>
                                    </span>
                                </div>
                                <ul class="inline-flex text-sm font-medium -space-x-px rounded-lg shadow-xs">
                                    <li>
                                        <span class="inline-flex items-center justify-center rounded-l-lg leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-violet-500">1</span>
                                    </li>
                                    <li>
                                        <a class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300" href="#0">2</a>
                                    </li>
                                    <li>
                                        <a class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300" href="#0">3</a>
                                    </li>
                                    <li>
                                        <span class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 text-gray-400 dark:text-gray-500">…</span>
                                    </li>
                                    <li>
                                        <a class="inline-flex items-center justify-center rounded-r-lg leading-5 px-3.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-gray-600 dark:text-gray-300" href="#0">9</a>
                                    </li>
                                </ul>
                                <div class="ml-2">
                                    <a href="#0" class="inline-flex items-center justify-center rounded-lg leading-5 px-2.5 py-2 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-900 border border-gray-200 dark:border-gray-700/60 text-violet-500 shadow-xs">
                                        <span class="sr-only">Next</span><wbr />
                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 16 16">
                                            <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                                        </svg>
                                    </a>
                                </div>
                            </nav>
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
