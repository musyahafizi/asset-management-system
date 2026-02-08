<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Google\Auth\Credentials\ServiceAccountCredentials;

class StaffDashboardController extends Controller
{
    /**
     * Helper: Firestore Authentication
     */
    private function getFirestoreConfig()
    {
        $keyPath = storage_path('app/projectli-b85f9-firebase-adminsdk-fbsvc-f7e89e3169.json');
        if (!File::exists($keyPath)) abort(500, "Service account file not found.");
        
        $keyData = json_decode(File::get($keyPath), true);
        $fetcher = new ServiceAccountCredentials('https://www.googleapis.com/auth/datastore', $keyData);
        $token = $fetcher->fetchAuthToken(function ($request) {
            return (new \GuzzleHttp\Client(['verify' => false]))->send($request);
        })['access_token'];

        return ['token' => $token, 'project_id' => $keyData['project_id']];
    }

    /**
     * Dashboard: Fetch Items and Personal Requests
     */
    public function index()
    {
        $config = $this->getFirestoreConfig();
        $projectId = $config['project_id'];
        $token = $config['token'];

        // 1. Fetch Available Items
        $itemsUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/Item";
        $itemsResponse = Http::withoutVerifying()->withToken($token)->get($itemsUrl)->json();

        $items = [];
        if (isset($itemsResponse['documents'])) {
            foreach ($itemsResponse['documents'] as $doc) {
                $f = $doc['fields'];
                $items[] = [
                    'id'   => basename($doc['name']),
                    'name' => $f['name']['stringValue'] ?? 'Unknown',
                    'qtty' => $f['qtty']['stringValue'] ?? '0',
                ];
            }
        }

        // 2. Fetch Borrowing History
        $currentStaffName = trim(Auth::user()->name); 
        $requestsUrl = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/Requests";
        $requestsResponse = Http::withoutVerifying()->withToken($token)->get($requestsUrl)->json();

        $myRequests = [];
        if (isset($requestsResponse['documents'])) {
            foreach ($requestsResponse['documents'] as $doc) {
                $f = $doc['fields'];
                $dbStaffName = trim($f['staff_name']['stringValue'] ?? '');

                if (strcasecmp($dbStaffName, $currentStaffName) === 0) {
    $myRequests[] = [
        'id'              => basename($doc['name']),
        'item_id'         => $f['item_id']['stringValue'] ?? '',
        'item_name'       => $f['item_name']['stringValue'] ?? 'N/A',
        'quantity'        => $f['quantity']['integerValue'] ?? ($f['quantity']['stringValue'] ?? 0),
        'status'          => $f['status']['stringValue'] ?? 'Pending',
        'request_date'    => $f['request_date']['stringValue'] ?? 'N/A',
        'return_deadline' => $f['return_deadline']['stringValue'] ?? 'N/A', // Add this line
    ];
}
            }
        }

        return view('admin.staff_dashboard', [
            'items' => $items,
            'myRequests' => $myRequests,
            'username' => $currentStaffName
        ]);
    }

    /**
     * Submit Borrow Request
     */
    public function borrowItem(Request $request)
    {
        $config = $this->getFirestoreConfig();
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Requests";

        $data = [
            'fields' => [
                'item_id'         => ['stringValue' => (string)$request->item_id],
                'item_name'       => ['stringValue' => (string)$request->item_name],
                'staff_name'      => ['stringValue' => (string)Auth::user()->name],
                // THIS LINE sets which email the notification will be sent to later
                'staff_email'     => ['stringValue' => (string)Auth::user()->email], 
                'quantity'        => ['integerValue' => (int)$request->quantity],
                'request_date'    => ['stringValue' => now()->format('Y-m-d')],
                'return_deadline' => ['stringValue' => (string)$request->return_date], 
                'status'          => ['stringValue' => 'Pending'],
            ]
        ];

        Http::withoutVerifying()->withToken($config['token'])->post($url, $data);
        return redirect()->back()->with('success', 'Request sent!');
    }

    /**
     * Return Item and update inventory
     */
    public function returnItem(Request $request)
    {
        $config = $this->getFirestoreConfig();
        
        $requestId = $request->request_id;
        $urlReq = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Requests/{$requestId}?updateMask.fieldPaths=status";
        
        Http::withoutVerifying()->withToken($config['token'])->patch($urlReq, [
            'fields' => ['status' => ['stringValue' => 'Returned']]
        ]);

        $itemId = $request->item_id;
        $qtyToReturn = (int)$request->quantity;

        $urlItem = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Item/{$itemId}";
        $itemData = Http::withoutVerifying()->withToken($config['token'])->get($urlItem)->json();
        
        $currentQty = (int)($itemData['fields']['qtty']['stringValue'] ?? 0);
        $newQty = $currentQty + $qtyToReturn;

        Http::withoutVerifying()->withToken($config['token'])->patch($urlItem . "?updateMask.fieldPaths=qtty", [
            'fields' => ['qtty' => ['stringValue' => (string)$newQty]]
        ]);

        return redirect()->back()->with('success', 'Item marked as returned and inventory updated.');
    }
}