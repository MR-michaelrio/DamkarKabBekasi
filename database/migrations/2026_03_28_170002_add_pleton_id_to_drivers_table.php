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
        Schema::table('drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('pleton_id')->nullable()->after('phone');
            $table->foreign('pleton_id')->references('id')->on('pletons')->onDelete('set null');
            
            // We can keep 'pleton' string for now or drop it later.
            // Let's drop it to be clean if it is empty.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropForeign(['pleton_id']);
            $table->dropColumn('pleton_id');
        });
    }
};
