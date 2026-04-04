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

    public function generate($text)
    {
        if (empty($text))
            return null;

        $filename = md5($text) . '.wav';
        $directory = 'tts'; // Relative to storage/app/public
        $filePath = $directory . '/' . $filename;

        $disk = Storage::disk('public');

        // Check if file already exists (caching)
        if ($disk->exists($filePath)) {
            return $disk->url($filePath);
        }

        // Ensure directory exists
        if (!$disk->exists($directory)) {
            $disk->makeDirectory($directory);
        }

        $fullPath = $disk->path($filePath);

        // Test directory write access
        if (!is_writable(dirname($fullPath))) {
            Log::error("Piper TTS: Directory not writable: " . dirname($fullPath));
            return null;
        }

        // Command execution
        $escapedText = \escapeshellarg($text);
        // We 'cd' into the piper directory first to ensure libraries are found
        $piperDir = dirname($this->piperPath);
        $binaryFile = basename($this->piperPath);
        $command = "cd {$piperDir} && echo {$escapedText} | ./{$binaryFile} --model {$this->modelPath} --output_file {$fullPath} 2>&1";

        \exec($command, $output, $returnCode);

        Log::info("Piper Debug - Command: {$command}");
        Log::info("Piper Debug - Return Code: {$returnCode}");
        Log::info("Piper Debug - Output: " . implode("\n", $output));

        if ($returnCode !== 0) {
            Log::error("Piper TTS Command Failed [Code {$returnCode}]: " . implode("\n", $output));
            return null;
        }

        if (!file_exists($fullPath)) {
            Log::error("Piper TTS: Command succeeded but file was not created at {$fullPath}");
            return null;
        }

        return $disk->url($filePath);
    }
}