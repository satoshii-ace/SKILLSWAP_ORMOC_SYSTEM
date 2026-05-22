<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Transaction;
use App\Services\CalendarService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // 1. Added the Log Facade here!
use Exception;

class TransactionController extends Controller
{
    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the skill_id
        $validated = $request->validate([
            'skill_id' => 'required|exists:skills,id',
        ]);

        // Get the skill
        $skill = Skill::findOrFail($validated['skill_id']);

        // Prevent user from creating a transaction for their own skill
        if ($skill->user_id === Auth::id()) {
            return redirect()->back()
                ->with('error', 'You cannot create a transaction for your own skill.');
        }

        // Check if a pending transaction already exists for this skill and user
        $existingTransaction = Transaction::where('skill_id', $skill->id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if ($existingTransaction) {
            return redirect()->back()
                ->with('error', 'You already have a pending request for this skill.');
        }

        // Create the transaction
        Transaction::create([
            'skill_id' => $skill->id,
            'provider_id' => $skill->user_id,  // Owner of the skill
            'receiver_id' => Auth::id(),        // Current user
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
        // Authorize that the user can update this transaction
        if (Auth::id() !== $transaction->provider_id && Auth::id() !== $transaction->receiver_id) {
            return redirect()->back()
                ->with('error', 'Unauthorized action.');
        }

        // Validate the status
        $validated = $request->validate([
            'status' => 'required|in:accepted,rejected',
            'scheduled_date' => 'required_if:status,accepted|nullable|date_format:Y-m-d H:i',
        ]);

        try {
            // Update the transaction status
            $transaction->update([
                'status' => $validated['status'],
                'scheduled_date' => $validated['scheduled_date'] ?? $transaction->scheduled_date,
            ]);

            // If status is accepted and has a scheduled date, create Google Calendar event
            if ($validated['status'] === 'accepted' && $transaction->scheduled_date) {
                $this->createCalendarEvent($transaction);
            }

            $statusMessage = ucfirst($validated['status']);
            return redirect()->back()
                ->with('success', "Swap request {$statusMessage}!");
        } catch (Exception $e) {
            // 2. Changed \Log::error to Log::error
            Log::error('Transaction Update Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while updating the transaction.');
        }
    }

    /**
     * Create a Google Calendar event for the swap meeting.
     */
    private function createCalendarEvent(Transaction $transaction): void
    {
        try {
            // Reload transaction with relationships
            $transaction = $transaction->load(['provider', 'receiver', 'skill']);
            
            // Only create event if the provider has Google tokens
            if (!$transaction->provider->google_access_token) {
                // 3. Changed \Log::info to Log::info
                Log::info('Provider has not authorized Google Calendar access.');
                return;
            }

            $calendarService = new CalendarService();
            $skill = $transaction->skill;
            $eventTitle = "SkillSwap Meeting: {$skill->title}";
            $eventDescription = "Swap meeting between {$transaction->receiver->name} and {$transaction->provider->name}. "
                . "Skill: {$skill->title}\n\nCategory: {$skill->category}\nType: {$skill->type}";

            // Create event on provider's calendar
            $calendarService->createSkillSwapEvent(
                $transaction->provider,
                $eventTitle,
                $eventDescription,
                $transaction->scheduled_date->format('Y-m-d H:i:s')
            );

            // 4. Changed \Log::info to Log::info
            Log::info('Calendar event created for transaction ID: ' . $transaction->id);
        } catch (Exception $e) {
            // 5. Changed \Log::error to Log::error
            Log::error('Failed to create calendar event: ' . $e->getMessage());
            // Don't throw - we don't want calendar creation failure to break the transaction update
        }
    }
}