<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('My Swaps') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            @if (session('success'))
                <div class="p-4 bg-green-500/10 border border-green-500/50 text-green-400 rounded-xl shadow-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-4 bg-red-500/10 border border-red-500/50 text-red-400 rounded-xl shadow-sm">{{ session('error') }}</div>
            @endif

            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-700 pb-3">Incoming Requests</h3>
                
                @if($incomingRequests->isEmpty())
                    <div class="p-6 bg-gray-800/50 border border-gray-700 rounded-xl text-gray-400 text-sm">No pending requests at the moment.</div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($incomingRequests as $request)
                            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-sm flex flex-col justify-between hover:border-teal-500/40 transition-colors duration-300">
                                <div>
                                    <div class="flex gap-2 mb-3">
                                        <span class="px-2 py-1 text-[10px] font-bold text-yellow-400 bg-yellow-400/10 rounded-full tracking-wider uppercase">Needs Action</span>
                                    </div>
                                    <h4 class="text-lg font-bold text-white mb-2">{{ $request->skill->title }}</h4>
                                </div>

                                <div class="border-t border-gray-700 my-4"></div>

                                <div class="flex items-center gap-3 mb-5">
                                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold text-xs uppercase shadow-inner">
                                        {{ substr($request->receiver->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $request->receiver->name }}</p>
                                        <p class="text-xs text-gray-400">Wants to swap with you</p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 mt-auto">
                                    <form method="POST" action="{{ route('transactions.update', $request) }}" class="flex flex-col gap-3">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        
                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="text-[10px] text-gray-400 uppercase tracking-wider mb-1 block">Start Time:</label>
                                                <input type="datetime-local" name="scheduled_date" required class="bg-gray-900 text-gray-300 border border-gray-700 rounded-lg text-xs p-2 w-full focus:ring-teal-500 focus:border-teal-500">
                                            </div>
                                            <div>
                                                <label class="text-[10px] text-gray-400 uppercase tracking-wider mb-1 block">End Time:</label>
                                                <input type="datetime-local" name="scheduled_end_date" class="bg-gray-900 text-gray-300 border border-gray-700 rounded-lg text-xs p-2 w-full focus:ring-teal-500 focus:border-teal-500">
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="w-full bg-teal-500 hover:bg-teal-400 text-white font-bold py-2.5 rounded-lg text-sm transition shadow-[0_0_15px_rgba(20,184,166,0.15)] hover:shadow-[0_0_20px_rgba(20,184,166,0.3)]">Accept & Sync</button>
                                    </form>

                                    <form method="POST" action="{{ route('transactions.update', $request) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="w-full bg-transparent hover:bg-red-500/10 text-gray-400 hover:text-red-400 border border-gray-600 hover:border-red-500/50 font-semibold py-2 rounded-lg text-sm transition">Decline</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-700 pb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Upcoming Scheduled Meetings
                </h3>
                
                @if($scheduledSwaps->isEmpty())
                    <div class="p-6 bg-gray-800/50 border border-gray-700 rounded-xl text-gray-400 text-sm">No upcoming swaps scheduled.</div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($scheduledSwaps as $swap)
                            @php $partner = Auth::id() === $swap->provider_id ? $swap->receiver : $swap->provider; @endphp
                            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-sm flex flex-col justify-between relative overflow-hidden">
                                <div class="absolute top-0 left-0 w-full h-1 bg-teal-500"></div>
                                
                                <div>
                                    <div class="flex gap-2 mb-3">
                                        <span class="px-2 py-1 text-[10px] font-bold text-teal-400 bg-teal-400/10 rounded-full tracking-wider uppercase">Confirmed</span>
                                    </div>
                                    <h4 class="text-lg font-bold text-white mb-2">{{ $swap->skill->title }}</h4>
                                </div>

                                <div class="border-t border-gray-700 my-4"></div>

                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold text-xs uppercase shadow-inner">
                                        {{ substr($partner->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $partner->name }}</p>
                                        <p class="text-xs text-gray-400">Meeting Partner</p>
                                    </div>
                                </div>

                                <div class="bg-gray-900/50 rounded-lg p-3 border border-gray-700 mt-auto">
                                    <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Scheduled For:</p>
                                    <p class="text-sm font-medium text-white">{{ \Carbon\Carbon::parse($swap->scheduled_date)->format('F j, Y @ g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 border-b border-gray-700 pb-3">My Sent Requests</h3>
                
                @if($myRequests->isEmpty())
                    <div class="p-6 bg-gray-800/50 border border-gray-700 rounded-xl text-gray-400 text-sm">You haven't requested any swaps yet.</div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($myRequests as $request)
                            <div class="bg-gray-800 rounded-xl p-6 border border-gray-700 shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex gap-2 mb-3">
                                        <span class="px-2 py-1 text-[10px] font-bold rounded-full tracking-wider uppercase
                                            {{ $request->status === 'pending' ? 'bg-yellow-400/10 text-yellow-400' : '' }}
                                            {{ $request->status === 'accepted' ? 'bg-teal-400/10 text-teal-400' : '' }}
                                            {{ $request->status === 'rejected' ? 'bg-red-400/10 text-red-400' : '' }}">
                                            {{ $request->status }}
                                        </span>
                                    </div>
                                    <h4 class="text-lg font-bold text-white mb-2">{{ $request->skill->title }}</h4>
                                </div>

                                <div class="border-t border-gray-700 my-4"></div>

                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold text-xs uppercase shadow-inner">
                                        {{ substr($request->provider->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $request->provider->name }}</p>
                                        <p class="text-xs text-gray-400">Provider</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>