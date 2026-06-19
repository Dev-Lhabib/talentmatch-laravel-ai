<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offre_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->text('cv_text')->nullable();
            $table->timestamps();

            $table->unique(['candidate_id', 'offre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
