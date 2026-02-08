<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Google\Auth\Credentials\ServiceAccountCredentials;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 1. Setup Firestore Access
        $keyPath = storage_path('app/projectli-b85f9-firebase-adminsdk-fbsvc-f7e89e3169.json');
        $keyData = json_decode(File::get($keyPath), true);
        
        $fetcher = new ServiceAccountCredentials('https://www.googleapis.com/auth/datastore', $keyData);
        $token = $fetcher->fetchAuthToken(function ($request) {
            return (new \GuzzleHttp\Client(['verify' => false]))->send($request);
        })['access_token'];

        // 2. Search Firestore Staff collection for this email
        $url = "https://firestore.googleapis.com/v1/projects/{$keyData['project_id']}/databases/(default)/documents:runQuery";
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => 'Staff']],
                'where' => [
                    'fieldFilter' => [
                        'field' => ['fieldPath' => 'email_address'],
                        'op' => 'EQUAL',
                        'value' => ['stringValue' => $request->email]
                    ]
                ]
            ]
        ];

        $response = Http::withoutVerifying()->withToken($token)->post($url, $query)->json();

        // 3. Verify Identity
        if (!empty($response) && isset($response[0]['document'])) {
            $fields = $response[0]['document']['fields'];
            
            // Get data and trim any trailing spaces from Firestore
            $firestorePassword = $fields['password']['stringValue'] ?? '';
            $role = trim($fields['role']['stringValue'] ?? 'staff'); 
            $firstName = $fields['first_name']['stringValue'] ?? 'User';

            // Check if input password matches Firestore string
            if ($request->password === $firestorePassword) {
                
                // Sync with local Laravel Auth
                $user = User::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'name' => $firstName, 
                        'password' => bcrypt($firestorePassword),
                        'role' => $role 
                    ]
                );
                
                Auth::login($user);

                // Role-Based Redirection
                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard');
                } else {
                    return redirect()->route('staff.dashboard');
                }
            }
        }

        return back()->withErrors(['email' => 'Invalid email or password.']);
    }
    public function logout()
{
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/')->with('success', 'You have been logged out.');
}
}