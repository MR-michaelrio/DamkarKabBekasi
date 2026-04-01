<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TTSService
{
    protected $piperPath;
    protected $modelPath;

    public function __construct()
    {
        // Define these in your .env or handle defaults
        $this->piperPath = env('PIPER_PATH', base_path('piper/piper'));
        $this->modelPath = env('PIPER_MODEL', base_path('piper/id_ID-news_tts-medium.onnx'));
    }

    /**
     * Generate TTS audio file and return the public URL.
     * 
     * @param string $text
     * @return string|null
     */
    public function generate($text)
    {
        if (empty($text))
            return null;

        $filename = md5($text) . '.wav';
        $directory = 'public/tts';
        $filePath = $directory . '/' . $filename;

        // Check if file already exists (caching)
        if (Storage::exists($filePath)) {
            return Storage::url($filePath);
        }

        // Ensure directory exists
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        $fullPath = storage_path('app/' . $filePath);

        // Command execution
        // echo "text" | ./piper --model model.onnx --output_file out.wav
        $escapedText = \escapeshellarg($text);
        $command = "echo {$escapedText} | {$this->piperPath} --model {$this->modelPath} --output_file {$fullPath} 2>&1";

        \exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error("Piper TTS Error: " . implode("\n", $output));
            return null;
        }

        return Storage::url($filePath);
    }
}