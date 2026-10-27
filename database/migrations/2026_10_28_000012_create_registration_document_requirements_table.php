<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->restrictOnDelete();
            $table->string('requirement_code');
            $table->string('requirement_name');
            $table->text('description_snapshot')->nullable();
            $table->boolean('is_required');
            $table->json('allowed_mime_types_snapshot');
            $table->unsignedInteger('max_file_size_kb_snapshot');
            $table->timestamps();

            $table->unique(['registration_id', 'requirement_code'], 'reg_doc_requirement_code_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_document_requirements');
    }
};
