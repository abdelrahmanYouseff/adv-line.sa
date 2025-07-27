<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route to display PDF files
Route::get('qrs/{id}', function ($id) {
    $pdfFiles = [
        1 => 'Delawa Menu.pdf',
        2 => 'منيو بكيزة.pdf'
    ];

    if (!isset($pdfFiles[$id])) {
        abort(404, 'QR code not found');
    }

    $fileName = $pdfFiles[$id];
    $pdfPath = public_path('pdf/' . $fileName);

    if (file_exists($pdfPath)) {
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $fileName . '"'
        ]);
    }

    abort(404, 'PDF file not found');
})->name('qrs.show');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
