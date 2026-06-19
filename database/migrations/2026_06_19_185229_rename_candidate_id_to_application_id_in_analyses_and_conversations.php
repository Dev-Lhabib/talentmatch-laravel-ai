<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analyses', function (Blueprint $table) {
            $table->dropForeign('analyses_candidate_id_foreign');
            $table->dropUnique('analyses_candidature_id_unique');
        });

        Schema::table('analyses', function (Blueprint $table) {
            $table->renameColumn('candidate_id', 'application_id');
        });

        DB::statement('UPDATE analyses a JOIN applications app ON app.candidate_id = a.application_id SET a.application_id = app.id');

        Schema::table('analyses', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->unique()->change();
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign('conversations_candidate_id_foreign');
            $table->dropUnique('conversations_candidature_id_unique');
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->renameColumn('candidate_id', 'application_id');
        });

        DB::statement('UPDATE conversations c JOIN applications app ON app.candidate_id = c.application_id SET c.application_id = app.id');

        Schema::table('conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->unique()->change();
            $table->foreign('application_id')->references('id')->on('applications')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        DB::statement('UPDATE analyses a JOIN applications app ON app.id = a.application_id SET a.application_id = app.candidate_id');

        Schema::table('analyses', function (Blueprint $table) {
            $table->dropForeign('analyses_application_id_foreign');
            $table->dropUnique('analyses_application_id_unique');
            $table->renameColumn('application_id', 'candidate_id');
            $table->unsignedBigInteger('candidate_id')->unique()->change();
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
        });

        DB::statement('UPDATE conversations c JOIN applications app ON app.id = c.application_id SET c.application_id = app.candidate_id');

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign('conversations_application_id_foreign');
            $table->dropUnique('conversations_application_id_unique');
            $table->renameColumn('application_id', 'candidate_id');
            $table->unsignedBigInteger('candidate_id')->unique()->change();
            $table->foreign('candidate_id')->references('id')->on('candidates')->cascadeOnDelete();
        });
    }
};
