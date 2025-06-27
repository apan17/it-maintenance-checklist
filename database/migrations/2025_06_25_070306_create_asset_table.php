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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('component_id')->constrained('ref_components')->onDelete('cascade');
            $table->string('serial_no')->unique()->comment('Nombor Siri Aset');
            $table->string('name')->comment('Nama Aset');
            $table->string('location')->comment('Lokasi Aset');
            $table->string('status')->comment('Status Aset');
            $table->text('procedure')->comment('Prosedur Penyelenggaraan Aset');

            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset');
    }
};
