<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\RecommandationEnum;
use App\Enums\StatutCandidatureEnum;
use App\Models\Application;
use App\Models\Comparison;
use App\Models\Offre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Ai\AnonymousAgent;

class ComparisonsController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application1_id' => 'required|exists:applications,id',
            'application2_id' => 'required|exists:applications,id|different:application1_id',
        ]);

        $app1 = Application::with('candidate', 'offre')->findOrFail((int) $validated['application1_id']);
        $app2 = Application::with('candidate', 'offre')->findOrFail((int) $validated['application2_id']);

        $app1->loadMissing('analyse');
        $app2->loadMissing('analyse');

        abort_if($app1->offre->user_id !== auth()->id(), 403);
        abort_if($app2->offre->user_id !== auth()->id(), 403);
        abort_if($app1->offre_id !== $app2->offre_id, 422, 'Les deux candidatures doivent appartenir à la même offre.');
        abort_if(! $app1->analyse || $app1->analyse->matching_score === null, 422, 'La première candidature n\'est pas encore analysée.');
        abort_if(! $app2->analyse || $app2->analyse->matching_score === null, 422, 'La deuxième candidature n\'est pas encore analysée.');

        $offre = $app1->offre;
        $requiredSkills = $offre->required_skills ?? [];

        $prompt = $this->buildPrompt($app1, $app2, $offre, $requiredSkills);

        try {
            $agent = new AnonymousAgent(
                instructions: 'Tu es un expert RH spécialisé dans la présélection de candidats. Réponds uniquement en JSON valide, sans texte avant ni après.',
                messages: [],
                tools: [],
            );

            $response = $agent->prompt($prompt);
            $json = json_decode(trim($response->text), true, 512, JSON_THROW_ON_ERROR);

            $comparison = Comparison::create([
                'offre_id' => $offre->id,
                'application1_id' => $app1->id,
                'application2_id' => $app2->id,
                'candidate1_verdict' => $json['candidate1_verdict'] ?? 'Verdict non disponible.',
                'candidate2_verdict' => $json['candidate2_verdict'] ?? 'Verdict non disponible.',
                'winner_id' => (int) ($json['winner_id'] ?? $app1->id),
                'winner_reason' => $json['winner_reason'] ?? 'Analyse non disponible.',
            ]);
        } catch (\Throwable) {
            $score1 = $app1->analyse?->matching_score ?? 0;
            $score2 = $app2->analyse?->matching_score ?? 0;
            $fallbackWinnerId = $score1 >= $score2 ? $app1->id : $app2->id;

            $comparison = Comparison::create([
                'offre_id' => $offre->id,
                'application1_id' => $app1->id,
                'application2_id' => $app2->id,
                'candidate1_verdict' => 'L\'analyse IA est temporairement indisponible.',
                'candidate2_verdict' => 'L\'analyse IA est temporairement indisponible.',
                'winner_id' => $fallbackWinnerId,
                'winner_reason' => 'L\'analyse IA est temporairement indisponible.',
            ]);
        }

        return redirect()->route('comparisons.show', $comparison);
    }

    public function show(Comparison $comparison): View
    {
        $comparison->load(['offre', 'application1.candidate', 'application1.analyse', 'application1.offre', 'application2.candidate', 'application2.analyse', 'application2.offre']);

        abort_if($comparison->offre->user_id !== auth()->id(), 403);

        $offre = $comparison->offre;
        $requiredSkills = $offre->required_skills ?? [];

        $app1 = $comparison->application1;
        $app2 = $comparison->application2;

        $skillMatrix1 = $this->buildSkillMatrix($requiredSkills, $app1->analyse->competences_extraites ?? []);
        $skillMatrix2 = $this->buildSkillMatrix($requiredSkills, $app2->analyse->competences_extraites ?? []);

        $extraSkills1 = $this->getExtraSkills($requiredSkills, $app1->analyse->competences_extraites ?? []);
        $extraSkills2 = $this->getExtraSkills($requiredSkills, $app2->analyse->competences_extraites ?? []);

        $isWinner1 = $comparison->winner_id === $app1->id;

        return view('comparisons.show', compact(
            'comparison', 'offre', 'app1', 'app2',
            'skillMatrix1', 'skillMatrix2',
            'extraSkills1', 'extraSkills2',
            'isWinner1',
        ));
    }

    private function buildPrompt(Application $app1, Application $app2, Offre $offre, array $requiredSkills): string
    {
        $truncate = fn (string $text) => mb_strlen($text) > 2000 ? mb_substr($text, 0, 2000).'...' : $text;

        $skills1 = json_encode($app1->analyse->competences_extraites ?? [], JSON_UNESCAPED_UNICODE);
        $strengths1 = json_encode($app1->analyse->points_forts ?? [], JSON_UNESCAPED_UNICODE);
        $gaps1 = json_encode($app1->analyse->lacunes ?? [], JSON_UNESCAPED_UNICODE);
        $skills2 = json_encode($app2->analyse->competences_extraites ?? [], JSON_UNESCAPED_UNICODE);
        $strengths2 = json_encode($app2->analyse->points_forts ?? [], JSON_UNESCAPED_UNICODE);
        $gaps2 = json_encode($app2->analyse->lacunes ?? [], JSON_UNESCAPED_UNICODE);
        $reqSkills = json_encode($requiredSkills, JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
Compare these two candidates for the role: {$offre->titre}
Required skills: {$reqSkills}

Candidate 1: {$app1->candidate->name}, Score: {$app1->analyse->matching_score}
CV: {$truncate($app1->cv_text ?? '')}
Competences: {$skills1}
Strengths: {$strengths1}
Gaps: {$gaps1}

Candidate 2: {$app2->candidate->name}, Score: {$app2->analyse->matching_score}
CV: {$truncate($app2->cv_text ?? '')}
Competences: {$skills2}
Strengths: {$strengths2}
Gaps: {$gaps2}

Return ONLY valid JSON:
{
  "candidate1_verdict": "2-3 sentence summary of candidate 1",
  "candidate2_verdict": "2-3 sentence summary of candidate 2",
  "winner_id": integer (application_id of the stronger candidate),
  "winner_reason": "1 sentence why this candidate wins"
}
PROMPT;
    }

    private function buildSkillMatrix(array $requiredSkills, array $candidateSkills): array
    {
        $candidateSkillsLower = array_map('mb_strtolower', $candidateSkills);
        $matrix = [];

        foreach ($requiredSkills as $skill) {
            $matrix[] = [
                'skill' => $skill,
                'has' => in_array(mb_strtolower($skill), $candidateSkillsLower, true),
            ];
        }

        return $matrix;
    }

    private function getExtraSkills(array $requiredSkills, array $candidateSkills): array
    {
        $requiredLower = array_map('mb_strtolower', $requiredSkills);

        return array_values(array_filter($candidateSkills, fn ($skill) => ! in_array(mb_strtolower($skill), $requiredLower, true)));
    }

    public function convoquer(Comparison $comparison): RedirectResponse
    {
        $comparison->load(['offre', 'application1.analyse', 'application2.analyse']);

        abort_if($comparison->offre->user_id !== auth()->id(), 403);

        $winner = $comparison->winner_id === $comparison->application1_id
            ? $comparison->application1
            : $comparison->application2;

        $loser = $comparison->winner_id === $comparison->application1_id
            ? $comparison->application2
            : $comparison->application1;

        $winner->analyse->recommandation = RecommandationEnum::Convoquer;
        $winner->status = StatutCandidatureEnum::Completed;
        $winner->analyse->save();
        $winner->save();

        $loser->analyse->recommandation = RecommandationEnum::Rejeter;
        $loser->status = StatutCandidatureEnum::Completed;
        $loser->analyse->save();
        $loser->save();

        return redirect()->route('offres.show', $comparison->offre)
            ->with('success', 'Candidat convoqué avec succès');
    }
}
