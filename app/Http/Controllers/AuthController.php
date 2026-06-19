<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\StatutCandidatureEnum;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Analyse;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
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

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPassword(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => Hash::make($password)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function dashboard(): View
    {
        $userId = Auth::id();
        $offreIds = Offre::where('user_id', $userId)->pluck('id');

        $totalCandidats = Candidate::count();

        $totalOffres = Offre::where('user_id', $userId)->count();

        $analysesCompleted = Application::whereIn('offre_id', $offreIds)
            ->where('status', StatutCandidatureEnum::Completed)
            ->whereHas('analyse')
            ->count();

        $analysesEnAttente = Application::whereIn('offre_id', $offreIds)
            ->whereIn('status', [StatutCandidatureEnum::Pending, StatutCandidatureEnum::Processing])
            ->count();

        $analysesEchouees = Application::whereIn('offre_id', $offreIds)
            ->where('status', StatutCandidatureEnum::Failed)
            ->count();

        $candidatsSansAnalyse = Candidate::doesntHave('applications')->count();

        $offresActives = Offre::where('user_id', $userId)
            ->where('status', 'open')
            ->count();

        $completedHighScore = Analyse::whereHas('application', fn ($q) => $q->whereIn('offre_id', $offreIds))
            ->where('matching_score', '>=', 70)
            ->count();

        $tauxReussite = $analysesCompleted > 0 ? round($completedHighScore / $analysesCompleted * 100) : 0;

        $avgScore = Analyse::whereHas('application', fn ($q) => $q->whereIn('offre_id', $offreIds))
            ->where('matching_score', '>', 0)
            ->avg('matching_score');

        $recentOffres = Offre::where('user_id', $userId)
            ->withCount('applications')
            ->latest()
            ->take(5)
            ->get();

        $recentCompletedAnalyses = Application::whereIn('offre_id', $offreIds)
            ->where('status', StatutCandidatureEnum::Completed)
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

        $aConvoquer = $recommandationCounts['convoquer'];
        $enAttenteRecommandation = $recommandationCounts['attente'];
        $nonRetenu = $recommandationCounts['rejeter'];

        return view('dashboard', compact(
            'totalCandidats',
            'totalOffres',
            'analysesCompleted',
            'analysesEnAttente',
            'analysesEchouees',
            'candidatsSansAnalyse',
            'offresActives',
            'tauxReussite',
            'aConvoquer',
            'enAttenteRecommandation',
            'nonRetenu',
            'avgScore',
            'recentOffres',
            'recentCompletedAnalyses',
            'recommandationCounts',
        ));
    }
}
