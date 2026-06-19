<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->migrateData();

        Schema::table('candidates', function (Blueprint $table) {
            $table->dropForeign(['offre_id']);
            $table->dropColumn(['offre_id', 'status']);
        });
    }

    private function migrateData(): void
    {
        $candidates = DB::table('candidates')->whereNotNull('offre_id')->get();

        foreach ($candidates as $candidate) {
            DB::table('applications')->insert([
                'candidate_id' => $candidate->id,
                'offre_id' => $candidate->offre_id,
                'status' => $candidate->status ?? 'pending',
                'cv_text' => $candidate->cv_text,
                'created_at' => $candidate->created_at ?? now(),
                'updated_at' => $candidate->updated_at ?? now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->foreignId('offre_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending')->after('cv_text');
        });

        DB::table('applications')->truncate();
    }
};
