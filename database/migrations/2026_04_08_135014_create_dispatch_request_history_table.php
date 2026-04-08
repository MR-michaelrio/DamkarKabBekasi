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
        Schema::create('dispatch_request_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ambulance_id')->index();
            $table->unsignedBigInteger('dispatch_id')->index();
            $table->integer('sequence')->default(1); // Order: 1, 2, 3, etc.
            $table->timestamp('completed_at')->nullable(); // When this request was completed
            $table->boolean('returned_to_base')->default(false); // Whether they returned to base after this
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('ambulance_id')->references('id')->on('ambulances')->onDelete('cascade');
            $table->foreign('dispatch_id')->references('id')->on('dispatches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_request_history');
    }
};
