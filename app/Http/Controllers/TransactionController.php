<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Transaction;
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the user's swap requests and schedules.
     */
    public function index()
    {
        $calendarService = new \App\Services\CalendarService();
        $incomingRequests = Transaction::with(['skill', 'receiver'])
            ->where('provider_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->get();

        $scheduledSwaps = Transaction::with(['skill', 'provider', 'receiver'])
            ->where('status', 'accepted')
            ->where(function($query) {
                $query->where('provider_id', Auth::id())
                      ->orWhere('receiver_id', Auth::id());
            })
            ->orderBy('scheduled_date', 'asc')
            ->get();

        $myRequests = Transaction::with(['skill', 'provider'])
            ->where('receiver_id', Auth::id())
            ->whereIn('status', ['pending', 'rejected'])
            ->latest()
            ->get();

        return view('swaps.index', compact('incomingRequests', 'scheduledSwaps', 'myRequests', 'calendarService'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
        ]);

        $skill = Skill::findOrFail($validated['skill_id']);

        if ($skill->user_id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot create a transaction for your own skill.');
        }

        $existingTransaction = Transaction::where('skill_id', $skill->id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($existingTransaction) {
            return redirect()->back()
                ->with('error', 'You already have a pending request for this skill.');
        }

        Transaction::create([
            'skill_id' => $skill->id,
            'provider_id' => $skill->user_id,
            'receiver_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('skills.index')
            ->with('success', 'Swap request sent successfully!');
    }

    /**
     * Update transaction status and create calendar event if accepted.
     */
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        if (Auth::id() !== $transaction->provider_id && Auth::id() !== $transaction->receiver_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // MODIFIED: Validate both start date and end date from the scheduling form
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'scheduled_date' => 'required_if:status,accepted|nullable|date',
            'scheduled_end_date' => 'required_if:status,accepted|nullable|date|after:scheduled_date',
        ]);

        try {
            // Update the transaction model status and start date
            $transaction->update([
                'status' => $validated['status'],
                'scheduled_date' => $validated['scheduled_date'] ?? $transaction->scheduled_date,
            ]);

            if ($validated['status'] === 'accepted' && $transaction->scheduled_date) {
                // Convert start time to string format safely
               

    // Force Laravel to treat the input as Manila time, then convert to RFC3339
    $startDateString = $validated['scheduled_date'];
    $endDateString = $validated['scheduled_end_date'];

                // Capture end time from form request validation array
                $endDateString = $validated['scheduled_end_date'];

                // Trigger calendar generation with explicit boundaries
                try {
                    $this->createCalendarEvent($transaction, $startDateString, $endDateString);
                } catch (\Google\Service\Exception $e) {
                    if ($e->getCode() == 401) {
                        Log::warning('Calendar token invalid for user ' . $transaction->provider_id);
                    } else {
                        Log::error('Google Calendar Sync Error: ' . $e->getMessage());
                    }
                } catch (Exception $e) {
                    Log::error('General Calendar Error: ' . $e->getMessage());
                }
            }

            $statusMessage = ucfirst($validated['status']);
            return redirect()->back()->with('success', "Swap request {$statusMessage}!");
        } catch (Exception $e) {
            Log::error('Transaction Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the transaction.');
        }
    }

    /**
     * Create a Google Calendar event for the swap meeting with exact start and end ranges.
     */
    private function createCalendarEvent(Transaction $transaction, string $startDateString, string $endDateString): void
    {
        try {
            $transaction = $transaction->load(['provider', 'receiver', 'skill']);
            
            if (!$transaction->provider->google_access_token) {
                Log::info('Provider has not authorized Google Calendar access.');
                return;
            }

            $calendarService = new CalendarService();
            $skill = $transaction->skill;
            $attendees = [$transaction->provider->email, $transaction->receiver->email];

            $calendarService->createSkillSwapEvent(
                $transaction->provider,
                "SkillSwap Meeting: {$skill->title}",
                "Swap meeting between {$transaction->receiver->name} and {$transaction->provider->name}.\n\nSkill: {$skill->title}",
                $startDateString,
                $endDateString, // Passed dynamic value directly to the parameter position
                $attendees
            );

            Log::info('Calendar event created for transaction ID: ' . $transaction->id);
        } catch (Exception $e) {
            Log::error('Failed to parse relationships for calendar dispatch: ' . $e->getMessage());
            throw $e;
        }
    }
}