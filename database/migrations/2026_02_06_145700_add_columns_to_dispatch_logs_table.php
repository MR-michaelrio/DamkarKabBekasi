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
        Schema::table('dispatch_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('dispatch_logs', 'dispatch_id')) {
                $table->foreignId('dispatch_id')->constrained()->cascadeOnDelete()->after('id');
            }
            if (!Schema::hasColumn('dispatch_logs', 'status')) {
                $table->string('status')->after('dispatch_id');
            }
            if (!Schema::hasColumn('dispatch_logs', 'note')) {
                $table->text('note')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_logs', function (Blueprint $table) {
            if (Schema::hasColumn('dispatch_logs', 'dispatch_id')) {
                $table->dropForeign(['dispatch_id']);
                $table->dropColumn('dispatch_id');
            }
            if (Schema::hasColumn('dispatch_logs', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('dispatch_logs', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
