<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RapportController;

Route::get('/rapport/transactions/clients', [RapportController::class, 'rapportTransactionClient'])->name('admin.rapport')->middleware(['auth']);
Route::post('/search/transactions/client', [RapportController::class, 'searchTransactionClient'])->middleware(['auth']);
Route::get('/download/transactions/client', [RapportController::class, 'downloadTransactionClient'])->middleware(['auth']);


Route::get('/rapport/achat/cartes', [RapportController::class, 'rapportAchatCarte'])->middleware(['auth']);
Route::post('/search/achat/cartes', [RapportController::class, 'searchAchatCarte'])->middleware(['auth']);
Route::get('/download/achat/cartes', [RapportController::class, 'downloadAchatCarte'])->middleware(['auth']);


Route::get('/rapport/transactions/partenaires', [RapportController::class, 'rapportTransactionPartenaire'])->middleware(['auth']);
Route::post('/search/transactions/partenaire', [RapportController::class, 'searchTransactionPartenaire'])->middleware(['auth']);
Route::get('/download/transactions/partenaire', [RapportController::class, 'downloadTransactionPartenaire'])->middleware(['auth']);


Route::get('/rapport/transactions/apporteurs', [RapportController::class, 'rapportTransactionApporteur'])->middleware(['auth']);
Route::post('/search/transactions/apporteur', [RapportController::class, 'searchTransactionApporteur'])->middleware(['auth']);
Route::get('/download/transactions/apporteur', [RapportController::class, 'downloadTransactionApporteur'])->middleware(['auth']);