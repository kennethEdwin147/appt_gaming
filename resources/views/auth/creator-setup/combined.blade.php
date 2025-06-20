<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Configuration de votre profil créateur</title>
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

        <!-- Content area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <main class="grow">

                <div class="lg:relative lg:flex">

                    <!-- Content -->
                    <div class="px-4 sm:px-6 lg:px-8 py-8 lg:grow lg:pr-8 xl:pr-16 2xl:ml-[80px]">
                        <div class="lg:max-w-[640px] lg:mx-auto">

                            <!-- Combined Setup Form -->
                            <div class="mb-6 lg:mb-0">
                                <header class="mb-6">
                                    <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold mb-2">Configuration de votre profil créateur</h1>
                                    <p>Complétez votre profil pour commencer à créer des événements et interagir avec vos clients.</p>
                                </header>

                                @if(session('success'))
                                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                
                                @if(session('error'))
                                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                        {{ session('error') }}
                                    </div>
                                @endif

                                @if(session('info'))
                                    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                                        {{ session('info') }}
                                    </div>
                                @endif

                                <!-- Combined Form -->
                                <div>
                                    <form method="POST" action="{{ route('creator.setup.combined.save') }}">
                                        @csrf
                                        <div class="space-y-8">
                                            <!-- Timezone Section -->
                                            <div>
                                                <div class="text-gray-800 dark:text-gray-100 font-semibold mb-4">Fuseau horaire</div>
                                                <div class="space-y-4">
                                                    <!-- Timezone Selection -->
                                                    <div>
                                                        <label class="block text-sm font-medium mb-1" for="timezone">
                                                            Fuseau horaire <span class="text-red-500">*</span>
                                                        </label>
                                                        <select 
                                                            id="timezone" 
                                                            name="timezone" 
                                                            class="form-select w-full @error('timezone') border-red-500 @enderror" 
                                                            required
                                                        >
                                                            <option value="">Sélectionnez votre fuseau horaire</option>
                                                            @foreach(\App\Enums\Timezone::getTimezonesByRegion() as $region => $timezones)
                                                                <optgroup label="{{ $region }}">
                                                                    @foreach($timezones as $value => $label)
                                                                        <option value="{{ $value }}" {{ old('timezone', $creator->timezone ?? '') == $value ? 'selected' : '' }}>
                                                                            {{ $label }}
                                                                        </option>
                                                                    @endforeach
                                                                </optgroup>
                                                            @endforeach
                                                        </select>
                                                        @error('timezone')
                                                            <div class="text-red-500 mt-1 text-sm" data-testid="validation-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <hr class="border-gray-200 dark:border-gray-700">

                                            <!-- Profile Section -->
                                            <div>
                                                <div class="text-gray-800 dark:text-gray-100 font-semibold mb-4">Informations du profil</div>
                                                <div class="space-y-4">
                                                    <!-- Gaming Pseudo -->
                                                    <div>
                                                        <label class="block text-sm font-medium mb-1" for="gaming_pseudo">
                                                            Pseudo de gaming <span class="text-red-500">*</span>
                                                        </label>
                                                        <input 
                                                            id="gaming_pseudo" 
                                                            name="gaming_pseudo"
                                                            data-testid="gaming-pseudo-input"
                                                            class="form-input w-full @error('gaming_pseudo') border-red-500 @enderror" 
                                                            type="text" 
                                                            value="{{ old('gaming_pseudo', $creator->gaming_pseudo ?? '') }}" 
                                                            required
                                                        />
                                                        @error('gaming_pseudo')
                                                            <div class="text-red-500 mt-1 text-sm" data-testid="validation-error">{{ $message }}</div>
                                                        @enderror
                                                        <p class="text-gray-500 text-sm mt-1">Ce pseudo sera visible par les clients et utilisé pour votre URL publique.</p>
                                                    </div>

                                                    <!-- Bio -->
                                                    <div>
                                                        <label class="block text-sm font-medium mb-1" for="bio">
                                                            Biographie <span class="text-red-500">*</span>
                                                        </label>
                                                        <textarea 
                                                            id="bio" 
                                                            name="bio" 
                                                            data-testid="bio-input"
                                                            rows="4" 
                                                            class="form-textarea w-full @error('bio') border-red-500 @enderror" 
                                                            required
                                                        >{{ old('bio', $creator->bio ?? '') }}</textarea>
                                                        @error('bio')
                                                            <div class="text-red-500 mt-1 text-sm" data-testid="validation-error">{{ $message }}</div>
                                                        @enderror
                                                        <p class="text-gray-500 text-sm mt-1">Décrivez votre expérience, vos compétences et ce que vous pouvez offrir aux clients.</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <button type="submit" data-testid="complete-setup-button" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                                                    Terminer la configuration
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
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
