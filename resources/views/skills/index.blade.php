<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Explore Skills') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ search: '', category: 'All' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 flex flex-col md:flex-row gap-4">
                <input x-model="search" type="text" placeholder="Search skills by title..." 
                       class="flex-1 bg-gray-900 border-gray-700 rounded-lg text-white text-sm focus:ring-blue-500">
                
                <select x-model="category" class="bg-gray-900 border-gray-700 rounded-lg text-white text-sm focus:ring-blue-500">
                    <option value="All">All Categories</option>
                    @foreach($skills->unique('category') as $s)
                        <option value="{{ $s->category }}">{{ $s->category }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($skills as $skill)
                    <div x-show="(search === '' || '{{ strtolower($skill->title) }}'.includes(search.toLowerCase())) && (category === 'All' || '{{ $skill->category }}' === category)"
                         class="bg-white dark:bg-gray-800 shadow-md rounded-xl p-6 border border-gray-700 flex flex-col h-full transition-all duration-300 hover:shadow-xl hover:border-blue-500/30">
                        
                        <div class="flex gap-2 mb-4">
                            <span class="bg-blue-900/30 text-blue-400 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">
                                {{ $skill->category }}
                            </span>
                            <span class="{{ $skill->type === 'offered' ? 'bg-green-900/30 text-green-400' : 'bg-amber-900/30 text-amber-400' }} text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wider">
                                {{ ucfirst($skill->type) }}
                            </span>
                        </div>

                        <h3 class="font-bold text-lg text-gray-100 mb-2">{{ $skill->title }}</h3>
                        
                        <p class="text-sm text-gray-400 flex-grow mb-4 break-words">
                            {{ Str::limit($skill->description, 120) }}
                        </p>

                        <div class="border-t border-gray-700 pt-4 mt-auto">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-xs font-bold text-white">
                                    {{ substr($skill->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-gray-200">{{ $skill->user->name }}</p>
                                    <p class="text-[10px] text-gray-500">{{ $skill->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>

                        @if (Auth::id() !== $skill->user_id)
                            <form method="POST" action="{{ route('transactions.store') }}" class="mt-6">
                                @csrf
                                <input type="hidden" name="skill_id" value="{{ $skill->id }}">
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-lg transition text-sm">
                                    Request Swap
                                </button>
                            </form>
                        @else
                            <button disabled class="mt-6 w-full bg-gray-700 text-gray-500 font-bold py-2 rounded-lg text-sm cursor-not-allowed">
                                Your Skill
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>