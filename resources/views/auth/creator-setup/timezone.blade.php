@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Configuration de votre fuseau horaire</h1>
        
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
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <p class="mb-6 text-gray-600">
                Pour vous offrir la meilleure expérience possible, nous avons besoin de connaître votre fuseau horaire. 
                Cela nous permettra de synchroniser correctement vos disponibilités avec celles de vos clients.
            </p>
            
            <form method="POST" action="{{ route('creator.setup.timezone.save') }}">
                @csrf
                
                <div class="mb-6">
                    <label for="timezone" class="block text-sm font-medium text-gray-700 mb-1">
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
                                    <option value="{{ $value }}" {{ old('timezone') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('timezone')
                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Ce fuseau horaire sera utilisé pour afficher correctement vos disponibilités.</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                        Continuer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
