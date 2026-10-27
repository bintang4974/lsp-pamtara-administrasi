<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_assessors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessor_id')->constrained()->restrictOnDelete();
            $table->timestamps();

            $table->unique(['assessment_schedule_id', 'assessor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_assessors');
    }
};
