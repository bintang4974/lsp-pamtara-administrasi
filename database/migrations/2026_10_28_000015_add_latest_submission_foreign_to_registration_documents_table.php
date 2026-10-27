<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->foreign('latest_submission_id')
                ->references('id')->on('document_submissions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->dropForeign(['latest_submission_id']);
        });
    }
};
