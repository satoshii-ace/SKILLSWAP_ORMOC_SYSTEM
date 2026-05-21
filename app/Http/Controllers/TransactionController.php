<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

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
}
