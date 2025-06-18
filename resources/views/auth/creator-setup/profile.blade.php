@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Configuration de votre profil créateur</h1>
        
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
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('creator.setup.profile.save') }}">
                @csrf
                
                <div class="mb-4">
                    <label for="gaming_pseudo" class="block text-sm font-medium text-gray-700 mb-1">
                        Pseudo de gaming <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="gaming_pseudo" 
                        name="gaming_pseudo" 
                        value="{{ old('gaming_pseudo', $creator->gaming_pseudo) }}" 
                        class="form-input w-full @error('gaming_pseudo') border-red-500 @enderror" 
                        required
                    >
                    @error('gaming_pseudo')
                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Ce pseudo sera visible par les clients et utilisé pour votre URL publique.</p>
                </div>
                
                <div class="mb-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                        Biographie <span class="text-red-500">*</span>
                    </label>
                    <textarea 
                        id="bio" 
                        name="bio" 
                        rows="4" 
                        class="form-textarea w-full @error('bio') border-red-500 @enderror" 
                        required
                    >{{ old('bio', $creator->bio) }}</textarea>
                    @error('bio')
                        <div class="text-red-500 mt-1 text-sm">{{ $message }}</div>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Décrivez votre expérience, vos compétences et ce que vous pouvez offrir aux clients.</p>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn bg-violet-500 hover:bg-violet-600 text-white">
                        Terminer la configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
