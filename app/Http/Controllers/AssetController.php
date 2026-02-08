<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AssetController extends Controller
{
    public function store(Request $request)
    {
        $firestore = Firebase::firestore()->database();

        // This creates a document in your Firestore 'assets' collection
        $firestore->collection('assets')->document($request->serial_no)->set([
            'model' => $request->model,
            'serial' => $request->serial_no,
            'location' => $request->location,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Asset synced to your Gmail Firestore!');
    }
}