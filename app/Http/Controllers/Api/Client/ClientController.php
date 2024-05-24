<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserClient;
use App\Models\Info;
use App\Models\KycClient;
use App\Models\UserCardBuy;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailAlerteVerification;
use App\Models\BccPayment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Frai;
use App\Models\UserCard;
use App\Models\Service;
use App\Models\Beneficiaire;
use App\Models\Depot;
use App\Models\PasswordResetQuestion;
use App\Models\PasswordResetQuestionClient;
use App\Models\Recharge;
use App\Models\Retrait;
use App\Models\FrontPayment;
use App\Models\TransfertOut;
use App\Services\PaiementService;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    
    public function __construct() {
        $this->middleware('is-auth', ['except' => ['initiationBmo','confirmationBmo','resetPasswordWithQuestions','getQuestionsAll','getQuestionsByPhone','addContact','getLatestVersion','checkCodeOtp','createCompteClient', 'loginCompteClient', 'sendCode', 'checkCodeOtp', 'resetPassword','saveSignature','verificationPhone', 'verificationInfoPerso','verificationInfoPiece','saveFile','sendCodeTelephoneRegistration','getServices','sendCodeTelephone','callBackKkiapay']]);
    }

    public function tokenValide(Request $request){
        try {            
            return true;
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getLatestVersion(){
        try{ 
            $data = [
                'id' => Uuid::uuid4()->toString(),
                'version' => '7.0',
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

    //payment component
    public function initiationBmo(Request $request){
        try{ 
            $amount = (int)$request->amount;
            $telephone = $request->telephone; //"22962617848";

            $partner_reference = substr($telephone, -4).time();
            
            $base_url_bmo = env('BASE_BMO');

            // Realisation de la transaction

            $client = new Client();
            $url = $base_url_bmo."/operations/transfert-collect";
            
            $body = [
                "amount" => $amount,
                "customer" => ["phone" => "+".$telephone],
                "receiver" => ["phone" => env('COMPTE_DEBPOT_BMO')],
                "partnerReference" => $partner_reference,
                "reason" => "",
                "idType" => "",
                "idNumber" => "",
                "cardExpiration" => "" 
            ];

            $body = json_encode($body);
    
            $headers = [
                'X-Auth-ApiKey' => env('APIKEY_BMO'),
                'X-Auth-ApiSecret' => env('APISECRET_BMO'),
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];
    
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);

            $resultat_debit_bmo = json_decode($response->getBody());

            $token = JWTAuth::getToken();
            $userId = JWTAuth::getPayload($token)->toArray()['sub'];
            $message = ['success' => true, 'status' => 200,'message' => 'Initiation  depot bmo','timestamp' => Carbon::now(),'user' => $userId]; 
            writeLog($message);
            return sendResponse($resultat_debit_bmo, 'Operation initié avec succes.');

        } catch (BadResponseException $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 403);
        }
    }

    public function confirmationBmo(Request $request){
        try{             
            $base_url_bmo = env('BASE_BMO');

            $client = new Client();
            $url = $base_url_bmo."/operations-collect/confirm";
            
            $body = [
                "operation" => $request->operation,
                "confirmationCode" => $request->code,
            ];

            $body = json_encode($body);
    
            $headers = [
                'X-Auth-ApiKey' => env('APIKEY_BMO'),
                'X-Auth-ApiSecret' => env('APISECRET_BMO'),
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];
    
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);

            $resultat_debit_bmo = json_decode($response->getBody());
            
            $token = JWTAuth::getToken();
            $userId = JWTAuth::getPayload($token)->toArray()['sub'];
            $message = ['success' => true, 'status' => 200,'message' => 'Confirmation de paiement BMO','timestamp' => Carbon::now(),'user' => $userId]; 
            writeLog($message);
            return sendResponse($resultat_debit_bmo, 'Operation confirmée avec succes.');

        } catch (BadResponseException $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 401);
        }
    }

    public function paymentBcc(Request $request, PaiementService $paiementService){
        try{
            $validator = Validator::make($request->all(), [
                'user_client_id' => ["required" , "string"],
                'montant' => ["required" , "integer"]
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $userClient = UserClient::where('id',$request->user_client_id)->first();

            if(!$userClient){
                return sendError('Ce compte BCC n\'existe pas', [], 404);
            }

            $card = $userClient->userCard;

            $cardDebited = $paiementService->cardDebited($card->customer_id, $card->last_digits, $request->montant, 0, $userClient);

            if($cardDebited == false){
                return sendError('Erreur lors du debit de la carte principale', [], 401);
            }else{
                $paymentBcc = BccPayment::create([
                    'id' => Uuid::uuid4()->toString(),
                    "reference" => $cardDebited->transaction_id,
                    "status" => 1,
                    "montant" => $request->montant,
                    "deleted" => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }

            return sendResponse($paymentBcc, 'Paiement effectué avec succes. Consulter votre solde');

        } catch (BadResponseException $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 401);
        }
    }

    public function getKkpInfos(){
        try {            
            $encrypt_Key = env('ENCRYPT_KEY');
            $key = encryptData((string)env('API_KEY_KKIAPAY'),$encrypt_Key);

            $data['kkiapayApiKey'] = $key;
            $data['kkiapaySandBox'] = env('KKIAPAY_SANDBOX');
            $data['kkiapayTheme'] = '#000000';
            return sendResponse($data, 'Api Kkiapay.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getCompteClientInfo(Request $request){
        try {            
            $encrypt_Key = env('ENCRYPT_KEY');
            $client = UserClient::where('deleted',0)->where('username',$request->username)->first();

            if(!$client){
                return sendError('Ce compte client n\'exite pas. Verifiez le numero de telephone et recommencez');
            }else{
                if($client->status == 0){
                    return sendError('Ce compte client est inactif');
                }
                if($client->verification == 0){
                    return sendError('Ce compte client n\'est pas encore verifié');
                }
            }

            $data = [
                'name' => $client->name,
                'lastname' => $client->lastname,
                'main_card_balance' => encryptData((string)getCarteInformation((string)$client->userCard->first()->customer_id, 'balance'),$encrypt_Key),
                'main_card_last_digits' => $client->userCard->first()->last_digits,
                'is_active' => $client->status,
                'is_valid' => $client->verification,
            ];

            return sendResponse($data, 'Info.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getServices(){
        try {            
            $services = Service::where('deleted',0)->where('type','client')->get();
            return sendResponse($services, 'Modules.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getFees(){
        try {
            $frais = Frai::where('deleted',0)->orderBy('created_at','DESC')->get();            
            return sendResponse($frais, 'Frais.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }
    
    // A coder de façon dynamique
    public function getMobileWallet(){
        try {            
            $mobileWallets = [
                [
                    'libelle' => 'MTN MOBILE MONEY',
                    'tag' => 'momo',
                    'logo' => '/storage/logowallet/momo.svg',
                    'countries' => 'bj,ci',
                    'is_available' => 1,
                ],
                [
                    'libelle' => 'MOOV MONEY',
                    'tag' => 'flooz',
                    'logo' => '/storage/logowallet/flooz.svg',
                    'countries' => 'bj,tg,ci',
                    'is_available' => 1,
                ],
                [
                    'libelle' => 'ORANGE MONEY',
                    'tag' => 'orange',
                    'logo' => '/storage/logowallet/orange.svg',
                    'countries' => 'sn,ci',
                    'is_available' => 1,
                ],
                [
                    'libelle' => 'TMONEY',
                    'tag' => 'tmoney',
                    'logo' => '/storage/logowallet/tmoney.svg',
                    'countries' => 'tg',
                    'is_available' => 1,
                ],
                [
                    'libelle' => 'CELTIIS CASH',
                    'tag' => 'celtiis',
                    'logo' => '/storage/logowallet/celtiis-cash.svg',
                    'countries' => 'bj',
                    'is_available' => 1,
                ],
                [
                    'libelle' => 'FREE MONEY',
                    'tag' => 'free',
                    'logo' => '/storage/logowallet/free.svg',
                    'countries' => 'sn',
                    'is_available' => 1,
                ],
            ];
            
            return sendResponse($mobileWallets, 'Frais.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    // fin payment component


    public function createCompteClient(Request $request){
        try {            
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:user_clients',
                'phone_code' => 'required',
                'phone' => 'required',
                'password' => 'required|min:8'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $req = $request->all();
            $user = UserClient::create([
                'id' => Uuid::uuid4()->toString(),
                'username' => $req['username'],
                'password' => Hash::make($req['password']),
                'phone_code' => $req['phone_code'],
                'phone' => $req['phone'],
                'status' => 1,
                'double_authentification' => 0,
                'sms' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $user->makeHidden(['password','code_otp']);

            $message = ['success' => true, 'status' => 200,'message' => 'Compte crée avec succès.','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message);

            return sendResponse($user, 'Compte crée avec succès.');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function loginCompteClient(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|int',
                'password' => 'required|string|min:8',
            ]);
    
            if ($validator->fails())
            {
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }

            
            if($request->app_id != '9EEq5Ucm5VsX0nz53jRfD47p6xdK0zxRXOJsRcivzluaw9PnU9'){
                $message = ['success' => false, 'status' => 401, 'message' => 'App Id incorrecte', 'timestamp' => Carbon::now(), 'user' => null]; 
                writeLog($message);
                return response()->json([
                    'message' => 'Mauvais app id',
                ], 401);
            }
    
            $user = UserClient::where('username', $request->username)->first();
    
            if ($user) {
                if($user->status == 0){
                    $message = ['success' => false, 'status' => 401, 'message' => 'Echec de connexion : Compte inactif', 'timestamp' => Carbon::now(), 'user' => $user->id]; 
                    writeLog($message);
                    return response()->json([
                        'message' => 'Ce compte est désactivé',
                    ], 401);
                }
    
                $credentials = $request->only('username', 'password');
                $token = Auth::guard('apiUser')->attempt($credentials);
        
                if (!$token) {
                    $message = ['success' => false, 'status' => 401,'message' => 'Echec de connexion : Informations de connexion incorrectes','timestamp' => Carbon::now()];  
                    writeLog($message);
                    return response()->json([
                        'message' => 'Informations de connexion incorrectes',
                    ], 401);
                }
    
                $user->last_connexion = Carbon::now();
                $user->save();

                $user->kycClient;
                $user->makeHidden(['password','code_otp']);
                $modules = Service::where('deleted',0)->where('type','client')->get();
                $frais = Frai::where('deleted',0)->orderBy('created_at','DESC')->get();

                $message = ['success' => true, 'status' => 200, 'message' => 'Connexion réussie avec succès.', 'timestamp' => Carbon::now(), 'user' => $user->id];  
                writeLog($message);

                $questions = PasswordResetQuestionClient::where('user_client_id',$user->id)->where('deleted',0)->pluck('password_reset_question');

                return response()->json([
                    'user' => $user,
                    'services' => $modules,
                    'fees' => $frais,
                    'questions' => $questions,
                    'authorization' => [
                        'token' => $token,
                        'type' => 'bearer',
                    ]
                ],200);
            } else {
                $message = ['success' => false, 'status' => 422,'message' => 'Echec de connexion : l\'utilisateur n\'exite pas','timestamp' => Carbon::now()];  
                writeLog($message);
                return response()->json([
                    "message" =>'L\'utilisateur n\'existe pas'
                ], 422);
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }
    
    public function sendCode(Request $request){
        try {
            $user = UserClient::where('id',$request->id)->first();         

            if(!$user){
                return sendError('L\'utilisateur n\'existe pas', [], 404);
            }
            
            if($request->type == 'whatsapp'){
                $whatsapp = true;
            }else{
                $whatsapp = false;
            }

            $code = rand(1000,9999);

            $user->code_otp = $code;
            $user->save();

            $message = getSms('otp_verification', $code, null, null, null, null, null);
            sendSms($user->username,$message,$whatsapp);
            return sendResponse([], 'Code envoyé avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }
    
    public function sendCodeTelephone(Request $request){
        try {
            $user = UserClient::where('username',$request->telephone)->first();

            if(!$user){
                return sendError('L\'utilisateur n\'existe pas', [], 404);
            }

            $code = rand(1000,9999);
            
            if($request->type == 'whatsapp'){
                $whatsapp = true;
            }else{
                $whatsapp = false;
            }

            $user->code_otp = $code;
            $user->save();

            $message = getSms('otp_verification', $code, null, null, null, null, null);
            sendSms($user->username,$message,$whatsapp);

            $message = ['success' => true, 'status' => 200,'message' => 'Envoie de code OTP','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message);
            return sendResponse([], 'Code envoyé avec succès');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }
    
    public function sendCodeTelephoneRegistration(Request $request){
        try { 

            $code = rand(1000,9999);
            
            if($request->type == 'whatsapp'){
                $whatsapp = true;
            }else{
                $whatsapp = false;
            }

            $message = getSms('otp_verification', $code, null, null, null, null, null);
            sendSms($request->telephone,$message,$whatsapp);

            $message = ['success' => true, 'status' => 200,'message' => 'Envoie de code OTP a l\'inscription','timestamp' => Carbon::now(),'user' => $request->telephone];
            writeLog($message);

            $encrypt_Key = env('ENCRYPT_KEY');
            $code = encryptData((string)$code,$encrypt_Key);
            
            return sendResponse($code, 'Code envoyé avec succès');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function checkCodeOtp(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'code' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }

            $user = UserClient::where('id',$request->user_id)->first();
            
            if($user->code_otp != $request->code){
                return sendError('Code OTP incorrect', [], 401);
            }

            return sendResponse([], 'Vérification effectuée avec succès');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }
    
    public function resetPassword(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|int',
                'phone' => 'required|int',
                'phone_code' => 'required|int',
                'password' => 'required|min:8'
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }

            $user = UserClient::where('username',$request->phone_code.$request->phone)->first();

            if(!$user){
                return sendError('L\'utilisateur n\'existe pas', [], 401);
            }

            if($user->code_otp != $request->code){
                return sendError('Code OTP incorrect', [], 401);
            }
            $user->password = Hash::make($request->password);
            $user->save();
            
            $message = ['success' => true, 'status' => 200,'message' => 'Changement de mot de passe','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message);
            return sendResponse([], 'Mot de passe modifé avec succès');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }
    
    public function configPin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|string',
                'pin' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }

            $user = UserClient::where('id',$request->user_id)->first();

            if(!$user){
                return sendError('L\'utilisateur n\'existe pas', [], 401);
            }
            

            $user->pin = $request->pin;
            $user->save();
            $user->kycClient;
            $user->makeHidden(['password','code_otp']);
            
            $message = ['success' => true, 'status' => 200,'message' => 'Configuration de code PIN','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message);
            return sendResponse($user, 'PIN configurer avec succes');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function changePin(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|string',
                'password' => 'required|string',
                'pin' => 'required|string',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }
            
            $user = UserClient::where('id',$request->user_id)->first();
            if(!$user){
                return sendError('L\'utilisateur n\'existe pas', [], 401);
            }

            if (Hash::check($request->password, $user->password)) {
                $user->pin = $request->pin;
                $user->save();
                $user->kycClient;
                $user->makeHidden(['password','code_otp']);
                $message = ['success' => true, 'status' => 200,'message' => 'Changement de code PIN','timestamp' => Carbon::now(),'user' => $user->id]; 
                writeLog($message);
                return sendResponse($user, 'Code PIN changé avec succes');

            }else{
                $message = ['success' => false, 'status' => 500, 'message' => 'Mot de passe incorrecte lors du changement de code PIN','timestamp' => Carbon::now(),'user' => $user->id]; 
                writeLog($message);
                return sendError('Mot de passe incorrecte', [], 500);
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function verificationPhone(Request $request){
        try {
            $data = [];

            $user = UserClient::where('id',$request->user_id)->where('deleted',0)->first();

            if(!$user){
                return sendError('L\'utilisateur n\'existe pas', [], 404);
            }

            $user->verification_step_one = 1;
            $user->updated_at = carbon::now();
            
            $kyc = KycClient::create([
                'id' => Uuid::uuid4()->toString(),
                'telephone' => $user->phone_code.' '.$user->phone,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $user->kyc_client_id = $kyc->id;
            $user->save();

            $data['user'] = $user;
            $data['kyc'] = $kyc;
            $message = ['success' => true, 'status' => 200,'message' => 'Verification du numero de telephone effectué','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message);
            
            return sendResponse($data, 'Numero de telephone vérifié avec succes.');            
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function verificationInfoPerso(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user' => 'required',
                'email' => 'required',
                'name' => 'required',
                'lastname' => 'required',
                'birthday' => 'required',
                'country' => 'required',
                'departement' => 'required',
                'city' => 'required',
                'address' => 'required',
                'job' => 'required',
                'salary' => 'required',
                'agreement' => 'required',
                'user_partenaire_id' => 'nullable',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            $req = $request->all();
            $user = UserClient::where('id',$req['user'])->where('deleted',0)->first();

            if($user){
                if($user->kycClient == null){
                    $kyc = KycClient::create([
                        'id' => Uuid::uuid4()->toString(),
                        'name' => $req['name'],
                        'lastname' => $req['lastname'],
                        'email' => $req['email'],
                        'birthday' => $req['birthday'],
                        'country' => $req['country'],
                        'departement' => $req['departement'],
                        'job' => $req['job'],
                        'salary' => $req['salary'],
                        'agreement' => $req['agreement'],
                        'city' => $req['city'],
                        'address' => $req['address'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    $user->kyc_client_id = $kyc->id;
                }else{
                    $user->kycClient->name = $request->name;
                    $user->kycClient->lastname = $request->lastname;
                    $user->kycClient->email = $req['email'];
                    $user->kycClient->birthday = $req['birthday'];
                    $user->kycClient->country = $req['country'];
                    $user->kycClient->departement = $req['departement'];
                    $user->kycClient->city = $req['city'];
                    $user->kycClient->address = $req['address'];
                    $user->kycClient->job = $req['job'];
                    $user->kycClient->salary = $req['salary'];
                    $user->kycClient->agreement = $req['agreement'];
                    $user->kycClient->save();
                }
    
                $user->name = $req['name'];
                $user->lastname = $req['lastname'];
                $user->verification_step_two = 1;
                $user->is_rejected = 0;
                $user->updated_at = carbon::now();
                $user->save();

                if($request->user_partenaire_id == null){
                    foreach($request->answers as $item){
                        PasswordResetQuestionClient::create([
                            'id' => Uuid::uuid4()->toString(),
                            'user_client_id' => $user->id,
                            'password_reset_question' => $item['question'],
                            'answer' => $item['answer'],
                            'deleted' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                }else{
                    $user->kycClient->user_partenaire_id = $request->user_partenaire_id;
                    $user->kycClient->save();
                }
    
                $message = ['success' => true, 'status' => 200,'message' => 'Verification des informations personelles','timestamp' => Carbon::now(),'user' => $user->id]; 
                writeLog($message); 
                return sendResponse($user, 'Informations personnelles enregistrées avec succes.');
            }else{
                return sendError('L\'utilisateur n\'existe pas', [], 404);
            }
            
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function verificationInfoPiece(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user' => 'required',
                'piece_type' => 'required',
                'piece_id' => 'required',
                'piece_file' => 'required',
                'user_with_piece' => 'required',
                'user_partenaire_id' => 'nullable',
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            $req = $request->all();
            $user = UserClient::where('id',$req['user'])->where('deleted',0)->first();
            
            if($user->kycClient == null){
                $kyc = KycClient::create([
                    'id' => Uuid::uuid4()->toString(),
                    'piece_type' => $req['piece_type'],
                    'piece_id' => $req['piece_id'],
                    'piece_file' => $req['piece_file'],
                    'user_with_piece' => $req['user_with_piece'],
                    'signature' => $req['signature'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                $user->kyc_client_id = $kyc->id;
            }else{

                $user->kycClient->piece_type = $req['piece_type'];
                $user->kycClient->piece_id = $req['piece_id'];
                $user->kycClient->piece_file = $req['piece_file'];
                $user->kycClient->user_with_piece = $req['user_with_piece'];
                $user->kycClient->signature = $req['signature'];
                $user->kycClient->save();
            }
            if($request->user_partenaire_id != null){
                $user->kycClient->user_partenaire_id =  $request->user_partenaire_id;
                $user->kycClient->save();
            }

            $user->verification_step_three = 1;
            $user->is_rejected = 0;
            $user->updated_at = carbon::now();
            $user->save();

            $name = $user->name.' '.$user->lastname;
            try{
                Mail::to(['bmo-uba-noreply@bestcash.me',])->send(new MailAlerteVerification($name));
            } catch (\Exception $e) {
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $user->id];  
                writeLog($message);
            }
            
            $message = ['success' => true, 'status' => 200,'message' => 'Verification des informations de la piece d\'identité','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message); 
            
            return sendResponse($user, 'Enregistrement des pieces effectué avec succes. Patientez maintenant le temps du traitement de votre requete de verification.');
            
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function saveFile(Request $request){
        try{
            $path = '';
            if ($request->hasFile('piece')) {
                $path = $request->file('piece')->store('pieces','pieces');
                return sendResponse('/storage/pieces/'.$path, 'success');
            }
            if ($request->hasFile('user_with_piece')) {
                $path = $request->file('user_with_piece')->store('user_with_pieces','pieces');
                return sendResponse('/storage/pieces/'.$path, 'success');
            }
            if ($request->hasFile('signature')) {
                $path = $request->file('signature')->store('signatures','pieces');
                return sendResponse('/storage/pieces/'.$path, 'success');
            }

        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getCompteClient(Request $request){
        try {            
            $token = JWTAuth::getToken();
            $userId = JWTAuth::getPayload($token)->toArray()['sub'];

            $user = UserClient::where('id',$userId)->first();
            $user->kycClient;
            $user->makeHidden(['password','code_otp']);

            return response()->json(compact('user'));
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }


    
    
    public function checkClient(Request $request){
        try {
            $client = UserClient::where('id',$request->id)->first();
            if(!$client){
                return sendError('Ce client n\'existe pas', [], 404);
            }
            $client->makeHidden(['password','code_otp']);

            return sendResponse($client, 'Success');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function checkClientUsername(Request $request){
        try {
            $client = UserClient::where('username',$request->username)->first();
            if(!$client){
                return sendError('Ce client n\'existe pas', [], 404);
            }
            $cards = UserCard::where('user_client_id',$client->id)->get();
            return sendResponse($cards, 'Success');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    
    

    public function saveSignature(Request $request){
        try{
            $path = '';
            $path = $request->file('signature')->store('signatures','signatures');
            return sendResponse('/storage/signatures/'.$path, 'success');

        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function changeInfoUser(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user' => 'required',
                'name' => 'required',
                'lastname' => 'required',
                'sms' => 'required',
                'double_authentification' => 'required',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            $req = $request->all();
            $user = UserClient::where('id',$req['user'])->where('deleted',0)->first();
            $user->name = $req['name'];
            $user->lastname = $req['lastname'];
            $user->sms = $req['sms'];
            $user->double_authentification = $req['double_authentification'];
            $user->updated_at = carbon::now();
            $user->save();
            
            $message = ['success' => true, 'status' => 200,'message' => 'Changement des informations personelles','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message); 
            return sendResponse($user, 'Success.');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function changePasswordUser(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'oldPassword' => 'required',
                'password' => 'required',
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            $req = $request->all();
            $user = UserClient::where('id',$req['id'])->where('deleted',0)->first();

            if (Hash::check($request->oldPassword, $user->password)) {
                $user->password = Hash::make($req['password']);
                $user->updated_at = carbon::now();
                $user->save();
                $message = ['success' => true, 'status' => 200,'message' => 'Changement de mot de passe','timestamp' => Carbon::now(),'user' => $user->id]; 
                writeLog($message);
                return sendResponse($user, 'Mot de passe changé avec succes');

            }else{
                $message = ['success' => false, 'status' => 500, 'message' => 'Ancien mot de passe incorrecte lors du changement du mot de passe','timestamp' => Carbon::now(),'user' => $user->id]; 
                writeLog($message);
                return sendError('Ancien mot de passe incorrecte', [], 500);
            }
            
            $message = ['success' => true, 'status' => 200,'message' => 'Changement de mot de passe','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message); 
            return sendResponse($user, 'Mot de passe changé avec succès.');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function changeDoubleUser(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'double_authentification' => 'required',
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            $req = $request->all();
            $user = UserClient::where('id',$req['id'])->where('deleted',0)->first();
            $user->double_authentification = $req['double_authentification'];
            $user->save();

            return sendResponse($user, 'Succès.');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getQuestionsAll(){
        try {
            $questions = PasswordResetQuestion::where('deleted',0)->get();
            return sendResponse($questions, 'Succes');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getQuestionsByPhone(Request $request){
        try {             
            $client = UserClient::where('username',$request->username)->where('deleted',0)->first();
            if($client){
                $questions = PasswordResetQuestionClient::with('userClient')->where('user_client_id',$client->id)->where('deleted',0)->orderBy('created_at', 'desc')->take(3)->get();
                if(count($questions) > 0){
                    return sendResponse($questions, 'Succes');
                }else{
                    return sendResponse([], 'Succes');
                }
            }else{
                return sendError('L\'utilisateur n\'existe pas', [], 404);
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function resetPasswordWithQuestions(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            
            $client = UserClient::where('username',$request->username)->where('deleted',0)->first();
            if($client){
                $i = 0;
                foreach($request->answers as $item){
                    $question_exist = PasswordResetQuestionClient::where('user_client_id',$client->id)->where('password_reset_question',$item['question'])->first();
                    if($question_exist){
                        if(strtolower(unaccentWithoutSpace($question_exist->answer)) != strtolower(unaccentWithoutSpace($item['answer']))){
                            $i++;
                        }
                    }else{
                        return sendError('Questions incorrectes', [], 500);
                    }
                }
                if($i != 0){
                    return sendError('Reponses incorrectes', [], 500);
                }else{
                    $client->password = Hash::make($request->password);
                    $client->save();
                }
                return sendResponse($client, 'Succes');
            }else{
                return sendError('L\'utilisateur n\'existe pas', [], 404);
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }    
    
    public function getDashboard(Request $request){
        try{                
            $data = [];
            $client = UserClient::where('id',$request->id)->first()->makeHidden(['password','code_otp']);

            $cards = UserCard::where('user_client_id',$request->id)->orderBy('created_at','DESC')->limit(5)->get();
            $nb_card = UserCard::where('user_client_id',$request->id)->orderBy('created_at','DESC')->count();

            $beneficiaires = Beneficiaire::where('user_client_id',$request->id)->where('deleted',0)->orderBy('id','DESC')->limit(5)->get();

            
            $transactions = Depot::with('partenaire')->with('userClient')
            ->select(DB::raw("id as id_operation"), 'libelle', 'created_at','user_client_id', DB::raw("'depot' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0)

            ->union(Recharge::with('userClient')->with('userCard')
            ->select(DB::raw("id as id_operation"), DB::raw("'Rechargement de compte' as libelle"), 'created_at', 'user_client_id', DB::raw("'recharge' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))

            ->union(Retrait::with('partenaire')->with('userClient')
            ->select(DB::raw("id as id_operation"), 'libelle', 'created_at', 'user_client_id', DB::raw("'retrait' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))

            ->union(TransfertOut::with('userClient')->with('receveur')->with('userCard')
            ->select(DB::raw("id as id_operation"), 'libelle', 'created_at', 'user_client_id', DB::raw("'transfert_out' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))
            
            ->union(UserCardBuy::with('userClient')
            ->select(DB::raw("id as id_operation"), DB::raw("'Achat de carte' as libelle"), 'created_at', 'user_client_id', DB::raw("'card_buy' as type_operation"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))

            ->orderBy('created_at','desc')->take(5)->get();

            $data['cards'] = $cards;
            $data['beneficiaires'] = $beneficiaires;
            $data['transactions'] = $transactions;
            $data['client'] = $client;

            $info_card = Info::where('deleted',0)->first();

            $data['infos'] =  [
                'nb_card' => $nb_card,
                'max_card' => $info_card ? $info_card->card_max : 5,
                'price_card' => $info_card ? $info_card->card_price : 0
            ];
            return sendResponse($data, 'Dashboard');
        }catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getSolde(Request $request){
        try{
            $cards = UserCard::where('user_client_id',$request->id)->orderBy('created_at','DESC')->get();
            $solde = 0;
            foreach ($cards as $key => $card) {
                $solde += (int) getCarteInformation((string)$card->customer_id, 'balance');
            }
            $encrypt_Key = env('ENCRYPT_KEY');
            $solde = encryptData((string)$solde,$encrypt_Key);
            return sendResponse($solde, 'Solde.');
            
        }catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getClientAllTransaction(Request $request){
        try{
            $transactions = Depot::with('partenaire')->with('userClient')
            ->select(DB::raw("id as id_operation"), 'libelle', 'created_at','user_client_id', DB::raw("'depot' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0)

            ->union(Recharge::with('userClient')->with('userCard')
            ->select(DB::raw("id as id_operation"), DB::raw("'Rechargement de compte' as libelle"), 'created_at', 'user_client_id', DB::raw("'recharge' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))

            ->union(Retrait::with('partenaire')->with('userClient')
            ->select(DB::raw("id as id_operation"), 'libelle', 'created_at', 'user_client_id', DB::raw("'retrait' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))

            ->union(TransfertOut::with('userClient')->with('receveur')->with('userCard')
            ->select(DB::raw("id as id_operation"), 'libelle', 'created_at', 'user_client_id', DB::raw("'transfert_out' as type_operation"), 'montant', 'frais')
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))
            
            ->union(UserCardBuy::with('userClient')
            ->select(DB::raw("id as id_operation"), DB::raw("'Achat de carte' as libelle"), 'created_at', 'user_client_id', DB::raw("'card_buy' as type_operation"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('user_client_id', $request->id)->where('deleted', 0))

            ->orderBy('created_at','desc')->get();

            $i = 0;
            $lastKey = '';
            $data = [];
            foreach ($transactions as $key => $value) {
                $date = $this->get_day_name(strtotime($value->created_at));
                if($lastKey !== $date){
                    $lastKey = $date;
                    $data[$i]['id'] = $i;
                    $data[$i]['title'] = $date;
                    $i++;
                }
                $data[$i]['transactions'][$value->id_operation] = $value;
            }
            
            return sendResponse(collect($data), 'Liste transactions.');
            
        }catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }
    
    public function callBackKkiapay(Request $request)
    {
        $payload = $request->all();

        return response()->json(['status' => 'success', 'message' => 'Webhook received successfully']);
    }




    
//gghcghcgh
    public function getClientPendingTransaction(Request $request){
        try{
            $transactions = Recharge::select('id', DB::raw("'Rechargement de la carte' as libelle"), 'created_at', 'user_client_id', DB::raw("'card_load' as type_operation"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'pending')->where('user_client_id', $request->id)->where('deleted', 0)

            ->union(UserCardBuy::select('id', DB::raw("'Paiement de carte' as libelle"), 'created_at', 'user_client_id', DB::raw("'card_buy' as type_operation"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'pending')->where('user_client_id', $request->id)->where('deleted', 0))

            ->union(TransfertOut::select('id', 'libelle', 'created_at', 'user_client_id', DB::raw("'transfert_out' as type_operation"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'pending')->where('user_client_id', $request->id)->where('deleted', 0))

            ->orderBy('created_at','desc')->get();
            return sendResponse($transactions, 'Liste transactions.');
            
        }catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function saveFrontPayment(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'montant' => 'required|string',
                'moyen_paiement' => 'required|string',
                'reference_paiement' => 'required|string',
                'telephone' => 'required|string',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            
            $frontPayment = FrontPayment::create([
                'id' => Uuid::uuid4()->toString(),
                'moyen_paiement' => $request->moyen_paiement,
                'reference_paiement' => $request->reference_paiement,
                'telephone' => $request->telephone,
                'user_client_id' => $request->user_client_id,
                'user_partenaire_id' => $request->user_partenaire_id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return sendResponse($frontPayment, 'Succes');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }
    
    protected function createNewToken($token){
        return $token;
    }

    function get_day_name($timestamp) {

        $date = date('d/m/Y', $timestamp);
    
        if($date == date('d/m/Y')) {
          $date = 'Aujourd\'hui';
        } 
        else if($date == date('d/m/Y',now()->timestamp - (24 * 60 * 60))) {
          $date = 'Hier';
        }
        return $date;
    }
}