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
        Schema::table('ambulances', function (Blueprint $table) {
            if (!Schema::hasColumn('ambulances', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('ambulances', 'type')) {
                $table->string('type')->nullable()->after('code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambulances', function (Blueprint $table) {
            if (Schema::hasColumn('ambulances', 'code')) {
                $table->dropColumn('code');
            }
            if (Schema::hasColumn('ambulances', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
