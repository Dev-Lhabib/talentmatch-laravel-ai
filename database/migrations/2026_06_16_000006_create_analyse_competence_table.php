<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analyse_competence', function (Blueprint $table) {
            $table->foreignId('analyse_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competence_id')->constrained()->cascadeOnDelete();
            $table->primary(['analyse_id', 'competence_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analyse_competence');
    }
};
