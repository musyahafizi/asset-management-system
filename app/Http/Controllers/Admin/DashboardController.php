<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * CORE UTILITIES - Used by all functions
     */
    private function safeGet($fields, $key, $default = 'N/A') 
    {
        if (!isset($fields[$key])) return $default;
        return $fields[$key]['stringValue'] ?? ($fields[$key]['booleanValue'] ?? ($fields[$key]['integerValue'] ?? ($fields[$key]['doubleValue'] ?? $default)));
    }

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
     * VIEW COLLECTIONS (Laptop & Staff)
     * Fixes the "31 showing 30" issue by strict batch matching
     */
    public function viewLaptop(Request $request) { return $this->fetchCollection($request, 'Laptop', 'view_laptop'); }
    public function viewStaff(Request $request) { return $this->fetchCollection($request, 'Staff', 'staff_list'); }

    private function fetchCollection(Request $request, $collection, $viewName)
    {
        $config = $this->getFirestoreConfig();
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/{$collection}";
        $response = Http::withoutVerifying()->withToken($config['token'])->get($url)->json();

        $dataList = [];
        $searchTerm = strtolower($request->query('search', ''));

        if (isset($response['documents'])) {
            foreach ($response['documents'] as $doc) {
                $f = $doc['fields'];
                $item = ['id' => basename($doc['name'])];
                
                if ($collection === 'Laptop') {
                    $item += [
                        'model' => $this->safeGet($f, 'model'),
                        'serial_number' => $this->safeGet($f, 'serial_number'),
                        'batchno' => $this->safeGet($f, 'batchno'), //
                        'firstname' => $this->safeGet($f, 'firstname'), //
                        'lastname' => $this->safeGet($f, 'lastname'),
                        'department' => $this->safeGet($f, 'department'),
                        'status' => $this->safeGet($f, 'availability_status', 'In Stock'),
                    ];
                } else if ($collection === 'Staff') {
                    $item += [
                        'userid' => $this->safeGet($f, 'userid'),
                        'first_name' => $this->safeGet($f, 'first_name'),
                        'last_name' => $this->safeGet($f, 'last_name'),
                        'email_address' => $this->safeGet($f, 'email_address'), //
                        'account_disabled' => $this->safeGet($f, 'account_disabled'), //
                        'account_locked_out' => $this->safeGet($f, 'account_locked_out'), //
                        'company' => $this->safeGet($f, 'company'),
                    ];
                }else if ($collection === 'Item') {
        $item += [
            'name'        => $this->safeGet($f, 'name'),        //
            'description' => $this->safeGet($f, 'description'), //
            'qtty'        => $this->safeGet($f, 'qtty'),        //
        ];
    }

                if ($searchTerm) {
                    $match = false;
                    foreach ($item as $key => $value) {
                        $valString = strtolower((string)$value);
                        // Strict check for Batch No to prevent partial matches like 31 matching 30
                        if ($key === 'batchno' && $valString === $searchTerm) { $match = true; break; }
                        else if ($key !== 'batchno' && str_contains($valString, $searchTerm)) { $match = true; break; }
                    }
                    if ($match) $dataList[] = $item;
                } else {
                    $dataList[] = $item;
                }
            }
        }
        return view("admin.{$viewName}", ['staffList' => $dataList]);
    }

    /**
     * EXPORT METHODS (Single and Multiple)
     */
    public function exportSelectedPdf(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        if (empty($selectedIds)) return back()->with('error', 'No items selected.');

        $config = $this->getFirestoreConfig();
        $laptops = [];

        foreach ($selectedIds as $id) {
            $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Laptop/{$id}";
            $res = Http::withoutVerifying()->withToken($config['token'])->get($url)->json();
            if (isset($res['fields'])) {
                $f = $res['fields'];
                $laptops[] = [
                    'model' => $this->safeGet($f, 'model'),
                    'serial_number' => $this->safeGet($f, 'serial_number'),
                    'batchno' => $this->safeGet($f, 'batchno'),
                    'user' => $this->safeGet($f, 'firstname') . ' ' . $this->safeGet($f, 'lastname'),
                ];
            }
        }
        return Pdf::loadView('admin.laptop_export_pdf', compact('laptops'))->download('Selected_Inventory.pdf');
    }

    public function downloadLaptopPdf($id) {
        $config = $this->getFirestoreConfig();
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Laptop/{$id}";
        $doc = Http::withoutVerifying()->withToken($config['token'])->get($url)->json();
        return Pdf::loadView('admin.laptop_pdf', ['id' => $id, 'model' => $this->safeGet($doc['fields'], 'model')])->download("Laptop_{$id}.pdf");
    }

    /**
     * STAFF MANAGEMENT
     */
    public function editStaff($id)
    {
        $config = $this->getFirestoreConfig();
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Staff/{$id}";
        $res = Http::withoutVerifying()->withToken($config['token'])->get($url)->json();

        if (!isset($res['fields'])) return redirect()->route('admin.view_staff')->with('error', 'Staff not found.');

        $f = $res['fields'];
        $staff = [
            'id' => $id,
            'userid' => $this->safeGet($f, 'userid'),
            'first_name' => $this->safeGet($f, 'first_name'),
            'last_name' => $this->safeGet($f, 'last_name'),
            'email_address' => $this->safeGet($f, 'email_address'), //
            'account_disabled' => $this->safeGet($f, 'account_disabled'),
            'account_locked_out' => $this->safeGet($f, 'account_locked_out'),
            'company' => $this->safeGet($f, 'company'),
        ];
        return view('admin.edit_staff', compact('staff'));
    }

    public function updateStaff(Request $request, $id)
    {
        $config = $this->getFirestoreConfig();
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Staff/{$id}?updateMask.fieldPaths=first_name&updateMask.fieldPaths=last_name&updateMask.fieldPaths=email_address&updateMask.fieldPaths=account_disabled&updateMask.fieldPaths=account_locked_out";
        
        $fields = ['fields' => [
            'first_name' => ['stringValue' => $request->first_name],
            'last_name' => ['stringValue' => $request->last_name],
            'email_address' => ['stringValue' => $request->email_address],
            'account_disabled' => ['stringValue' => $request->account_disabled ?? 'FALSE'],
            'account_locked_out' => ['stringValue' => $request->account_locked_out ?? 'FALSE'],
        ]];

        Http::withoutVerifying()->withToken($config['token'])->patch($url, $fields);
        return redirect()->route('admin.staff')->with('success', 'Staff updated.');
    }

    /**
     * OTHER ACTIONS
     */
    public function generateQr($id) {
        $config = $this->getFirestoreConfig();
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Laptop/{$id}";
        $res = Http::withoutVerifying()->withToken($config['token'])->get($url)->json();
        $f = $res['fields'];
        return view('admin.laptop_qr', [
            'id' => $id, 'model' => $this->safeGet($f, 'model'),
            'sn' => $this->safeGet($f, 'serial_number'),
            'user' => $this->safeGet($f, 'firstname') . ' ' . $this->safeGet($f, 'lastname')
        ]);
    }

    public function deleteLaptop($id) {
        $config = $this->getFirestoreConfig();
        $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Laptop/{$id}";
        Http::withoutVerifying()->withToken($config['token'])->delete($url);
        return back()->with('success', 'Deleted.');
    }

    public function index()
    {
        $config = $this->getFirestoreConfig();
        $username = Auth::user()->name ?? 'Admin';

        // 1. Check Low Stock Items
        $urlItem = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Item";
        $resItem = Http::withoutVerifying()->withToken($config['token'])->get($urlItem)->json();

        $lowStockCount = 0;
        $lowStockList = [];
        if (isset($resItem['documents'])) {
            foreach ($resItem['documents'] as $doc) {
                $f = $doc['fields'];
                $qty = intval($f['qtty']['stringValue'] ?? 0);
                if ($qty < 50) { 
                    $lowStockCount++; 
                    $lowStockList[] = ['name' => $f['name']['stringValue'] ?? 'Unknown', 'qty' => $qty];
                }
            }
        }
        if (count($lowStockList) > 0) { $this->sendLowStockSummary($lowStockList); }

        // 2. Fetch Pending Requests (Now includes Return Deadline)
        $urlReq = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Requests";
        $resReq = Http::withoutVerifying()->withToken($config['token'])->get($urlReq)->json();

        $pendingRequests = [];
        if (isset($resReq['documents'])) {
            foreach ($resReq['documents'] as $doc) {
                $f = $doc['fields'];
                if (($f['status']['stringValue'] ?? '') === 'Pending') {
                    $pendingRequests[basename($doc['name'])] = [
                        'staff_name'      => $this->safeGet($f, 'staff_name'),
                        'item_name'       => $this->safeGet($f, 'item_name'),
                        'quantity'        => $f['quantity']['integerValue'] ?? ($f['quantity']['stringValue'] ?? 0),
                        'request_date'    => $this->safeGet($f, 'request_date'),
                        'return_deadline' => $this->safeGet($f, 'return_deadline'), // Displayed in Admin Dashboard
                    ];
                }
            }
        }

        return view('admin.dashboard', [
            'itemCount' => $lowStockCount, 
            'username' => $username,
            'pendingRequests' => $pendingRequests
        ]);
    }
    
    // Add these if you have "Add Item" or "Add Batch" buttons
    /**
     * ITEM INVENTORY METHODS
     */
    public function viewItems(Request $request) 
    { 
        // This reuses the logic we used for Laptops/Staff
        return $this->fetchCollection($request, 'Item', 'view_items'); 
    }

    public function createItem() 
    { 
        return view('admin.insert_item'); 
    }

   public function storeItem(Request $request)
{
    $config = $this->getFirestoreConfig();
    $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Item";

    $fields = [
        'fields' => [
            'name'        => ['stringValue' => $request->input('name')],        //
            'description' => ['stringValue' => $request->input('description')], //
            'qtty'        => ['stringValue' => $request->input('quantity')],        //
        ]
    ];

    // Added error checking to see why it fails
    $response = Http::withoutVerifying()->withToken($config['token'])->post($url, $fields);

    if ($response->failed()) {
        return back()->with('error', 'Firestore Error: ' . $response->body());
    }

    return redirect()->route('admin.items')->with('success', 'Item added to Firestore!');
}
  

    private function sendLowStockSummary($items) {
        Mail::send([], [], function ($message) use ($items) {
            $html = "<ul>";
            foreach ($items as $i) { $html .= "<li>{$i['name']}: {$i['qty']} remaining</li>"; }
            $html .= "</ul>";
            $message->to('musyahafizi00@gmail.com')->subject('📊 Low Stock Alert')->html($html);
        });
    }

/**
     * Display the form to create a new staff member.
     */
    public function createStaff()
    {
        return view('admin.insert_staff'); // Ensure this blade file exists
    }

    /**
     * Store a new staff member in Firestore.
     */
  public function storeStaff(Request $request)
{
    $config = $this->getFirestoreConfig();
    $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Staff";

    $fields = [
        'fields' => [
            'user_id'          => ['stringValue' => $request->input('UserID')],
            'first_name'       => ['stringValue' => $request->input('FirstName')],
            'last_name'        => ['stringValue' => $request->input('LastName')],
            'email_address'    => ['stringValue' => $request->input('EmailAddress')], //
            'company'          => ['stringValue' => $request->input('Company')],
            'role'             => ['stringValue' => 'staff'],
            // Convert "TRUE"/"FALSE" strings to real Booleans
            'account_disabled' => ['booleanValue' => $request->input('AccountDisabled') === 'TRUE'], //
            'account_locked_out' => ['booleanValue' => $request->input('AccountLockedOut') === 'TRUE'], //
        ]
    ];

    $response = Http::withoutVerifying()->withToken($config['token'])->post($url, $fields);

    if ($response->successful()) {
        return redirect()->route('admin.staff_list')->with('success', 'Staff member added!');
    }

    // This will help us find the error if it still fails
    dd('Firestore Error:', $response->json());
}
    

// Method for Single Laptop
public function createLaptop() {
    return view('admin.insert_laptop');
}

// Method for Batch Laptop
public function createBatch() {
    return view('admin.insert_batch'); // Create this view next
}

// Store Batch Logic
public function storeBatch(Request $request)
{
    $config = $this->getFirestoreConfig();
    $token = $config['token'];
    $project = $config['project_id'];

    $batchNo = $request->input('batchno');
    $laptops = $request->input('laptops'); // This is the array from the table

    foreach ($laptops as $laptopData) {
        $url = "https://firestore.googleapis.com/v1/projects/{$project}/databases/(default)/documents/Laptop";
        
        $fields = [
            'fields' => [
                'asset_tag'     => ['stringValue' => $laptopData['asset_tag']],
                'brand'         => ['stringValue' => $laptopData['brand']],
                'model'         => ['stringValue' => $laptopData['model']],
                'serial_no'     => ['stringValue' => $laptopData['serial_no']],
                'batch_id'      => ['stringValue' => $batchNo], // Links the laptop to the batch
                'status'        => ['stringValue' => 'Available'],
                'date_created'  => ['stringValue' => date('Y-m-d H:i:s')]
            ]
        ];

        Http::withoutVerifying()->withToken($token)->post($url, $fields);
    }

    return redirect()->route('admin.laptop')->with('success', count($laptops) . ' laptops added in batch ' . $batchNo);
}
public function storeLaptop(Request $request)
{
    $config = $this->getFirestoreConfig();
    // Collection is 'Laptop'
    $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Laptop";

    $fields = [
        'fields' => [
            'agreement_no'       => ['stringValue' => (string)$request->input('agreement_no', '')], //
            'availability_status'=> ['stringValue' => 'Available'], // Default status
            'batchno'            => ['stringValue' => (string)$request->input('batchno', '')],      //
            'department'         => ['stringValue' => (string)$request->input('department', '')],   //
            'firstname'          => ['stringValue' => (string)$request->input('firstname', '')],    //
            'lastname'           => ['stringValue' => (string)$request->input('lastname', '')],     //
            'last_owner'         => ['stringValue' => ''], // Empty for new entry
            'lease_start_date'   => ['stringValue' => (string)$request->input('lease_start_date', '')], //
            'lease_end_date'     => ['stringValue' => (string)$request->input('lease_end_date', '')],   //
            'leasing_period'     => ['stringValue' => (string)$request->input('leasing_period', '')],   //
            'location'           => ['stringValue' => (string)$request->input('location', '')],     //
            'model'              => ['stringValue' => (string)$request->input('model', '')],        //
            'date_of_collection' => ['stringValue' => ''], //
            'date_of_return'     => ['stringValue' => ''], //
        ]
    ];

    $response = Http::withoutVerifying()->withToken($config['token'])->post($url, $fields);

    if ($response->successful()) {
        return redirect()->route('admin.laptop')->with('success', 'Laptop registered successfully!');
    }

    // Displays the error if something goes wrong
    return dd('Firestore Error: ' . $response->body());
}

public function editLaptop($id)
{
    $config = $this->getFirestoreConfig();
    // Fetch the specific document by ID
    $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Laptop/{$id}";

    $response = Http::withoutVerifying()->withToken($config['token'])->get($url);

    if ($response->successful()) {
        $data = $response->json();
        $fields = $data['fields'] ?? [];

        // Map Firestore fields back to a simple array for the view
        $laptop = [
            'id'                 => $id,
            'agreement_no'       => $fields['agreement_no']['stringValue'] ?? '',
            'batchno'            => $fields['batchno']['stringValue'] ?? '',
            'firstname'          => $fields['firstname']['stringValue'] ?? '',
            'lastname'           => $fields['lastname']['stringValue'] ?? '',
            'department'         => $fields['department']['stringValue'] ?? '',
            'location'           => $fields['location']['stringValue'] ?? '',
            'lease_start_date'   => $fields['lease_start_date']['stringValue'] ?? '',
            'lease_end_date'     => $fields['lease_end_date']['stringValue'] ?? '',
            'leasing_period'     => $fields['leasing_period']['stringValue'] ?? '',
            'model'              => $fields['model']['stringValue'] ?? '',
            'availability_status'=> $fields['availability_status']['stringValue'] ?? '',
        ];

        return view('admin.edit_laptop', compact('laptop'));
    }

    return redirect()->route('admin.laptop')->with('error', 'Laptop not found.');
}

public function updateLaptop(Request $request, $id)
{
    $config = $this->getFirestoreConfig();
    
    // The Firestore URL for a specific document
    // We use the 'updateMask' or a PATCH request to update fields
    $url = "https://firestore.googleapis.com/v1/projects/{$config['project_id']}/databases/(default)/documents/Laptop/{$id}";

    $fields = [
        'fields' => [
            'agreement_no'       => ['stringValue' => (string)$request->input('agreement_no', '')],
            'batchno'            => ['stringValue' => (string)$request->input('batchno', '')],     
            'firstname'          => ['stringValue' => (string)$request->input('firstname', '')],   
            'lastname'           => ['stringValue' => (string)$request->input('lastname', '')],    
            'department'         => ['stringValue' => (string)$request->input('department', '')],  
            'location'           => ['stringValue' => (string)$request->input('location', '')],    
            'lease_start_date'   => ['stringValue' => (string)$request->input('lease_start_date', '')],
            'lease_end_date'     => ['stringValue' => (string)$request->input('lease_end_date', '')],  
            'leasing_period'     => ['stringValue' => (string)$request->input('leasing_period', '')],  
            'model'              => ['stringValue' => (string)$request->input('model', '')],       
            'availability_status'=> ['stringValue' => (string)$request->input('availability_status', 'Available')],
        ]
    ];

    // Use PATCH to update existing data in Firestore
    $response = Http::withoutVerifying()->withToken($config['token'])->patch($url, $fields);

    if ($response->successful()) {
        return redirect()->route('admin.laptop')->with('success', 'Laptop updated successfully!');
    }

    return dd('Update Error:', $response->json());
}

}