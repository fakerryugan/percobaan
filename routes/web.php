<?php

use App\Http\Controllers\GuestBookController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\WeddingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Wedding Invitation & Ticket Scanning System
|--------------------------------------------------------------------------
*/

// Main Wedding Invitation (Supports ?to=Nama+Tamu or ?code=TKT-XXX)
Route::get('/', [WeddingController::class, 'index'])->name('wedding.home');

// Direct Personalized Invitation Link
Route::get('/to/{slug}', [WeddingController::class, 'showBySlug'])->name('wedding.to');

// E-Ticket Page for Guests
Route::get('/ticket/{code}', [WeddingController::class, 'ticket'])->name('wedding.ticket');

// RSVP & Wishes Submission
Route::post('/rsvp', [WeddingController::class, 'rsvp'])->name('wedding.rsvp');

// Self Check-in by Guest scanning Event QR
Route::post('/self-checkin', [WeddingController::class, 'selfCheckIn'])->name('wedding.self-checkin');

// Ticket Scanner for Receptionists / Ushers
Route::get('/scan', [ScannerController::class, 'index'])->name('scanner.index');
Route::post('/scan/verify', [ScannerController::class, 'verify'])->name('scanner.verify');
Route::get('/scan/event-qr', [ScannerController::class, 'eventQr'])->name('scanner.event-qr');

// Guest Management & Digital Guestbook (Admin)
Route::prefix('admin')->name('admin.guests.')->group(function () {
    Route::get('/', [GuestBookController::class, 'index'])->name('index');
    Route::post('/guests', [GuestBookController::class, 'store'])->name('store');
    Route::put('/guests/{guest}', [GuestBookController::class, 'update'])->name('update');
    Route::delete('/guests/{guest}', [GuestBookController::class, 'destroy'])->name('destroy');
    Route::post('/guests/{guest}/toggle-checkin', [GuestBookController::class, 'toggleCheckIn'])->name('toggle-checkin');
});

// Alias for guestbook
Route::get('/buku-tamu', [GuestBookController::class, 'index']);
