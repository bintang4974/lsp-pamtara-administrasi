<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('result_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_result_id')->constrained()->restrictOnDelete();
            $table->string('old_result')->nullable();
            $table->string('new_result');
            $table->text('reason');
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('changed_at');
            $table->timestamp('created_at')->nullable();

            $table->index('assessment_result_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('result_histories');
    }
};
