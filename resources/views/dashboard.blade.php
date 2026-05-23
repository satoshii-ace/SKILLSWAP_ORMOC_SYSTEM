<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Overview & Management') }}
        </h2>
    </x-slot>

    <div x-data="{ isModalOpen: false, deleteUrl: '', skillName: '' }" class="py-12 relative">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            @if (session('success'))
                <div class="p-4 bg-green-900/50 border border-green-500 text-green-400 rounded-xl shadow-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-900/50 border border-red-500 text-red-400 rounded-xl shadow-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-6 shadow-md">
                    <h3 class="text-gray-400 text-sm font-bold uppercase tracking-wider">Total Skills Posted</h3>
                    <p class="text-3xl font-bold text-white mt-2">{{ $mySkills->count() }}</p>
                </div>

                <div class="bg-gradient-to-br from-gray-800 to-gray-900 border border-orange-500/30 rounded-xl p-6 shadow-md relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-orange-500/10 pointer-events-none">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"></path></svg>
                    </div>

                    <div class="relative z-10">
                        <h3 class="text-orange-400 text-sm font-bold uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03z" clip-rule="evenodd"></path></svg>
                            Swap Streak
                        </h3>
                        
                        <p class="text-3xl font-bold text-white mt-1 flex items-baseline gap-1">
                            3 <span class="text-sm text-gray-400 font-normal">Weeks</span>
                        </p>
                        
                        <p class="text-xs text-gray-500 mt-2">
                            Complete a swap this week to keep it alive!
                        </p>
                    </div>
                </div>

                <div class="bg-blue-900/20 border border-blue-500/30 rounded-xl p-6 shadow-md flex flex-col justify-center items-start">
                    <h3 class="text-blue-400 text-sm font-bold uppercase tracking-wider mb-2">Google Calendar</h3>
                    <a href="{{ route('profile.edit') }}" class="text-white font-bold hover:text-blue-300 transition flex items-center gap-2">
                        Manage Connection &rarr;
                    </a>
                </div>
            </div>

            <div class="bg-gray-800 border border-gray-700 rounded-xl shadow-md overflow-hidden relative">
                <div class="p-6 border-b border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Manage Your Skills</h3>
                    <a href="{{ route('skills.create') }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                        + Add New Skill
                    </a>
                </div>

                @if($mySkills->count() > 0)
                    <div class="divide-y divide-gray-700">
                        @foreach($mySkills as $skill)
                            <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center hover:bg-gray-700/30 transition gap-4">
                                
                                <div class="min-w-0 flex-1 w-full">
                                    <div class="flex items-center gap-3 mb-1">
                                        <h4 class="text-md font-bold text-gray-100 truncate">{{ $skill->title }}</h4>
                                        <span class="bg-blue-900/30 text-blue-400 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase whitespace-nowrap shrink-0">
                                            {{ $skill->category }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-400">Posted {{ $skill->created_at->diffForHumans() }}</p>
                                </div>

                                <div class="flex flex-row items-center gap-3 w-full sm:w-auto shrink-0">
                                    <a href="{{ route('skills.edit', $skill->id) }}" class="flex-1 sm:flex-none text-center bg-gray-700 hover:bg-gray-600 text-white text-sm font-bold py-2 px-4 rounded-lg transition">
                                        Edit
                                    </a>

                                    <button @click="isModalOpen = true; deleteUrl = '{{ route('skills.destroy', $skill->id) }}'; skillName = '{{ addslashes($skill->title) }}'" 
                                            type="button" 
                                            class="flex-1 sm:flex-none bg-red-600/20 hover:bg-red-600 border border-red-500/30 hover:border-red-600 text-red-400 hover:text-white text-sm font-bold py-2 px-4 rounded-lg transition">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center">
                        <p class="text-gray-400 mb-4">You haven't posted any skills yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <div x-show="isModalOpen" 
             style="display: none;" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0 overflow-y-auto"
             aria-labelledby="modal-title" role="dialog" aria-modal="true">
            
            <div x-show="isModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" 
                 @click="isModalOpen = false"></div>

            <div x-show="isModalOpen"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative z-[101] bg-gray-800 border border-gray-700 rounded-xl shadow-2xl p-6 w-full max-w-lg transform transition-all text-left">
                
                <div class="flex items-start">
                    <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-0 ml-4 text-left w-full">
                        <h3 class="text-lg leading-6 font-bold text-white" id="modal-title">
                            Delete '<span x-text="skillName"></span>'
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-400">
                                Are you sure you want to delete this skill? It will be permanently removed from the marketplace. This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3 w-full">
                    <button @click="isModalOpen = false" type="button" class="w-full sm:w-auto px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-sm font-medium text-gray-300 hover:bg-gray-600 hover:text-white transition text-center">
                        Cancel
                    </button>
                    <form method="POST" :action="deleteUrl" class="m-0 w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-red-600 rounded-lg text-sm font-medium text-white hover:bg-red-700 transition flex justify-center items-center">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>