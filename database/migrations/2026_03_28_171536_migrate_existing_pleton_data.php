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
        $drivers = DB::table('drivers')->whereNotNull('pleton')->where('pleton', '!=', '')->get();

        foreach ($drivers as $driver) {
            $pletonName = trim($driver->pleton);
            
            // Find or create pleton by name
            $pletonId = DB::table('pletons')->where('name', $pletonName)->value('id');
            
            if (!$pletonId) {
                $pletonId = DB::table('pletons')->insertGetId([
                    'name' => $pletonName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($pletonId) {
                DB::table('drivers')->where('id', $driver->id)->update([
                    'pleton_id' => $pletonId
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
