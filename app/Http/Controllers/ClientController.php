<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserClient;
use App\Models\Depot;
use App\Models\Retrait;
use App\Models\KycClient;
use App\Models\TransfertOut;
use App\Models\CartePerso;
use App\Mail\AlerteValidation;
use App\Mail\MailAlerte;
use App\Models\FrontPayment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\Recharge;
use App\Models\UserCard;
use App\Models\UserCardBuy;
use App\Models\AccountCommission;
use App\Models\AccountCommissionOperation;
use Ramsey\Uuid\Uuid;
use App\Services\PaiementService;
use App\Services\CardService;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Validator;
use App\Mail\VenteVirtuelle as MailVenteVirtuelle;

class ClientController extends Controller
{
    public function clients(Request $request){
        try{
            $userClients = UserClient::where('deleted',0)->where('verification',1)->orderBy('lastname','asc')->paginate(50);
            
            $countries = $this->countries;
            return view('clients.index',compact('userClients','countries'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function searchCompteClient(Request $request){
        try{
            $debut = $request->debut ? explode('T',$request->debut)[0].' '.explode('T',$request->debut)[1].':00' : null;
            $fin = $request->fin ? explode('T',$request->fin)[0].' '.explode('T',$request->fin)[1].':00' : null;
            $userClients = UserClient::where('deleted',0)->where('verification',1)->whereBetween('created_at',[$debut,$fin])->get();
            
            $countries = $this->countries;
            return view('clients.search',compact('userClients','countries'));
        } catch (\Exception $e) {
            dump($e);die();
        }
    }

    public function clientAdd(Request $request){
        try{
            $exist = UserClient::where('username',$request->codePays.$request->telephone)->where('deleted',0)->first();
            
            if($exist){
                return back()->withWarning("Un compte existe deja avec ce numero de telephone");
            }
            
            $password = generateRandomString();

            $lien = env('PLAY_STORE_URL');

            UserClient::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $request->name,
                'lastname' => $request->lastname,
                'username' => $request->codePays.$request->telephone,
                'phone_code' => $request->codePays,
                'phone' => $request->telephone,
                'password' => Hash::make($password),
                'status' => 1,
                'double_authentification' => 0,
                'sms' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $message = 'Felicitations!! Votre compte BCC virtuelle est cree avec succes. Telecharger l\'application en cliquant ici :'.$lien.'
            Connectez vous avec votre numero pour valider votre compte. Votre mot de passe est :'.$password.'. 
            Prière changer apres la connexion.';

            sendSms($request->codePays.$request->telephone,$message);
            
            return back()->withSuccess("Compte client crée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientsAttentes(Request $request){
        try{
            $userClients = UserClient::where('deleted',0)->where('verification',0)->where('is_rejected','<>',1)->orderBy('lastname','asc')->paginate(50);
            $countries = $this->countries;
            return view('clients.attentes',compact('userClients','countries'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientValidation(Request $request){
        try{
            $userClient = UserClient::where('id',$request->id)->where('deleted',0)->first();

            $userClient->status = 1;
            $userClient->verification = 1;
            $userClient->updated_at = Carbon::now();
            $userClient->save();

            try{
                if($userClient->kycClient->email){
                    Mail::to([$userClient->kycClient->email,])->send(new AlerteValidation());
                }
            } catch (\Exception $e) {
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userClient->id];  
                writeLog($message);
            }
            return back()->withSuccess("Validation effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientDelete(Request $request){
        try{
            $userClient = UserClient::where('id',$request->id)->where('deleted',0)->first();

            $userClient->deleted = 1;
            $userClient->updated_at = Carbon::now();
            $userClient->save();
            return back()->withSuccess("Suppression effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientResetPassword(Request $request){
        try{
            $userClient = UserClient::where('id',$request->id)->where('deleted',0)->first();

            $password = generateRandomString();

            $userClient->password = Hash::make($password);
            $userClient->updated_at = Carbon::now();
            $userClient->save();

            $message = 'Mot de passe  reinitialise avec succes. Votre mot de passe temporaire est :'.$password.'. 
            Prière changer apres la connexion pour votre securite.';

            sendSms($userClient->username,$message);
            return back()->withSuccess("Reinitialisation effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientActivation(Request $request){
        try{
            $userClient = UserClient::where('id',$request->id)->where('deleted',0)->first();

            $userClient->status = 1;
            $userClient->updated_at = Carbon::now();
            $userClient->save();
            return back()->withSuccess("Activation effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientDesactivation(Request $request){
        try{
            $userClient = UserClient::where('id',$request->id)->where('deleted',0)->first();

            $userClient->status = 0;
            $userClient->updated_at = Carbon::now();
            $userClient->save();
            return back()->withSuccess("Desactivation effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientRejet(Request $request){
        try{
            $userClient = UserClient::where('id',$request->id)->where('deleted',0)->first();

            $userClient->verification = 0;
            if($request->niveau == 2){
                $motif = 'Information incorrectes';
                $userClient->verification_step_two = 0;
            }else if($request->niveau == 3){
                $motif = 'Pieces ou photo non valide';
                $userClient->verification_step_three = 0;
            }

            $description = $request->description;

            $userClient->motif_rejet = $description;
            $userClient->is_rejected = 1;
            $userClient->save();

            

            try{
                if($userClient->kycClient->email){
                    $arr = ['messages'=> $description ,'objet'=>'Rejet de compte BCC : '.$motif,'from'=>'bmo-uba-noreply@bestcash.me'];
                    Mail::to([$userClient->kycClient->email,])->send(new MailAlerte($arr)); 
                }
            } catch (\Exception $e) {
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userClient->id];  
                writeLog($message);
            }


            return back()->withSuccess("Rejet effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientsRejetes(Request $request){
        try{
            $userClients = UserClient::where('deleted',0)->where('is_rejected',1)->orderBy('lastname','asc')->paginate(50);
            $countries = $this->countries;
            return view('clients.rejetes',compact('userClients','countries'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientsNonCompletes(Request $request){
        try{
            $userClients = UserClient::where('deleted',0)->where('verification_step_one',1)->where('verification','<>',1)->orderBy('created_at','desc')->paginate(50);
            $countries = $this->countries;
            return view('clients.noncompletes',compact('userClients','countries'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientDetails(Request $request){
        try{
            $userClient = UserClient::where('id',$request->id)->first();
            $recharges = Recharge::where('user_client_id',$request->id)->where('status','completed')->where('deleted',0)->orderBy('created_at','desc')->get();
            $depots = Depot::where('user_client_id',$request->id)->where('status','completed')->where('deleted',0)->orderBy('created_at','desc')->get();
            $retraits = Retrait::where('user_client_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at','desc')->get();
            $transferts = TransfertOut::where('user_client_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at','desc')->get();
            $cards = UserCard::where('user_client_id',$request->id)->where('deleted',0)->orderBy('created_at','desc')->get();
            $countries = $this->countries;

            $date = explode('-',$userClient->kycClient->birthday);
            $birthday = $date[2].'-'.array_search($date[1], $this->dateLibelle).'-'.$date[0];
            return view('clients.detail',compact('userClient','depots','retraits','transferts','countries','recharges','cards','birthday'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientOperationsAttentes(Request $request){
        try{
            $transactions = Recharge::with('userClient')
            ->select('id','created_at', 'moyen_paiement','user_client_id', DB::raw('reference_operateur as reference'), DB::raw("'Rechargement' as type"), 'montant', 'frais')
            ->where('status', 'pending')
            ->where('is_debited', 1)
            ->where('deleted', 0)

            ->union(TransfertOut::with('userClient')
            ->select('id','created_at', 'moyen_paiement','user_client_id', DB::raw('reference_gtp_debit as reference'), DB::raw("'Transfert' as type"), 'montant', 'frais')
            ->where('status', 'pending')
            ->where('is_debited', 1)
            ->where('deleted', 0))

            ->union(UserCardBuy::with('userClient')
            ->select('id','created_at', 'moyen_paiement', 'user_client_id', DB::raw('reference_paiement as reference'), DB::raw("'Achat de carte' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'pending')
            ->where('is_debited', 1)
            ->where('deleted', 0))

            ->orderBy('created_at','desc')->get();
            
            return view('clients.operations.attentes',compact('transactions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientOperationsAttentesRefund(Request $request, PaiementService $paiementService){
        try{
            $user = Auth::user();
            $encrypt_Key = env('ENCRYPT_KEY');
            
            if($request->type_operation == 'Rechargement'){
                $depot = Recharge::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();
                
                $response = $paiementService->getPayment($request->moyen_paiement,$depot->reference_operateur);
                
                if(!$response){
                    return back()->withWarning('Le paiment n\'existe pas');
                }
                
                if($response->amount != $depot->montant){
                    return back()->withWarning('Le montant que vous essayez de rembourser ne correspond pas à celui de la transaction');
                }
                
                if ($request->moyen_paiement == 'bmo') {
                    $bmoCredited = $paiementService->bmoCredited('+'.$depot->userClient->username, $depot->userClient->name, $depot->userClient->lastname, $response->amount,$user);

                    if($bmoCredited != false){                        
                        $depot->status = 'refunded';
                        $depot->refunded_at = Carbon::now();
                        $depot->refunder_id = $user->id;
                        $depot->refunded_reference = $bmoCredited->reference;
                        $depot->save();
                            
                        //Voir que message envoyé au client a l'annulation

                        $message = ['success' => true, 'status' => 200,'message' => 'Remboursement du rechargement client','timestamp' => Carbon::now(),'user' => $user->id]; 
                        writeLog($message);  
                    }else{
                        return back()->withWarning("Echec lors du remboursement de la transaction");                        
                    }
                }else{
                    $momoCredited = $paiementService->momoCredited($response->client->phone, $response->amount);

                    if($momoCredited == "FAILED"){
                        return back()->withWarning("Echec lors du remboursement de la transaction");
                    }else if($momoCredited == "FAILED_TIME"){
                        return back()->withWarning("Echec du a un temps d'attente trop long");
                    }else{
                        $depot->status = 'refunded';
                        $depot->refunded_at = Carbon::now();
                        $depot->refunder_id = $user->id;
                        $depot->refunded_reference = $momoCredited->transactionId;
                        $depot->save();
                            
                        //Voir que message envoyé au client a l'annulation

                        $message = ['success' => true, 'status' => 200,'message' => 'Remboursement du rechargement client','timestamp' => Carbon::now(),'user' => $user->id]; 
                        writeLog($message); 
                    }
                }
            }else if($request->type_operation == 'Transfert'){
                $transfert = TransfertOut::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();

                $sender =  UserClient::where('deleted',0)->where('id',$transfert->user_client_id)->first();
                $sender_card =  UserCard::where('deleted',0)->where('id',$transfert->user_card_id)->first();

                if($sender_card == null){
                    $sender_card =  $sender->userCard->first();
                }
                $reference_memo_gtp = unaccent("Remboursement de transfert de : " . (int)$transfert->montant . " XOF vers la carte " . decryptData((string)$sender_card->customer_id, $encrypt_Key));
                $cardCredited = $paiementService->cardCredited($sender_card->customer_id, $sender_card->last_digits, $transfert->montant, $user, $reference_memo_gtp);

                if($cardCredited == false){
                    return back()->withError('Probleme lors du credit de la carte');
                }else{                    
                    $transfert->status = 'refunded';
                    $transfert->refunded_at = Carbon::now();
                    $transfert->refunder_id = $user->id;
                    $transfert->refunded_reference = $cardCredited->transactionId;
                    $transfert->save();
                }
            }else{
                $achat = UserCardBuy::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();
                
                $response = $paiementService->getPayment($request->moyen_paiement,$achat->reference_paiement);
                if(!$response){
                    return back()->withWarning('Le paiment n\'existe pas');
                }
                if($response->amount != $achat->montant){
                    return back()->withWarning('Le montant que vous essayez de rembourser ne correspond pas à celui de la transaction');
                }
                
                if ($request->moyen_paiement == 'bmo') {
                    $bmoCredited = $paiementService->bmoCredited('+'.$achat->userClient->username, $achat->userClient->name, $achat->userClient->lastname, $response->amount,$user);
                    
                    if($bmoCredited != false){                        
                        $achat->status = 'refunded';
                        $achat->refunded_at = Carbon::now();
                        $achat->refunder_id = $user->id;
                        $achat->refunded_reference = $bmoCredited->reference;
                        $achat->save();
                            
                        //Voir que message envoyé au client a l'annulation

                        $message = ['success' => true, 'status' => 200,'message' => 'Remboursement du rechargement client','timestamp' => Carbon::now(),'user' => $user->id]; 
                        writeLog($message);  
                    }else{
                        return back()->withWarning("Echec lors du remboursement de la transaction");
                    }
                }else{
                    $momoCredited = $paiementService->momoCredited($response->client->phone, $response->amount);
                    if($momoCredited == "FAILED"){
                        return back()->withWarning("Echec lors du remboursement de la transaction");
                    }else if($momoCredited == "FAILED_TIME"){
                        return back()->withWarning("Echec du a un temps d'attente trop long");
                    }else{
                        $achat->status = 'refunded';
                        $achat->refunded_at = Carbon::now();
                        $achat->refunder_id = $user->id;
                        $achat->refunded_reference = $momoCredited->transactionId;
                        $achat->save();
                            
                        //Voir que message envoyé au client a l'annulation

                        $message = ['success' => true, 'status' => 200,'message' => 'Remboursement du rechargement client','timestamp' => Carbon::now(),'user' => $user->id]; 
                        writeLog($message); 
                    }
                }
            }
            return back()->withSuccess('Remboursement effectué avec succes');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientOperationsAttentesCancel(Request $request){
        try{
            $user = Auth::user();
            if($request->type_operation == 'Recharge'){
                $depot = Recharge::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();                    
                $depot->status = 'cancelled';
                $depot->cancel_motif = $request->motif_cancel;
                $depot->cancelled_at = Carbon::now();
                $depot->canceller_id = $user->id;
                $depot->save();
            }else if($request->type_operation == 'Transfert'){
                $transfert = TransfertOut::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();                  
                $transfert->status = 'cancelled';
                $transfert->cancel_motif = $request->motif_cancel;
                $transfert->cancelled_at = Carbon::now();
                $transfert->canceller_id = $user->id;
                $transfert->save();
            }else{
                $achat = UserCardBuy::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();                  
                $achat->status = 'cancelled';
                $achat->cancel_motif = $request->motif_cancel;
                $achat->cancelled_at = Carbon::now();
                $achat->canceller_id = $user->id;
                $achat->save();
            }
            return back()->withSuccess('Annulation effectuée avec succes');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientOperationsAttentesComplete(Request $request, PaiementService $paiementService){
        try{
            $encrypt_Key = env('ENCRYPT_KEY');
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            /*$user = Auth::user();
            if($request->type_operation == 'depot'){
                $depot = Recharge::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();                    
                $depot->status = 'cancelled';
                $depot->cancel_motif = $request->motif;
                $depot->cancelled_at = Carbon::now();
                $depot->canceller_id = $user->id;
                $depot->save();
            }else if($request->type_operation == 'depot'){
                $transfert = TransfertOut::where('status','pending')->where('id',$request->id)->where('deleted',0)->first();                  
                $transfert->status = 'cancelled';
                $transfert->cancel_motif = $request->motif;
                $transfert->cancelled_at = Carbon::now();
                $transfert->canceller_id = $user->id;
                $transfert->save();
            }else{*/
                $userCardBuy = UserCardBuy::where('id',$request->id)->first();
                $user = UserClient::where('id',$userCardBuy->user_client_id)->first();
    
    
                if($user->verification == 0){
                    return response()->json([
                        'message' => 'Ce compte n\'est pas encore validé',
                    ], 401);
                }
                
                $paymentVerification = $paiementService->paymentVerification($request->moyen_paiement, $userCardBuy->reference_paiement, $userCardBuy->montant, $user->id);
                
                if($paymentVerification == true){
                    
                
                    $userCardBuy->is_debited = 1;
                    $userCardBuy->save();
    
                    $client = new Client();
                    $url = $base_url."accounts/virtual";
                    
                    $name = $user->kycClient->name.' '.$user->kycClient->lastname;
                    if (strlen($name) > 19){
                        $name = substr($name, 0, 19);
                    }
                    $address = substr($user->kycClient->address, 0, 10);
                    
                    $body = [
                        "firstName" => $user->kycClient->name,
                        "lastName" => $user->kycClient->lastname,
                        "preferredName" => unaccent($name),
                        "address1" => unaccent($address),
                        "city" => unaccent($user->kycClient->city),
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
                    //dd($user->kycClient);
                    $body = json_encode($body,JSON_THROW_ON_ERROR);
                    
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
                            'pass_code' => encryptData((string)$responseBody->registrationPassCode,$encrypt_Key),
                            'type' => $request->moyen_paiement,
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
                            $compteCommissionPartenaire->solde += 200;
                            $compteCommissionPartenaire->save();
                            
                            $soldeApIncr = $compteCommissionPartenaire->solde;
                
                            AccountCommissionOperation::insert([
                                'id' => Uuid::uuid4()->toString(),
                                'reference_gtp'=> '',
                                'solde_avant' => $soldeAvIncr,
                                'montant' => 200,
                                'solde_apres' => $soldeApIncr,
                                'libelle' => 'Commission pour achat de carte par code promo',
                                'type' => 'credit',
                                'account_commission_id' => $compteCommissionPartenaire->id,
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
                        return back()->withSuccess('Completion effectuée avec succes');
                    } catch (BadResponseException $e) {
                        dd($e);
                        $json = json_decode($e->getResponse()->getBody()->getContents());
                        $error = $json->title.'.'.$json->detail;
                        
                        $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                        writeLog($message);
                        return back()->withError($error);
                    }
                }else{
                    return back()->withError('Erreur lors du paiement de la carte');
                }
            //}
        } catch (\Exception $e) {
            dd($e);
            return back()->withError($e->getMessage());
        }
    }


    public function clientOperationsRemboursees(Request $request){
        try{
            $transactions = Recharge::with('userClient')
            ->select('id','created_at', 'refunded_at', 'moyen_paiement','user_client_id', DB::raw('reference_operateur as reference'), DB::raw("'Rechargement' as type"), 'montant', 'frais')
            ->where('status', 'refunded')
            ->where('deleted', 0)

            ->union(TransfertOut::with('userClient')
            ->select('id','created_at', 'refunded_at', 'moyen_paiement','user_client_id', DB::raw('reference_gtp_debit as reference'), DB::raw("'Transfert' as type"), 'montant', 'frais')
            ->where('status', 'refunded')
            ->where('deleted', 0))

            ->union(UserCardBuy::with('userClient')
            ->select('id','created_at', 'refunded_at', 'moyen_paiement', DB::raw('reference_paiement as reference'), DB::raw('null as user_client_id'), DB::raw("'Achat de carte' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'refunded')
            ->where('deleted', 0))

            ->orderBy('created_at','desc')->get();

            return view('clients.operations.refunds',compact('transactions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientOperationsFinalisees(Request $request){
        try{
            $transactions = Recharge::with('userClient')
            ->select('id','created_at', 'moyen_paiement','user_client_id', DB::raw('reference_operateur as reference'), DB::raw("'Rechargement' as type"), 'montant', 'frais')
            ->where('status', 'completed')
            ->where('deleted', 0)

            ->union(TransfertOut::with('userClient')
            ->select('id','created_at', 'moyen_paiement','user_client_id', DB::raw('reference_gtp_debit as reference'), DB::raw("'Transfert' as type"), 'montant', 'frais')
            ->where('status', 'completed')
            ->where('deleted', 0))

            ->union(UserCardBuy::with('userClient')
            ->select('id','created_at', 'moyen_paiement', DB::raw('reference_paiement as reference'), DB::raw('null as user_client_id'), DB::raw("'Achat de carte' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')
            ->where('deleted', 0))

            ->orderBy('created_at','desc')->get();
            return view('clients.operations.finalises',compact('transactions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientOperationsAnnulees(Request $request){
        try{
            $transactions = Recharge::with('userClient')
            ->select('id','created_at', 'cancel_motif', 'moyen_paiement','user_client_id', DB::raw('reference_operateur as reference'), DB::raw("'Rechargement' as type"), 'montant', 'frais')
            ->where('status', 'cancelled')
            ->where('deleted', 0)

            ->union(TransfertOut::with('userClient')
            ->select('id','created_at', 'cancel_motif', 'moyen_paiement','user_client_id', DB::raw('reference_gtp_debit as reference'), DB::raw("'Transfert' as type"), 'montant', 'frais')
            ->where('status', 'cancelled')
            ->where('deleted', 0))

            ->union(UserCardBuy::with('userClient')
            ->select('id','created_at', 'cancel_motif', 'moyen_paiement', DB::raw('reference_paiement as reference'), DB::raw('null as user_client_id'), DB::raw("'Achat de carte' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'cancelled')
            ->where('deleted', 0))

            ->orderBy('created_at','desc')->get();
            return view('clients.operations.annulees',compact('transactions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function clientPaiements(Request $request){
        try{
            $paiements = FrontPayment::where('deleted',0)->orderBy('created_at','desc')->get();
            return view('clients.operations.paiements',compact('paiements'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }
    
    public function viewClientReleve(Request $request,CardService $cardService){
        try{
            $client = UserClient::where('id',$request->client_id)->first();
            $debut = $request->debut.' 00:00:00';
            $fin = $request->fin.' 23:59:59';

            $transactions = $cardService->getClientOperation($client->id, $debut, $fin);
            return view('clients.releve.view',compact('transactions','debut','fin','client'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function downloadClientReleve(Request $request,CardService $cardService){
        try{
            $userClient = UserClient::where('id',$request->client_id)->first();
            $debut = $request->debut.' 00:00:00';
            $fin = $request->fin.' 23:59:59';

            $transactions = $cardService->getClientOperation($userClient->id, $debut, $fin);
            $pdf = FacadePdf::loadView('clients.releve.pdf',compact('transactions','debut','fin','userClient'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download ('Releve de '.$userClient->name.' '.$userClient->lastname.' du '.$debut.' au '.$fin.'.pdf');

        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function kycEdit(Request $request){ 
        try {
            $url = $url2 = null;

            if($request->piece_file){
                $filename = time().'.'.$request->piece_file->getClientOriginalExtension();
                $request->piece_file->move('storage/clients/pieces/', $filename);
                $url = '/storage/clients/pieces/'.$filename;
            }

            if($request->user_with_piece){
                $filename = time().'.'.$request->user_with_piece->getClientOriginalExtension();
                $request->piece_file->move('storage/clients/user_with_pieces/', $filename);
                $url2 = '/storage/clients/user_with_pieces/'.$filename;
            }

            $kyc = KycClient::where('id',$request->id)->where('deleted',0)->first();

            $date = explode('-',$request->birthday);

            $kyc->name = $request->name;
            $kyc->lastname = $request->lastname;
            $kyc->telephone = phoneSeparator($request->phone_full,$request->telephone);
            $kyc->email = $request->email;
            $kyc->birthday = $date[2].'-'.$this->dateLibelle[$date[1]].'-'.$date[0];
            $kyc->departement = $request->departement;
            $kyc->city = $request->city;
            $kyc->country = $request->country;
            $kyc->address = $request->address;
            $kyc->job = $request->job;
            $kyc->salary = $request->salary;
            $kyc->piece_type = $request->piece_type;
            $kyc->piece_id = $request->piece_id;
            
            if($request->piece_file){
                $kyc->piece_file = $url;
            }
            if($request->user_with_piece){
                $kyc->user_with_piece = $url2;
            }
            $kyc->updated_at = Carbon::now();
            $kyc->save();
            return back()->withSuccess("Modification effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        } 
    }

    public function cartePerso(Request $request){
        try{
            $cartes = CartePerso::where('deleted',0)->get();
            $country = $this->countries;
            return view('clients.cartePerso',compact('cartes','countries'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }    

    public $countries = array(
        'AD'=>array('name'=>'ANDORRA','code'=>'376'),
        'AE'=>array('name'=>'UNITED ARAB EMIRATES','code'=>'971'),
        'AF'=>array('name'=>'AFGHANISTAN','code'=>'93'),
        'AG'=>array('name'=>'ANTIGUA AND BARBUDA','code'=>'1268'),
        'AI'=>array('name'=>'ANGUILLA','code'=>'1264'),
        'AL'=>array('name'=>'ALBANIA','code'=>'355'),
        'AM'=>array('name'=>'ARMENIA','code'=>'374'),
        'AN'=>array('name'=>'NETHERLANDS ANTILLES','code'=>'599'),
        'AO'=>array('name'=>'ANGOLA','code'=>'244'),
        'AQ'=>array('name'=>'ANTARCTICA','code'=>'672'),
        'AR'=>array('name'=>'ARGENTINA','code'=>'54'),
        'AS'=>array('name'=>'AMERICAN SAMOA','code'=>'1684'),
        'AT'=>array('name'=>'AUSTRIA','code'=>'43'),
        'AU'=>array('name'=>'AUSTRALIA','code'=>'61'),
        'AW'=>array('name'=>'ARUBA','code'=>'297'),
        'AZ'=>array('name'=>'AZERBAIJAN','code'=>'994'),
        'BA'=>array('name'=>'BOSNIA AND HERZEGOVINA','code'=>'387'),
        'BB'=>array('name'=>'BARBADOS','code'=>'1246'),
        'BD'=>array('name'=>'BANGLADESH','code'=>'880'),
        'BE'=>array('name'=>'BELGIUM','code'=>'32'),
        'BF'=>array('name'=>'BURKINA FASO','code'=>'226'),
        'BG'=>array('name'=>'BULGARIA','code'=>'359'),
        'BH'=>array('name'=>'BAHRAIN','code'=>'973'),
        'BI'=>array('name'=>'BURUNDI','code'=>'257'),
        'BJ'=>array('name'=>'BENIN','code'=>'229'),
        'BL'=>array('name'=>'SAINT BARTHELEMY','code'=>'590'),
        'BM'=>array('name'=>'BERMUDA','code'=>'1441'),
        'BN'=>array('name'=>'BRUNEI DARUSSALAM','code'=>'673'),
        'BO'=>array('name'=>'BOLIVIA','code'=>'591'),
        'BR'=>array('name'=>'BRAZIL','code'=>'55'),
        'BS'=>array('name'=>'BAHAMAS','code'=>'1242'),
        'BT'=>array('name'=>'BHUTAN','code'=>'975'),
        'BW'=>array('name'=>'BOTSWANA','code'=>'267'),
        'BY'=>array('name'=>'BELARUS','code'=>'375'),
        'BZ'=>array('name'=>'BELIZE','code'=>'501'),
        'CA'=>array('name'=>'CANADA','code'=>'1'),
        'CC'=>array('name'=>'COCOS (KEELING) ISLANDS','code'=>'61'),
        'CD'=>array('name'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE','code'=>'243'),
        'CF'=>array('name'=>'CENTRAL AFRICAN REPUBLIC','code'=>'236'),
        'CG'=>array('name'=>'CONGO','code'=>'242'),
        'CH'=>array('name'=>'SWITZERLAND','code'=>'41'),
        'CI'=>array('name'=>'COTE D IVOIRE','code'=>'225'),
        'CK'=>array('name'=>'COOK ISLANDS','code'=>'682'),
        'CL'=>array('name'=>'CHILE','code'=>'56'),
        'CM'=>array('name'=>'CAMEROON','code'=>'237'),
        'CN'=>array('name'=>'CHINA','code'=>'86'),
        'CO'=>array('name'=>'COLOMBIA','code'=>'57'),
        'CR'=>array('name'=>'COSTA RICA','code'=>'506'),
        'CU'=>array('name'=>'CUBA','code'=>'53'),
        'CV'=>array('name'=>'CAPE VERDE','code'=>'238'),
        'CX'=>array('name'=>'CHRISTMAS ISLAND','code'=>'61'),
        'CY'=>array('name'=>'CYPRUS','code'=>'357'),
        'CZ'=>array('name'=>'CZECH REPUBLIC','code'=>'420'),
        'DE'=>array('name'=>'GERMANY','code'=>'49'),
        'DJ'=>array('name'=>'DJIBOUTI','code'=>'253'),
        'DK'=>array('name'=>'DENMARK','code'=>'45'),
        'DM'=>array('name'=>'DOMINICA','code'=>'1767'),
        'DO'=>array('name'=>'DOMINICAN REPUBLIC','code'=>'1809'),
        'DZ'=>array('name'=>'ALGERIA','code'=>'213'),
        'EC'=>array('name'=>'ECUADOR','code'=>'593'),
        'EE'=>array('name'=>'ESTONIA','code'=>'372'),
        'EG'=>array('name'=>'EGYPT','code'=>'20'),
        'ER'=>array('name'=>'ERITREA','code'=>'291'),
        'ES'=>array('name'=>'SPAIN','code'=>'34'),
        'ET'=>array('name'=>'ETHIOPIA','code'=>'251'),
        'FI'=>array('name'=>'FINLAND','code'=>'358'),
        'FJ'=>array('name'=>'FIJI','code'=>'679'),
        'FK'=>array('name'=>'FALKLAND ISLANDS (MALVINAS)','code'=>'500'),
        'FM'=>array('name'=>'MICRONESIA, FEDERATED STATES OF','code'=>'691'),
        'FO'=>array('name'=>'FAROE ISLANDS','code'=>'298'),
        'FR'=>array('name'=>'FRANCE','code'=>'33'),
        'GA'=>array('name'=>'GABON','code'=>'241'),
        'GB'=>array('name'=>'UNITED KINGDOM','code'=>'44'),
        'GD'=>array('name'=>'GRENADA','code'=>'1473'),
        'GE'=>array('name'=>'GEORGIA','code'=>'995'),
        'GH'=>array('name'=>'GHANA','code'=>'233'),
        'GI'=>array('name'=>'GIBRALTAR','code'=>'350'),
        'GL'=>array('name'=>'GREENLAND','code'=>'299'),
        'GM'=>array('name'=>'GAMBIA','code'=>'220'),
        'GN'=>array('name'=>'GUINEA','code'=>'224'),
        'GQ'=>array('name'=>'EQUATORIAL GUINEA','code'=>'240'),
        'GR'=>array('name'=>'GREECE','code'=>'30'),
        'GT'=>array('name'=>'GUATEMALA','code'=>'502'),
        'GU'=>array('name'=>'GUAM','code'=>'1671'),
        'GW'=>array('name'=>'GUINEA-BISSAU','code'=>'245'),
        'GY'=>array('name'=>'GUYANA','code'=>'592'),
        'HK'=>array('name'=>'HONG KONG','code'=>'852'),
        'HN'=>array('name'=>'HONDURAS','code'=>'504'),
        'HR'=>array('name'=>'CROATIA','code'=>'385'),
        'HT'=>array('name'=>'HAITI','code'=>'509'),
        'HU'=>array('name'=>'HUNGARY','code'=>'36'),
        'ID'=>array('name'=>'INDONESIA','code'=>'62'),
        'IE'=>array('name'=>'IRELAND','code'=>'353'),
        'IL'=>array('name'=>'ISRAEL','code'=>'972'),
        'IM'=>array('name'=>'ISLE OF MAN','code'=>'44'),
        'IN'=>array('name'=>'INDIA','code'=>'91'),
        'IQ'=>array('name'=>'IRAQ','code'=>'964'),
        'IR'=>array('name'=>'IRAN, ISLAMIC REPUBLIC OF','code'=>'98'),
        'IS'=>array('name'=>'ICELAND','code'=>'354'),
        'IT'=>array('name'=>'ITALY','code'=>'39'),
        'JM'=>array('name'=>'JAMAICA','code'=>'1876'),
        'JO'=>array('name'=>'JORDAN','code'=>'962'),
        'JP'=>array('name'=>'JAPAN','code'=>'81'),
        'KE'=>array('name'=>'KENYA','code'=>'254'),
        'KG'=>array('name'=>'KYRGYZSTAN','code'=>'996'),
        'KH'=>array('name'=>'CAMBODIA','code'=>'855'),
        'KI'=>array('name'=>'KIRIBATI','code'=>'686'),
        'KM'=>array('name'=>'COMOROS','code'=>'269'),
        'KN'=>array('name'=>'SAINT KITTS AND NEVIS','code'=>'1869'),
        'KP'=>array('name'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF','code'=>'850'),
        'KR'=>array('name'=>'KOREA REPUBLIC OF','code'=>'82'),
        'KW'=>array('name'=>'KUWAIT','code'=>'965'),
        'KY'=>array('name'=>'CAYMAN ISLANDS','code'=>'1345'),
        'KZ'=>array('name'=>'KAZAKSTAN','code'=>'7'),
        'LA'=>array('name'=>'LAO PEOPLES DEMOCRATIC REPUBLIC','code'=>'856'),
        'LB'=>array('name'=>'LEBANON','code'=>'961'),
        'LC'=>array('name'=>'SAINT LUCIA','code'=>'1758'),
        'LI'=>array('name'=>'LIECHTENSTEIN','code'=>'423'),
        'LK'=>array('name'=>'SRI LANKA','code'=>'94'),
        'LR'=>array('name'=>'LIBERIA','code'=>'231'),
        'LS'=>array('name'=>'LESOTHO','code'=>'266'),
        'LT'=>array('name'=>'LITHUANIA','code'=>'370'),
        'LU'=>array('name'=>'LUXEMBOURG','code'=>'352'),
        'LV'=>array('name'=>'LATVIA','code'=>'371'),
        'LY'=>array('name'=>'LIBYAN ARAB JAMAHIRIYA','code'=>'218'),
        'MA'=>array('name'=>'MOROCCO','code'=>'212'),
        'MC'=>array('name'=>'MONACO','code'=>'377'),
        'MD'=>array('name'=>'MOLDOVA, REPUBLIC OF','code'=>'373'),
        'ME'=>array('name'=>'MONTENEGRO','code'=>'382'),
        'MF'=>array('name'=>'SAINT MARTIN','code'=>'1599'),
        'MG'=>array('name'=>'MADAGASCAR','code'=>'261'),
        'MH'=>array('name'=>'MARSHALL ISLANDS','code'=>'692'),
        'MK'=>array('name'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','code'=>'389'),
        'ML'=>array('name'=>'MALI','code'=>'223'),
        'MM'=>array('name'=>'MYANMAR','code'=>'95'),
        'MN'=>array('name'=>'MONGOLIA','code'=>'976'),
        'MO'=>array('name'=>'MACAU','code'=>'853'),
        'MP'=>array('name'=>'NORTHERN MARIANA ISLANDS','code'=>'1670'),
        'MR'=>array('name'=>'MAURITANIA','code'=>'222'),
        'MS'=>array('name'=>'MONTSERRAT','code'=>'1664'),
        'MT'=>array('name'=>'MALTA','code'=>'356'),
        'MU'=>array('name'=>'MAURITIUS','code'=>'230'),
        'MV'=>array('name'=>'MALDIVES','code'=>'960'),
        'MW'=>array('name'=>'MALAWI','code'=>'265'),
        'MX'=>array('name'=>'MEXICO','code'=>'52'),
        'MY'=>array('name'=>'MALAYSIA','code'=>'60'),
        'MZ'=>array('name'=>'MOZAMBIQUE','code'=>'258'),
        'NA'=>array('name'=>'NAMIBIA','code'=>'264'),
        'NC'=>array('name'=>'NEW CALEDONIA','code'=>'687'),
        'NE'=>array('name'=>'NIGER','code'=>'227'),
        'NG'=>array('name'=>'NIGERIA','code'=>'234'),
        'NI'=>array('name'=>'NICARAGUA','code'=>'505'),
        'NL'=>array('name'=>'NETHERLANDS','code'=>'31'),
        'NO'=>array('name'=>'NORWAY','code'=>'47'),
        'NP'=>array('name'=>'NEPAL','code'=>'977'),
        'NR'=>array('name'=>'NAURU','code'=>'674'),
        'NU'=>array('name'=>'NIUE','code'=>'683'),
        'NZ'=>array('name'=>'NEW ZEALAND','code'=>'64'),
        'OM'=>array('name'=>'OMAN','code'=>'968'),
        'PA'=>array('name'=>'PANAMA','code'=>'507'),
        'PE'=>array('name'=>'PERU','code'=>'51'),
        'PF'=>array('name'=>'FRENCH POLYNESIA','code'=>'689'),
        'PG'=>array('name'=>'PAPUA NEW GUINEA','code'=>'675'),
        'PH'=>array('name'=>'PHILIPPINES','code'=>'63'),
        'PK'=>array('name'=>'PAKISTAN','code'=>'92'),
        'PL'=>array('name'=>'POLAND','code'=>'48'),
        'PM'=>array('name'=>'SAINT PIERRE AND MIQUELON','code'=>'508'),
        'PN'=>array('name'=>'PITCAIRN','code'=>'870'),
        'PR'=>array('name'=>'PUERTO RICO','code'=>'1'),
        'PT'=>array('name'=>'PORTUGAL','code'=>'351'),
        'PW'=>array('name'=>'PALAU','code'=>'680'),
        'PY'=>array('name'=>'PARAGUAY','code'=>'595'),
        'QA'=>array('name'=>'QATAR','code'=>'974'),
        'RO'=>array('name'=>'ROMANIA','code'=>'40'),
        'RS'=>array('name'=>'SERBIA','code'=>'381'),
        'RU'=>array('name'=>'RUSSIAN FEDERATION','code'=>'7'),
        'RW'=>array('name'=>'RWANDA','code'=>'250'),
        'SA'=>array('name'=>'SAUDI ARABIA','code'=>'966'),
        'SB'=>array('name'=>'SOLOMON ISLANDS','code'=>'677'),
        'SC'=>array('name'=>'SEYCHELLES','code'=>'248'),
        'SD'=>array('name'=>'SUDAN','code'=>'249'),
        'SE'=>array('name'=>'SWEDEN','code'=>'46'),
        'SG'=>array('name'=>'SINGAPORE','code'=>'65'),
        'SH'=>array('name'=>'SAINT HELENA','code'=>'290'),
        'SI'=>array('name'=>'SLOVENIA','code'=>'386'),
        'SK'=>array('name'=>'SLOVAKIA','code'=>'421'),
        'SL'=>array('name'=>'SIERRA LEONE','code'=>'232'),
        'SM'=>array('name'=>'SAN MARINO','code'=>'378'),
        'SN'=>array('name'=>'SENEGAL','code'=>'221'),
        'SO'=>array('name'=>'SOMALIA','code'=>'252'),
        'SR'=>array('name'=>'SURINAME','code'=>'597'),
        'ST'=>array('name'=>'SAO TOME AND PRINCIPE','code'=>'239'),
        'SV'=>array('name'=>'EL SALVADOR','code'=>'503'),
        'SY'=>array('name'=>'SYRIAN ARAB REPUBLIC','code'=>'963'),
        'SZ'=>array('name'=>'SWAZILAND','code'=>'268'),
        'TC'=>array('name'=>'TURKS AND CAICOS ISLANDS','code'=>'1649'),
        'TD'=>array('name'=>'CHAD','code'=>'235'),
        'TG'=>array('name'=>'TOGO','code'=>'228'),
        'TH'=>array('name'=>'THAILAND','code'=>'66'),
        'TJ'=>array('name'=>'TAJIKISTAN','code'=>'992'),
        'TK'=>array('name'=>'TOKELAU','code'=>'690'),
        'TL'=>array('name'=>'TIMOR-LESTE','code'=>'670'),
        'TM'=>array('name'=>'TURKMENISTAN','code'=>'993'),
        'TN'=>array('name'=>'TUNISIA','code'=>'216'),
        'TO'=>array('name'=>'TONGA','code'=>'676'),
        'TR'=>array('name'=>'TURKEY','code'=>'90'),
        'TT'=>array('name'=>'TRINIDAD AND TOBAGO','code'=>'1868'),
        'TV'=>array('name'=>'TUVALU','code'=>'688'),
        'TW'=>array('name'=>'TAIWAN, PROVINCE OF CHINA','code'=>'886'),
        'TZ'=>array('name'=>'TANZANIA, UNITED REPUBLIC OF','code'=>'255'),
        'UA'=>array('name'=>'UKRAINE','code'=>'380'),
        'UG'=>array('name'=>'UGANDA','code'=>'256'),
        'US'=>array('name'=>'UNITED STATES','code'=>'1'),
        'UY'=>array('name'=>'URUGUAY','code'=>'598'),
        'UZ'=>array('name'=>'UZBEKISTAN','code'=>'998'),
        'VA'=>array('name'=>'HOLY SEE (VATICAN CITY STATE)','code'=>'39'),
        'VC'=>array('name'=>'SAINT VINCENT AND THE GRENADINES','code'=>'1784'),
        'VE'=>array('name'=>'VENEZUELA','code'=>'58'),
        'VG'=>array('name'=>'VIRGIN ISLANDS, BRITISH','code'=>'1284'),
        'VI'=>array('name'=>'VIRGIN ISLANDS, U.S.','code'=>'1340'),
        'VN'=>array('name'=>'VIET NAM','code'=>'84'),
        'VU'=>array('name'=>'VANUATU','code'=>'678'),
        'WF'=>array('name'=>'WALLIS AND FUTUNA','code'=>'681'),
        'WS'=>array('name'=>'SAMOA','code'=>'685'),
        'XK'=>array('name'=>'KOSOVO','code'=>'381'),
        'YE'=>array('name'=>'YEMEN','code'=>'967'),
        'YT'=>array('name'=>'MAYOTTE','code'=>'262'),
        'ZA'=>array('name'=>'SOUTH AFRICA','code'=>'27'),
        'ZM'=>array('name'=>'ZAMBIA','code'=>'260'),
        'ZW'=>array('name'=>'ZIMBABWE','code'=>'263')
    );

    public $dateLibelle = array(
        '01'=> 'JAN',
        '02'=> 'FEB',
        '03'=> 'MAR',
        '04'=> 'APR',
        '05'=> 'MAY',
        '06'=> 'JUN',
        '07'=> 'JUL',
        '08'=> 'AUG',
        '09'=> 'SEP',
        '10'=> 'OCT',
        '11'=> 'NOV',
        '12'=> 'DEC'
    );
}
