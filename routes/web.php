<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Staff\StaffDashboardController;

// 1. PUBLIC ROUTES
Route::get('/', function () { return view('auth.login'); })->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// 2. STAFF ROUTES
Route::prefix('staff')->middleware(['auth'])->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::post('/borrow', [StaffDashboardController::class, 'borrowItem'])->name('borrow');
    Route::post('/return-item', [StaffDashboardController::class, 'returnItem'])->name('return');
});

// 3. ADMIN ROUTES
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Borrow Request Actions
    Route::post('/approve/{id}', [DashboardController::class, 'approveRequest'])->name('approve');
    Route::post('/reject/{id}', [DashboardController::class, 'rejectRequest'])->name('reject');

    // --- LAPTOP MANAGEMENT ---
    Route::get('/laptop', [DashboardController::class, 'viewLaptop'])->name('laptop');
    Route::get('/laptop/create', [DashboardController::class, 'createLaptop'])->name('laptop.insert'); 
    Route::post('/laptop/store', [DashboardController::class, 'storeLaptop'])->name('laptop.store');
    Route::get('/laptop/edit/{id}', [DashboardController::class, 'editLaptop'])->name('laptop.edit');
    // Note: This becomes admin.laptop.update because of the group prefix
    Route::put('/laptop/update/{id}', [DashboardController::class, 'updateLaptop'])->name('laptop.update');
    Route::delete('/laptop/delete/{id}', [DashboardController::class, 'deleteLaptop'])->name('laptop.delete');
    
    // PDF & QR Tools
    Route::post('/laptop/export-selected', [DashboardController::class, 'exportSelectedPdf'])->name('laptop.pdf.selected');
    Route::get('/laptop/pdf/{id}', [DashboardController::class, 'downloadLaptopPdf'])->name('laptop.single_pdf');
    Route::get('/laptop/qr/{id}', [DashboardController::class, 'generateQr'])->name('laptop.qr');

    // --- STAFF MANAGEMENT ---
    Route::get('/staff', [DashboardController::class, 'viewStaff'])->name('staff'); 
    Route::get('/staff-list', [DashboardController::class, 'viewStaff'])->name('staff_list'); 
    Route::get('/staff/create', [DashboardController::class, 'createStaff'])->name('staff.insert');
    Route::post('/staff/store', [DashboardController::class, 'storeStaff'])->name('staff.store');
    Route::get('/staff/edit/{id}', [DashboardController::class, 'editStaff'])->name('staff.edit');
    Route::match(['put', 'patch'], '/staff/update/{id}', [DashboardController::class, 'updateStaff'])->name('staff.update');
    Route::delete('/staff/delete/{id}', [DashboardController::class, 'deleteStaff'])->name('staff.delete');

    // --- ITEM INVENTORY ---
    Route::get('/items', [DashboardController::class, 'viewItems'])->name('items');
    Route::get('/items/create', [DashboardController::class, 'createItem'])->name('item.insert');
    Route::post('/items/store', [DashboardController::class, 'storeItem'])->name('item.store');

    // --- DESKTOP & BATCHES ---
    Route::get('/desktop', [DashboardController::class, 'viewDesktop'])->name('desktop');
    Route::get('/desktop/create', [DashboardController::class, 'createDesktop'])->name('desktop.insert');
    
    // Batch Laptop Insertion
    Route::get('/batch/create', [DashboardController::class, 'createBatch'])->name('batch.create');
    Route::post('/batch/store', [DashboardController::class, 'storeBatch'])->name('batch.store');

    // History
    Route::get('/history', [DashboardController::class, 'viewHistory'])->name('history');
});