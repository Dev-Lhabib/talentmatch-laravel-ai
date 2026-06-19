<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('application1_id')->constrained('applications')->cascadeOnDelete();
            $table->foreignId('application2_id')->constrained('applications')->cascadeOnDelete();
            $table->text('candidate1_verdict');
            $table->text('candidate2_verdict');
            $table->unsignedBigInteger('winner_id');
            $table->text('winner_reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comparisons');
    }
};
