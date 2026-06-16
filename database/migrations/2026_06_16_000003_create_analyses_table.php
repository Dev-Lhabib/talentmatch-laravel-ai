<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('competences_extraites');
            $table->unsignedInteger('annees_experience')->default(0);
            $table->string('niveau_etudes')->default('');
            $table->json('langues');
            $table->unsignedInteger('matching_score')->default(0);
            $table->json('points_forts');
            $table->json('lacunes');
            $table->json('competences_manquantes');
            $table->string('recommandation');
            $table->text('justification')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyses');
    }
};
