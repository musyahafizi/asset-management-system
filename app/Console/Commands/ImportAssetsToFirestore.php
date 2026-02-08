<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Google\Auth\Credentials\ServiceAccountCredentials;

class ImportAssetsToFirestore extends Command
{
    protected $signature = 'import:assets';
    protected $description = 'Organize JSON data into separate Firestore Collections';

    public function handle()
    {
        $this->info("--- Initializing Organized Import ---");

        $keyPath = storage_path('app/projectli-b85f9-firebase-adminsdk-fbsvc-f7e89e3169.json'); 
        $jsonPath = storage_path('app/projectli.json');

        $keyData = json_decode(File::get($keyPath), true);
        $rawJson = json_decode(File::get($jsonPath), true);
        $projectId = $keyData['project_id'];
        $token = $this->getGoogleToken($keyData);

        foreach ($rawJson as $section) {
            if (!isset($section['type']) || $section['type'] !== 'table') continue;

            // This identifies if it is 'asset', 'users', etc.
            $tableName = $section['name']; 
            $dataRows = $section['data'] ?? [];

            // Map PHPMyAdmin names to clean Firestore Collection names
            $collectionName = match($tableName) {
                'users' => 'Staff',
                'asset' => 'Assets',
                default => ucfirst($tableName)
            };

            $this->info("\nCreating Collection: [$collectionName]");

            foreach ($dataRows as $row) {
                // Skip headers
                if (isset($row['SerialNo']) && $row['SerialNo'] == 'SerialNo') continue;

                // Set Document ID based on the table type
                $docId = $row['SerialNo'] ?? $row['UserID'] ?? $row['id'] ?? uniqid();

                $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collectionName}/{$docId}";

                $fields = [];
                foreach ($row as $key => $value) {
                    $cleanKey = strtolower(str_replace(' ', '_', $key));
                    $fields[$cleanKey] = ['stringValue' => (string)($value ?? 'N/A')];
                }

                $response = Http::withoutVerifying()->withToken($token)->patch($url, ['fields' => $fields]);

                if ($response->successful()) {
                    $this->output->write("<info>#</info>");
                }
            }
        }

        $this->info("\n\n--- SUCCESS: Database is now organized by category ---");
    }

    private function getGoogleToken($key) {
        $fetcher = new ServiceAccountCredentials('https://www.googleapis.com/auth/datastore', $key);
        $httpHandler = function ($request) {
            $client = new \GuzzleHttp\Client(['verify' => false]);
            return $client->send($request);
        };
        return $fetcher->fetchAuthToken($httpHandler)['access_token'];
    }
}