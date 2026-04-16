<?php

namespace App\Services;

use App\Models\Dispatch;
use Carbon\Carbon;

class ReportNumberService
{
    /**
     * Generate a unique report number in format: 001/Kebakaran/Damkar/2026
     * 
     * @param string $type - 'kebakaran' or 'rescue'
     * @return string
     */
    public function generate(string $type): string
    {
        $currentYear = Carbon::now()->year;
        
        // Map type to display name
        $typeDisplay = $this->getTypeDisplay($type);
        
        // Get the next number for this type and year
        $nextNumber = $this->getNextNumber($type, $currentYear);
        
        // Format: 001/Kebakaran/Damkar/2026
        return sprintf('%03d/%s/Damkar/%d', $nextNumber, $typeDisplay, $currentYear);
    }
    
    /**
     * Get the next sequential number for the given type and year
     * 
     * @param string $type
     * @param int $year
     * @return int
     */
    private function getNextNumber(string $type, int $year): int
    {
        // Find the last dispatch of this type for this year
        $lastDispatch = Dispatch::where('patient_condition', $type)
            ->whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->first();
        
        if (!$lastDispatch || !$lastDispatch->nomor) {
            return 1;
        }
        
        // Extract the number from the nomor field
        // Format: 001/Kebakaran/Damkar/2026
        preg_match('/^(\d+)\//', $lastDispatch->nomor, $matches);
        
        if (isset($matches[1])) {
            return intval($matches[1]) + 1;
        }
        
        return 1;
    }
    
    /**
     * Get display name for the type
     * 
     * @param string $type
     * @return string
     */
    private function getTypeDisplay(string $type): string
    {
        $typeMap = [
            'kebakaran' => 'Kebakaran',
            'rescue' => 'Penyelamatan',
            'penyelamatan' => 'Penyelamatan',
        ];
        
        return $typeMap[$type] ?? ucfirst($type);
    }
}
