<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checklists', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets');
            $table->foreignUuid('inspector_id')->constrained('users')->nullable();
            
            $table->string('asset_status')->comment('Status Aset');
            $table->string('current_asset_status')->nullable()->comment('Status Aset Semasa');
            
            $table->dateTime('due_date')->comment('Tarikh Akhir');
            $table->dateTime('complete_date')->nullable()->comment('Tarikh Penyelesaian');
            $table->text('notes')->nullable()->comment('Keterangan Senarai Semak');
            $table->timestamps();
        });

        Schema::create('checklist_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('checklist_id')->constrained('checklists')->onDelete('cascade');
            $table->foreignUuid('attachment_id')->constrained('attachments')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_attachment');
        Schema::dropIfExists('checklist');
    }
};
