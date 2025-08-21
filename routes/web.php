<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route to display book-like interface
Route::get('qrs/{id}', function ($id) {
    // Validate that the ID is valid (1 or 2 for now)
    if (!in_array($id, [1, 2])) {
        abort(404, 'QR code not found');
    }

    return Inertia::render('Book', [
        'id' => (int) $id
    ]);
})->name('qrs.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
