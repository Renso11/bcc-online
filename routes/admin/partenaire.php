<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Partenaires\PartenaireController;

Route::get('/partenaires', [PartenaireController::class, 'partenaires'])->name('admin.partenaires')->middleware(['auth','hasPermission']);

Route::get('/partenaire/details/{id}', [PartenaireController::class, 'partenaireDetails'])->name('admin.partenaire.details')->middleware(['auth','hasPermission']);
Route::get('/partenaire/compte/commission/{id}', [PartenaireController::class, 'partenaireCompteCommission'])->middleware(['auth']);
Route::get('/partenaire/compte/distribution/{id}', [PartenaireController::class, 'partenaireCompteDistribution'])->middleware(['auth']);

Route::get('/partenaire/users/{id}', [PartenaireController::class, 'partenaireUsers'])->name('admin.partenaire.users')->middleware(['auth','hasPermission']);
Route::get('/partenaire/user/new/{id}', [PartenaireController::class, 'partenaireNewUser'])->middleware(['auth']);
Route::post('/partenaire/user/add/{id}', [PartenaireController::class, 'partenaireUserAdd'])->middleware(['auth']);
Route::post('/partenaire/user/edit/{id}', [PartenaireController::class, 'partenaireUserEdit'])->middleware(['auth']);
Route::post('/partenaire/user/delete/{id}', [PartenaireController::class, 'partenaireUserDelete'])->middleware(['auth']);
Route::post('/partenaire/user/activation/{id}', [PartenaireController::class, 'partenaireUserActivation'])->middleware(['auth']);
Route::post('/partenaire/user/desactivation/{id}', [PartenaireController::class, 'partenaireUserDesactivation'])->middleware(['auth']);
Route::post('/partenaire/user/reset/password/{id}', [PartenaireController::class, 'partenaireUserResetPassword'])->middleware(['auth']);

Route::get('/partenaire/new', [PartenaireController::class, 'partenaireNew'])->middleware(['auth']);
Route::post('/partenaire/add', [PartenaireController::class, 'partenaireAdd'])->middleware(['auth']);
Route::get('/partenaire/edit/{id}', [PartenaireController::class, 'partenaireEdit'])->middleware(['auth']);
Route::post('/partenaire/update/{id}', [PartenaireController::class, 'partenaireUpdate'])->middleware(['auth']);
Route::post('/partenaire/delete/{id}', [PartenaireController::class, 'partenaireDelete'])->middleware(['auth']);


Route::post('/download/partenaire/revele/{partenaire_id}', [PartenaireController::class, 'downloadPartenaireReleve'])->name('admin.download.partenaire.revele')->middleware(['auth','hasPermission']);
Route::post('/view/partenaire/revele/{partenaire_id}', [PartenaireController::class, 'viewPartenaireReleve'])->middleware(['auth']);


Route::get('/partenaire/operations/attentes', [PartenaireController::class, 'partenaireOperationsAttentes'])->name('admin.partenaire.operations.attentes')->middleware(['auth','hasPermission']);
Route::post('/partenaire/operations/attentes/cancel', [PartenaireController::class, 'partenaireOperationsAttentesCancel'])->name('admin.partenaire.operations.attentes.cancel')->middleware(['auth','hasPermission']);
Route::post('/partenaire/operations/attentes/refund', [PartenaireController::class, 'partenaireOperationsAttentesRefund'])->name('admin.partenaire.operations.attentes.refund')->middleware(['auth','hasPermission']);
Route::post('/partenaire/operations/attentes/complete', [PartenaireController::class, 'partenaireOperationsAttentesComplete'])->name('admin.partenaire.operations.attentes.complete')->middleware(['auth','hasPermission']);
Route::get('/partenaire/operations/finalises', [PartenaireController::class, 'partenaireOperationsFinalises'])->middleware(['auth']);
Route::get('/partenaire/operations/remboursees', [PartenaireController::class, 'partenaireOperationsRemboursees'])->middleware(['auth']);
Route::get('/partenaire/operations/annulees', [PartenaireController::class, 'partenaireOperationsAnnulees'])->middleware(['auth']);

Route::post('/partenaire/recharge/init/{id}', [PartenaireController::class, 'partenaireRechargeInit'])->name('admin.partenaire.recharge.init')->middleware(['auth','hasPermission']);
Route::get('/partenaire/recharges/attentes', [PartenaireController::class, 'partenaireRechargeAttentes'])->name('admin.partenaire.recharge.attentes')->middleware(['auth','hasPermission']);
Route::post('/partenaire/valide/recharge/{id}', [PartenaireController::class, 'partenaireRechargeValidation'])->middleware(['auth']);
