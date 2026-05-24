<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create New Skill') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            @if (session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/50 text-red-400 rounded-xl shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 bg-green-500/10 border border-green-500/50 text-green-400 rounded-xl shadow-md">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gray-800 border border-gray-700 overflow-hidden shadow-md sm:rounded-xl p-8">
                <form method="POST" action="{{ route('skills.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="title" class="block text-sm font-bold text-gray-300 uppercase tracking-wider mb-2">
                            {{ __('Title') }}
                        </label>
                        <input type="text" name="title" id="title" 
                            class="mt-1 block w-full rounded-lg bg-gray-900 text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-colors @error('title') border-red-500 @else border-gray-700 @endif"
                            value="{{ old('title') }}"
                            placeholder="e.g., Web Development"
                            required>
                        @error('title')
                            <p class="mt-2 text-xs text-red-400 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-300 uppercase tracking-wider mb-2">
                            {{ __('Description') }}
                        </label>
                        <textarea name="description" id="description" rows="5"
                            class="mt-1 block w-full rounded-lg bg-gray-900 text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-colors @error('description') border-red-500 @else border-gray-700 @endif"
                            placeholder="Describe your skill in detail..."
                            required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-xs text-red-400 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="category" class="block text-sm font-bold text-gray-300 uppercase tracking-wider mb-2">
                                {{ __('Category') }}
                            </label>
                            <select name="category" id="category"
                                class="mt-1 block w-full rounded-lg bg-gray-900 text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-colors @error('category') border-red-500 @else border-gray-700 @endif"
                                required>
                                <option value="">{{ __('Select a category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(old('category') === $category)>
                                        {{ $category }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-2 text-xs text-red-400 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-bold text-gray-300 uppercase tracking-wider mb-2">
                                {{ __('Type') }}
                            </label>
                            <select name="type" id="type"
                                class="mt-1 block w-full rounded-lg bg-gray-900 text-white shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm transition-colors @error('type') border-red-500 @else border-gray-700 @endif"
                                required>
                                <option value="">{{ __('Select type') }}</option>
                                @foreach ($types as $typeOption)
                                    <option value="{{ $typeOption }}" @selected(old('type') === $typeOption)>
                                        {{ ucfirst($typeOption) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-2 text-xs text-red-400 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-gray-700 flex flex-row-reverse items-center justify-start gap-4">
                        <button type="submit" class="bg-teal-500 hover:bg-teal-400 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-[0_0_15px_rgba(20,184,166,0.15)] hover:shadow-[0_0_20px_rgba(20,184,166,0.3)]">
                            {{ __('Create Skill') }}
                        </button>
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-gray-400 hover:text-white transition">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>