<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offre_competence', function (Blueprint $table) {
            $table->foreignId('offre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competence_id')->constrained()->cascadeOnDelete();
            $table->primary(['offre_id', 'competence_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offre_competence');
    }
};
