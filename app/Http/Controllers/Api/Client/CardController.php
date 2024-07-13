<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserClient;
use App\Models\Info;
use App\Models\UserCardBuy;
use Illuminate\Support\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\VenteVirtuelle as MailVenteVirtuelle;
use App\Models\AccountCommission;
use App\Models\AccountCommissionOperation;
use App\Models\UserCard;
use App\Models\UserPartenaire;
use App\Models\Apporteur;
use App\Models\ApporteurOperation;
use App\Services\PaiementService;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;

class CardController extends Controller
{

    public function __construct() {
        $this->middleware('is-auth', ['except' => ['checkCodePromo','callBackCardPurchase']]);
    }

    public function buyCard(Request $request, PaiementService $paiementService){
        
        try{
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $validator = Validator::make($request->all(), [
                'user_id' => ["required" , "string"],
                'montant' => ["required" , "integer"],
                'promo_code' => ["nullable" , "string"],
                'type' => ["required" , "max:255", "regex:(kkiapay|bmo)"],
            ]);


            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $userClient = UserClient::where('id',$request->user_id)->first();

            if($userClient->verification == 0){
                return response()->json([
                    'message' => 'Ce compte n\'est pas encore validé',
                ], 401);
            }

            $montant = $request->montant;
            $partenaire = $app =  null;

            if($request->promo_code){
                $userPartenaire = UserPartenaire::where('promo_code',$request->promo_code)->first();
                $apporteur = Apporteur::where('code_promo',$request->promo_code)->first();
                $montant = $montant - 500;

                if (!$userPartenaire && !$apporteur) {
                    return sendError('Code promo inexistant',[], 404);
                }else if($userPartenaire){
                    $partenaire = $userPartenaire->partenaire->id;
                }else if($apporteur){
                    $app = $apporteur->id;
                }  
            }          

            $userCardBuy = UserCardBuy::create([
                'id' => Uuid::uuid4()->toString(),
                'moyen_paiement' => $request->type,
                'reference_paiement' => $request->transaction_id,
                'montant' => $montant,
                'user_client_id' => $request->user_id,
                'status' => 'pending',
                'partenaire_id' => $partenaire,
                'apporteur_id' => $app,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            if($request->type == 'kkiapay'){
                $userCardBuy->is_debited = 1;
                return sendResponse($userCardBuy, 'Paiement effectué.. Votre achat est en cours de verification');
            }else{
                $paymentVerification = $paiementService->paymentVerification($request->type, $request->transaction_id, $request->montant, $userClient->id);
                
                if($paymentVerification == true){
                    $userCardBuy->is_debited = 1;
                    $userCardBuy->save();
    
                    $client = new Client();
                    $url = $base_url."accounts/virtual";
                    
                    $name = $userClient->kycClient->name.' '.$userClient->kycClient->lastname;
                    if (strlen($name) > 19){
                        $name = substr($name, 0, 19);
                    }
                    $address = substr($userClient->kycClient->address, 0, 25);
                    
                    $body = [
                        "firstName" => $userClient->kycClient->name,
                        "lastName" => $userClient->kycClient->lastname,
                        "preferredName" => unaccent($name),
                        "address1" => $address,
                        "city" => $userClient->kycClient->city,
                        "country" => $userClient->kycClient->country,
                        "stateRegion" => $userClient->kycClient->departement,
                        "birthDate" =>  $userClient->kycClient->birthday,
                        "idType" => $userClient->kycClient->piece_type,
                        "idValue" => $userClient->kycClient->piece_id,
                        "mobilePhoneNumber" => [
                            "countryCode" => explode(' ',$userClient->kycClient->telephone)[0],
                            "number" =>  explode(' ',$userClient->kycClient->telephone)[1],
                        ],
                        "emailAddress" => $userClient->kycClient->email,
                        "accountSource" => "OTHER",
                        "referredBy" => $accountId,
                        "subCompany" => $accountId,
                        "return" => "RETURNPASSCODE"
                    ];    
                    $body = json_encode($body);
                    
                    $headers = [
                        'programId' => $programID,
                        'requestId' => Uuid::uuid4()->toString(),
                        'Content-Type' => 'application/json', 'Accept' => 'application/json'
                    ];
    
                    $auth = [
                        $authLogin,
                        $authPass
                    ];
                    
                    try {
                        $response = $client->request('POST', $url, [
                            'auth' => $auth,
                            'headers' => $headers,
                            'body' => $body,
                            'verify'  => false,
                        ]);    
                        $responseBody = json_decode($response->getBody());
        
                        $oldCard = UserCard::where('deleted',0)->where('user_client_id',$userClient->id)->get();
                        $firstly = 0;
                        if(count($oldCard) == 0){
                            $firstly = 1;
                        }
        
                        $card = UserCard::create([
                            'id' => Uuid::uuid4()->toString(),
                            'user_client_id' => $userClient->id,
                            'last_digits' => encryptData((string)$responseBody->registrationLast4Digits,$encrypt_Key),
                            'customer_id' => encryptData((string)$responseBody->registrationAccountId,$encrypt_Key),
                            'type' => $request->type,
                            'is_first' => $firstly,
                            'pass_code' => encryptData((string)$responseBody->registrationPassCode,$encrypt_Key),
                            'is_buy' => 1,
                            'deleted' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
    
                        $userCardBuy->user_card_id = $card->id;
                        $userCardBuy->status = 'completed';
                        $userCardBuy->save();
    
                        if($request->promo_code){
                            if($userPartenaire){
                                $compteCommissionPartenaire = AccountCommission::where('partenaire_id',$partenaire)->first();
                                $soldeAvIncr = $compteCommissionPartenaire->solde;
                                $compteCommissionPartenaire->solde += 400;
                                $compteCommissionPartenaire->save();
                                
                                $soldeApIncr = $compteCommissionPartenaire->solde;
                    
                                AccountCommissionOperation::insert([
                                    'id' => Uuid::uuid4()->toString(),
                                    'reference_gtp'=> '',
                                    'solde_avant' => $soldeAvIncr,
                                    'montant' => 400,
                                    'solde_apres' => $soldeApIncr,
                                    'libelle' => 'Commission pour achat de carte par code promo',
                                    'type' => 'credit',
                                    'account_commission_id' => $compteCommissionPartenaire->id,
                                    'deleted' => 0,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),            
                                ]);
                            }else if($apporteur){
                                $compteCommissionApporteur = Apporteur::where('id',$app)->first();
                                $compteCommissionApporteur->solde_commission += 400;
                                $compteCommissionApporteur->save();
                                
                    
                                ApporteurOperation::insert([
                                    'id' => Uuid::uuid4()->toString(),
                                    'apporteur_id' => $app,
                                    'montant' => 400,
                                    'libelle' => 'Commission pour achat de carte par code promo',
                                    'sens' => 'credit',
                                    'deleted' => 0,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),            
                                ]);
                            }
                        }
                        
                        $message = 'Felicitations! Votre achat de carte virtuelle Bcc est finalisé. Les informations suivantes sont celles de votre carte : Customer ID: '. $responseBody->registrationAccountId.', Quatre dernier Chiffre :'. $responseBody->registrationLast4Digits.', Registration pass code :'.$responseBody->registrationPassCode.'.';
                        sendSms($userClient->username,$message);
                        try{
                            Mail::to([$userClient->kycClient->email,])->send(new MailVenteVirtuelle(['registrationAccountId' => $responseBody->registrationAccountId,'registrationLast4Digits' => $responseBody->registrationLast4Digits,'registrationPassCode' => $responseBody->registrationPassCode,'type' => $request->type])); 
                        } catch (\Exception $e) {
                            $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userClient->id];  
                            writeLog($message);
                        }
                        $message = ['success' => true, 'status' => 200,'message' => 'Achat effectué avec succes','timestamp' => Carbon::now(),'user' => $userClient->id]; 
                        writeLog($message);
                        return sendResponse($card, 'Achat terminé avec succes');
                    } catch (BadResponseException $e) {
                        $json = json_decode($e->getResponse()->getBody()->getContents());
                        
                        $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now(),'user' => $userClient->id]; 
                        writeLog($message);
                        return sendError($json, [], 500);
                    }
                }else{
                    // Voir comment rembourser la transaction des clients

                    $userCardBuy->reasons = $paymentVerification;
                    $userCardBuy->status = 'failed';
                    $userCardBuy->save();
                    return sendError('Erreur lors de la verification du paiement de la carte', [], 500);
                }
            }
            
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage().'-'.$e->getLine(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function checkCodePromo(Request $request){
        try {
            $promo_code = strtoupper(trim($request->promo_code));
            $exist_code_promo = UserPartenaire::where('promo_code',$promo_code)->where('deleted',0)->first();
            $exist_code_promo_apporteur = Apporteur::where('code_promo',$request->promo_code)->where('deleted',0)->first();

            if (!$exist_code_promo && !$exist_code_promo_apporteur) {
                return sendError('Code promo inexistant',[], 404);
            }

            return sendResponse([], 'Code verifié avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function buyCardOld(Request $request, PaiementService $paiementService){
        try{
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $validator = Validator::make($request->all(), [
                'user_id' => ["required" , "string"],
                'transaction_id' => ["required" , "string"],
                'montant' => ["required" , "integer"],
                'promo_code' => ["nullable" , "string"],
                'type' => ["required" , "max:255", "regex:(kkiapay|bmo)"],
            ]);


            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $userClient = UserClient::where('id',$request->user_id)->first();

            if($userClient->verification == 0){
                return response()->json([
                    'message' => 'Ce compte n\'est pas encore validé',
                ], 401);
            }

            $montant = $request->montant;
            $partenaire = $app =  null;

            if($request->promo_code){
                $userPartenaire = UserPartenaire::where('promo_code',$request->promo_code)->first();
                $apporteur = Apporteur::where('code_promo',$request->promo_code)->first();
                $montant = $montant - 500;

                if (!$userPartenaire && !$apporteur) {
                    return sendError('Code promo inexistant',[], 404);
                }else if($userPartenaire){
                    $partenaire = $userPartenaire->partenaire->id;
                }else if($apporteur){
                    $app = $apporteur->id;
                }  
            }          

            $userCardBuy = UserCardBuy::create([
                'id' => Uuid::uuid4()->toString(),
                'moyen_paiement' => $request->type,
                'reference_paiement' => $request->transaction_id,
                'montant' => $montant,
                'user_client_id' => $request->user_id,
                'status' => 'pending',
                'partenaire_id' => $partenaire,
                'apporteur_id' => $app,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            
            //$paymentVerification = $paiementService->paymentVerification($request->type, $request->transaction_id, $request->montant, $userClient->id);
            
            //if($paymentVerification == true){
                $userCardBuy->is_debited = 1;
                $userCardBuy->save();

                $client = new Client();
                $url = $base_url."accounts/virtual";
                
                $name = $userClient->kycClient->name.' '.$userClient->kycClient->lastname;
                if (strlen($name) > 19){
                    $name = substr($name, 0, 19);
                }
                $address = substr($userClient->kycClient->address, 0, 25);
                
                $body = [
                    "firstName" => $userClient->kycClient->name,
                    "lastName" => $userClient->kycClient->lastname,
                    "preferredName" => unaccent($name),
                    "address1" => $address,
                    "city" => $userClient->kycClient->city,
                    "country" => $userClient->kycClient->country,
                    "stateRegion" => $userClient->kycClient->departement,
                    "birthDate" =>  $userClient->kycClient->birthday,
                    "idType" => $userClient->kycClient->piece_type,
                    "idValue" => $userClient->kycClient->piece_id,
                    "mobilePhoneNumber" => [
                        "countryCode" => explode(' ',$userClient->kycClient->telephone)[0],
                        "number" =>  explode(' ',$userClient->kycClient->telephone)[1],
                    ],
                    "emailAddress" => $userClient->kycClient->email,
                    "accountSource" => "OTHER",
                    "referredBy" => $accountId,
                    "subCompany" => $accountId,
                    "return" => "RETURNPASSCODE"
                ];    
                $body = json_encode($body);
                
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                    'Content-Type' => 'application/json', 'Accept' => 'application/json'
                ];

                $auth = [
                    $authLogin,
                    $authPass
                ];
                
                try {
                    $response = $client->request('POST', $url, [
                        'auth' => $auth,
                        'headers' => $headers,
                        'body' => $body,
                        'verify'  => false,
                    ]);    
                    $responseBody = json_decode($response->getBody());
    
                    $oldCard = UserCard::where('deleted',0)->where('user_client_id',$userClient->id)->get();
                    $firstly = 0;
                    if(count($oldCard) == 0){
                        $firstly = 1;
                    }
    
                    $card = UserCard::create([
                        'id' => Uuid::uuid4()->toString(),
                        'user_client_id' => $userClient->id,
                        'last_digits' => encryptData((string)$responseBody->registrationLast4Digits,$encrypt_Key),
                        'customer_id' => encryptData((string)$responseBody->registrationAccountId,$encrypt_Key),
                        'type' => $request->type,
                        'is_first' => $firstly,
                        'pass_code' => encryptData((string)$responseBody->registrationPassCode,$encrypt_Key),
                        'is_buy' => 1,
                        'deleted' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    $userCardBuy->user_card_id = $card->id;
                    $userCardBuy->status = 'completed';
                    $userCardBuy->save();

                    if($request->promo_code){
                        if($userPartenaire){
                            $compteCommissionPartenaire = AccountCommission::where('partenaire_id',$partenaire)->first();
                            $soldeAvIncr = $compteCommissionPartenaire->solde;
                            $compteCommissionPartenaire->solde += 400;
                            $compteCommissionPartenaire->save();
                            
                            $soldeApIncr = $compteCommissionPartenaire->solde;
                
                            AccountCommissionOperation::insert([
                                'id' => Uuid::uuid4()->toString(),
                                'reference_gtp'=> '',
                                'solde_avant' => $soldeAvIncr,
                                'montant' => 400,
                                'solde_apres' => $soldeApIncr,
                                'libelle' => 'Commission pour achat de carte par code promo',
                                'type' => 'credit',
                                'account_commission_id' => $compteCommissionPartenaire->id,
                                'deleted' => 0,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),            
                            ]);
                        }else if($apporteur){
                            $compteCommissionApporteur = Apporteur::where('id',$app)->first();
                            $compteCommissionApporteur->solde_commission += 400;
                            $compteCommissionApporteur->save();
                            
                
                            ApporteurOperation::insert([
                                'id' => Uuid::uuid4()->toString(),
                                'apporteur_id' => $app,
                                'montant' => 400,
                                'libelle' => 'Commission pour achat de carte par code promo',
                                'sens' => 'credit',
                                'deleted' => 0,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now(),            
                            ]);
                        }

                    }
                    
                    $message = 'Felicitations! Votre achat de carte virtuelle Bcc est finalisé. Les informations suivantes sont celles de votre carte : Customer ID: '. $responseBody->registrationAccountId.', Quatre dernier Chiffre :'. $responseBody->registrationLast4Digits.', Registration pass code :'.$responseBody->registrationPassCode.'.';
                    sendSms($userClient->username,$message);
                    try{
                        Mail::to([$userClient->kycClient->email,])->send(new MailVenteVirtuelle(['registrationAccountId' => $responseBody->registrationAccountId,'registrationLast4Digits' => $responseBody->registrationLast4Digits,'registrationPassCode' => $responseBody->registrationPassCode,'type' => $request->type])); 
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userClient->id];  
                        writeLog($message);
                    }
                    $message = ['success' => true, 'status' => 200,'message' => 'Achat effectué avec succes','timestamp' => Carbon::now(),'user' => $userClient->id]; 
                    writeLog($message);
                    return sendResponse($card, 'Achat terminé avec succes');
                } catch (BadResponseException $e) {
                    $json = json_decode($e->getResponse()->getBody()->getContents());
                    
                    $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now(),'user' => $userClient->id]; 
                    writeLog($message);
                    return sendError($json, [], 500);
                }
            /*}else{
                $userCardBuy->reasons = $paymentVerification;
                $userCardBuy->status = 'failed';
                $userCardBuy->save();
                return sendError('Erreur lors de la verification du paiement de la carte', [], 500);
            }*/
            
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage().'-'.$e->getLine(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function completeBuyCard(Request $request, PaiementService $paiementService){
        try{
            $encrypt_Key = env('ENCRYPT_KEY');
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            
            $validator = Validator::make($request->all(), [
                'transaction_id' => ["required" , "string"],
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            
            $userCardBuy = UserCardBuy::where('id',$request->transaction_id)->first();
            $user = UserClient::where('id',$userCardBuy->user_client_id)->first();


            if($user->verification == 0){
                return response()->json([
                    'message' => 'Ce compte n\'est pas encore validé',
                ], 401);
            }
            
            $paymentVerification = $paiementService->paymentVerification($userCardBuy, $request->type, $userCardBuy->reference_paiement, $userCardBuy->montant, $user->id);
            
            if($paymentVerification == true){
                
            
                $userCardBuy->is_debited = 1;
                $userCardBuy->save();

                $client = new Client();
                $url = $base_url."accounts/virtual";
                
                $name = $user->kycClient->name.' '.$user->kycClient->lastname;
                if (strlen($name) > 19){
                    $name = substr($name, 0, 19);
                }
                $address = substr($user->kycClient->address, 0, 25);
                
                $body = [
                    "firstName" => $user->kycClient->name,
                    "lastName" => $user->kycClient->lastname,
                    "preferredName" => unaccent($name),
                    "address1" => $address,
                    "city" => $user->kycClient->city,
                    "country" => $user->kycClient->country,
                    "stateRegion" => $user->kycClient->departement,
                    "birthDate" =>  $user->kycClient->birthday,
                    "idType" => $user->kycClient->piece_type,
                    "idValue" => $user->kycClient->piece_id,
                    "mobilePhoneNumber" => [
                        "countryCode" => explode(' ',$user->kycClient->telephone)[0],
                        "number" =>  explode(' ',$user->kycClient->telephone)[1],
                    ],
                    "emailAddress" => $user->kycClient->email,
                    "accountSource" => "OTHER",
                    "referredBy" => $accountId,
                    "subCompany" => $accountId,
                    "return" => "RETURNPASSCODE"
                ];    
                $body = json_encode($body);
                
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                    'Content-Type' => 'application/json', 'Accept' => 'application/json'
                ];

                $auth = [
                    $authLogin,
                    $authPass
                ];
                
                try {
                    $response = $client->request('POST', $url, [
                        'auth' => $auth,
                        'headers' => $headers,
                        'body' => $body,
                        'verify'  => false,
                    ]);    
                    $responseBody = json_decode($response->getBody());
    
                    $oldCard = UserCard::where('deleted',0)->where('user_client_id',$user->id)->get();
                    $firstly = 0;
                    if(count($oldCard) == 0){
                        $firstly = 1;
                    }
    
                    $card = UserCard::create([
                        'id' => Uuid::uuid4()->toString(),
                        'user_client_id' => $user->id,
                        'last_digits' => encryptData((string)$responseBody->registrationLast4Digits,$encrypt_Key),
                        'customer_id' => encryptData((string)$responseBody->registrationAccountId,$encrypt_Key),
                        'type' => $request->type,
                        'is_first' => $firstly,
                        'is_buy' => 1,
                        'deleted' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                    
                    $userCardBuy->user_card_id = $card->id;
                    $userCardBuy->status = 'completed';
                    $userCardBuy->save();

                    
                    if($userCardBuy->userPartenaire){   

                        // Ajout compte de commission 
                        $compteCommissionPartenaire = AccountCommission::where('partenaire_id',$userCardBuy->partenaire_id)->first();
                        $soldeAvIncr = $compteCommissionPartenaire->solde;
                        $compteCommissionPartenaire->solde += 400;
                        $compteCommissionPartenaire->save();
                        
                        $soldeApIncr = $compteCommissionPartenaire->solde;
            
                        AccountCommissionOperation::insert([
                            'id' => Uuid::uuid4()->toString(),
                            'reference_gtp'=> '',
                            'solde_avant' => $soldeAvIncr,
                            'montant' => 400,
                            'solde_apres' => $soldeApIncr,
                            'libelle' => 'Commission pour achat de carte par code promo',
                            'type' => 'credit',
                            'account_commission_id' => $compteCommissionPartenaire->id,
                            'deleted' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),            
                        ]);
                    }else if ($userCardBuy->apporteur){
                        $compteCommissionApporteur = Apporteur::where('id',$userCardBuy->apporteur->id)->first();
                        $compteCommissionApporteur->solde_commission += 400;
                        $compteCommissionApporteur->save();
                        
            
                        ApporteurOperation::insert([
                            'id' => Uuid::uuid4()->toString(),
                            'apporteur_id' => $userCardBuy->apporteur->id,
                            'montant' => 400,
                            'libelle' => 'Commission pour achat de carte par code promo',
                            'sens' => 'credit',
                            'deleted' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),            
                        ]);
                    }
                    $message = 'Felicitations! Votre achat de carte virtuelle Bcc est finalisé. Les informations suivantes sont celles de votre carte : Customer ID: '. $responseBody->registrationAccountId.', Quatre dernier Chiffre :'. $responseBody->registrationLast4Digits.', Registration pass code :'.$responseBody->registrationPassCode.'.';
                    sendSms($userCardBuy->userClient->username,$message);

                    try{
                        Mail::to([$user->kycClient->email,])->send(new MailVenteVirtuelle(['registrationAccountId' => $responseBody->registrationAccountId,'registrationLast4Digits' => $responseBody->registrationLast4Digits,'registrationPassCode' => $responseBody->registrationPassCode,'type' => $request->type])); 
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $user->id];  
                        writeLog($message);
                    }
                    
                    $message = ['success' => true, 'status' => 200,'message' => 'Achat effectué avec succes','timestamp' => Carbon::now(),'user' => $user->id]; 
                    writeLog($message);
                    return sendResponse($card, 'Achat terminé avec succes');
                } catch (BadResponseException $e) {
                    $json = json_decode($e->getResponse()->getBody()->getContents());
                    $error = $json->title.'.'.$json->detail;
                    
                    $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                    writeLog($message);
                    return sendError($error, [], 500);
                }
            }else{
                return sendError('Erreur lors du paiement de la carte', [], 500);
            }
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }
    
    public function setDefaultCard(Request $request){
        try{            
            $validator = Validator::make($request->all(), [
                'card_id' => ["required" , "string"]
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            
            $newDefaultCard = UserCard::where('id',$request->card_id)->first();
            $user = UserClient::where('id',$newDefaultCard->user_client_id)->where('deleted',0)->first();

            foreach($user->userCards as $item){
                $item->is_first = 0;
                $item->save();
            }
            $newDefaultCard->is_first = 1;
            $newDefaultCard->save();

            $message = ['success' => true, 'status' => 200,'message' => 'Changement de carte effectué avec success','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message);
            return sendResponse($newDefaultCard, 'Carte defini avec success');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function liaisonCarte(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'customer_id' => 'required|unique:user_cards',
                'last_digits' => 'required|unique:user_cards',
                'type' => 'required',
                'mobile_phone_number' => 'required'
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $encrypt_Key = env('ENCRYPT_KEY');
            
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');

            try {
                $client = new Client();
                $url = $base_url."accounts/".$request->customer_id;
            
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                ];
            
                $auth = [
                    $authLogin,
                    $authPass
                ];
                $response = $client->request('GET', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                ]);
            
                $clientInfo = json_decode($response->getBody());

                if($clientInfo->cardStatus == 'LC'){
                    return sendError('Cette carte est pour le moment bloqué. Veuillez contacter le service clientèle', [], 404);
                }
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                writeLog($message);
                return sendError($error, [], 500);
            }
            try {
                $client = new Client();
                $url = $base_url."accounts/phone-number";
    
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString()
                ];
        
                $query = [
                    'phoneNumber' => $request->mobile_phone_number
                ];
        
                $auth = [
                    $authLogin,
                    $authPass
                ];

                $response = $client->request('GET', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                    'query' => $query
                ]);
                
                $accountInfoLists = json_decode($response->getBody())->accountInfoList;

                Log::info($accountInfoLists);
                //return $response->getBody();
                $i = 0;
                foreach ($accountInfoLists as $value) {
                    if($value->accountId == $request->customer_id && $value->lastFourDigits == $request->last_digits){
                        $i = 100;
                        break;
                    }else{
                        $i++;
                    }
                }
                if($i != 100){                    
                    return sendError('Les 4 derniers chiffres ne correspondent pas a l\'ID', [], 403);
                }
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());   
                $error = $json->title.'.'.$json->detail;
                $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                writeLog($message);
                return sendError($error, [], 500);
            }

            Log::info("je suis ici maintenant");

            
            $user = UserClient::where('id',$request->user_id)->first();
            $firstly = 0;

            if($user->username !== $request->mobile_phone_number){
                $message = ['success' => false, 'status' => 500,'message' => 'ok','timestamp' => Carbon::now()]; 
                writeLog($message);
                return sendError($user->username.' '.$request->mobile_phone_number, [], 500);
            }

            $card = UserCard::create([
                'id' => Uuid::uuid4()->toString(),
                'user_client_id' => $user->id,
                'customer_id' => encryptData((string)$request->customer_id,$encrypt_Key),
                'last_digits' => encryptData((string)$request->last_digits,$encrypt_Key),
                'type' => $request->type,
                'is_first' => $firstly,
                'is_buy' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $message = ['success' => true, 'status' => 200,'message' => 'Liaison de carte effectuée avec succes','timestamp' => Carbon::now(),'user' => $user->id]; 
            writeLog($message);
            
            return sendResponse($card, 'Liaison effectuée avec succes');
            
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getUserCards(Request $request){
        try{
            $nb_card = UserCard::where('user_client_id',$request->id)->orderBy('created_at','DESC')->count();
            $cards = UserCard::where('user_client_id',$request->id)->get();
            
            $data['cards'] = $cards;
            
            $info_card = Info::where('deleted',0)->first();

            $data['infos'] =  [
                'nb_card' => $nb_card,
                'max_card' => $info_card ? $info_card->card_max : 5,
                'price_card' => $info_card ? $info_card->card_price : 0
            ];
            return sendResponse($data, 'Liste de carte.');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getUserCard(Request $request){
        try{
            $buy = UserCardBuy::where('id',$request->id)->first();
            $card = UserCard::where('id',$buy->user_card_id)->first();
            
            return sendResponse($card, 'Carte');

        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function getCardsInfos(Request $request){
        try{
            $nb_card = UserCard::where('user_client_id',$request->id)->orderBy('created_at','DESC')->count();
            $info_card = Info::where('deleted',0)->first();
    
            $data =  [
                'nb_card' => $nb_card,
                'max_card' => $info_card ? $info_card->card_max : 5,
                'price_card' => $info_card ? $info_card->card_price : 0,
                'on_sale' => 1,
                'discount' => 100,
            ];
            return sendResponse($data, 'Carte infos.');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }
    
    public function getCardInfo(Request $request){
        $card = UserCard::where('id',$request->id)->first();
        $card->info = getCarteInformation((string)$card->customer_id, 'all');
        return sendResponse($card, 'Carte.');
    }

    public function getAccountInfo(Request $request){
        $card = UserCard::where('id',$request->id)->first();
        $card->accountInfo = getCarteInformation((string)$card->customer_id, 'accountInfo');
        return sendResponse($card, 'Carte.');
    }

    public function getBalance(Request $request){
        $card = UserCard::where('id',$request->id)->first();
        $encrypt_Key = env('ENCRYPT_KEY');
        $card->balance = encryptData((string)getCarteInformation((string)$card->customer_id, 'balance'),$encrypt_Key);
        return sendResponse($card, 'Carte.');
    }

    public function getClientInfo(Request $request){
        $card = UserCard::where('id',$request->id)->first();
        $card->clientInfo = getCarteInformation((string)$card->customer_id, 'clientInfo');
        return sendResponse($card, 'Carte.');
    }

    public function changeCardStatus(Request $request){
        try {
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');

            $validator = Validator::make($request->all(), [
                'code' => 'required',
                'telephone' => 'required',
                'last' => 'required',
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }   

            $client = new Client();
            $url = $base_url."accounts/".$request->code."/status";
            
            $body = [
                "last4Digits" => $request->last,
                "mobilePhoneNumber" => $request->telephone,
                "newCardStatus" => $request->status
            ];
    
            $body = json_encode($body);
    
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];
    
            $auth = [
                $authLogin,
                $authPass
            ];
    
            try {
                $response = $client->request('PATCH', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                    'body' => $body,
                    'verify'  => false,
                ]);

                $user = UserCard::where('deleted',0)->where('customer_id',$request->code)->first()->userClient;
                
                $message = ['success' => true, 'status' => 200,'message' => 'Status de la carte effectuée avec succes','timestamp' => Carbon::now(),'user' => $user->id]; 
                writeLog($message);
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                writeLog($message);
                return sendError($error, [], 500);
            }

            
            if($request->status == "Active"){
                $message = "Déverouillage effectué avec succes";
            }else{
                $message = "Verouillage effectué avec succes";
            }

            return sendResponse([], $message);            
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }    
    
    public function callBackCardPurchase(Request $request, PaiementService $paiementService)
    {
        try{
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $userCardBuy = UserCardBuy::where('id',$request->transaction_id)->first();
            $user = UserClient::where('id',$userCardBuy->user_client_id)->first();        
            
            $userCardBuy->is_debited = 1;
            $userCardBuy->save();

            $client = new Client();
            $url = $base_url."accounts/virtual";
            
            $name = $user->kycClient->name.' '.$user->kycClient->lastname;
            if (strlen($name) > 19){
                $name = substr($name, 0, 19);
            }
            $address = substr($user->kycClient->address, 0, 25);
            
            $body = [
                "firstName" => $user->kycClient->name,
                "lastName" => $user->kycClient->lastname,
                "preferredName" => unaccent($name),
                "address1" => $address,
                "city" => $user->kycClient->city,
                "country" => $user->kycClient->country,
                "stateRegion" => $user->kycClient->departement,
                "birthDate" =>  $user->kycClient->birthday,
                "idType" => $user->kycClient->piece_type,
                "idValue" => $user->kycClient->piece_id,
                "mobilePhoneNumber" => [
                    "countryCode" => explode(' ',$user->kycClient->telephone)[0],
                    "number" =>  explode(' ',$user->kycClient->telephone)[1],
                ],
                "emailAddress" => $user->kycClient->email,
                "accountSource" => "OTHER",
                "referredBy" => $accountId,
                "subCompany" => $accountId,
                "return" => "RETURNPASSCODE"
            ];    
            $body = json_encode($body);
            
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];

            $auth = [
                $authLogin,
                $authPass
            ];
            
            try {
                $response = $client->request('POST', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                    'body' => $body,
                    'verify'  => false,
                ]);    
                $responseBody = json_decode($response->getBody());

                $oldCard = UserCard::where('deleted',0)->where('user_client_id',$user->id)->get();
                $firstly = 0;
                if(count($oldCard) == 0){
                    $firstly = 1;
                }

                $card = UserCard::create([
                    'id' => Uuid::uuid4()->toString(),
                    'user_client_id' => $user->id,
                    'last_digits' => encryptData((string)$responseBody->registrationLast4Digits,$encrypt_Key),
                    'customer_id' => encryptData((string)$responseBody->registrationAccountId,$encrypt_Key),
                    'type' => $request->type,
                    'is_first' => $firstly,
                    'is_buy' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $userCardBuy->user_card_id = $card->id;
                $userCardBuy->status = 'completed';
                $userCardBuy->save();

                
                if($userCardBuy->userPartenaire){   

                    // Ajout compte de commission 
                    $compteCommissionPartenaire = AccountCommission::where('partenaire_id',$userCardBuy->partenaire_id)->first();
                    $soldeAvIncr = $compteCommissionPartenaire->solde;
                    $compteCommissionPartenaire->solde += 400;
                    $compteCommissionPartenaire->save();
                    
                    $soldeApIncr = $compteCommissionPartenaire->solde;
        
                    AccountCommissionOperation::insert([
                        'id' => Uuid::uuid4()->toString(),
                        'reference_gtp'=> '',
                        'solde_avant' => $soldeAvIncr,
                        'montant' => 400,
                        'solde_apres' => $soldeApIncr,
                        'libelle' => 'Commission pour achat de carte par code promo',
                        'type' => 'credit',
                        'account_commission_id' => $compteCommissionPartenaire->id,
                        'deleted' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),            
                    ]);
                }else if ($userCardBuy->apporteur){
                    $compteCommissionApporteur = Apporteur::where('id',$userCardBuy->apporteur->id)->first();
                    $compteCommissionApporteur->solde_commission += 400;
                    $compteCommissionApporteur->save();
                    
        
                    ApporteurOperation::insert([
                        'id' => Uuid::uuid4()->toString(),
                        'apporteur_id' => $userCardBuy->apporteur->id,
                        'montant' => 400,
                        'libelle' => 'Commission pour achat de carte par code promo',
                        'sens' => 'credit',
                        'deleted' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),            
                    ]);
                }
                
                $message = 'Felicitations! Votre achat de carte virtuelle Bcc est finalisé. Les informations suivantes sont celles de votre carte : Customer ID: '. $responseBody->registrationAccountId.', Quatre dernier Chiffre :'. $responseBody->registrationLast4Digits.', Registration pass code :'.$responseBody->registrationPassCode.'.';
                sendSms($userCardBuy->userClient->username,$message);

                try{
                    Mail::to([$user->kycClient->email,])->send(new MailVenteVirtuelle(['registrationAccountId' => $responseBody->registrationAccountId,'registrationLast4Digits' => $responseBody->registrationLast4Digits,'registrationPassCode' => $responseBody->registrationPassCode,'type' => $request->type])); 
                } catch (\Exception $e) {
                    $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $user->id];  
                    writeLog($message);
                }
                
                $message = ['success' => true, 'status' => 200,'message' => 'Achat effectué avec succes','timestamp' => Carbon::now(),'user' => $user->id]; 
                writeLog($message);
                return sendResponse($card, 'Achat terminé avec succes');
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                
                $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                writeLog($message);
                return sendError($error, [], 500);
            }
        }catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function showCardInfos(Request $request, PaiementService $paiementService)
    {
        $base_url = env('BASE_GTP_API');
        $programID = env('PROGRAM_ID');
        $authLogin = env('AUTH_LOGIN');
        $authPass = env('AUTH_PASS');

        $encrypt_Key = env('ENCRYPT_KEY');
        $base_url_vgs = "https://www.verygoodsecurity.com/";
        $programID_vgs = "tok_sandbox_qz4oTazqqfircmkkwkh6eh";
        
        
        $card = UserCard::where('id',$request->card_id)->first();
        $customerId = decryptData((string) $card->customer_id, $encrypt_Key);
        $lastDigits = decryptData((string) $card->last_digits, $encrypt_Key);

        try {
            $client = new Client();
            $url = $base_url."accounts/".$customerId;
        
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
            ];
        
            $auth = [
                $authLogin,
                $authPass
            ];
            $response = $client->request('GET', $url, [
                'auth' => $auth,
                'headers' => $headers,
            ]);
        
            $clientInfo = json_decode($response->getBody());
        } catch (BadResponseException $e) {
            $json = json_decode($e->getResponse()->getBody()->getContents());
            $error = $json->title.'.'.$json->detail;
            return sendError($error, [], 500);
        }


        if($card->pan !== null){
            return sendResponse($card, 'Infos de la carte');
        }

        $client = new Client();
        $cardHash = $clientInfo->cardHash;

        $url = $base_url_vgs."/rest/api/v1/accounts/".$customerId."/pci-info";
        
        $body = [
            "last4Digits”" => $lastDigits,
            "cardHash" => $cardHash,
        ];    
        $body = json_encode($body);
        
        $headers = [
            'programId' => $programID_vgs,
            'requestId' => Uuid::uuid4()->toString(),
            'Content-Type' => 'application/json', 'Accept' => 'application/json'
        ];
        
        try {
            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);    
            $responseBody = json_decode($response->getBody());
            
            
            $card->pan = $responseBody->pan;
            $card->name = $responseBody->name;
            $card->expire_date = $responseBody->expireDate;
            $card->expire_date_card_format = $responseBody->expireDateCardFormat;
            $card->cvv = $responseBody->cvv;
            $card->card_status = $responseBody->cardStatus;

            $card->status = 'completed';
            $card->save();
            return sendResponse($card, 'Achat terminé avec succes');
        } catch (BadResponseException $e) {
            $json = json_decode($e->getResponse()->getBody()->getContents());
            $error = $json->title.'.'.$json->detail;
            
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($error, [], 500);
        }
    }
}
