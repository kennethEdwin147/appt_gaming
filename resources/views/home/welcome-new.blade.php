<!--Start Layout-->
<!DOCTYPE html>
<html
  lang="en"
  x-data="layout()"
  :class="{
  'dark': $store.app.isDark,
  '': !$store.app.isDark
}"
>
  <head>
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="{{ asset('apollo-theme/img/favicon.png') }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Apollo - Tailwind CSS Banking Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Roboto+Flex:opsz,wght@8..144,300;8..144,400;8..144,500;8..144,600&display=swap"
      rel="stylesheet"
    />

    
    <!-- Apollo Theme Assets -->
    <link rel="stylesheet" href="{{ asset('apollo-theme/assets/404-65d6cc7d.css') }}">
    <script type="module" crossorigin src="{{ asset('apollo-theme/assets/404-f1ca4536.js') }}"></script>
    
    <!-- Vite Assets (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="w-full h-full bg-white dark:bg-muted-900">
    <!-- prettier-ignore -->

    <main class="w-full">
      <!-- Renders the page body -->
      
      <!--Nav-->
      <div x-cloak class="absolute top-0 left-0 w-full">
        <div class="w-full max-w-6xl mx-auto px-4">
          <div class="w-full flex items-center justify-between py-5">
            <div class="flex-1 flex items-center">
              <a href="{{ route('home') }}" class="flex items-center gap-2">
                <img
                  src="{{ asset('apollo-theme/img/logo/logo-square-outline.svg') }}"
                  class="w-8 h-8 dark:invert"
                  alt="App logo"
                  width="48"
                  height="48"
                />
                <img
                  src="{{ asset('apollo-theme/img/logo/text.svg') }}"
                  class="hidden md:block w-24 dark:invert"
                  :class="$store.app.isLayoutCompact ? 'hidden' : 'block'"
                  alt="App logo"
                  width="112"
                  height="15"
                />
              </a>
            </div>
            <div class="grow">
              <div class="w-full flex items-center justify-center">
               {{--  <p class="font-heading text-muted-700 dark:text-muted-200">
                  Settings
                </p> --}}
              </div>
            </div>
            <div class="">
            {{--   <div class="flex items-center justify-end">
                <button
                  type="button"
                  class="group text-center"
                  onclick="window.history.go(-1); return false;"
                >
                  <iconify-icon
                    class="iconify w-8 h-8 flex items-center justify-center text-3xl text-muted-800 dark:text-muted-500 dark:group-hover:text-muted-200 transition-colors duration-300"
                    icon="lucide:x"
                  ></iconify-icon>
                  <span
                    class="block font-heading text-xs text-muted-400 dark:text-muted-400 dark:group-hover:text-muted-200 transition-colors duration-300"
                  >
                    Back
                  </span>
                </button>
              </div> --}}

              <div class="flex items-center gap-x-4">
            <!--Theme toggler-->
            <label
              class="
                relative
                block
                h-6
                w-14
                bg-muted-200
                dark:bg-muted-700
                rounded-full
                scale-[0.8]
              "
            >
              <input
                class="
                  peer
                  absolute
                  top-0
                  left-0
                  w-full
                  h-full
                  opacity-0
                  cursor-pointer
                  z-10
                "
                type="checkbox"
                :checked="$store.app.isDark"
                @click="toggleTheme()"
              />
              <span
                class="
                  absolute
                  -top-2
                  -left-1
                  flex
                  items-center
                  justify-center
                  h-10
                  w-10
                  bg-white
                  dark:bg-muted-900
                  border border-muted-200
                  dark:border-muted-800
                  rounded-full
                  -ml-1
                  peer-checked:ml-[45%] peer-checked:rotate-[360deg]
                  transition-all
                  duration-300
                "
              >
                <iconify-icon
                  class="iconify w-6 h-6 flex items-center justify-center text-2xl text-yellow-400 fill-current"
                  icon="heroicons-solid:sun"
                  x-show="!$store.app.isDark"
                ></iconify-icon>
                <iconify-icon
                  class="iconify w-6 h-6 flex items-center justify-center text-2xl text-yellow-400 fill-current"
                  icon="pepicons:moon-filled"
                  x-show="$store.app.isDark"
                ></iconify-icon>
              </span>
            </label>
        
            <!--Money dropdown-->
            <div x-data="dropdown()" class="relative" @click.away="close()">
              <button
                type="button"
                class="
                  h-10
                  inline-flex
                  justify-center
                  items-center
                  gap-x-2
                  px-6
                  py-2
                  font-sans
                  text-sm text-white
                  bg-primary-500
                  rounded-full
                  shadow-lg shadow-primary-500/20
                  hover:shadow-xl
                  tw-accessibility
                  transition-all
                  duration-300
                "
                @click="open()"
              >
                <span>Move Money</span>
                <iconify-icon
                  class="iconify w-4 h-4 flex items-center justify-center text-lg transition-transform duration-300"
                  :class="isOpen ? 'rotate-180' : ''"
                  icon="lucide:chevron-down"
                ></iconify-icon>
              </button>
            
              <!--Dropdown menu-->
              <div
                x-cloak
                x-show="isOpen"
                x-transition
                class="
                  absolute
                  top-11
                  right-0
                  w-[240px]
                  overflow-y-auto
                  slimscroll
                  p-2
                  rounded-lg
                  bg-white
                  dark:bg-muted-900
                  border border-muted-200
                  dark:border-muted-800
                  shadow-2xl shadow-muted-400/20
                  dark:shadow-muted-800/10
                  z-20
                "
              >
                <ul class="space-y-1">
                  <li>
                    <a
                      href="/payments-send.html"
                      class="
                        flex
                        items-center
                        gap-3
                        p-2
                        rounded-lg
                        text-muted-400
                        hover:text-primary-500 hover:bg-muted-100
                        dark:hover:bg-muted-800
                        transition-colors
                        duration-300
                      "
                    >
                      <iconify-icon class="iconify w-5 h-5 flex items-center justify-center text-lg" icon="ph:arrow-right-duotone"></iconify-icon>
                      <div class="font-sans">
                        <h5
                          class="text-sm font-semibold text-muted-800 dark:text-muted-200"
                        >
                          Send
                        </h5>
                        <p class="text-xs text-muted-400">Send someone money</p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <a
                      href="/payments-receive.html"
                      class="
                        flex
                        items-center
                        gap-3
                        p-2
                        rounded-lg
                        text-muted-400
                        hover:text-primary-500 hover:bg-muted-100
                        dark:hover:bg-muted-800
                        transition-colors
                        duration-300
                      "
                    >
                      <iconify-icon class="iconify w-5 h-5 flex items-center justify-center text-lg" icon="ph:arrow-left-duotone"></iconify-icon>
                      <div class="font-sans">
                        <h5
                          class="text-sm font-semibold text-muted-800 dark:text-muted-200"
                        >
                          Receive
                        </h5>
                        <p class="text-xs text-muted-400">Add or receive funds</p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <a
                      href="/cards.html"
                      class="
                        flex
                        items-center
                        gap-3
                        p-2
                        rounded-lg
                        text-muted-400
                        hover:text-primary-500 hover:bg-muted-100
                        dark:hover:bg-muted-800
                        transition-colors
                        duration-300
                      "
                    >
                      <iconify-icon class="iconify w-5 h-5 flex items-center justify-center text-lg" icon="ph:credit-card-duotone"></iconify-icon>
                      <div class="font-sans">
                        <h5
                          class="text-sm font-semibold text-muted-800 dark:text-muted-200"
                        >
                          Cards
                        </h5>
                        <p class="text-xs text-muted-400">Manage your cards</p>
                      </div>
                    </a>
                  </li>
                  <li>
                    <a
                      href="/accounts.html"
                      class="
                        flex
                        items-center
                        gap-3
                        p-2
                        rounded-lg
                        text-muted-400
                        hover:text-primary-500 hover:bg-muted-100
                        dark:hover:bg-muted-800
                        transition-colors
                        duration-300
                      "
                    >
                      <iconify-icon class="iconify w-5 h-5 flex items-center justify-center text-lg" icon="ph:wallet-duotone"></iconify-icon>
                      <div class="font-sans">
                        <h5
                          class="text-sm font-semibold text-muted-800 dark:text-muted-200"
                        >
                          Accounts
                        </h5>
                        <p class="text-xs text-muted-400">Manage your accounts</p>
                      </div>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
            </button>
          </div>
            
            </div>
          </div>
        </div>
      </div>
      
      <!--Content-->
      <div class="w-full pt-32 pb-20">
        <div x-data="tabs()" class="w-full max-w-6xl mx-auto px-4">
          <div class="w-full grid md:grid-cols-12 gap-8 md:gap-16">

 
            <!--Stepper column-->
            <div class="md:col-span-3 lg:col-span-3">
              <!--Tabs-->
              
              <div class="h-full border-r border-muted-200 dark:border-muted-800">
                <ul class="-mr-0.5 xs:flex xs:gap-4">
                  <li>
                               {{-- ajout --}}

<div class="flex
gap-4
 flex-col
 mb-10">
    <img class="w-20 h-20 rounded-full object-cover" src="https://images.pexels.com/photos/1310452/pexels-photo-1310452.jpeg?_gl=1*1sm54ys*_ga*MjEzOTYxMjc4MS4xNzQ3ODc2ODc0*_ga_8JE65Q40S6*czE3NTA0Mzg2NDAkbzE0JGcxJHQxNzUwNDM5OTIwJGozMCRsMCRoMA.." alt="">
    <div class="">
        <p class="text-muted-800 dark:text-muted-100 border-primary-500 font-bold
">Jilian Ross</p>
    </div>
</div>

            {{-- end ajout --}}
                  </li>
                  <li>
                    <a
                      class="
                        flex
                        w-full
                        py-2
                        font-heading
                        text-sm
                        cursor-pointer
                        md:border-r-[3px]
                        xs:border-b-[3px]
                      "
                      :class="activeTab === 'tab-1' ? 'text-muted-800 dark:text-muted-100 border-primary-500' : 'text-muted-500 dark:text-muted-400 border-transparent'"
                      data-tab="tab-1"
                      @click.prevent="toggle($event)"
                    >
                      Personal
                    </a>
                  </li>
                  <li>
                    <a
                      class="
                        flex
                        w-full
                        py-2
                        font-heading
                        text-sm
                        cursor-pointer
                        md:border-r-[3px]
                        xs:border-b-[3px]
                      "
                      :class="activeTab === 'tab-2' ? 'text-muted-800 dark:text-muted-100 border-primary-500' : 'text-muted-500 dark:text-muted-400 border-transparent'"
                      data-tab="tab-2"
                      @click.prevent="toggle($event)"
                    >
                      Security
                    </a>
                  </li>
                  <li>
                    <a
                      class="
                        flex
                        w-full
                        py-2
                        font-heading
                        text-sm
                        cursor-pointer
                        md:border-r-[3px]
                        xs:border-b-[3px]
                      "
                      data-tab="tab-3"
                      :class="activeTab === 'tab-3' ? 'text-muted-800 dark:text-muted-100 border-primary-500' : 'text-muted-500 dark:text-muted-400 border-transparent'"
                      @click.prevent="toggle($event)"
                    >
                      Notifications
                    </a>
                  </li>
                  <li>
                    <a
                      class="
                        flex
                        w-full
                        py-2
                        font-heading
                        text-sm
                        cursor-pointer
                        md:border-r-[3px]
                        xs:border-b-[3px]
                      "
                      data-tab="tab-4"
                      :class="activeTab === 'tab-4' ? 'text-muted-800 dark:text-muted-100 border-primary-500' : 'text-muted-500 dark:text-muted-400 border-transparent'"
                      @click.prevent="toggle($event)"
                    >
                      API Tokens
                    </a>
                  </li>
                </ul>
              </div>
            </div>
      
            <!--Steps column-->
            <div class="md:col-span-9 lg:col-span-9">
              <!--Tab 1-->
              <div
                x-show="activeTab === 'tab-1'"
                class="py-6 space-y-10 divide-muted-200 dark:divide-muted-800"
              >
              {{-- intro --}}
              

                {{-- image --}}

                <div>

                  
                <h2
                  class="font-heading text-2xl md:text-3xl text-muted-800 dark:text-white mb-8"
                >
                  Select a transfer method
                </h2>

                <div class="w-full max-w-md">
                  <div class="w-full space-y-4">
                    <!--Item-->
                    <button type="button" class="group w-full flex items-center py-4 px-6 bg-white dark:bg-muted-1000 border-2 border-muted-200 dark:border-muted-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-xl hover:shadow-muted-400/10 dark:shadow-muted-800/10 hover:-translate-x-0.5 rounded-xl cursor-pointer transition-all duration-300 tw-accessibility" @click="nextStep(), paymentMethod = 'transfer'">
                      <span class="flex items-center justify-center w-12 h-12 text-muted-600 dark:text-muted-400 bg-muted-100 dark:bg-muted-900 group-hover:bg-primary-500 group-hover:text-white group-hover:rotate-180 rounded-full transition-all duration-300">
                        <iconify-icon class="iconify w-6 h-6 flex items-center justify-center text-2xl" icon="ph:arrows-left-right-duotone"></iconify-icon>
                      </span>
                      <span class="flex flex-col ml-6">
                        <span class="font-heading text-base font-medium text-muted-800 dark:text-muted-100">
                          Bank transfer
                        </span>
                      </span>
                      <span class="flex flex-col ml-auto">
                        <iconify-icon class="iconify w-5 h-5 flex items-center justify-center text-xl text-muted-400" icon="lucide:arrow-right"></iconify-icon>
                      </span>
                    </button>
                    <!--Item-->
                    <button type="button" class="group w-full flex items-center py-4 px-6 bg-white dark:bg-muted-1000 border-2 border-muted-200 dark:border-muted-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-xl hover:shadow-muted-400/10 dark:shadow-muted-800/10 hover:-translate-x-0.5 rounded-xl cursor-pointer transition-all duration-300 tw-accessibility" @click="nextStep(), paymentMethod = 'link'">
                      <span class="flex items-center justify-center w-12 h-12 text-muted-600 dark:text-muted-400 bg-muted-100 dark:bg-muted-900 group-hover:bg-primary-500 group-hover:text-white group-hover:rotate-180 rounded-full transition-all duration-300">
                        <iconify-icon class="iconify w-6 h-6 flex items-center justify-center text-2xl" icon="ph:link-duotone"></iconify-icon>
                      </span>
                      <span class="flex flex-col ml-6">
                        <span class="font-heading text-base font-medium text-muted-800 dark:text-muted-100">
                          Payment link
                        </span>
                      </span>
                      <span class="flex flex-col ml-auto">
                        <iconify-icon class="iconify w-5 h-5 flex items-center justify-center text-xl text-muted-400" icon="lucide:arrow-right"></iconify-icon>
                      </span>
                    </button>
                    <!--Item-->
                    <button type="button" class="group w-full flex items-center py-4 px-6 bg-white dark:bg-muted-1000 border-2 border-muted-200 dark:border-muted-800 hover:border-primary-500 dark:hover:border-primary-500 hover:shadow-xl hover:shadow-muted-400/10 dark:shadow-muted-800/10 hover:-translate-x-0.5 rounded-xl cursor-pointer transition-all duration-300 tw-accessibility" @click="nextStep(), paymentMethod = 'wire'">
                      <span class="flex items-center justify-center w-12 h-12 text-muted-600 dark:text-muted-400 bg-muted-100 dark:bg-muted-900 group-hover:bg-primary-500 group-hover:text-white group-hover:rotate-180 rounded-full transition-all duration-300">
                        <iconify-icon class="iconify w-6 h-6 flex items-center justify-center text-2xl" icon="ph:note-duotone"></iconify-icon>
                      </span>
                      <span class="flex flex-col ml-6">
                        <span class="font-heading text-base font-medium text-muted-800 dark:text-muted-100">
                          Wire
                        </span>
                      </span>
                      <span class="flex flex-col ml-auto">
                        <iconify-icon class="iconify w-5 h-5 flex items-center justify-center text-xl text-muted-400" icon="lucide:arrow-right"></iconify-icon>
                      </span>
                    </button>
                  </div>
                </div>



                </div>



                {{-- fin image --}}
              </div>
      
              <!--Tab 2-->
              <div
                x-show="activeTab === 'tab-2'"
                class="py-6 space-y-10 divide-muted-200 dark:divide-muted-800"
              >
                <!--Statements-->
                <div class="grid md:grid-cols-12 gap-8">
                  <!--Column-->
                  <div class="md:col-span-4">
                    <h3
                      class="font-heading font-medium mb-1 text-muted-800 dark:text-muted-100"
                    >
                      Account
                    </h3>
                    <p class="font-heading text-sm text-muted-500 dark:text-muted-400">
                      Set a unique password to protect your account. Don't forget to change it
                      from time to time.
                    </p>
                  </div>
                  <!--Column-->
                  <div class="md:col-span-8">
                    <h3
                      class="
                        font-heading
                        text-xs
                        pb-4
                        px-4
                        border-b border-muted-200
                        dark:border-muted-800
                        text-muted-800
                        dark:text-muted-100
                      "
                    >
                      Account info
                    </h3>
                    <div
                      class="flex flex-col divide-y divide-muted-200 dark:divide-muted-800"
                    >
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <div>
                            <h3 class="font-heading text-xs text-muted-400">Password</h3>
                            <span>Change password</span>
                          </div>
              
                          <iconify-icon class="iconify w-4 h-4 flex items-center justify-center" icon="lucide:edit"></iconify-icon>
                          <span
                            class="
                              font-medium
                              text-primary-500
                              opacity-0
                              group-hover:opacity-100
                              transition-opacity
                              duration-300
                            "
                          >
                            Edit
                          </span>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <div>
                            <h3 class="font-heading text-xs text-muted-400">Backup codes</h3>
                            <span>Generate codes</span>
                          </div>
              
                          <iconify-icon class="iconify w-4 h-4 flex items-center justify-center" icon="lucide:edit"></iconify-icon>
                          <span
                            class="
                              font-medium
                              text-primary-500
                              opacity-0
                              group-hover:opacity-100
                              transition-opacity
                              duration-300
                            "
                          >
                            Edit
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              
                <!--2 Factor-->
                <div class="grid md:grid-cols-12 gap-8">
                  <!--Column-->
                  <div class="md:col-span-4">
                    <h3
                      class="font-heading font-medium mb-1 text-muted-800 dark:text-muted-100"
                    >
                      2 Factor
                    </h3>
                    <p class="font-heading text-sm text-muted-500 dark:text-muted-400">
                      Reset or edit the authentication method (e.g. Authy or Google
                      Authenticator) for this account.
                    </p>
                  </div>
                  <!--Column-->
                  <div class="md:col-span-8">
                    <h3
                      class="
                        font-heading
                        text-xs
                        pb-4
                        px-4
                        border-b border-muted-200
                        dark:border-muted-800
                        text-muted-800
                        dark:text-muted-100
                      "
                    >
                      2 Factor
                    </h3>
                    <div
                      class="flex flex-col divide-y divide-muted-200 dark:divide-muted-800"
                    >
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <div>
                            <h3 class="font-heading text-xs text-muted-400">Setup</h3>
                            <span>Setup 2 factor</span>
                          </div>
              
                          <iconify-icon class="iconify w-4 h-4 flex items-center justify-center" icon="lucide:edit"></iconify-icon>
                          <span
                            class="
                              font-medium
                              text-primary-500
                              opacity-0
                              group-hover:opacity-100
                              transition-opacity
                              duration-300
                            "
                          >
                            Edit
                          </span>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <div>
                            <h3 class="font-heading text-xs text-muted-400">Key</h3>
                            <span>Setup key</span>
                          </div>
              
                          <iconify-icon class="iconify w-4 h-4 flex items-center justify-center" icon="lucide:edit"></iconify-icon>
                          <span
                            class="
                              font-medium
                              text-primary-500
                              opacity-0
                              group-hover:opacity-100
                              transition-opacity
                              duration-300
                            "
                          >
                            Edit
                          </span>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <div>
                            <h3 class="font-heading text-xs text-muted-400">Phone</h3>
                            <span>Phone number</span>
                          </div>
              
                          <iconify-icon class="iconify w-4 h-4 flex items-center justify-center" icon="lucide:edit"></iconify-icon>
                          <span
                            class="
                              font-medium
                              text-primary-500
                              opacity-0
                              group-hover:opacity-100
                              transition-opacity
                              duration-300
                            "
                          >
                            Edit
                          </span>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              
                <!--Notifications-->
                <div class="grid md:grid-cols-12 gap-8">
                  <!--Column-->
                  <div class="md:col-span-4">
                    <h3
                      class="font-heading font-medium mb-1 text-muted-800 dark:text-muted-100"
                    >
                      Security notifications
                    </h3>
                    <p class="font-heading text-sm text-muted-500 dark:text-muted-400">
                      Some notifications are essential when looking at your account security.
                      Stay safe!
                    </p>
                  </div>
                  <!--Column-->
                  <div class="md:col-span-8">
                    <h3
                      class="
                        font-heading
                        text-xs
                        pb-4
                        px-4
                        border-b border-muted-200
                        dark:border-muted-800
                        text-muted-800
                        dark:text-muted-100
                      "
                    >
                      Notifications
                    </h3>
                    <div
                      class="flex flex-col divide-y divide-muted-200 dark:divide-muted-800"
                    >
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-7"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-7"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                                checked
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Session</h3>
                              <span>New session started</span>
                            </div>
                          </label>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-8"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-8"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                                checked
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Password</h3>
                              <span>Password change</span>
                            </div>
                          </label>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
      
              <!--Tab 3-->
              <div
                x-show="activeTab === 'tab-3'"
                class="py-6 space-y-10 divide-muted-200 dark:divide-muted-800"
              >
                <!--Statements-->
                <div class="grid md:grid-cols-12 gap-8">
                  <!--Column-->
                  <div class="md:col-span-4">
                    <h3
                      class="font-heading font-medium mb-1 text-muted-800 dark:text-muted-100"
                    >
                      Account activity
                    </h3>
                    <p class="font-heading text-sm text-muted-500 dark:text-muted-400">
                      Customize what email notifications you want to receive about
                      transactions on your account.
                    </p>
                  </div>
                  <!--Column-->
                  <div class="md:col-span-8">
                    <h3
                      class="
                        font-heading
                        text-xs
                        pb-4
                        px-4
                        border-b border-muted-200
                        dark:border-muted-800
                        text-muted-800
                        dark:text-muted-100
                      "
                    >
                      Activity
                    </h3>
                    <div
                      class="flex flex-col divide-y divide-muted-200 dark:divide-muted-800"
                    >
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-2"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-2"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                                checked
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Incoming</h3>
                              <span>Incoming transactions</span>
                            </div>
                          </label>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-1"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-1"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Outgoing</h3>
                              <span>Outgoing transactions</span>
                            </div>
                          </label>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-3"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-3"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Failed</h3>
                              <span>Failed transactions</span>
                            </div>
                          </label>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-4"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-4"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Uncashed</h3>
                              <span>Uncashed cheques</span>
                            </div>
                          </label>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-5"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-5"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                                checked
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Payments</h3>
                              <span>Payment requests</span>
                            </div>
                          </label>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              
                <!--Balance-->
                <div class="grid md:grid-cols-12 gap-8">
                  <!--Column-->
                  <div class="md:col-span-4">
                    <h3
                      class="font-heading font-medium mb-1 text-muted-800 dark:text-muted-100"
                    >
                      Low balance
                    </h3>
                    <p class="font-heading text-sm text-muted-500 dark:text-muted-400">
                      We’ll email you when the balance on one of your accounts drops below the
                      amount you set in your account.
                    </p>
                  </div>
                  <!--Column-->
                  <div class="md:col-span-8">
                    <h3
                      class="
                        font-heading
                        text-xs
                        pb-4
                        px-4
                        border-b border-muted-200
                        dark:border-muted-800
                        text-muted-800
                        dark:text-muted-100
                      "
                    >
                      Low balance
                    </h3>
                    <div
                      class="flex flex-col divide-y divide-muted-200 dark:divide-muted-800"
                    >
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-6"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-6"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                                checked
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Low alert</h3>
                              <span>Balance drops under $200.00</span>
                            </div>
                          </label>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              
                <!--Updates-->
                <div class="grid md:grid-cols-12 gap-8">
                  <!--Column-->
                  <div class="md:col-span-4">
                    <h3
                      class="font-heading font-medium mb-1 text-muted-800 dark:text-muted-100"
                    >
                      Apollo updates
                    </h3>
                    <p class="font-heading text-sm text-muted-500 dark:text-muted-400">
                      Stay up to date on cool new product features or events you might be
                      interested in.
                    </p>
                  </div>
                  <!--Column-->
                  <div class="md:col-span-8">
                    <h3
                      class="
                        font-heading
                        text-xs
                        pb-4
                        px-4
                        border-b border-muted-200
                        dark:border-muted-800
                        text-muted-800
                        dark:text-muted-100
                      "
                    >
                      Updates
                    </h3>
                    <div
                      class="flex flex-col divide-y divide-muted-200 dark:divide-muted-800"
                    >
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-7"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-7"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                                checked
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Features</h3>
                              <span>New feature</span>
                            </div>
                          </label>
                        </a>
                      </div>
                      <!--Item-->
                      <div class="group">
                        <a
                          href="#"
                          class="
                            flex
                            items-center
                            gap-4
                            font-heading
                            text-sm
                            p-4
                            text-muted-600
                            dark:text-muted-400
                            hover:bg-muted-100
                            dark:hover:bg-muted-800
                            transition-colors
                            duration-300
                          "
                        >
                          <label
                            for="switch-thin-8"
                            class="flex items-center gap-4 cursor-pointer"
                          >
                            <span class="block relative h-4">
                              <input
                                id="switch-thin-8"
                                type="checkbox"
                                class="
                                  peer
                                  cursor-pointer
                                  opacity-0
                                  absolute
                                  h-full
                                  w-full
                                  z-20
                                "
                                checked
                              />
                              <span
                                class="
                                  absolute
                                  flex
                                  items-center
                                  justify-center
                                  w-6
                                  h-6
                                  border
                                  bg-white
                                  dark:bg-slate-700 dark:border-slate-600
                                  rounded-full
                                  shadow
                                  -left-1
                                  top-1/2
                                  -translate-y-1/2
                                  transition
                                  peer-checked:-translate-y-1/2 peer-checked:translate-x-full
                                "
                              ></span>
                              <span
                                class="
                                  block
                                  w-10
                                  h-4
                                  bg-slate-300
                                  dark:bg-slate-600
                                  rounded-full
                                  shadow-inner
                                  peer-checked:bg-violet-400
                                  peer-focus:ring-0
                                  outline-1 outline-transparent
                                  peer-focus:outline-dashed peer-focus:outline-gray-300
                                  dark:peer-focus:outline-gray-600
                                  peer-focus:outline-offset-2
                                  transition-all
                                  duration-300
                                "
                              ></span>
                            </span>
                            <div>
                              <h3 class="font-heading text-xs text-muted-400">Offers</h3>
                              <span>Special offers</span>
                            </div>
                          </label>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
      
              <!--Tab 4-->
              <div x-show="activeTab === 'tab-4'">
                <!--Placeholder-->
                <div class="px-8 py-12 text-center bg-muted-100 dark:bg-muted-1000">
                  <div class="w-full max-w-lg mx-auto space-y-3">
                    <h3 class="font-heading text-lg text-muted-800 dark:text-white">
                      You currently have no API tokens
                    </h3>
                    <p class="font-sans text-muted-500">
                      If you need to connect to the Apollo backend, you can create an API
                      token to be able to connect to our services from an external app.
                    </p>
              
                    <div class="flex items-center justify-center">
                      <button
                        type="button"
                        class="
                          h-10
                          w-40
                          inline-flex
                          justify-center
                          items-center
                          gap-x-2
                          px-6
                          py-2
                          font-sans
                          text-sm text-white
                          bg-primary-500
                          rounded-full
                          shadow-lg shadow-primary-500/20
                          hover:shadow-xl
                          tw-accessibility
                          transition-all
                          duration-300
                        "
                      >
                        <span>Create token</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!--End Layout-->
    </main>

    
  </body>
</html>
