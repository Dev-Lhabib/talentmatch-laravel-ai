<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('candidates');

        Schema::table('analyses', function (Blueprint $table) {
            $table->dropForeign(['candidature_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['candidature_id']);
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->renameColumn('candidature_id', 'candidate_id');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->renameColumn('candidature_id', 'candidate_id');
        });

        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropForeign(['offre_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('candidatures', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nom_candidat');
            $table->string('phone', 50)->nullable()->after('email');
        });

        Schema::table('candidatures', function (Blueprint $table) {
            $table->renameColumn('nom_candidat', 'name');
        });

        Schema::rename('candidatures', 'candidates');

        Schema::table('candidates', function (Blueprint $table) {
            $table->foreign('offre_id')->references('id')->on('offres')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropForeign(['candidate_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['candidate_id']);
        });

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropForeign(['offre_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::rename('candidates', 'candidatures');

        Schema::table('candidatures', function (Blueprint $table) {
            $table->renameColumn('name', 'nom_candidat');
        });

        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone']);
        });

        Schema::table('candidatures', function (Blueprint $table) {
            $table->foreign('offre_id')->references('id')->on('offres')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->renameColumn('candidate_id', 'candidature_id');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->renameColumn('candidate_id', 'candidature_id');
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->foreign('candidature_id')->references('id')->on('candidatures')->cascadeOnDelete();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreign('candidature_id')->references('id')->on('candidatures')->cascadeOnDelete();
        });
    }
};
