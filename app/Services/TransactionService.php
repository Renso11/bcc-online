<?php

namespace App\Services;

use App\Models\Depot;
use App\Models\PartnerCession;
use App\Models\PartnerWalletDeposit;
use App\Models\PartnerWalletWithdraw;
use App\Models\Recharge;
use App\Models\Retrait;
use App\Models\TransfertOut;
use App\Models\UserCardBuy;
use Illuminate\Support\Facades\DB;
use App\Services\PaiementService;

class TransactionService{

    public function confirmCardPurchase($transactionId, $cardType){
        try{
            $encrypt_Key = env('ENCRYPT_KEY');
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            
            $userCardBuy = UserCardBuy::where('id',$transactionId)->first();
            $userClient = UserClient::where('id',$userCardBuy->user_client_id)->first();

            
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
                    'type' => 'virtuelle',
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
                
                $message = 'Felicitations! Votre achat de carte virtuelle Bcc est finalisé. Les informations suivantes sont celles de votre carte : Customer ID: '. $responseBody->registrationAccountId.', Quatre dernier Chiffre :'. $responseBody->registrationLast4Digits.', Registration pass code :'.$responseBody->registrationPassCode.'.';
                sendSms($userClient->username,$message);
                try{
                    Mail::to([$userClient->kycClient->email,])->send(new MailVenteVirtuelle(['registrationAccountId' => $responseBody->registrationAccountId,'registrationLast4Digits' => $responseBody->registrationLast4Digits,'registrationPassCode' => $responseBody->registrationPassCode,'type' => 'virtuelle'])); 
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
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function dismissCardPurchase($transactionId){
        try{          
            $userCardBuy = UserCardBuy::where('id',$transactionId)->first();

            $userCardBuy->reasons = "Echec de paiement de la transaction";
            $userCardBuy->status = 'failed';
            $userCardBuy->save();
            return sendResponse($userCardBuy, 'Echec du paiement');

        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }    

    public function confirmTransfert($transfert_id){
        try{      
            $transfert = TransfertOut::where('id',$transfert_id)->first();
            $transfert->status = 'completed';
            $transfert->is_credited = 1;
            $transfert->save();
            return true;
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function dismissTransfert(Request $request){
        try{      
            $transfert = TransfertOut::where('id',$transfert_id)->first();
            $transfert->status = 'failed';
            $transfert->is_credited = 0;
            $transfert->save();
            return true;
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }
    

    public function confirmDeposit($transactionId, $montant, PaiementService $paiementService){
        try{       
            $recharge = Recharge::where('id',$transactionId)->first();
            $recharge->is_debited = 1;
            $recharge->save();

            $fraisAndRepartition = getFeeAndRepartition('rechargement', $montant);
            $frais = getFee($fraisAndRepartition,$montant); 
            $montantWithoutFee = $montant - $frais;

            $soldeAvantDepot = getCardSolde($card);

            $reference_memo_gtp = unaccent("Rechargement de " . $montantWithoutFee . " XOF sur votre carte. Frais de rechargement : " . $frais . " XOF");
            $cardCredited = $paiementService->cardCredited($card->customer_id, $card->last_digits, $montantWithoutFee, $user, $reference_memo_gtp);

            if($cardCredited == false){
                return sendError('Probleme lors du credit de la carte', [], 500);                    
            }else{
                $referenceGtp = $cardCredited->transactionId;                    
                $soldeApresDepot = $soldeAvantDepot + $montantWithoutFee;
                
                $recharge->reference_gtp = $referenceGtp;
                $recharge->frais = $frais;
                $recharge->montant_recu = $montantWithoutFee;
                $recharge->status =  'completed';
                $recharge->is_credited =  1;
                $recharge->solde_avant = $soldeAvantDepot;
                $recharge->solde_apres = $soldeApresDepot;
                $recharge->save();                    
                
                $message = getSms('rechargement', null, $montant, $frais, $soldeApresDepot, null, null);
                
                if($user->sms == 1){
                    sendSms($user->username,$message);
                }else{
                    try{
                        $arr = ['messages'=> $message,'objet'=>'Confirmation du rechargement','from'=>'bmo-uba-noreply@bestcash.me'];
                        Mail::to([$user->kycClient->email,])->send(new MailAlerte($arr)); 
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $user->id];  
                        writeLog($message);
                    }
                }
                
                $paiementService->repartitionCommission($fraisAndRepartition,$frais,$montant,$referenceGtp, 'rechargement');
                return sendResponse($recharge, 'Rechargement effectué avec succes. Consulter votre solde');
            }


            $recharge = Recharge::where('id',$transactionId)->first();
            $card = UserCard::where('id',$recharge->user_card_id)->first();

            $fraisAndRepartition = getFeeAndRepartition('rechargement', $montant);
            $frais = getFee($fraisAndRepartition,$montant); 
            $montantWithoutFee = $montant - $frais;

            $soldeAvantDepot = getCardSolde($card);

            $reference_memo_gtp = unaccent("Rechargement de " . $montantWithoutFee . " XOF sur votre carte. Frais de rechargement : " . $frais . " XOF");
            $cardCredited = $paiementService->cardCredited($card->customer_id, $card->last_digits, $montantWithoutFee, $user, $reference_memo_gtp);

            if($cardCredited == false){
                return sendError('Probleme lors du credit de la carte', [], 500);                    
            }else{
                $referenceGtp = $cardCredited->transactionId;                    
                $soldeApresDepot = $soldeAvantDepot + $montantWithoutFee;
                
                $recharge->reference_gtp = $referenceGtp;
                $recharge->frais = $frais;
                $recharge->montant_recu = $montantWithoutFee;
                $recharge->status =  'completed';
                $recharge->is_credited =  1;
                $recharge->solde_avant = $soldeAvantDepot;
                $recharge->solde_apres = $soldeApresDepot;
                $recharge->save();                    
                
                $message = getSms('rechargement', null, $montant, $frais, $soldeApresDepot, null, null);
                
                if($user->sms == 1){
                    sendSms($user->username,$message);
                }else{
                    try{
                        $arr = ['messages'=> $message,'objet'=>'Confirmation du rechargement','from'=>'bmo-uba-noreply@bestcash.me'];
                        Mail::to([$user->kycClient->email,])->send(new MailAlerte($arr)); 
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $user->id];  
                        writeLog($message);
                    }
                }
                
                $paiementService->repartitionCommission($fraisAndRepartition,$frais,$montant,$referenceGtp, 'rechargement');
                return sendResponse($recharge, 'Rechargement effectué avec succes. Consulter votre solde');
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function dismissDeposit($transactionId){
        try{       
            $recharge = Recharge::where('id',$transactionId)->first();
            $recharge->reasons = "Echec de rechargement de la carte";
            $recharge->status = 'failed';
            $recharge->save();
            return sendError('Probleme lors de la verification du paiement', [], 500);
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }
}