<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="p-4 sm:p-8 bg-gray-800 border border-gray-700 shadow-md sm:rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 border border-gray-700 shadow-md sm:rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 border border-gray-700 shadow-md sm:rounded-xl">
                <div class="max-w-xl">
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-teal-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12.006 11.26v2.36h3.402c-.141 1.01-.84 2.87-3.402 2.87-2.046 0-3.722-1.69-3.722-3.77s1.676-3.77 3.722-3.77c1.171 0 2.144.44 2.843 1.11l1.85-1.85C15.485 7.02 13.916 6.26 12.006 6.26 8.522 6.26 5.7 9.08 5.7 12.56s2.822 6.3 6.306 6.3c3.636 0 6.066-2.56 6.066-6.17 0-.43-.05-.83-.12-1.2h-5.946z"/></svg>
                        Google Calendar Integration
                    </h2>
                    <p class="mt-1 text-sm text-gray-400 mb-6">
                        Connect your Google Calendar so SkillSwap can automatically schedule meetings when you accept a swap request.
                    </p>

                    @if(Auth::user()->google_access_token)
                        <span class="inline-flex items-center px-4 py-2 bg-teal-900/30 border border-teal-500/50 text-teal-400 rounded-lg font-bold text-xs uppercase tracking-widest">
                            ✓ Calendar Connected
                        </span>
                    @else
                        <a href="{{ route('google.redirect') }}" class="inline-flex items-center px-6 py-2.5 bg-teal-500 hover:bg-teal-400 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest transition shadow-[0_0_15px_rgba(20,184,166,0.15)] hover:shadow-[0_0_20px_rgba(20,184,166,0.3)]">
                            Connect Google Calendar
                        </a>
                    @endif
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-gray-800 border border-gray-700 shadow-md sm:rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>