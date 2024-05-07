<?php

use App\Http\Controllers\Api\Partenaire\RetraitController;
use App\Http\Controllers\Api\Partenaire\DepotController;
use App\Http\Controllers\Api\Partenaire\ApproController;
use App\Http\Controllers\Api\Partenaire\CashoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Partenaire\PartenaireController;
use App\Http\Controllers\Api\Partenaire\UserController;

Route::post('login/partenaire', [PartenaireController::class, 'loginPartenaire'])->name('loginPartenaire');
Route::get('get/services/partenaire', [PartenaireController::class, 'getServices'])->name('getServices');
Route::get('get/dashboard/partenaire/{id}', [PartenaireController::class, 'getDashboardPartenaire'])->name('getDashboardPartenaire');

Route::post('add/withdraw/partner', [RetraitController::class, 'addWithdrawPartenaire'])->name('addWithdrawPartenaire');
Route::post('cancel/client/withdraw/as/partner', [RetraitController::class, 'cancelClientWithdrawAsPartner'])->name('cancelClientWithdrawAsPartner');
Route::post('add/depot/partner', [DepotController::class, 'addDepotPartenaire'])->name('addDepotPartenaire');
Route::post('complete/depot/partner', [DepotController::class, 'completeDepotPartenaire'])->name('completeDepotPartenaire');

Route::get('get/partner/pending/customers/transactions/{user_partenaire_id}', [PartenaireController::class, 'getPartnerPendingCustomersTransactions'])->name('getPartnerPendingCustomersTransactions');
Route::get('get/partner/pending/admins/transactions/{user_partenaire_id}', [PartenaireController::class, 'getPartnerPendingAdminsTransactions'])->name('getPartnerPendingAdminsTransactions');
Route::get('get/partner/all/transactions/{user_partenaire_id}/{type?}', [PartenaireController::class, 'getPartnerAllTransactions'])->name('getPartnerAllTransactions');

Route::post('update/user/partenaire/info', [UserController::class, 'updateUserPartenaireInfo'])->name('updateUserPartenaireInfo');
Route::post('update/user/partenaire/password', [UserController::class, 'updateUserPartenairePassword'])->name('updateUserPartenairePassword');
Route::get('get/user/partenaire/info/{id}', [UserController::class, 'getUserPartenaireInfo'])->name('getUserPartenaireInfo');

Route::get('get/partner/wallets/{partnerId}', [CashoutController::class, 'getPartnerWallets'])->name('getPartnerWallets');
Route::post('add/partner/wallet/{walletType}', [CashoutController::class, 'addPartnerWallet'])->name('addPartnerWallet');
Route::post('update/partner/wallet/{walletType}/{walletId}', [CashoutController::class, 'updatePartnerWallet'])->name('updatePartnerWallet');
Route::get('delete/partner/wallet/{walletId}', [CashoutController::class, 'deletePartnerWallet'])->name('deletePartnerWallet');

Route::post('withdraw/partner/to/wallet/{walletId}', [CashoutController::class, 'withdrawPartnerToWallet'])->name('withdrawPartnerWallet');
Route::post('complete/withdraw/partner/to/wallet', [CashoutController::class, 'completeWithdrawPartnerToWallet'])->name('completeWithdrawPartnerWallet');
Route::post('withdraw/partner/to/distribution/account', [CashoutController::class, 'withdrawPartnerToDistributionAccount'])->name('withdrawPartnerDistributionAccount');
Route::post('withdraw/partner/to/atm', [CashoutController::class, 'withdrawPartnerToAtm'])->name('withdrawPartnerToAtm');

Route::post('deposit/partner/from/wallet/{walletId}', [ApproController::class, 'depositPartnerFromWallet'])->name('depositPartnerFromWallet');
Route::post('complete/deposit/partner/from/wallet', [ApproController::class, 'completeDepositPartnerFromWallet'])->name('completeDepositPartnerFromWallet');

Route::get('get/compte/commission/{id}', [PartenaireController::class, 'compteCommissionSolde'])->name('compteCommissionSolde');
Route::get('get/compte/distribution/{id}', [PartenaireController::class, 'compteDistributionSolde'])->name('compteDistributionSolde');

Route::post('retrait/commission/{id}', [PartenaireController::class, 'retraitCommission'])->name('retraitCommission');
Route::post('retrait/distribution/{id}', [PartenaireController::class, 'retraitDistribution'])->name('retraitDistribution');

Route::post('config/partner/pin', [PartenaireController::class, 'configPin'])->name('configPin');

Route::get('liste/retrait/unvalidate/partenaire', [PartenaireController::class, 'listeRetraitUnvalidatePartenaire'])->name('listeRetraitUnvalidatePartenaire');
Route::get('show/retrait/partenaire/{id}', [PartenaireController::class, 'showRetraitPartenaire'])->name('showRetraitPartenaire');
Route::post('cancel/retrait/partenaire', [PartenaireController::class, 'cancelRetraitPartenaire'])->name('cancelRetraitPartenaire');
Route::post('validate/retrait/partenaire', [PartenaireController::class, 'validateRetraitPartenaire'])->name('validateRetraitPartenaire');

Route::get('liste/depot/partenaire', [PartenaireController::class, 'listeDepotPartenaire'])->name('listeDepotPartenaire');
Route::get('liste/depot/unvalidate/partenaire', [PartenaireController::class, 'listeDepotUnvalidatePartenaire'])->name('listeDepotUnvalidatePartenaire');
Route::get('show/depot/partenaire/{id}', [PartenaireController::class, 'showDepotPartenaire'])->name('showDepotPartenaire');
Route::post('cancel/depot/partenaire', [PartenaireController::class, 'cancelDepotPartenaire'])->name('cancelDepotPartenaire');
Route::post('validate/depot/partenaire', [PartenaireController::class, 'validateDepotPartenaire'])->name('validateDepotPartenaire');

Route::get('liste/user/partenaire', [PartenaireController::class, 'listeUserPartenaire'])->name('listeUserPartenaire');
Route::get('show/user/partenaire', [PartenaireController::class, 'showUserPartenaire'])->name('showUserPartenaire');
Route::post('add/user/partenaire', [PartenaireController::class, 'addUserPartenaire'])->name('addUserPartenaire');
Route::post('edit/user/partenaire', [PartenaireController::class, 'editUserPartenaire'])->name('editUserPartenaire');
Route::post('delete/user/partenaire', [PartenaireController::class, 'deleteUserPartenaire'])->name('deleteUserPartenaire');
Route::post('activation/user/partenaire', [PartenaireController::class, 'activationUserPartenaire'])->name('activationUserPartenaire');
Route::post('desactivation/user/partenaire', [PartenaireController::class, 'desactivationUserPartenaire'])->name('desactivationUserPartenaire');
Route::post('reset/user/partenaire', [PartenaireController::class, 'resetUserPartenaire'])->name('resetUserPartenaire');

Route::get('liste/partenaire/seuil', [PartenaireController::class, 'listePartenaireSeuil'])->name('listePartenaireSeuil');
Route::post('add/partenaire/seuil', [PartenaireController::class, 'addPartenaireSeuil'])->name('addPartenaireSeuil');
Route::post('edit/partenaire/seuil', [PartenaireController::class, 'editPartenaireSeuil'])->name('editPartenaireSeuil');
Route::post('delete/partenaire/seuil', [PartenaireController::class, 'deletePartenaireSeuil'])->name('deletePartenaireSeuil');
Route::post('activation/partenaire/seuil', [PartenaireController::class, 'activationPartenaireSeuil'])->name('activationPartenaireSeuil');
Route::post('desactivation/partenaire/seuil', [PartenaireController::class, 'desactivationPartenaireSeuil'])->name('desactivationPartenaireSeuil');

Route::get('liste/partenaire/limit', [PartenaireController::class, 'listePartenaireLimit'])->name('listePartenaireLimit');
Route::post('add/partenaire/limit', [PartenaireController::class, 'addPartenaireLimit'])->name('addPartenaireLimit');
Route::post('edit/partenaire/limit', [PartenaireController::class, 'editPartenaireLimit'])->name('editPartenaireLimit');
Route::post('delete/partenaire/limit', [PartenaireController::class, 'deletePartenaireLimit'])->name('deletePartenaireLimit');

Route::get('role/liste', [PartenaireController::class, 'roleListe'])->name('roleListe');
Route::get('user/permissions', [PartenaireController::class, 'userPermissions'])->name('userPermissions');
Route::get('permissions', [PartenaireController::class, 'permissions'])->name('permissions');

Route::post('customer/credit/{program_id}', [PartenaireController::class, 'customerCredit'])->name('customer.credit');
Route::get('account/balance/{program_id}', [PartenaireController::class, 'accountBalance'])->name('account.balance');
Route::get('account/transactions/{program_id}', [PartenaireController::class, 'accountTransactions'])->name('account.transactions');

Route::get('get/partenaire/latest/version', [PartenaireController::class, 'getPartenaireLatestVersion'])->name('getPartenaireLatestVersion');

Route::get('get/client/{username}', [PartenaireController::class, 'getClient'])->name('getClient');
Route::get('generate/promo/code/{user_partenaire_id}', [PartenaireController::class, 'generateCodePromo'])->name('generateCodePromo');

Route::get('/check/partner/device/{serial}', [PartenaireController::class, 'checkPartnerDevice'])->name('checkPartnerDevice');
Route::post('/set/partner/location', [PartenaireController::class, 'setPartnerLocation'])->name('setPartnerLocation');
Route::get('/get/partners/location/{city}', [PartenaireController::class, 'getPartnersLocation'])->name('getPartnersLocation');
Route::get('/get/partner/location/{tpe}', [PartenaireController::class, 'getPartnerLocation'])->name('getPartnerLocation');

Route::post('/transfer/to/partner/distribution', [CashoutController::class, 'cessionBetweenPartner'])->name('cessionBetweenPartner');

Route::post('/init/payment/kkiapay', [PartenaireController::class, 'initPaiement'])->name('initPaiement');
Route::get('/check/payment/kkiapay/{payment_id}', [PartenaireController::class, 'checkPaiement'])->name('checkPaiement');