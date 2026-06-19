<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Jobs\AnalyseCandidatJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\Competence;
use App\Models\Offre;
use App\Models\User;
use Illuminate\Database\Seeder;

class TalentMatchSeeder extends Seeder
{
    public function run(): void
    {
        $user1 = User::firstOrCreate(
            ['email' => 'admin@talentmatch.ma'],
            ['name' => 'Admin TalentMatch', 'password' => bcrypt('password')],
        );

        $user2 = User::firstOrCreate(
            ['email' => 'recruteur@talentmatch.ma'],
            ['name' => 'Recruteur Test', 'password' => bcrypt('password')],
        );

        $this->command?->info('Users created.');

        /* ---------- Offer 1: Laravel Backend ---------- */

        $offre1 = Offre::create([
            'user_id' => $user1->id,
            'titre' => 'Développeur Backend Laravel Senior',
            'description' => "Nous recherchons un développeur Backend Laravel expérimenté pour développer des API REST, gérer des bases de données MySQL et travailler dans un environnement Docker et Agile.\n\nLe candidat devra participer à la conception technique, aux revues de code, à l'optimisation des performances et à l'intégration de services tiers.",
            'experience_min' => 4,
        ]);
        $offre1->competences()->sync(
            Competence::whereIn('nom', ['Laravel', 'PHP', 'MySQL', 'Docker', 'Redis', 'Git', 'API REST', 'Linux'])->pluck('id')
        );

        /* ---------- Offer 2: Data Analyst ---------- */

        $offre2 = Offre::create([
            'user_id' => $user1->id,
            'titre' => 'Data Analyst',
            'description' => "Nous recherchons un Data Analyst capable d'analyser les données métier, créer des tableaux de bord et fournir des recommandations stratégiques.\n\nMaîtrise de SQL, Python, Power BI et statistiques requise.",
            'experience_min' => 3,
        ]);
        $offre2->competences()->sync(
            Competence::whereIn('nom', ['Python', 'SQL', 'Power BI', 'Excel', 'Statistiques', 'Data Visualization'])->pluck('id')
        );

        /* ---------- Offer 3: DevOps Engineer ---------- */

        $offre3 = Offre::create([
            'user_id' => $user2->id,
            'titre' => 'Ingénieur DevOps',
            'description' => "Nous recherchons un Ingénieur DevOps pour automatiser les déploiements, gérer l'infrastructure cloud et améliorer la fiabilité des systèmes.\n\nLe candidat devra maîtriser les conteneurs, l'orchestration, l'infrastructure as code et les pipelines CI/CD.",
            'experience_min' => 5,
        ]);
        $offre3->competences()->sync(
            Competence::whereIn('nom', ['Docker', 'Kubernetes', 'Linux', 'AWS', 'Terraform', 'CI/CD', 'GitHub Actions'])->pluck('id')
        );

        $this->command?->info('Offers created.');

        /* ---------- Candidates ---------- */

        $candidate1 = Candidate::create([
            'name' => 'Yassine El Amrani',
            'cv_text' => "Développeur Backend Laravel avec 5 années d'expérience dans le développement d'applications web et d'API REST.\n\nCompétences : Laravel, PHP 8, MySQL, Docker, Redis, Git, Linux, API REST, PHPUnit.\n\nExpérience :\n2021 - Aujourd'hui : Développeur Backend Senior chez Tech Solutions\n- Développement d'API REST sous Laravel.\n- Gestion des files d'attente avec Redis.\n- Optimisation MySQL.\n- Déploiement avec Docker.\n\n2019 - 2021 : Développeur PHP chez Digital Factory\n- Maintenance d'applications Laravel.\n- Création de fonctionnalités backend.\n\nFormation : Master Génie Logiciel.\nLangues : Français, Anglais, Arabe.",
        ]);

        $candidate2 = Candidate::create([
            'name' => 'Sara Benali',
            'cv_text' => "Développeuse Full Stack avec 3 années d'expérience.\n\nCompétences : PHP, Laravel, JavaScript, Vue.js, MySQL, Git.\n\nExpérience :\n2022 - Aujourd'hui : Développeuse Web\n- Développement d'applications Laravel.\n- Création d'interfaces Vue.js.\n\nFormation : Licence Informatique.\nLangues : Français, Anglais.",
        ]);

        $candidate3 = Candidate::create([
            'name' => 'Karim Tazi',
            'cv_text' => "Développeur Frontend spécialisé React avec 2 années d'expérience.\n\nCompétences : React, TypeScript, HTML, CSS, Figma.\n\nExpérience :\n2023 - Aujourd'hui : Frontend Developer.\n- Création d'interfaces utilisateur.\n- Intégration API.\n\nFormation : Licence Multimédia.\nLangues : Français.",
        ]);

        $candidate4 = Candidate::create([
            'name' => 'Imane Alaoui',
            'cv_text' => "Data Analyst avec 4 années d'expérience.\n\nCompétences : Python, Pandas, SQL, Power BI, Excel, Tableau.\n\nExpérience :\nAnalyse de données commerciales, création de dashboards, automatisation de rapports.\n\nFormation : Master Data Science.\nLangues : Français, Anglais.",
        ]);

        $candidate5 = Candidate::create([
            'name' => 'Hassan DevOps',
            'cv_text' => "Ingénieur DevOps avec 6 années d'expérience.\n\nCompétences : AWS, Docker, Kubernetes, Terraform, Linux, CI/CD, GitHub Actions, Ansible.\n\nExpérience :\nAutomatisation de déploiements, gestion d'infrastructure cloud, mise en place de pipelines CI/CD, orchestration de conteneurs Kubernetes.\n\nFormation : Master Ingénierie Informatique.\nLangues : Français, Anglais.",
        ]);

        /* ---------- Applications (candidate-offre links) ---------- */

        $applications = [
            Application::create([
                'candidate_id' => $candidate1->id,
                'offre_id' => $offre1->id,
                'cv_text' => $candidate1->cv_text,
            ]),
            Application::create([
                'candidate_id' => $candidate2->id,
                'offre_id' => $offre1->id,
                'cv_text' => $candidate2->cv_text,
            ]),
            Application::create([
                'candidate_id' => $candidate3->id,
                'offre_id' => $offre1->id,
                'cv_text' => $candidate3->cv_text,
            ]),
            Application::create([
                'candidate_id' => $candidate4->id,
                'offre_id' => $offre2->id,
                'cv_text' => $candidate4->cv_text,
            ]),
            Application::create([
                'candidate_id' => $candidate5->id,
                'offre_id' => $offre3->id,
                'cv_text' => $candidate5->cv_text,
            ]),
        ];

        $this->command?->info('Candidates and applications created. Dispatching analysis jobs...');

        foreach ($applications as $application) {
            AnalyseCandidatJob::dispatch($application);
        }

        $this->command?->info('Analysis jobs dispatched to queue. Run "php artisan queue:work" to process them.');
    }
}
