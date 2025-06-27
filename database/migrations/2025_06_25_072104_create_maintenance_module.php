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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets');
            $table->foreignUuid('reporter_id')->constrained('users');
            $table->foreignUuid('maintainer_id')->constrained('users')->nullable();

            $table->string('asset_status')->comment('Status Aset');
            $table->string('current_status')->comment('Status Penyelenggaraan Terkini');

            $table->dateTime('complete_date')->nullable()->comment('Tarikh Penyelesaian');
            $table->text('notes')->nullable()->comment('Keterangan Penyelenggaraan');
            $table->timestamps();
        });

        Schema::create('maintenance_attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('maintenance_id')->constrained('maintenances')->onDelete('cascade');
            $table->foreignUuid('attachment_id')->constrained('attachments')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('maintenance_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('maintenance_id')->constrained('maintenances')->onDelete('cascade');

            $table->string('status')->comment('Status Penyelenggaraan');
            $table->string('notes')->nullable()->comment('Keterangan Status Penyelenggaraan');
            $table->dateTime('date')->comment('Tarikh Status Penyelenggaraan');

            $table->boolean('is_current')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_statuses');
        Schema::dropIfExists('maintenance_attachments');
        Schema::dropIfExists('maintenances');
    }
};
