<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->restrictOnDelete();
            $table->foreignId('registration_document_requirement_id');
            $table->foreign('registration_document_requirement_id', 'reg_doc_requirement_id_foreign')
                ->references('id')->on('registration_document_requirements')
                ->restrictOnDelete();
            $table->string('current_status')->default('missing');
            // FK ke document_submissions ditambahkan di migration terpisah setelah tabel itu ada (lihat 000015).
            $table->unsignedBigInteger('latest_submission_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['registration_id', 'registration_document_requirement_id'], 'reg_doc_requirement_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_documents');
    }
};
