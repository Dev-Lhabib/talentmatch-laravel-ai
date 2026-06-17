<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function show(): View
    {
        return view('feedback.show');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
        ]);

        // Log feedback for now (no DB table needed yet)
        \Log::info('User feedback', [
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return redirect()->route('feedback.show')->with('success', 'Merci pour votre retour !');
    }
}
