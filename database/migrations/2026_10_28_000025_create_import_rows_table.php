<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_batch_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->json('raw_data');
            $table->boolean('is_valid')->default(false);
            $table->json('errors')->nullable();
            $table->boolean('committed')->default(false);
            $table->timestamps();

            $table->index(['import_batch_id', 'is_valid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_rows');
    }
};
