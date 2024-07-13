<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\TpeController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ApporteurController;
use App\Http\Controllers\CallbackController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'welcome'])->name('welcome')->middleware(['auth']);

Route::get('/recover', [HomeController::class, 'recovery'])->name('recovery')->middleware(['auth']);
Route::post('/recovery-database', [HomeController::class, 'recoveryDatabase'])->name('recoveryDatabase')->middleware(['auth']);

Route::get('/roles', [ParametreController::class, 'roles'])->name('admin.roles')->middleware(['auth','hasPermission']);
Route::post('/roles/add', [ParametreController::class, 'rolesAdd'])->middleware(['auth']);
Route::post('/roles/edit/{id}', [ParametreController::class, 'rolesEdit'])->middleware(['auth']);
Route::post('/roles/delete/{id}', [ParametreController::class, 'rolesDelete'])->middleware(['auth']);

Route::get('/permissions', [ParametreController::class, 'permissions'])->name('admin.permissions')->middleware(['auth']);
Route::post('/permissions/add', [ParametreController::class, 'permissionsAdd'])->middleware(['auth']);
Route::post('/permissions/edit/{id}', [ParametreController::class, 'permissionsEdit'])->middleware(['auth']);
Route::post('/permissions/delete/{id}', [ParametreController::class, 'permissionsDelete'])->middleware(['auth']);

Route::get('/frais', [ParametreController::class, 'frais'])->name('admin.frais')->middleware(['auth','hasPermission']);
Route::post('/frais/add', [ParametreController::class, 'fraisAdd'])->middleware(['auth']);
Route::post('/frais/edit/{id}', [ParametreController::class, 'fraisEdit'])->middleware(['auth']);
Route::post('/frais/delete/{id}', [ParametreController::class, 'fraisDelete'])->middleware(['auth']);

Route::get('/restrictions', [ParametreController::class, 'restrictions'])->name('admin.restrictions')->middleware(['auth','hasPermission']);
Route::post('/restrictions/add', [ParametreController::class, 'restrictionsAdd'])->middleware(['auth']);
Route::post('/restrictions/edit/{id}', [ParametreController::class, 'restrictionsEdit'])->middleware(['auth']);
Route::post('/restrictions/delete/{id}', [ParametreController::class, 'restrictionsDelete'])->middleware(['auth']);
Route::post('/restrictions/activate/{id}', [ParametreController::class, 'restrictionsActivate'])->middleware(['auth']);
Route::post('/restrictions/desactivate/{id}', [ParametreController::class, 'restrictionsDesactivate'])->middleware(['auth']);


Route::get('/app/client', [AppController::class, 'appClient'])->name('admin.app.client')->middleware(['auth','hasPermission']);
Route::post('/card/infos/update', [AppController::class, 'cardInfosUpdate'])->middleware(['auth']);
Route::post('/service/client/add', [AppController::class, 'serviceClientAdd'])->middleware(['auth']);
Route::post('/question/add', [AppController::class, 'questionAdd'])->middleware(['auth']);
Route::post('/question/delete/{id}', [AppController::class, 'questionDelete'])->middleware(['auth']);

Route::get('/app/partenaire', [AppController::class, 'appPartenaire'])->name('admin.app.partenaire')->middleware(['auth','hasPermission']);
Route::post('/service/partenaire/add', [AppController::class, 'servicePartenaireAdd'])->middleware(['auth']);
Route::post('/service/delete/{id}', [AppController::class, 'serviceDelete'])->middleware(['auth']);
Route::post('/service/activate/{id}', [AppController::class, 'serviceActivate'])->middleware(['auth']);
Route::post('/service/desactivate/{id}', [AppController::class, 'serviceDesactivate'])->middleware(['auth']); 

Route::get('/app/admin', [AppController::class, 'appAdmin'])->name('admin.app.admin')->middleware(['auth','hasPermission']);
Route::post('/service/admin/add', [AppController::class, 'serviceAdminAdd'])->middleware(['auth']);
Route::get('/transfert/admin', [AppController::class, 'transfertAdmin'])->name('admin.transfert')->middleware(['auth']);
Route::post('/transfert/admin/add', [AppController::class, 'transfertAdminAdd'])->middleware(['auth']);
Route::get('/retrait/kkp', [AppController::class, 'retraitKkp'])->name('retrait.kkp')->middleware(['auth']);
Route::post('/retrait/kkp/add', [AppController::class, 'retraitKkpAdd'])->middleware(['auth']);

Route::get('/tpes', [TpeController::class, 'index'])->name('admin.tpes')->middleware(['auth','hasPermission']);
Route::post('/tpe/add', [TpeController::class, 'tpeAdd'])->middleware(['auth']);
Route::post('/tpe/edit/{id}', [TpeController::class, 'tpeEdit'])->middleware(['auth']);
Route::post('/tpe/delete/{id}', [TpeController::class, 'tpeDelete'])->middleware(['auth']);
Route::post('/tpe/activation/{id}', [TpeController::class, 'tpeActivation'])->middleware(['auth']);
Route::post('/tpe/desactivation/{id}', [TpeController::class, 'tpeDesactivation'])->middleware(['auth']);


Route::get('/users', [UserController::class, 'users'])->name('admin.users')->middleware(['auth','hasPermission']);
Route::post('/user/add', [UserController::class, 'userAdd'])->middleware(['auth']);
Route::post('/user/edit/{id}', [UserController::class, 'userEdit'])->middleware(['auth']);
Route::post('/user/delete/{id}', [UserController::class, 'userDelete'])->middleware(['auth']);
Route::post('/user/activation/{id}', [UserController::class, 'userActivation'])->middleware(['auth']);
Route::post('/user/desactivation/{id}', [UserController::class, 'userDesactivation'])->middleware(['auth']);
Route::get('/user/details/{id}', [UserController::class, 'userDetails'])->middleware(['auth']);
Route::post('/user/reset/password/{id}', [UserController::class, 'userResetPassword'])->middleware(['auth']);


Route::get('/promotion/partenaires', [PromotionController::class, 'partenaires'])->name('admin.promo.partenaires')->middleware(['auth','hasPermission']);
Route::post('/promotion/partenaire/add', [PromotionController::class, 'partenairePromoAdd'])->middleware(['auth']);
Route::post('/promotion/partenaire/edit/{id}', [PromotionController::class, 'partenairePromoEdit'])->middleware(['auth']);
Route::post('/promotion/partenaire/delete/{id}', [PromotionController::class, 'partenairePromoDelete'])->middleware(['auth']);

Route::get('/promotion/clients', [PromotionController::class, 'clients'])->name('admin.promo.clients')->middleware(['auth','hasPermission']);
Route::post('/promotion/client/add', [PromotionController::class, 'clientPromoAdd'])->middleware(['auth']);
Route::post('/promotion/client/edit/{id}', [PromotionController::class, 'clientPromoEdit'])->middleware(['auth']);
Route::post('/promotion/client/delete/{id}', [PromotionController::class, 'clientPromoDelete'])->middleware(['auth']);


Route::get('/apporteurs', [ApporteurController::class, 'index'])->name('admin.apporteurs')->middleware(['auth']);
Route::post('/apporteur/add', [ApporteurController::class, 'add'])->middleware(['auth']);
Route::post('/apporteur/edit/{id}', [ApporteurController::class, 'edit'])->middleware(['auth']);
Route::post('/apporteur/delete/{id}', [ApporteurController::class, 'delete'])->middleware(['auth']);
Route::post('/apporteur/activate/{id}', [ApporteurController::class, 'activate'])->middleware(['auth']);
Route::post('/apporteur/desactivate/{id}', [ApporteurController::class, 'desactivate'])->middleware(['auth']);
Route::post('/apporteur/reset/password/{id}', [ApporteurController::class, 'resetPassword'])->middleware(['auth']);
Route::post('/apporteur/reset/code/{id}', [ApporteurController::class, 'resetCode'])->middleware(['auth']);
Route::get('/apporteur/operations/{id}', [ApporteurController::class, 'operations'])->name('getOperationsApporteur');


Auth::routes();
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/test', [HomeController::class, 'test'])->name('test');

Route::get('/pay/{payment_id}', [AppController::class, 'initTransactionKkiapay'])->name('admin.init.transaction.kkiapay');
Route::post('/validation/transaction/kkiapay/{payment_id}', [AppController::class, 'validationTransactionKkiapay'])->name('admin.validation.transaction.kkiapay');
Route::post('/rejet/transaction/kkiapay/{payment_id}', [AppController::class, 'rejetTransactionKkiapay'])->name('admin.rejet.transaction.kkiapay');

//Route::post('/callback/kkiapay', [CallbackController::class, 'callBackKkiapay'])->name('callBack.Kkiapay');