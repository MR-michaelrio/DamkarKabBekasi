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
        if (!Schema::hasTable('patient_requests')) {
            Schema::create('patient_requests', function (Blueprint $table) {
                $table->id();
                $table->string('patient_name');
                $table->enum('service_type', ['ambulance', 'jenazah']);
                $table->date('request_date');
                $table->string('phone');
                $table->text('pickup_address');
                $table->text('destination');
                $table->enum('patient_condition', ['emergency', 'kontrol'])->nullable();
                $table->enum('status', ['pending', 'dispatched', 'rejected'])->default('pending');
                $table->foreignId('dispatch_id')->nullable()->constrained()->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patient_requests');
    }
};
