<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitation_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('document_number')->unique();
            $table->string('file_path');
            $table->json('schedule_snapshot');
            $table->foreignId('issued_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('issued_at');
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->unique(['registration_id', 'version_number'], 'invitation_version_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitation_documents');
    }
};
