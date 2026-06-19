<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Competence;
use Illuminate\Database\Seeder;

class CompetenceSeeder extends Seeder
{
    public function run(): void
    {
        $competences = [
            'Laravel', 'PHP', 'MySQL', 'Docker', 'Redis', 'Git', 'API REST', 'Linux',
            'JavaScript', 'Vue.js', 'React', 'TypeScript', 'HTML', 'CSS', 'Figma',
            'Python', 'SQL', 'Power BI', 'Excel', 'Statistiques', 'Data Visualization',
            'Pandas', 'Tableau',
            'Kubernetes', 'AWS', 'Terraform', 'CI/CD', 'GitHub Actions', 'Ansible',
            'PHPUnit', 'Node.js', 'Agile',
        ];

        foreach ($competences as $nom) {
            Competence::firstOrCreate(['nom' => $nom]);
        }
    }
}
