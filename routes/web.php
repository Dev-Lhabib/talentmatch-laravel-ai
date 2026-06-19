<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\CandidatureController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/candidates', [DashboardController::class, 'candidates'])->name('dashboard.candidates');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/feedback', [FeedbackController::class, 'show'])->name('feedback.show');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    Route::resource('candidates', CandidateController::class);
    Route::post('candidates/{candidate}/assign', [CandidateController::class, 'assign'])->name('candidates.assign');

    Route::resource('offres', OffreController::class);
    Route::post('offres/{offre}/assign', [OffreController::class, 'assign'])->name('offres.assign');
    Route::post('offres/{offre}/analyse-all', [OffreController::class, 'analyseAll'])->name('offres.analyse-all');

    Route::resource('offres.candidatures', CandidatureController::class)->only(['store', 'show', 'destroy']);
    Route::get('/offres/{offre}/candidatures/{candidature}/chat', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/offres/{offre}/candidatures/{candidature}/chat', [ChatController::class, 'store'])->name('chat.store');

    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/applications/{application}/retry', [ApplicationController::class, 'retry'])->name('applications.retry');
    Route::post('/applications/{application}/chat', [ApplicationController::class, 'jsonChat'])->name('applications.chat');
});
