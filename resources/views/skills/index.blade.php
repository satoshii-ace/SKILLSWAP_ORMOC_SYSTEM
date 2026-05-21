<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Skills') }}
            </h2>
            <a href="{{ route('skills.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                {{ __('Add Skill') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if ($skills->count())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($skills as $skill)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                                {{ $skill->title }}
                            </h3>
                            
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ Str::limit($skill->description, 100) }}
                            </p>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="inline-block bg-blue-200 text-blue-800 text-xs px-2 py-1 rounded">
                                        {{ $skill->category }}
                                    </span>
                                    <span class="inline-block {{ $skill->type === 'offered' ? 'bg-green-200 text-green-800' : 'bg-yellow-200 text-yellow-800' }} text-xs px-2 py-1 rounded">
                                        {{ ucfirst($skill->type) }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                <p class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $skill->user->name }}
                                </p>
                                <p class="text-xs">
                                    {{ $skill->user->email }}
                                </p>
                            </div>

                            <div class="mt-4 text-xs text-gray-400">
                                Posted {{ $skill->created_at->diffForHumans() }}
                            </div>

                            @if (Auth::id() !== $skill->user_id)
                                <form method="POST" action="{{ route('transactions.store') }}" class="mt-4">
                                    @csrf
                                    <input type="hidden" name="skill_id" value="{{ $skill->id }}">
                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        {{ __('Request Swap') }}
                                    </button>
                                </form>
                            @else
                                <div class="mt-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('Your skill') }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $skills->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('No skills found. ') }}
                        <a href="{{ route('skills.create') }}" class="text-blue-500 hover:text-blue-700">
                            {{ __('Create one now!') }}
                        </a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
