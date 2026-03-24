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
        Schema::create('ambulance_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambulance_id')->constrained()->onDelete('cascade');
            $table->date('maintenance_date');
            $table->string('maintenance_type');
            $table->string('workshop');
            $table->decimal('cost', 15, 2);
            $table->json('spare_parts')->nullable();
            $table->integer('odometer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambulance_maintenances');
    }
};
