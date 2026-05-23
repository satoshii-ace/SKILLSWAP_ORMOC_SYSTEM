<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; 

Route::get('/', function () {
    return Auth::check() 
        ? redirect()->route('skills.index')
        : view('home'); 
});

Route::get('/dashboard', function () {
    return redirect()->route('skills.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Skill Routes
    Route::get('/skills', [SkillController::class, 'index'])->name('skills.index');
    Route::get('/skills/create', [SkillController::class, 'create'])->name('skills.create');
    Route::post('/skills', [SkillController::class, 'store'])->name('skills.store');

    // Transaction & Swap Routes
    Route::get('/swaps', [TransactionController::class, 'index'])->name('swaps.index'); // <- New My Swaps Route!
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::patch('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');

    // Google Calendar OAuth Routes
    Route::get('/auth/google', [\App\Http\Controllers\GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [\App\Http\Controllers\GoogleAuthController::class, 'callback'])->name('google.callback');
});

require __DIR__.'/auth.php';