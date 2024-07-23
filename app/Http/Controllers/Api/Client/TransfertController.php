<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserClient;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailAlerte;
use Illuminate\Support\Facades\Validator;
use App\Models\TransfertOut;
use App\Models\ClientTransaction;
use App\Models\Recharge;
use App\Models\UserCard;
use App\Models\UserCardBuy;
use App\Services\PaiementService;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;


class TransfertController extends Controller
{
    public function __construct() {
        $this->middleware('is-auth', ['except' => ['addContact','createCompteClient', 'loginCompteClient', 'sendCode', 'checkCodeOtp', 'resetPassword','verificationPhone', 'verificationInfoPerso','verificationInfoPiece','saveFile','sendCodeTelephoneRegistration','getServices','sendCodeTelephone','callBackTransfer']]);
    }
    

    public function addNewTransfertClient(Request $request, PaiementService $paiementService){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');

            $validator = Validator::make($request->all(), [
                'name' => ["nullable" , "string"],
                'lastname' => ["nullable" , "string"],
                'montant' => ["required" , "integer"],
                'type' => ["required" , "max:255", "regex:(momo|bmo|card|bcv)"],
                'receveur_telephone' => ["nullable","string"],
                'user_id' => ["required" , "string"],
                'user_card_id' => ["required" , "string"],
                'last_digits' => ["nullable" , "string"],
                'customer_id' => ["nullable" , "string"],
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $sender =  UserClient::where('deleted',0)->where('id',$request->user_id)->first();
            $sender_card =  UserCard::where('deleted',0)->where('id',$request->user_card_id)->first();

            if($request->type == 'bcv'){
                $receiver =  UserClient::where('deleted',0)->where('username',$request->receveur_telephone)->first(); 
                if(!$receiver){
                    $message = ['success' => false, 'status' => 500,'message' => 'Ce compte n\'existe pas','timestamp' => Carbon::now(),'user_id' => $sender->id]; 
                    writeLog($message);
                    return sendError('Ce compte n\'existe pas', [], 500); 
                }else if($receiver->verification != 1){
                    $message = ['success' => false, 'status' => 500,'message' => 'Ce compte est inactif','timestamp' => Carbon::now(),'user_id' => $sender->id]; 
                    writeLog($message);
                    return sendError('Ce compte est inactif', [], 500); 
                }else if(!$receiver->userCard){
                    return sendError('Ce compte n\'a pas de carte liée', [], 500);
                }
            }
            
            $montant = $request->montant;            
            $fraisAndRepartition = getFeeAndRepartition('transfert', $montant);
            $frais = getFee($fraisAndRepartition,$montant);

            $soldeAvant = getCardSolde($sender_card); 
            $reference_memo_gtp = unaccent('    . Frais de transaction : ' . $frais . ' XOF.');
    
            $transfert = TransfertOut::create([
                'id' => Uuid::uuid4()->toString(),
                "user_client_id" => $sender->id,
                "user_card_id" => $sender_card->id,
                "montant" => $montant,
                "frais" => $frais,
                "moyen_paiement" => $request->type,
                "is_debited" => 0,
                "status" => 'pending',
                "deleted" => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $cardDebited = $paiementService->cardDebited($sender_card->customer_id, $sender_card->last_digits, $montant, $frais, $sender,$reference_memo_gtp);

            if($cardDebited == false){
                return sendError('Probleme lors du debit de la carte', [], 500);                    
            }else{
                $referenceGtp = $cardDebited->transactionId;                    
                $soldeApres = $soldeAvant - $montant - $frais;

                $transfert->is_debited = 1;
                $transfert->reference_gtp_debit = $referenceGtp;
                $transfert->save();

                if($request->type == 'card'){
                    $reference_memo_gtp = unaccent("Transfert de " . $montant . " XOF de la carte " . decryptData((string)$sender_card->customer_id, $encrypt_Key) . ". Frais du transfert : " . $frais . " XOF");
                    $cardCredited = $paiementService->cardCredited($request->customer_id, $request->last_digits, $montant, $sender, $reference_memo_gtp);

                    if($cardCredited == false){
                        return sendError('Probleme lors du credit de la carte', [], 500);                    
                    }else{
                        $referenceGtpCredit = $cardCredited->transactionId;
                        $transfert->receveur_customer_id = $request->customer_id;
                        $transfert->receveur_last_digits = $request->last_digits;
                        $transfert->libelle = 'Transfert de '.$montant.' vers la carte '.decryptData($request->last_digits, $encrypt_Key);
                        $transfert->reference_gtp_credit = $referenceGtpCredit;
                        $transfert->solde_avant = $soldeAvant;
                        $transfert->solde_apres = $soldeApres;
                        $transfert->status = 'completed';
                        $transfert->is_credited = 1;
                        $transfert->montant_recu = $montant - $frais;
                        $transfert->save();

                        $paiementService->repartitionCommission($fraisAndRepartition,$frais,$montant,$referenceGtp, 'transfert');
                        $message = getSms('transfert', null, $montant, 0, $soldeApres, ' vers la carte '.$request->customer_id, null);  
                        if($sender->sms == 1){
                            sendSms($sender->username,$message);
                        }else{
                            try{
                                $email = $sender->kycClient->email;
                                $arr = ['messages'=> $message,'objet'=>'Alerte transfert','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $transfert->userClient->id];  
                                writeLog($message);
                            }
                        }

                        return sendResponse($transfert, 'Transfert effectué avec succes. Consulter votre solde');
                    }
                }else if($request->type == 'bcv'){
                    $receiverFirstCard =  $receiver->userCard->first();

                    $reference_memo_gtp = unaccent('Transfert de ' . $montant . ' XOF vers ' . decryptData($receiverFirstCard->customer_id, $encrypt_Key) . ' de ' . $receiver->lastname . ' ' . $receiver->name . '. Frais du transfert : ' . $frais . " XOF");
                    $bcvCredited = $paiementService->cardCredited($receiverFirstCard->customer_id, $receiverFirstCard->last_digits, $montant, $sender, $reference_memo_gtp);

                    if($bcvCredited == false){
                        return sendError('Probleme lors du credit de la carte', [], 500);                    
                    }else{
                        $transfert->name = $request->name;
                        $transfert->lastname = $request->lastname;
                        $transfert->receveur_id = $receiver->id;
                        $transfert->receveur_card_id = $receiverFirstCard->id;
                        $transfert->libelle = 'Transfert de '.$montant.' vers la carte '.decryptData($receiverFirstCard->customer_id, $encrypt_Key).'.';
                        $transfert->reference_gtp_credit = $bcvCredited->transactionId;
                        $transfert->solde_avant = $soldeAvant;
                        $transfert->solde_apres = $soldeApres;
                        $transfert->is_credited = 1;
                        $transfert->montant_recu = $montant - $frais;
                        $transfert->status = 'completed';
                        $transfert->save();
                        
                        $message = getSms('transfert', null, $montant, 0, $soldeApres, ' vers le compte BCC +'.$receiver->username, null);

                        if($sender->sms == 1){
                            sendSms($sender->username,$message);
                        }else{
                            try{
                                $email = $sender->kycClient->email;
                                $arr = ['messages'=> $message,'objet'=>'Alerte transfert','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $transfert->userClient->id];  
                                writeLog($message);
                            }
                        }

                        $message = ['success' => true, 'status' => 200,'message' => 'Transfert effectué avec succes','timestamp' => Carbon::now(),'user' => $transfert->userClient->id]; 
                        writeLog($message); 
                        $paiementService->repartitionCommission($fraisAndRepartition,$frais,$montant,$referenceGtp, 'transfert');
                        return sendResponse($transfert, 'Transfert effectué avec succes. Consulter votre solde');
                    }
                }else if($request->type == 'bmo'){
                    $bmoCredited = $paiementService->bmoCredited('+'.$request->receveur_telephone,  $request->name, $request->lastname, $montant, $sender);

                    if($bmoCredited == false){
                        return sendError('Probleme lors du credit du compt BMO', [], 500);                    
                    }else{
                        $transfert->name = $request->name;
                        $transfert->lastname = $request->lastname;   
                        $transfert->libelle = 'Transfert BMO de '.$request->montant.' vers le numero '.$request->receveur_telephone.'.';                       
                        $transfert->receveur_telephone = $request->receveur_telephone;
                        $transfert->reference_operateur = $bmoCredited->reference;
                        $transfert->solde_avant = $soldeAvant;
                        $transfert->solde_apres = $soldeApres;
                        $transfert->status = 'completed';
                        $transfert->is_credited = 1;
                        $transfert->montant_recu = $montant - $frais;
                        $transfert->save();
                            
                        $message = getSms('transfert', null, $montant, 0, $soldeApres, ' vers le compte BMO +'.$request->receveur_telephone, null);   
                        if($sender->sms == 1){
                            sendSms($sender->username,$message);
                        }else{
                            try{
                                $email = $sender->kycClient->email;
                                $arr = ['messages'=> $message,'objet'=>'Alerte transfert','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $transfert->userClient->id];  
                                writeLog($message);
                            }
                        }

                        $message = ['success' => true, 'status' => 200,'message' => 'Transfert effectué avec succes','timestamp' => Carbon::now(),'user' => $transfert->userClient->id]; 
                        writeLog($message); 

                        $paiementService->repartitionCommission($fraisAndRepartition,$frais,$montant,$referenceGtp, 'transfert');
                        return sendResponse($transfert, 'Transfert effectué avec succes. Consulter votre solde');
                    }
                }else{   
                   $transfert->solde_avant = $soldeAvant;
                   $transfert->solde_apres = $soldeApres;
                   $transfert->montant_recu = $montant - $frais;
                   $transfert->name = $request->name;
                   $transfert->lastname = $request->lastname;    
                   $transfert->libelle = 'Transfert Momo de '.$request->montant.' vers le numero '.$request->receveur_telephone.'.';   
                   $transfert->receveur_telephone = $request->receveur_telephone;
                   $transfert->save();                
                   $paiementService->momoCredited($request->receveur_telephone, $montant, $transfert->id); 

                    return sendResponse($transfert, 'Paiement effectué.. Votre transfert est en cours...');
                }
            }
        }catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function completeTransfertClient(Request $request, PaiementService $paiementService){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');

            $validator = Validator::make($request->all(), [
                'transaction_id' => ["required" , "string"],
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $transfert = TransfertOut::where('id',$request->transaction_id)->first();

            $sender =  UserClient::where('deleted',0)->where('id',$transfert->user_client_id)->first();
            $sender_card =  UserCard::where('deleted',0)->where('id',$request->user_card_id)->first();
            
            $montant = $transfert->montant;
            $soldeAvant = getCardSolde($sender_card); 

            $fraisAndRepartition = getFeeAndRepartition('transfert', $montant);
            $frais = getFee($fraisAndRepartition,$montant);            
            $soldeApres = $soldeAvant - $montant - $frais;
                          

            if($request->type == 'card'){
                
                $reference_memo_gtp = unaccent("Transfert de " . $montant . " XOF de la carte " . decryptData((string)$sender_card->customer_id, $encrypt_Key) . ". Frais du transfert : " . $frais . " XOF");
                $cardCredited = $paiementService->cardCredited($request->customer_id, $request->last_digits, $montant, $sender, $reference_memo_gtp);

                if($cardCredited == false){
                    return sendError('Probleme lors du credit de la carte', [], 500);                    
                }else{
                    $referenceGtpCredit = $cardCredited->transactionId;
                    $transfert->receveur_customer_id = $request->customer_id;
                    $transfert->receveur_last_digits = $request->last_digits;
                    $transfert->libelle = 'Transfert de '.$montant.' vers la carte '.decryptData($request->last_digits, $encrypt_Key);
                    $transfert->reference_gtp_credit = $referenceGtpCredit;
                    $transfert->solde_avant = $soldeAvant;
                    $transfert->solde_apres = $soldeApres;
                    $transfert->status = 'completed';
                    $transfert->is_credited = 1;
                    $transfert->montant_recu = $montant - $frais;
                    $transfert->save();

                    $message = getSms('transfert', null, $montant, 0, $soldeApres, ' vers la carte '.$request->customer_id, null);   
                    if($sender->sms == 1){
                        sendSms($sender->username,$message);
                    }else{
                            try{
                            $email = $sender->kycClient->email;
                            $arr = ['messages'=> $message,'objet'=>'Alerte transfert','from'=>'bmo-uba-noreply@bestcash.me'];
                            Mail::to([$email,])->send(new MailAlerte($arr));
                        } catch (\Exception $e) {
                            $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $transfert->userClient->id];  
                            writeLog($message);
                        }
                    }

                    return sendResponse($transfert, 'Transfert effectué avec succes. Consulter votre solde');
                }
            }else{
                if($request->type == 'bcv'){
                    $receiver =  UserClient::where('deleted',0)->where('username',$request->receveur_telephone)->first();                            
                    $receiverFirstCard =  $receiver->userCard->first();

                    $reference_memo_gtp = unaccent('Transfert de ' . $montant . ' XOF vers la carte ' . decryptData($receiverFirstCard->customer_id, $encrypt_Key) . ' de ' . $receiver->username . '. Frais de transfert : ' . $frais . " XOF");
                    $bcvCredited = $paiementService->cardCredited($receiverFirstCard->customer_id, $receiverFirstCard->last_digits, $montant, $sender, $reference_memo_gtp);

                    if($bcvCredited == false){
                        return sendError('Probleme lors du credit de la carte', [], 500);                    
                    }else{
                        $transfert->name = $request->name;
                        $transfert->lastname = $request->lastname;
                        $transfert->receveur_id = $receiver->id;
                        $transfert->receveur_card_id = $receiverFirstCard->id;
                        $transfert->libelle = 'Transfert de '.$montant.' vers la carte '.decryptData($receiverFirstCard->customer_id, $encrypt_Key).'.';
                        $transfert->reference_gtp_credit = $bcvCredited->transactionId;
                        $transfert->solde_avant = $soldeAvant;
                        $transfert->solde_apres = $soldeApres;
                        $transfert->is_credited = 1;
                        $transfert->montant_recu = $montant - $frais;
                        $transfert->status = 'completed';
                        $transfert->save();
                            
                        $message = getSms('transfert', null, $montant, 0, $soldeApres, ' vers le compte BCC +'.$receiver->username, null);
                        if($sender->sms == 1){
                            sendSms($sender->username,$message);
                        }else{
                            try{
                                $email = $sender->kycClient->email;
                                $arr = ['messages'=> $message,'objet'=>'Alerte transfert','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $transfert->userClient->id];  
                                writeLog($message);
                            }
                        }

                        $message = ['success' => true, 'status' => 200,'message' => 'Transfert effectué avec succes','timestamp' => Carbon::now(),'user' => $transfert->userClient->id]; 
                        writeLog($message); 
                    }
                }else if($request->type == 'bmo'){ 
                    $bmoCredited = $paiementService->bmoCredited('+'.$request->receveur_telephone,  $request->name, $request->lastname, $montant, $sender);

                    if($bmoCredited == false){
                        return sendError('Probleme lors du credit du compte BMO', [], 500);                    
                    }else{
                        $transfert->name = $request->name;
                        $transfert->lastname = $request->lastname;   
                        $transfert->libelle = 'Transfert BMO de '.$request->montant.' vers le numero '.$request->receveur_telephone.'.';                       
                        $transfert->receveur_telephone = $request->receveur_telephone;
                        $transfert->reference_operateur = $bmoCredited->reference;
                        $transfert->solde_avant = $soldeAvant;
                        $transfert->solde_apres = $soldeApres;
                        $transfert->status = 'completed';
                        $transfert->is_credited = 1;
                        $transfert->montant_recu = $montant - $frais;
                        $transfert->save();
                            
                        $message = getSms('transfert', null, $montant, 0, $soldeApres, ' vers le compte BMO +'.$request->receveur_telephone, null);      
                        if($sender->sms == 1){
                            sendSms($sender->username,$message);
                        }else{
                            try{
                                $email = $sender->kycClient->email;
                                $arr = ['messages'=> $message,'objet'=>'Alerte transfert','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $transfert->userClient->id];  
                                writeLog($message);
                            }
                        }

                        $message = ['success' => true, 'status' => 200,'message' => 'Transfert effectué avec succes','timestamp' => Carbon::now(),'user' => $transfert->userClient->id]; 
                        writeLog($message); 
                    }
                }else{
                    $momoCredited = $paiementService->momoCredited($request->receveur_telephone, $montant, $transfert->userClient->id);

                    if($momoCredited == "FAILED"){
                        $transfert->status = 'failed';
                        $transfert->save();
                        return sendError('Echec lors du remboursement de la transaction', [], 500); 
                    }else if($momoCredited == "FAILED_TIME"){
                        $transfert->status = 'failed';
                        $transfert->save();
                        return sendError('Echec du a un temps d\'attente trop long', [], 500);
                    }else{
                        $transfert->name = $request->name;
                        $transfert->lastname = $request->lastname;    
                        $transfert->libelle = 'Transfert Momo de '.$request->montant.' vers le numero '.$request->receveur_telephone.'.';   
                        $transfert->receveur_telephone = $request->receveur_telephone;
                        $transfert->reference_operateur = $momoCredited->transactionId;
                        $transfert->solde_avant = $soldeAvant;
                        $transfert->solde_apres = $soldeApres;
                        $transfert->status = 'completed';
                        $transfert->is_credited = 1;
                        $transfert->montant_recu = $montant - $frais;
                        $transfert->save();
                            
                        $message = getSms('transfert', null, $montant, 0, $soldeApres, ' vers le compte Momo +'.$request->receveur_telephone, null);     
                        if($sender->sms == 1){
                            sendSms($sender->username,$message);
                        }else{
                            try{
                                $email = $sender->kycClient->email;
                                $arr = ['messages'=> $message,'objet'=>'Alerte transfert','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $transfert->userClient->id];  
                                writeLog($message);
                            }
                        }

                        $message = ['success' => true, 'status' => 200,'message' => 'Transfert effectué avec succes','timestamp' => Carbon::now(),'user' => $transfert->userClient->id]; 
                        writeLog($message); 
                    }
                }
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function callBackTransfer(Request $request, PaiementService $paiementService)
    {
        $payload = $request->getContent();
        $headers = $request->headers->all()["x-kkiapay-secret"][0];
        
        Log::info($headers);
        if($headers == env("WEBHOOK_HASH")){
            $data = json_decode($payload, true);
    
            Log::info($payload);
            if (!array_key_exists("stateData",$data)){
                $transfert = TransfertOut::where('id',$data['partnerId'])->first();  
                if($transfert->status == "pending"){
                    if($data['isPaymentSucces'] == true){
                        $transfert->is_credited = 1;
                        $transfert->status = 'completed';
                        $transfert->save();
                        Log::info("succest");
                        return sendResponse($transfert, 'Transfert enregistre');
                    }else{
                        $transfert->status = 'failed';
                        $transfert->save();
                        Log::info("Echec de transfert de reference ".$data['transactionId']);
                    }
                }
            }else{
                if($data["stateData"]["transactionType"] == "load"){   
                    $recharge = Recharge::where('id',$data["stateData"]["internalTransactionId"])->first();   
                    
                    if($recharge->status == "pending"){
                        if($data['isPaymentSucces'] == true){        
                            $recharge->is_debited = 1;
                            $recharge->status = "on_going_payment";
                            $recharge->save();
                            Log::info("succesd");
                            return sendResponse($recharge, 'Rechargement enregistre');
                        }else{
                            $recharge->status = 'failed';
                            $recharge->save();
                            Log::info("Echec de recharge de reference ".$data['transactionId']);
                        }
                    }
                    
                }else{
                    $userCardBuy = UserCardBuy::where('id',$data["stateData"]["internalTransactionId"])->first();
                    
                    if($userCardBuy->status == "pending"){
                        if($data['isPaymentSucces'] == true){
                            $userCardBuy->is_debited = 1;
                            $userCardBuy->status = "on_going_payment";
                            $userCardBuy->save();
                            Log::info("succesa");
                            return sendResponse($userCardBuy, 'Achat enregistre');
                        }else{
                            $userCardBuy->status = 'failed';
                            $userCardBuy->save();
                            Log::info("Echec d'achat de reference ".$data['transactionId']);
                        }
                    }
                }
    
            }
        }
    }
}
