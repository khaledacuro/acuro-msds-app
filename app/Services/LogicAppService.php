<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogicAppService
{


    public function triggerWorkflow(string $fileName, string $fileContent)
    {
        $logicAppUrl = config('services.logic_app.url');

        if (!$logicAppUrl) {
            throw new \Exception('Logic App URL is not configured.');
        }

        Log::info('Preparing to trigger Logic App', [
            'url' => $logicAppUrl,
            'fileName' => $fileName,
        ]);

        try {
            // Send the JSON payload to the Logic App
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification if needed
            ])->post($logicAppUrl, [
                'fileName' => $fileName,       // Send file name
                'fileContent' => $fileContent // Send base64-encoded file content
            ]);

            if ($response->successful()) {
                //add delay to allow Logic App to process the request
                Log::info('Logic App Triggered Successfully', ['response' => $response->json()]);
                return $response->json();
            }

            Log::error('Logic App Response Error', ['response' => $response->body()]);
            throw new \Exception("Logic App request failed: " . $response->body());
        } catch (\Exception $e) {
            Log::error('Exception while triggering Logic App', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
