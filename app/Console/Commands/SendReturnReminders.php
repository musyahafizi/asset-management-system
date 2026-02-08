<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReturnReminder;
// Use the same Firestore Auth logic from your controller here

class SendReturnReminders extends Command
{
    protected $signature = 'notify:returns';
    protected $description = 'Send email reminders for items due in 24 hours';

    public function handle()
    {
        // 1. Get Firestore Token (Use your existing getFirestoreConfig logic)
        $config = $this->getFirestoreConfig(); 
        
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Requests";
        $response = Http::withoutVerifying()->withToken($config['token'])->get($url)->json();

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $doc) {
                $f = $doc['fields'];
                $status = $f['status']['stringValue'] ?? '';
                $deadline = $f['return_deadline']['stringValue'] ?? '';
                
                // Only notify for Approved items that are due tomorrow
                if ($status === 'Approved' && $deadline === now()->addDay()->format('Y-m-d')) {
                    $email = $f['staff_email']['stringValue'] ?? ''; // Ensure you save email during borrow
                    if ($email) {
                        Mail::to('musyahafizi00@gmail.com')->send(new ReturnReminder(
    $f['staff_name']['stringValue'],
    $f['item_name']['stringValue'],
    $f['return_deadline']['stringValue']
));
                    }
                }
            }
        }
    }
}