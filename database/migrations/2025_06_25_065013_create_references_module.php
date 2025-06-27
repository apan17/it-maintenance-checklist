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
        Schema::create('ref-maintenance-frequency', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique()->comment('Nama Kekerapan Penyelenggaraan');
            $table->timestamps();
        });

        Schema::create('ref-components', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique()->comment('Nama Jenis Komponen');
            $table->string('maintenance_frequency')->comment('Kekerapan Penyelenggaraan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref-components');
        Schema::dropIfExists('ref-maintenance-frequency');
    }
};
