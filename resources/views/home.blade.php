<x-guest-layout>
    <div class="sm:mx-auto sm:w-full sm:max-w-md flex flex-col items-center">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-white">
            Welcome to SkillSwap
        </h2>
        <p class="mt-2 text-center text-sm text-gray-400 max-w">
            Connect with other users to offer or request skills. Log in to start swapping.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-gray-800 py-8 px-6 shadow-2xl border border-gray-700 rounded-xl sm:px-10">
            <div class="space-y-4">
                <a href="{{ route('login') }}" class="w-full inline-flex justify-center rounded-lg border border-transparent bg-teal-500 py-2.5 px-4 text-sm font-bold text-white shadow-[0_0_15px_rgba(20,184,166,0.15)] hover:shadow-[0_0_20px_rgba(20,184,166,0.3)] hover:bg-teal-400 transition">
                    {{ __('Log in') }}
                </a>
                <a href="{{ route('register') }}" class="w-full inline-flex justify-center rounded-lg border border-gray-600 bg-gray-700 py-2.5 px-4 text-sm font-bold text-gray-300 hover:bg-gray-600 hover:text-white transition">
                    {{ __('Register') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>