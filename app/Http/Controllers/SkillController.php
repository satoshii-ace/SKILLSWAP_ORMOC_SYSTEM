<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth; // 1. Add the Auth facade here

class SkillController extends Controller
{
    /**
     * Display a listing of all skills with their users.
     */
    public function index(): View
    {
        $skills = Skill::with('user')->paginate(15);

        return view('skills.index', compact('skills'));
    }

    /**
     * Show the form for creating a new skill.
     */
    public function create(): View
    {
        $categories = [
            'Technology',
            'Design',
            'Business',
            'Language',
            'Music',
            'Sports',
            'Crafts',
            'Education',
            'Finance',
            'Health',
        ];

        $types = ['offered', 'requested'];

        return view('skills.create', compact('categories', 'types'));
    }

    /**
     * Store a newly created skill in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate the input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'category' => 'required|string|max:255',
            'type' => 'required|in:offered,requested',
        ]);

        // Check if skill already exists for this user
        $existingSkill = Skill::where('user_id', Auth::id())
            ->where('title', $validated['title'])
            ->where('type', $validated['type'])
            ->first();

        if ($existingSkill) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You already have a ' . $validated['type'] . ' skill with this title.');
        }

        $validated['user_id'] = Auth::id();

        // Create the skill
        Skill::create($validated);

        return redirect()->route('skills.index')
            ->with('success', 'Skill created successfully!');
    }
}