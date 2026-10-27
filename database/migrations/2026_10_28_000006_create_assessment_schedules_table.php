<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_code')->unique();
            $table->foreignId('certification_type_id')->constrained()->restrictOnDelete();
            $table->foreignId('tuk_id')->constrained()->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->dateTime('registration_deadline');
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['certification_type_id', 'tuk_id', 'status']);
            $table->index('starts_at');
            $table->index('registration_deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_schedules');
    }
};
