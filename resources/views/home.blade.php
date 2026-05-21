<x-guest-layout>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
            Welcome to SkillSwap
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-400 max-w">
            Connect with other users to offer or request skills. Log in to start swapping.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white dark:bg-gray-900 py-8 px-6 shadow rounded-lg sm:px-10">
            <div class="space-y-4">
                <a href="{{ route('login') }}" class="w-full inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                    {{ __('Log in') }}
                </a>
                <a href="{{ route('register') }}" class="w-full inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    {{ __('Register') }}
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
