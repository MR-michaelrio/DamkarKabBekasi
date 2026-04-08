<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('activity_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_log_id')->constrained('activity_logs')->onDelete('cascade');
            $table->string('photo_path'); // Path in storage
            $table->string('photo_name'); // Original file name
            $table->string('mime_type')->default('image/jpeg');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->text('description')->nullable(); // Photo description
            $table->unsignedTinyInteger('sequence')->default(1); // Photo order 1-5
            $table->timestamps();

            // Index for faster queries
            $table->index('activity_log_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_photos');
    }
};
