<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required Meta Tags Always Come First -->
  <meta charset="utf-8">
  <meta name="robots" content="max-snippet:-1, max-image-preview:large, max-video-preview:-1">
  <link rel="canonical" href="https://preline.co/">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Explore the Coffee Shop demo with a clean product detail page and flexible checkout options for modern, clean, and minimal e-commerce experiences.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta name="twitter:site" content="@preline">
  <meta name="twitter:creator" content="@preline">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Coffee Shop Tailwind CSS Template | Preline UI, crafted with Tailwind CSS">
  <meta name="twitter:description" content="Explore the Coffee Shop demo with a clean product detail page and flexible checkout options for modern, clean, and minimal e-commerce experiences.">
  <meta name="twitter:image" content="https://preline.co/assets/img/og-image.png">

  <meta property="og:url" content="https://preline.co/">
  <meta property="og:locale" content="en_US">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Preline">
  <meta property="og:title" content="Coffee Shop Tailwind CSS Template | Preline UI, crafted with Tailwind CSS">
  <meta property="og:description" content="Explore the Coffee Shop demo with a clean product detail page and flexible checkout options for modern, clean, and minimal e-commerce experiences.">
  <meta property="og:image" content="https://preline.co/assets/img/og-image.png">

  <!-- Title -->
  <title>Gaming Platform | Register Creator</title>

  <!-- Favicon -->
  <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Force Light Theme -->
  <script>
    // Force light theme by default - remove any dark class and set light theme
    document.documentElement.classList.remove('dark');
    localStorage.setItem('hs_theme', 'light');
  </script>
    <link href="{{ asset('admin_theme/style.css') }}" rel="stylesheet">

  <!-- Vite CSS -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white">
  <!-- Announcement Banner -->
  <div class="py-2 bg-gray-800 text-center">
    <div class="max-w-7xl px-4 sm:px-6 lg:px-8 mx-auto">
      <p class="text-sm text-white">
        Welcome to our Gaming Platform
      </p>
    </div>
  </div>
  <!-- End Announcement Banner -->

  <!-- ========== HEADER ========== -->
  <header class="flex flex-wrap lg:justify-start lg:flex-nowrap z-50 w-full py-7">
    <nav class="relative max-w-7xl w-full flex flex-wrap lg:grid lg:grid-cols-12 basis-full items-center px-4 md:px-6 lg:px-8 mx-auto">
      <div class="lg:col-span-3 flex items-center">
        <!-- Logo -->
        <a class="flex-none rounded-xl text-xl inline-block font-semibold focus:outline-hidden focus:opacity-80" 
        href="{{ route('home') }}" aria-label="Preline">
         <img src="{{ asset('images\logo\logo-dark.svg') }}" alt="">
        </a>
        <!-- End Logo -->
      </div>

      <!-- Button Group -->
      <div class="flex items-center gap-x-1 lg:gap-x-2 ms-auto py-1 lg:ps-6 lg:order-3 lg:col-span-3">
        <button type="button" class="size-9.5 relative flex justify-center items-center rounded-xl bg-white border border-gray-200 text-black hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:hover:bg-white/10 dark:text-white dark:hover:text-white dark:focus:text-white">
          <span class="sr-only">Search</span>
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m21 21-4.34-4.34" />
            <circle cx="11" cy="11" r="8" />
          </svg>
        </button>
        <button type="button" class="size-9.5 relative flex justify-center items-center rounded-xl bg-white border border-gray-200 text-black hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:hover:bg-white/10 dark:text-white dark:hover:text-white dark:focus:text-white">
          <span class="sr-only">Cart</span>
          <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="8" cy="21" r="1" />
            <circle cx="19" cy="21" r="1" />
            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12" />
          </svg>
        </button>
        <a href="{{ route('login') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium text-nowrap rounded-xl border border-transparent bg-yellow-400 text-black hover:bg-yellow-500 focus:outline-hidden focus:bg-yellow-500 transition disabled:opacity-50 disabled:pointer-events-none">
          Sign in
        </a>

        <div class="lg:hidden">
          <button type="button" class="hs-collapse-toggle size-9.5 flex justify-center items-center text-sm font-semibold rounded-xl border border-gray-200 text-black hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:border-neutral-700 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" id="hs-pro-hcail-collapse" aria-expanded="false" aria-controls="hs-pro-hcail" aria-label="Toggle navigation" data-hs-collapse="#hs-pro-hcail">
            <svg class="hs-collapse-open:hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="3" x2="21" y1="6" y2="6" />
              <line x1="3" x2="21" y1="12" y2="12" />
              <line x1="3" x2="21" y1="18" y2="18" />
            </svg>
            <svg class="hs-collapse-open:block hidden shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M18 6 6 18" />
              <path d="m6 6 12 12" />
            </svg>
          </button>
        </div>
      </div>
      <!-- End Button Group -->

      <!-- Collapse -->
      <div id="hs-pro-hcail" class="hs-collapse hidden overflow-hidden transition-all duration-300 basis-full grow lg:block lg:w-auto lg:basis-auto lg:order-2 lg:col-span-6" aria-labelledby="hs-pro-hcail-collapse">
        <div class="flex flex-col gap-y-4 gap-x-0 mt-5 lg:flex-row lg:justify-center lg:items-center lg:gap-y-0 lg:gap-x-7 lg:mt-0">
          <div>
            <a class="relative inline-block text-black focus:outline-hidden before:absolute before:bottom-0.5 before:start-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 dark:text-white" href="#" aria-current="page">Home</a>
          </div>
          <div>
            <a class="inline-block text-black hover:text-gray-600 focus:outline-hidden focus:text-gray-600 dark:text-white dark:hover:text-neutral-300 dark:focus:text-neutral-300" href="#">Listings</a>
          </div>
          <div>
            <a class="inline-block text-black hover:text-gray-600 focus:outline-hidden focus:text-gray-600 dark:text-white dark:hover:text-neutral-300 dark:focus:text-neutral-300" href="#">Product</a>
          </div>
          <div>
            <a class="inline-block text-black hover:text-gray-600 focus:outline-hidden focus:text-gray-600 dark:text-white dark:hover:text-neutral-300 dark:focus:text-neutral-300" href="#">Checkout</a>
          </div>
        </div>
      </div>
      <!-- End Collapse -->
    </nav>
  </header>
  <!-- ========== END HEADER ========== -->

  <!-- ========== MAIN CONTENT ========== -->
  <main id="content">
   
    <!-- Testimonials -->
    <div class="px-4 sm:px-6 lg:px-8 mt-10">
      <!-- Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 md:items-center">
        <div class="relative h-80 md:h-150 bg-gray-100 rounded-2xl dark:bg-neutral-800">
          <img class="absolute inset-0 size-full object-cover rounded-2xl" src="{{ asset('images\pexels-ivan-samkov-7676432.jpg') }}" alt="Testimonials Image">
        </div>
        <!-- End Col -->

        <div class="pt-10 md:p-10">
          
            <div class="max-w-sm mx-auto w-full px-4 py-8">

                        <h1 class="text-3xl text-gray-800  font-bold mb-6">{{ __('Créer un compte Créateur') }}</h1>

                        @if (session('success'))
                            <div class="bg-green-100 text-green-600 px-3 py-2 rounded mb-4" data-testid="success-message">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="bg-red-100 text-red-600 px-3 py-2 rounded mb-4" data-testid="validation-error">
                                {{ session('error') }}
                            </div>
                        @endif
                        
                        <!-- Form -->
                        <form method="POST" action="{{ route('creator.register.submit') }}" id="creatorRegisterForm" data-testid="register-form">
                            @csrf
                            <div class="space-y-4">
                                <div class="space-y-4 sm:flex sm:space-y-0 sm:space-x-4">
                                    <div class="sm:w-1/2">
                                        <label class="block text-sm font-medium mb-1" for="first_name">{{ __('Prénom') }} <span class="text-red-500">*</span></label>
                                        <input id="first_name" data-testid="first-name-input" class="form-input w-full @error('first_name') border-red-500 @enderror" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" />
                                        @error('first_name')
                                            <div class="text-red-500 mt-1 text-sm" data-testid="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="sm:w-1/2">
                                        <label class="block text-sm font-medium mb-1" for="last_name">{{ __('Nom') }} <span class="text-red-500">*</span></label>
                                        <input id="last_name" data-testid="last-name-input" class="form-input w-full @error('last_name') border-red-500 @enderror" type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" />
                                        @error('last_name')
                                            <div class="text-red-500 mt-1 text-sm" data-testid="validation-error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="email">{{ __('Adresse e-mail') }} <span class="text-red-500">*</span></label>
                                    <input id="email" data-testid="email-input" class="form-input w-full @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" />
                                    @error('email')
                                        <div class="text-red-500 mt-1 text-sm" data-testid="validation-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="password">{{ __('Mot de passe') }} <span class="text-red-500">*</span></label>
                                    <input id="password" data-testid="password-input" class="form-input w-full @error('password') border-red-500 @enderror" type="password" name="password" required autocomplete="new-password" />
                                    @error('password')
                                        <div class="text-red-500 mt-1 text-sm" data-testid="validation-error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="password_confirmation">{{ __('Confirmer le mot de passe') }} <span class="text-red-500">*</span></label>
                                    <input id="password_confirmation" data-testid="password-confirmation-input" class="form-input w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between mt-6">
                                <div class="mr-1">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" name="newsletter" />
                                        <span class="text-sm ml-2">{{ __('Recevoir les actualités par email.') }}</span>
                                    </label>
                                </div>
                                <button type="submit" data-testid="submit-button" class="btn bg-violet-500 hover:bg-violet-600 text-white ml-3 whitespace-nowrap" id="creatorSubmitBtn">
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
                            <!-- Warning -->
                            <div class="mt-5">
                                <div class="bg-yellow-500/20 text-yellow-700 px-3 py-2 rounded-lg">
                                    <svg class="inline w-3 h-3 shrink-0 fill-current" viewBox="0 0 12 12">
                                        <path d="M10.28 1.28L3.989 7.575 1.695 5.28A1 1 0 00.28 6.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 1.28z"></path>
                                    </svg>
                                    <span class="text-sm">
                                        {{ __('To support you during the pandemic super pro features are free until March 31st.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
        </div>
        <!-- End Col -->
      </div>
      <!-- End Grid -->
    </div>
    <!-- End Testimonials -->

    <!-- Contact -->
    {{-- <div class="max-w-7xl px-4 sm:px-6 lg:px-8 py-12 lg:py-24 mx-auto">
      <div class="mb-6 sm:mb-10 max-w-2xl text-center mx-auto">
        <h2 class="font-medium text-black text-2xl sm:text-4xl dark:text-white">
          Contacts
        </h2>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 lg:items-center gap-6 md:gap-8 lg:gap-12">
        <div class="aspect-w-16 aspect-h-6 lg:aspect-h-14 overflow-hidden bg-gray-100 rounded-2xl dark:bg-neutral-800">
          <img class="group-hover:scale-105 group-focus:scale-105 transition-transform duration-500 ease-in-out object-cover rounded-2xl"
          src=" {{ asset('images\pexels-cottonbro-6964862.jpg') }}" alt="Contacts Image">
        </div>
        <!-- End Col -->

        <div class="space-y-8 lg:space-y-16">
          <div>
            <h3 class="mb-5 font-semibold text-black dark:text-white">
              Our address
            </h3>

            <!-- Grid -->
            <div class="grid sm:grid-cols-2 gap-4 sm:gap-6 md:gap-8 lg:gap-12">
              <div class="flex gap-4">
                <svg class="shrink-0 size-5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>

                <div class="grow">
                  <p class="text-sm text-gray-600 dark:text-neutral-400">
                    United Kingdom
                  </p>
                  <address class="mt-1 text-black not-italic dark:text-white">
                    300 Bath Street, Tay House<br>
                    Glasgow G2 4JR
                  </address>
                </div>
              </div>
            </div>
            <!-- End Grid -->
          </div>

          <div>
            <h3 class="mb-5 font-semibold text-black dark:text-white">
              Our contacts
            </h3>

            <!-- Grid -->
            <div class="grid sm:grid-cols-2 gap-4 sm:gap-6 md:gap-8 lg:gap-12">
              <div class="flex gap-4">
                <svg class="shrink-0 size-5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21.2 8.4c.5.38.8.97.8 1.6v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V10a2 2 0 0 1 .8-1.6l8-6a2 2 0 0 1 2.4 0l8 6Z"></path>
                  <path d="m22 10-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 10"></path>
                </svg>

                <div class="grow">
                  <p class="text-sm text-gray-600 dark:text-neutral-400">
                    Email us
                  </p>
                  <p>
                    <a class="relative inline-block font-medium text-black before:absolute before:bottom-0.5 before:start-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 hover:before:bg-black focus:outline-hidden focus:before:bg-black dark:text-white dark:hover:before:bg-white dark:focus:before:bg-white" href="mailto:example@site.so">
                      hello@example.so
                    </a>
                  </p>
                </div>
              </div>

              <div class="flex gap-4">
                <svg class="shrink-0 size-5 text-gray-500 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>

                <div class="grow">
                  <p class="text-sm text-gray-600 dark:text-neutral-400">
                    Call us
                  </p>
                  <p>
                    <a class="relative inline-block font-medium text-black before:absolute before:bottom-0.5 before:start-0 before:-z-1 before:w-full before:h-1 before:bg-yellow-400 hover:before:bg-black focus:outline-hidden focus:before:bg-black dark:text-white dark:hover:before:bg-white dark:focus:before:bg-white" href="mailto:example@site.so">
                      +44 222 777-000
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <!-- End Grid -->
          </div>
        </div>
        <!-- End Col -->
      </div>
    </div> --}}
    <!-- End Contact -->
  </main>
  <!-- ========== END MAIN CONTENT ========== -->

  <!-- ========== FOOTER ========== -->
  <footer class="border-t md:border-t-0 border-gray-200 mt-20">
    <div class="w-full max-w-7xl py-10 md:pt-0 px-4 sm:px-6 lg:px-8 mx-auto">
      <!-- Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 items-center gap-5">
      {{--   <div class="text-center md:text-start">
          <!-- Logo -->
          <a href="">
            <img src="{{ asset('images\logo\logo-dark.svg') }}" alt="">
          </a>
          <!-- End Logo -->
        </div> --}}
        <!-- End Col -->

        <ul class="text-center">
          <li class="inline-block relative pe-8 last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-3 before:-translate-y-1/2 before:content-['/'] before:text-black">
            <a class="inline-flex gap-x-2 text-sm text-black hover:text-gray-600" href="#">
              About
            </a>
          </li>
          <li class="inline-block relative pe-8 last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-3 before:-translate-y-1/2 before:content-['/'] before:text-black">
            <a class="inline-flex gap-x-2 text-sm text-black hover:text-gray-600" href="#">
              Services
            </a>
          </li>
          <li class="inline-block relative pe-8 last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-3 before:-translate-y-1/2 before:content-['/'] before:text-black">
            <a class="inline-flex gap-x-2 text-sm text-black hover:text-gray-600" href="#">
              Blog
            </a>
          </li>
        </ul>
        <!-- End Col -->

        <!-- Social Brands -->
        <div class="text-center md:text-end space-x-2">
          <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-black hover:text-gray-600 disabled:opacity-50 disabled:pointer-events-none" href="#">
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M15.545 6.558a9.42 9.42 0 0 1 .139 1.626c0 2.434-.87 4.492-2.384 5.885h.002C11.978 15.292 10.158 16 8 16A8 8 0 1 1 8 0a7.689 7.689 0 0 1 5.352 2.082l-2.284 2.284A4.347 4.347 0 0 0 8 3.166c-2.087 0-3.86 1.408-4.492 3.304a4.792 4.792 0 0 0 0 3.063h.003c.635 1.893 2.405 3.301 4.492 3.301 1.078 0 2.004-.276 2.722-.764h-.003a3.702 3.702 0 0 0 1.599-2.431H8v-3.08h7.545z" />
            </svg>
          </a>
          <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-black hover:text-gray-600 disabled:opacity-50 disabled:pointer-events-none" href="#">
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z" />
            </svg>
          </a>
          <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-black hover:text-gray-600 disabled:opacity-50 disabled:pointer-events-none" href="#">
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z" />
            </svg>
          </a>
          <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-black hover:text-gray-600 disabled:opacity-50 disabled:pointer-events-none" href="#">
            <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path d="M3.362 10.11c0 .926-.756 1.681-1.681 1.681S0 11.036 0 10.111C0 9.186.756 8.43 1.68 8.43h1.682v1.68zm.846 0c0-.924.756-1.68 1.681-1.68s1.681.756 1.681 1.68v4.21c0 .924-.756 1.68-1.68 1.68a1.685 1.685 0 0 1-1.682-1.68v-4.21zM5.89 3.362c-.926 0-1.682-.756-1.682-1.681S4.964 0 5.89 0s1.68.756 1.68 1.68v1.682H5.89zm0 .846c.924 0 1.68.756 1.68 1.681S6.814 7.57 5.89 7.57H1.68C.757 7.57 0 6.814 0 5.89c0-.926.756-1.682 1.68-1.682h4.21zm6.749 1.682c0-.926.755-1.682 1.68-1.682.925 0 1.681.756 1.681 1.681s-.756 1.681-1.68 1.681h-1.681V5.89zm-.848 0c0 .924-.755 1.68-1.68 1.68A1.685 1.685 0 0 1 8.43 5.89V1.68C8.43.757 9.186 0 10.11 0c.926 0 1.681.756 1.681 1.68v4.21zm-1.681 6.748c.926 0 1.682.756 1.682 1.681S11.036 16 10.11 16s-1.681-.756-1.681-1.68v-1.682h1.68zm0-.847c-.924 0-1.68-.755-1.68-1.68 0-.925.756-1.681 1.68-1.681h4.21c.924 0 1.68.756 1.68 1.68 0 .926-.756 1.681-1.68 1.681h-4.21z" />
            </svg>
          </a>
        </div>
        <!-- End Social Brands -->
      </div>
      <!-- End Grid -->
    </div>
  </footer>
  <!-- ========== END FOOTER ========== -->

  <!-- Hidden trigger element for MicroModal -->
  <a href="#" data-micromodal-trigger="modal-creator-success" class="hidden"></a>

  <!-- Modal de succès pour la création de compte -->
  <div class="modal micromodal-slide hidden" id="modal-creator-success" aria-hidden="true" inert>
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
          // Initialiser MicroModal
          MicroModal.init({
              disableScroll: true,
              disableFocus: false,
              awaitOpenAnimation: false,
              awaitCloseAnimation: false,
              debugMode: false
          });

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