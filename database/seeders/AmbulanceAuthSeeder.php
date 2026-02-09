<?php

namespace Database\Seeders;

use App\Models\Ambulance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AmbulanceAuthSeeder extends Seeder
{
    public function run(): void
    {
        // Get all ambulances
        $ambulances = Ambulance::all();
        
        foreach ($ambulances as $ambulance) {
            // Use ambulance code as username
            $username = $ambulance->code;
            
            // Default password same as username for first login (or 'password')
            $password = 'password';

            $ambulance->update([
                'username' => $username,
                'password' => Hash::make($password),
            ]);
            
            $this->command->info("Updated Ambulance {$ambulance->plate_number} with username: {$username}");
        }
    }
}
