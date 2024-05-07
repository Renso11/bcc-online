<?php

namespace App\Http\Controllers\API\Partenaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retrait;
use App\Models\Depot;
use App\Models\Service;
use App\Models\Frai;
use App\Models\Partenaire;
use App\Models\UserClient;
use App\Models\UserPartenaire;
use App\Models\TpeLocation;
use App\Models\Tpe;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Carbon;
use App\Models\ApiPartenaireAccount;
use App\Models\ApiPartenaireFee;
use App\Models\ApiPartenaireTransaction;
use App\Models\PartnerWalletDeposit;
use App\Models\PartnerWalletWithdraw;
use App\Models\FrontPayment;
use App\Models\PartnerCession;
use App\Models\PartnerWallet;
use App\Models\Recharge;
use App\Models\TransfertOut;
use App\Models\UserCardBuy;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class PartenaireController extends Controller
{

    public function __construct() {
        //$this->middleware('auth:apiPartenaire', ['except' => ['loginPartenaire','addRetraitPartenaire','userPermissions','permissions']]);
    }    

    public function loginPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }

            if (! $token = Auth::guard('apiPartenaire')->attempt($validator->validated())) {
                return  sendError('Identifiants incorrectes', [],401);
            }
            
            $user = UserPartenaire::where('id',auth('apiPartenaire')->user()->id)->first();
            
            if($user->status == 0){
                return sendError('Ce compte est désactivé. Veuillez contactez le service clientèle', [], 401);
            }
            $resultat['token'] = $this->createNewToken($token);
            $user->lastconnexion = date('d-M-Y H:i:s');
            $user->partenaire;
            $user->role->rolePermissions;
            $user->save();
            $user->makeHidden(['password']);
            $modules = Service::where('deleted',0)->where('type','partenaire')->get();
            $resultat['user'] = $user;
            $resultat['services'] = $modules;

            return sendResponse($resultat, 'Connexion réussie');
            
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getPartenaireLatestVersion(Request $request){
        try{ 
            $data = [
                'id' => Uuid::uuid4()->toString(),
                'version' => '1.1.0',
                'link' => null,
                'news' => []
            ];
            return sendResponse($data, 'Version.');

        } catch (BadResponseException $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 403);
        }
    }

    public function getFees(Request $request){
        try {
            // revoir comment envoyer les frais des operations clients slmnt.
            
            $fees = Frai::where('deleted',0)->where('retrait',0)->get();
            return sendResponse($fees, 'fees.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getServices(Request $request){
        try {            
            $modules = Service::where('deleted',0)->where('type','partenaire')->get();
            return sendResponse($modules, 'Modules.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function configPin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_partner_id' => 'required|string',
                'pin' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }

            $user = UserPartenaire::where('id',$request->user_partner_id)->first();

            if(!$user){
                return sendError('L\'utilisateur n\'existe pas', [], 401);
            }
            

            $user->pin = $request->pin;
            $user->save();
            $user->makeHidden(['password','pin']);
            return sendResponse($user, 'PIN configurer avec succes');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getDashboardPartenaire(Request $request){
        try {            
            $partenaire = Partenaire::where('id',$request->id)->first();
            $distribution = $partenaire->accountDistribution;
            $commission = $partenaire->accountCommission;   
            
            $data['distribution'] = $distribution;
            $data['commission'] = $commission;
            return sendResponse($data, 'Dashboard.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getComptePartenaireInfo(Request $request){
        try {            
            $user = UserPartenaire::where('id',$request->id)->where('deleted',0)->first();
            $resultat = [];
            if($user){
                if($user->status == 0){
                    return sendError('Ce compte est désactivé.', [], 500);
                }
                
                $transactions = Depot::with('partenaire')->with('userClient')
                ->select('id', 'libelle', DB::raw("created_at as dateOperation"), 'partenaire_id','user_client_id', DB::raw("'depot' as typeOperation"), DB::raw("'depot' as sens"), 'montant', 'frais')
                ->where('status', 'completed')->where('deleted', 0)

                ->union(Retrait::with('partenaire')->with('userClient')
                ->select('id', 'libelle', DB::raw("created_at as dateOperation"), 'partenaire_id','user_client_id', DB::raw("'retrait' as typeOperation"), DB::raw("'retrait' as sens"), 'montant', 'frais')
                ->where('status', 'completed')->where('deleted', 0))

                ->orderBy('created_at','desc')->get();

                $resultat['utilisateur'] = $user;
                $resultat['transactions'] = $transactions;
                return sendResponse($resultat, 'Informations récupérées avec succes.');
            }else{
                return sendError('Cet utilisateur n\'exite pas dans la base', [], 500);
            }
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getPartnerAllTransactions(Request $request){
        try {
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            
            $partenaire = $userPartenaire->partenaire;
            
            if(!$request->type){           
                $transactions = Depot::with('partenaire')->with('userClient')->with('userCard')
                ->select('id', 'libelle', 'montant', 'frais', 'created_at', 'partenaire_id', 'user_partenaire_id','user_client_id','user_card_id', DB::raw("'depot' as type"), DB::raw("'client' as transaction_type"))
                ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id)
    
                ->union(Retrait::with('partenaire')->with('userClient')->with('userCard')
                ->select('id', 'libelle', 'montant', 'frais', 'created_at', 'partenaire_id', 'user_partenaire_id','user_client_id','user_card_id', DB::raw("'retrait' as type"), DB::raw("'client' as transaction_type"))
                ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id))
    
                ->union(PartnerWalletWithdraw::with('partenaire')
                ->select('id', 'libelle', 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Withdrawl' as type"), DB::raw("'partner' as transaction_type"))
                ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id))
    
                ->union(PartnerWalletDeposit::with('partenaire')
                ->select('id', 'libelle', 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Approvisionnement' as type"), DB::raw("'partner' as transaction_type"))
                ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id))
    
                ->union(PartnerCession::with('partenaire')
                ->select('id', DB::raw("'Cession de monnaie' as libelle"), 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Cession' as type"), DB::raw("'partner' as transaction_type"))
                ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id))
    
                ->orderBy('created_at','desc')->get();
                return sendResponse($transactions, 'transactions.');
            }else{
                if($request->type == 'client'){     
                    $transactions = Depot::with('partenaire')->with('userClient')->with('userCard')
                    ->select('id', 'libelle', 'montant', 'frais', 'created_at', 'partenaire_id', 'user_partenaire_id','user_client_id','user_card_id', DB::raw("'depot' as type"), DB::raw("'client' as transaction_type"))
                    ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id)
        
                    ->union(Retrait::with('partenaire')->with('userClient')->with('userCard')
                    ->select('id', 'libelle', 'montant', 'frais', 'created_at', 'partenaire_id', 'user_partenaire_id','user_client_id','user_card_id', DB::raw("'retrait' as type"), DB::raw("'client' as transaction_type"))
                    ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id))         
    
                    ->orderBy('created_at','desc')->get();
                    
                    return sendResponse($transactions, 'transactions.');
                }else{        
                    $transactions = PartnerWalletWithdraw::with('partenaire')
                    ->select('id', 'libelle', 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Withdrawl' as type"), DB::raw("'partner' as transaction_type"))
                    ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id)
        
                    ->union(PartnerWalletDeposit::with('partenaire')
                    ->select('id', 'libelle', 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Approvisionnement' as type"), DB::raw("'partner' as transaction_type"))
                    ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id))
        
                    ->union(PartnerCession::with('partenaire')
                    ->select('id', DB::raw("'Cession de monnaie' as libelle"), 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Cession' as type"), DB::raw("'partner' as transaction_type"))
                    ->where('status', 'completed')->where('deleted', 0)->where('partenaire_id',$partenaire->id))
        
                    ->orderBy('created_at','desc')->get();

                    return sendResponse($transactions, 'transactions.');                    
                }
            }
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getPartnerPendingCustomersTransactions(Request $request){
        try {          
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();

            $partenaire = $userPartenaire->partenaire;
            
            $transactions = Depot::with('partenaire')->with('userClient')->with('userCard')
            ->select('id', 'libelle', 'montant', 'frais', 'created_at', 'partenaire_id', 'user_partenaire_id','user_client_id','user_card_id', DB::raw("'depot' as type"), DB::raw("'client' as transaction_type"))
            ->where('status', 'pending')->where('is_debited', 1)->where('deleted', 0)->where('partenaire_id',$partenaire->id)

            ->union(Retrait::with('partenaire')->with('userClient')->with('userCard')
            ->select('id', 'libelle', 'montant', 'frais', 'created_at', 'partenaire_id', 'user_partenaire_id','user_client_id','user_card_id', DB::raw("'retrait' as type"), DB::raw("'client' as transaction_type"))
            ->where('status', 'pending')->where('is_debited', 1)->where('deleted', 0)->where('partenaire_id',$partenaire->id))     

            ->orderBy('created_at','desc')->get();

            return sendResponse($transactions, 'transactions.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getClient(Request $request){
        try {          
            $userClient = UserClient::where('username',$request->username)->first();

            if(!$userClient){
                return sendError('Compte client introuvable', [], 404);
            }

            $userClient->kycClient;
            $userClient->makeHidden(['password','code_otp']);
            return sendResponse($userClient, 'kyc.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getPartnerPendingAdminsTransactions(Request $request){
        try {            
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            $partenaire = $userPartenaire->partenaire;

            
    
            $transactions = PartnerWalletWithdraw::with('partenaire')
            ->select('id', 'libelle', 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Withdrawl' as type"), DB::raw("'partner' as transaction_type"))
            ->where('status', 'pending')->where('is_debited', 1)->where('deleted', 0)->where('partenaire_id',$partenaire->id)

            ->union(PartnerWalletDeposit::with('partenaire')
            ->select('id', 'libelle', 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Approvisionnement' as type"), DB::raw("'partner' as transaction_type"))
            ->where('status', 'pending')->where('is_debited', 1)->where('deleted', 0)->where('partenaire_id',$partenaire->id))

            ->union(PartnerCession::with('partenaire')
            ->select('id', DB::raw("'Cession de monnaie' as libelle"), 'montant', DB::raw('0 as frais'), 'created_at', 'partenaire_id', 'user_partenaire_id', DB::raw('null as user_client_id'), DB::raw('null as user_card_id'), DB::raw("'Cession' as type"), DB::raw("'partner' as transaction_type"))
            ->where('status', 'pending')->where('deleted', 0)->where('partenaire_id',$partenaire->id))

            ->orderBy('created_at','desc')->get();

            return sendResponse($transactions, 'transactions.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }  

    public function compteCommissionSolde(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->first();
            $compte = $partenaire->accountCommission;
            $data['compte'] = $compte;
            return sendResponse($data, 'Liste chargée avec succes.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function compteDistributionSolde(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->first();
            $compte = $partenaire->accountDistribution;  
            
            $data['compte'] = $compte;
            return sendResponse($data, 'Liste chargée avec succes.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    // A ne pas toucher. Ceci concerne les Api de vente a travers nos Api pour le partenaire

    public function getUserPartenaireInfo(Request $request){
        try{
            $user = UserPartenaire::where('id',$request->id)->first();
            $user->partenaire;
            $user->role->rolePermissions;
            $user->makeHidden(['password']);
        
            return sendResponse($user, 'Liste chargée avec succes.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function customerCredit(Request $request){
        try {
            // Validation du body de la requete
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'last_digits' => 'required',
                'amount' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()[0], [],422);
            }

            // Check du partenaire et des clés
            $partenaire = ApiPartenaireAccount::where('id',$request->program_id)->where('deleted',0)->first();

            if(!$partenaire){
                return sendError('Aucun partenaire n\'est lié a ce programme', [], 404);
            }

            if($request->header('API-KEY') == null || $request->header('API-KEY') != $partenaire->api_key ||
                $request->header('PUBLIC-API-KEY') == null || $request->header('PUBLIC-API-KEY') != $partenaire->public_api_key ||
                $request->header('SECRET-API-KEY') == null || $request->header('SECRET-API-KEY') != $partenaire->secret_api_key){
                return sendError('Verifier vos clés API', [], 401);
            }
            
            // Calcul des frais
            $frais = 0;
            $fee = ApiPartenaireFee::where('beguin','<=',$request->amount)->where('end','>=',$request->amount)->where('api_partenaire_account_id',$partenaire->id)->orderBy('id','DESC')->first();
            if(!$fee){
                $fee = ApiPartenaireFee::where('beguin','<=',$request->amount)->where('end','>=',$request->amount)->where('api_partenaire_account_id',0)->orderBy('id','DESC')->first();
                if($fee){
                    if($fee->type_fee == 'pourcentage'){
                        $frais = $request->amount * $fee->value / 100;
                    }else{
                        $frais = $fee->value;
                    }
                }
            }else{
                if($fee->type_fee == 'pourcentage'){
                    $frais = $request->amount * $fee->value / 100;
                }else{
                    $frais = $fee->value;
                }
            }

            // Check de la faisabilité
            if($partenaire->balance < ($request->amount + $frais)){
                return sendError('BALANCE INSUFFISANT', [], 401);
            }
    
            $soldeAvant = $partenaire->balance;

            // Préparation de la transaction
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');

            
            $clientHttp = new Client();
            $url =  $base_url."accounts/".$request->customer_id."/transactions";
            
            $body = [
                "transferType" => "WalletToCard",
                "transferAmount" => round($request->amount,2),
                "currencyCode" => "XOF",
                "referenceMemo" => "Depot de ".$request->amount." XOF sur votre carte ",
                "last4Digits" => $request->last_digits
            ];

            $body = json_encode($body);
            
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
                'accountId' => $accountId,
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];
        
            $auth = [
                $authLogin,
                $authPass
            ];
            
            $transaction = ApiPartenaireTransaction::create([
                'id' => Uuid::uuid4()->toString(),
                'api_partenaire_account_id' => $partenaire->id,
                'type' => 'Debit',
                'montant' => $request->amount,
                'frais' => $frais,
                'commission' => 0,
                'solde_avant' => $soldeAvant,
                'libelle' => 'Rechargement de la carte '.$request->cutomer_id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            try {
                $response = $clientHttp->request('POST', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                    'body' => $body,
                    'verify'  => false,
                ]);            
                $responseBody = json_decode($response->getBody());

                $referenceGtp = $responseBody->transactionId;
    
                $partenaire->balance = $soldeAvant - $request->amount - $frais;
                $partenaire->save();
                
                $transaction->reference = $referenceGtp;
                $transaction->solde_apres = $partenaire->balance;
                $transaction->status = 1;
                $transaction->save();
            } catch (BadResponseException $e) {
                $transaction->status = 0;
                $transaction->save();
                return sendError('Erreur lors de la transaction', [], 500);
            }
            return sendResponse($transaction, 'Client crédité avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function accountBalance(Request $request){
        try {        
            $partenaire = ApiPartenaireAccount::where('id',$request->program_id)->where('deleted',0)->first();

            if(!$partenaire){
                return sendError('Aucun partenaire n\'est lié a ce programme', [], 404);
            }

            if($request->header('API-KEY') == null || $request->header('API-KEY') != $partenaire->api_key ||
                $request->header('PUBLIC-API-KEY') == null || $request->header('PUBLIC-API-KEY') != $partenaire->public_api_key ||
                $request->header('SECRET-API-KEY') == null || $request->header('SECRET-API-KEY') != $partenaire->secret_api_key){
                return sendError('Verifier vos clés API', [], 401);
            }
            
            $data = [];
            $data['balance'] = $partenaire->balance;
            $data['currency'] = 'XOF';

            return sendResponse($data, 'Solde');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function accountTransactions(Request $request){
        try { 
            $partenaire = ApiPartenaireAccount::where('id',$request->program_id)->where('deleted',0)->first();

            if(!$partenaire){
                return sendError('Aucun partenaire n\'est lié a ce programme', [], 404);
            }

            if($request->header('API-KEY') == null || $request->header('API-KEY') != $partenaire->api_key ||
                $request->header('PUBLIC-API-KEY') == null || $request->header('PUBLIC-API-KEY') != $partenaire->public_api_key ||
                $request->header('SECRET-API-KEY') == null || $request->header('SECRET-API-KEY') != $partenaire->secret_api_key){
                return sendError('Verifier vos clés API', [], 401);
            }
            
            $transactions = ApiPartenaireTransaction::where('api_partenaire_account_id',$request->program_id)->get();

            return sendResponse($transactions, ' effectuée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
    //
    

    public function generateCodePromo(Request $request){
        try { 
            $promocode = generateRandomCode();
            $exist_code_promo = UserPartenaire::where('promo_code',$promocode)->where('deleted',0)->first();
            
            while ($exist_code_promo != null) {
                $promocode = generateRandomCode();
                $exist_code_promo = UserPartenaire::where('promo_code',$promocode)->where('deleted',0)->first();
            }

            $user = UserPartenaire::where('id',$request->user_partenaire_id)->where('deleted',0)->first();
            $user->promo_code = $promocode;
            $user->save();
            $user->partenaire;
            $user->role;

            return sendResponse($user, 'Code généré avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function checkPartnerDevice(Request $request){
        try {
            $tpe = Tpe::where('code',$request->serial)->where('deleted',0)->first();
            
            if ($tpe == null) {
                return sendError('Ce TPE n\'existe pas dans la base',[], 404);
            }else if ($tpe->status == 'off'){
                return sendError('TPE désactivé',[], 401);
            }



            return sendResponse([], 'TPE verifié avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function setPartnerLocation(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'country' => 'required',
                'department' => 'required',
                'city' => 'required',
                'lng' => 'required',
                'lat' => 'required',
                'description' => 'required',
                'partenaire_id' => 'required',
                'user_partenaire_id' => 'required',
                'pos_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            TpeLocation::create([
                'id' => Uuid::uuid4()->toString(),
                'country' => $request->country,
                'department' => $request->department,
                'city' => $request->city,
                'lng' => $request->lng,
                'lat' => $request->lat,
                'description' => $request->description,
                'partenaire_id' => $request->partenaire_id,
                'user_partenaire_id' => $request->user_partenaire_id,
                'tpe_id' => $request->pos_id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return sendResponse([], 'TPE localisé avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getPartnersLocation(Request $request){
        try {
            $tpes = TpeLocation::where('city','LIKE',"%{$request->city}%")->get();

            return sendResponse($tpes, 'Liste TPE');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getPartnerLocation(Request $request){
        try {
            $tpeLocation = TpeLocation::where('tpe_id',$request->tpe)->orderBy('created_at','desc')->first();
            return sendResponse($tpeLocation, 'AIzaSyCEls61gIK1OZjK_wJa_ypasbhNcwqkJp0');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
    
    protected function createNewToken($token){
        return $token;
    }



    public function initPaiement(Request $request){
        try {            
            $validator = Validator::make($request->all(), [
                'montant' => 'required',
                'wallet_id' => 'required',
                'user_partenaire_id' => 'required',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }            

            $wallet = PartnerWallet::where('id',$request->wallet_id)->first();
            $partner = $wallet->partenaire;
            $montant = $request->montant;
            
            $transfert = PartnerWalletDeposit::create([
                'id' => Uuid::uuid4()->toString(),
                'montant' => $montant,
                'partenaire_id'=> $partner->id,
                'user_partenaire_id'=> $request->user_partenaire_id,
                'libelle' => 'Depot sur le compte partenaire',
                'wallet_id' => $wallet->id,
                'status' => 'pending',
                'is_debited' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),    
            ]);
            return sendResponse($transfert, 'Check paiement');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function checkPaiement(Request $request){
        try {
            $id = $request->payment_id;
            $payment = PartnerWalletDeposit::where('id',$id)->first();

            if(!$payment){
                return sendError('Cette transaction n\'existe pas', [], 404);
            }

            return sendResponse($payment, 'Check paiement');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
}

