<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Swaps') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="p-4 bg-green-500/10 border border-green-500 text-green-500 rounded-md">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500 text-red-500 rounded-md">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">Incoming Requests (Needs Action)</h3>
                
                @if($incomingRequests->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No pending incoming requests.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($incomingRequests as $request)
                            <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <strong class="text-white">{{ $request->receiver->name }}</strong> wants your skill:
                                </p>
                                <p class="text-lg font-bold text-blue-500 mb-4">{{ $request->skill->title }}</p>

                                <div class="flex flex-col gap-3">
                                    <form method="POST" action="{{ route('transactions.update', $request) }}" class="flex flex-col gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <label class="text-xs text-gray-400 uppercase tracking-wider">Schedule Meeting (Google Calendar):</label>
                                        <input type="datetime-local" name="scheduled_date" required class="bg-gray-800 text-white border border-gray-600 rounded text-sm p-2 w-full">
                                        
                                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded text-sm transition">
                                            Accept & Schedule
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('transactions.update', $request) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-500 border border-red-500 font-bold py-2 px-4 rounded text-sm transition">
                                            Decline Swap
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border border-green-500/30">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Upcoming Scheduled Meetings
                </h3>
                
                @if($scheduledSwaps->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">No upcoming swaps scheduled.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($scheduledSwaps as $swap)
                            <div class="bg-green-500/5 border border-green-500/20 p-4 rounded-lg relative overflow-hidden">
                                <div class="absolute top-0 right-0 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded-bl-lg uppercase tracking-wider">
                                    Confirmed
                                </div>
                                <p class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $swap->skill->title }}</p>
                                
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-3 space-y-1">
                                    <p><strong class="text-gray-500">Meeting with:</strong> 
                                        {{ Auth::id() === $swap->provider_id ? $swap->receiver->name : $swap->provider->name }}
                                    </p>
                                    <p><strong class="text-gray-500">Date & Time:</strong> 
                                        {{ \Carbon\Carbon::parse($swap->scheduled_date)->format('F j, Y @ g:i A') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b pb-2 dark:border-gray-700">My Sent Requests</h3>
                
                @if($myRequests->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm">You haven't requested any swaps yet.</p>
                @else
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($myRequests as $request)
                            <li class="py-3 flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->skill->title }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Provider: {{ $request->provider->name }}</p>
                                </div>
                                <div>
                                    <span class="px-3 py-1 text-xs rounded-full font-bold uppercase tracking-wider
                                        {{ $request->status === 'pending' ? 'bg-yellow-500/20 text-yellow-500' : '' }}
                                        {{ $request->status === 'accepted' ? 'bg-green-500/20 text-green-500' : '' }}
                                        {{ $request->status === 'rejected' ? 'bg-red-500/20 text-red-500' : '' }}">
                                        {{ $request->status }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>