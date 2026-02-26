<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // --- Cadastros base ---
    Route::resource('statuses', StatusController::class)->except(['show']);
    Route::resource('clients', ClientController::class)->except(['show']);

    // --- Propostas (internamente: quotes) ---
    Route::resource('quotes', QuoteController::class);

    // ✅ NOVO: Quick edit do status na listagem (/quotes)
    Route::patch('quotes/{quote}/status', [QuoteController::class, 'updateStatus'])
        ->name('quotes.update-status');

    // --- Serviços e Parceiros ---
    Route::resource('services', ServiceController::class)->except(['show']);
    Route::resource('partners', PartnerController::class)->except(['show']);

    // --- Lookups (autocomplete) ---
    Route::get('/lookups/clients', [LookupController::class, 'clients'])->name('lookups.clients');
    Route::get('/lookups/services', [LookupController::class, 'services'])->name('lookups.services');
    Route::get('/lookups/partners', [LookupController::class, 'partners'])->name('lookups.partners');

    // --- Profile (Breeze) ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';