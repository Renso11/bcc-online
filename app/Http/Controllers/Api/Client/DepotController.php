<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserClient;
use App\Models\Recharge;
use App\Models\ClientTransaction;
use Illuminate\Support\Carbon;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailAlerte;
use Illuminate\Support\Facades\Validator;
use App\Models\UserCard;
use App\Services\PaiementService;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Log;

class DepotController extends Controller
{
    public function __construct() {
        $this->middleware('is-auth', ['except' => ['callBackCardLoad']]);
    }

    public function addNewDepotClient(Request $request, PaiementService $paiementService){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => ["required" , "string"],
                'user_card_id' => 'required',
                'montant' => ["required" , "integer"],
                'moyen_paiement' => ["required" , "max:255", "regex:(bmo|momo|flooz|card)"],
            ]);

            if ($validator->fails()){
                return  sendError($validator->errors()->first(), [],422);
            }

            $user = UserClient::where('id',$request->user_id)->first();
            $card = UserCard::where('id',$request->user_card_id)->first();
            
            $recharge = Recharge::create([
                'id' => Uuid::uuid4()->toString(),
                'user_client_id' => $request->user_id,
                'user_card_id' => $request->user_card_id,
                'montant' => $request->montant,
                'reference_operateur' => $request->reference,
                'moyen_paiement' => $request->moyen_paiement,
                'status' => 'pending',
                'is_debited' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $moyen_paiement = $request->moyen_paiement;
            $reference = $request->reference;
            $montant = $request->montant;

            // Si le moyen de paiement n'est pas kkp on le verifie sinn on return un message en attente
            
            if ($moyen_paiement == 'bmo' || $moyen_paiement == 'bcc') {
                $checkPaiement = $paiementService->paymentVerification($moyen_paiement, $reference, $montant, $user->id);
                
                if($checkPaiement == true){
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
                }else{
                    $recharge->reasons = $checkPaiement;
                    $recharge->status = 'failed';
                    $recharge->save();
                    return sendError('Probleme lors de la verification du paiement', [], 500);
                }
            } else {
                return sendResponse($recharge, 'Paiement effectué. Votre rechargement est en cours de verification');
            }

        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function completeDepotClient(Request $request, PaiementService $paiementService){
        try {
            $validator = Validator::make($request->all(), [
                'transaction_id' => ["required" , "string"]
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }
            
            $recharge = Recharge::where('id',$request->transaction_id)->first();

            $user = UserClient::where('id',$recharge->user_client_id)->first();
            $card = UserCard::where('id',$recharge->user_card_id)->first();
            
            $moyen_paiement =  $recharge->moyen_paiement;
            $reference = $recharge->reference_operateur;
            $montant = $recharge->montant;
            
            if($recharge->is_debited == 0){
                $checkPaiement = $paiementService->paymentVerification($moyen_paiement, $reference, $montant, $user->id);
                if($checkPaiement == true){
                    $recharge->is_debited = 1;
                    $recharge->save();
    
                    $fraisAndRepartition = getFeeAndRepartition('rechargement', $montant);
                    $frais = getFee($fraisAndRepartition,$montant);
                    $montantWithoutFee = $montant - $frais;
    
                    $soldeAvantDepot = getCardSolde($card);
    
                    if($recharge->is_credited == 0){
                        try {
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
                                return sendResponse($recharge, 'Rechargement complété avec succes. Consulter votre solde');
                            }
                        } catch (BadResponseException $e) {        
                            $json = json_decode($e->getResponse()->getBody()->getContents());
                            $error = $json->title.'.'.$json->detail;
                            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                            writeLog($message);
                            return sendError($error, [], 500);
                        }
                    }else{
                        return sendError('Cette transaction à été finalisé. Contactez le service clientele pour plus d\'informations', [], 500);
                    }
    
                }else{
                    $recharge->reasons = $checkPaiement;
                    $recharge->status = 'failed';
                    $recharge->save();
                    return sendError('Probleme lors de la verification du paiement', [], 500);
                }
            }

            return sendResponse($recharge, 'Rechargement effectué avec succes. Consulter votre solde');
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }        
    
    public function callBackCardLoad(Request $request, PaiementService $paiementService){
        $payload = $request->getContent();
        
        $data = json_decode($payload, true);
        
        $recharge = Recharge::where('id',$request->id)->first();

        $user = UserClient::where('id',$recharge->user_client_id)->first();
        $card = UserCard::where('id',$recharge->user_card_id)->first();
        
        $montant = $recharge->montant;
        
        $recharge->is_debited = 1;
        $recharge->save();

        $fraisAndRepartition = getFeeAndRepartition('rechargement', $montant);
        $frais = getFee($fraisAndRepartition,$montant);
        $montantWithoutFee = $montant - $frais;

        $soldeAvantDepot = getCardSolde($card);

        try {
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
                return sendResponse($recharge, 'Rechargement complété avec succes. Consulter votre solde');
            }
        } catch (BadResponseException $e) {        
            $json = json_decode($e->getResponse()->getBody()->getContents());
            $error = $json->title.'.'.$json->detail;
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($error, [], 500);
        }
    }  
}
