<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Analyse;
use App\Models\Application;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(): View
    {
        $userId = Auth::id();

        $totalOffres = Offre::where('user_id', $userId)->count();
        $offreIds = Offre::where('user_id', $userId)->pluck('id');

        $totalCandidates = Application::whereIn('offre_id', $offreIds)->count();

        $analysesCompleted = Analyse::whereHas('application', fn ($q) => $q->whereIn('offre_id', $offreIds))
            ->where('matching_score', '>', 0)
            ->count();

        $avgScore = Analyse::whereHas('application', fn ($q) => $q->whereIn('offre_id', $offreIds))
            ->where('matching_score', '>', 0)
            ->avg('matching_score');

        $recentApplications = Application::whereIn('offre_id', $offreIds)
            ->whereHas('analyse')
            ->with('candidate', 'offre', 'analyse')
            ->latest()
            ->take(5)
            ->get();

        $recommandationCounts = [
            'convoquer' => Analyse::whereHas('application', fn ($q) => $q->whereIn('offre_id', $offreIds))
                ->where('recommandation', 'convoquer')
                ->count(),
            'attente' => Analyse::whereHas('application', fn ($q) => $q->whereIn('offre_id', $offreIds))
                ->where('recommandation', 'attente')
                ->count(),
            'rejeter' => Analyse::whereHas('application', fn ($q) => $q->whereIn('offre_id', $offreIds))
                ->where('recommandation', 'rejeter')
                ->count(),
        ];

        return view('dashboard', compact(
            'totalOffres',
            'totalCandidates',
            'analysesCompleted',
            'avgScore',
            'recentApplications',
            'recommandationCounts',
        ));
    }
}
