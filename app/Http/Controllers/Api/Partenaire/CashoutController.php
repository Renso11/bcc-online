<?php

namespace App\Http\Controllers\Api\Partenaire;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserClient;
use App\Models\UserPartenaire;
use App\Models\PartnerWallet;
use App\Models\AccountCommission;
use App\Models\AccountCommissionOperation;
use App\Models\AccountDistribution;
use App\Models\PartnerCession;
use App\Models\AccountDistributionOperation;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\MailAlerte;
use App\Models\Partenaire;
use App\Models\PartnerWalletWithdraw;
use App\Services\PaiementService;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class CashoutController extends Controller
{  
    public function getPartnerWallets(Request $request){
        try {
            $wallets = PartnerWallet::where('partenaire_id',$request->partnerId)->where('deleted',0)->get();
            return sendResponse($wallets, 'Success');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function addPartnerWallet(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'phone_code' => 'nullable|string',
                'phone' => 'nullable|string',
                'customer_id' => 'nullable|string',
                'last_digits' => 'nullable|string',
                'user_partenaire_id' => 'required|string',
                'type' => 'required|in:bmo,momo,flooz,orange,tmoney,celtiis,free'
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            $partenaire = $userPartenaire->partenaire;

            $wallet = PartnerWallet::create([
                'id' => Uuid::uuid4()->toString(),
                "type" => $request->walletType,
                "phone" => $request->phone,
                "phone_code" => $request->phone_code,
                "customer_id" => $request->customer_id,
                "last_digits" => $request->last_digits,
                "partenaire_id" => $partenaire->id,
                "deleted" => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return sendResponse($wallet, 'Success');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function updatePartnerWallet(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'phone_code' => ["nullable" , "string"],
                'phone' => ["nullable" , "string"],
                'customer_id' => ["nullable" , "string"],
                'last_digits' => ["nullable" , "string"],
                'user_partenaire_id' => ["required" , "string"]
            ]);
            
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $wallet = PartnerWallet::where('id',$request->walletId)->first();

            if(!$wallet){
                return sendError('Portefeuille non trouvé', [], 401);
            }

            $wallet->phone_code = $request->phone_code;
            $wallet->phone = $request->phone;
            $wallet->customer_id = $request->customer_id;
            $wallet->last_digits = $request->last_digits;
            $wallet->save();

            return sendResponse($wallet, 'Success');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function deletePartnerWallet(Request $request){
        try {
            $wallet = PartnerWallet::where('id',$request->walletId)->first();

            if(!$wallet){
                return sendError('Portefeuille non trouvé', [], 401);
            }

            $wallet->deleted = 1;
            $wallet->save();

            return sendResponse($wallet, 'Success');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function withdrawPartnerToWallet(Request $request, PaiementService $paiementService){  
        try {
            $encrypt_Key = env('ENCRYPT_KEY');

            $validator = Validator::make($request->all(), [
                'montant' => 'required|int',
                'user_partenaire_id' => 'required|string',
                'wallet_id' => 'required|string',
            ]);

            if ($validator->fails()){
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }

            $wallet = PartnerWallet::where('id',$request->wallet_id)->first();
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            $partner = $userPartenaire->partenaire;
            $montant = $request->montant;

            $commissionAccount = AccountCommission::where('partenaire_id',$partner->id)->first();
            $soldeAvRetrait = $commissionAccount->solde;
            $soldeApRetrait = $soldeAvRetrait - $montant;
            
            $retrait = PartnerWalletWithdraw::create([
                'id' => Uuid::uuid4()->toString(),
                'montant' => $request->montant,
                'partenaire_id'=> $partner->id,
                'wallet_id' => $wallet->id,
                'status' => 'pending',
                'solde_avant' => $soldeAvRetrait,
                'solde_apres' => $soldeApRetrait,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),    
            ]);

            $commissionAccount->solde -= $montant;
            $commissionAccount->save();

            AccountCommissionOperation::create([
                'id' => Uuid::uuid4()->toString(),
                'solde_avant' => $soldeAvRetrait,
                'montant' => $montant,
                'solde_apres' => $soldeApRetrait,
                'libelle' => 'Retrait de '.$montant.' XOF de votre compte de commission BCV.',
                'type' => 'debit',
                'account_commission_id' => $commissionAccount->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),            
            ]);
            
            $retrait->is_debited = 1;
            $retrait->save();

            
            if($wallet->type == 'card'){
                $libelle = 'la carte '.decryptData($wallet->customer_id, $encrypt_Key).', ****'.decryptData($wallet->last_digits, $encrypt_Key);
                $reference_memo_gtp = unaccent('Cashout vers '.$libelle);
                $cardCredited = $paiementService->cardCredited($wallet->customer_id, $wallet->last_digits, $montant, $userPartenaire, $reference_memo_gtp);

                if($cardCredited == false){
                    return sendError('Probleme lors du credit de la carte', [], 500);                    
                }else{
                    $referenceGtpCredit = $cardCredited->transactionId;
                    $soldeApRetrait = $soldeAvRetrait - $montant;

                    $retrait->reference_gtp_credit = $referenceGtpCredit;
                    $retrait->libelle = 'Cashout vers '.$libelle;
                    $retrait->status = 'completed';
                    $retrait->save();

                    $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                    sendSms($partner->telephone,$message);

                    $email = $partner->email;
                    
                    try{
                        $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                        Mail::to([$email,])->send(new MailAlerte($arr));
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                        writeLog($message);
                    }

                    return sendResponse($retrait, 'Cashout effectué avec succes');
                }
            }else {
                if($wallet->type == 'bcv'){
                    $libelle = 'le compte BCV +'.$wallet->phone_code.$wallet->phone;
                    $receiver =  UserClient::where('deleted',0)->where('username',$wallet->phone_code.$wallet->phone)->first(); 
                    if(!$receiver){
                        $message = ['success' => false, 'status' => 500,'message' => 'Ce compte n\'existe pas','timestamp' => Carbon::now(),'user_id' => $userPartenaire->id]; 
                        writeLog($message);
                        return sendError('Ce compte n\'existe pas', [], 500); 
                    }
                    $receiverFirstCard =  $receiver->userCard->first();

                    $reference_memo_gtp = unaccent('Cashout de ' . $montant . ' XOF du partenaire '.$partner->libelle.' vers le compte BCV +' . $receiver->username);
                    $bcvCredited = $paiementService->cardCredited($receiverFirstCard->customer_id, $receiverFirstCard->last_digits, $montant, $userPartenaire, $reference_memo_gtp);

                    if($bcvCredited == false){
                        return sendError('Probleme lors du compte BCV', [], 500);                    
                    }else{
                        $referenceGtpCredit = $bcvCredited->transactionId;
                        $retrait->reference_gtp_credit = $referenceGtpCredit;
                        $retrait->libelle = 'Cashout vers '.$libelle;
                        $retrait->status = 'completed';
                        $retrait->save();
                            
                        $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                        sendSms($partner->telephone,$message);

                        $email = $partner->email;
                        try{
                            $arr = ['messages'=> $message,'objet'=> 'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                            Mail::to([$email,])->send(new MailAlerte($arr));
                        } catch (\Exception $e) {
                            $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                            writeLog($message);
                        }
                    }
                }else if($wallet->type == 'bmo'){ 
                    $libelle = 'le compte '.$wallet->type.' '.$wallet->phone_code.$wallet->phone ;                       
                    try{  
                        $bmoCredited = $paiementService->bmoCredited('+'.$wallet->phone_code.$wallet->phone,  'Cashout', $partner->libelle, $montant, $userPartenaire);
    
                        if($bmoCredited == false){
                            return sendError('Probleme lors du credit du compte BMO', [], 500);                    
                        }else{
                            $retrait->reference = $bmoCredited->reference;
                            $retrait->libelle = 'Cashout vers '.$libelle;
                            $retrait->status = 'completed';
                            $retrait->save();
                                
                            $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                            sendSms($partner->telephone,$message);

                            $email = $partner->email;
                            try{
                                $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                                writeLog($message);
                            }
                        }
            
                    } catch (BadResponseException $e) {
                        return sendError($e->getMessage(), [], 401);
                    }
                }else{
                    $libelle = 'le compte '.$wallet->type.' '.$wallet->phone_code.$wallet->phone ;
                    $momoCredited = $paiementService->momoCredited($wallet->phone_code.$wallet->phone, $montant);
                    if($momoCredited == "FAILED"){
                        return sendError('Echec lors du remboursement de la transaction', [], 500); 
                    }else if($momoCredited == "FAILED_TIME"){
                        return sendError('Echec du a un temps d\'attente trop long', [], 500);
                    }else{
                        $retrait->libelle = 'Cashout vers '.$libelle;
                        $retrait->reference = $momoCredited->transactionId;
                        $retrait->status = 'completed';
                        $retrait->save();
                            
                        $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                        sendSms($partner->telephone,$message);

                        $email = $partner->email;
                        try{
                            $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                            Mail::to([$email,])->send(new MailAlerte($arr));
                        } catch (\Exception $e) {
                            $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                            writeLog($message);
                        }
                    }
                }      
            }           

            return sendResponse($wallet, 'Success');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function completeWithdrawPartnerToWallet(Request $request, PaiementService $paiementService){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');

            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required|string',
                'user_partenaire_id' => 'required|string',
            ]);

            if ($validator->fails()){
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }

            $retrait = PartnerWalletWithdraw::where('id',$request->transaction_id)->first();
            $wallet = PartnerWallet::where('id',$retrait->wallet_id)->first();
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            $partner = $userPartenaire->partenaire;
            $montant = $retrait->montant;
            
            if($wallet->type == 'card'){
                $libelle = 'la carte '.decryptData($wallet->customer_id, $encrypt_Key).', ****'.decryptData($wallet->last_digits, $encrypt_Key);
                $reference_memo_gtp = unaccent('Cashout vers '.$libelle);
                $cardCredited = $paiementService->cardCredited($wallet->customer_id, $wallet->last_digits, $montant, $userPartenaire, $reference_memo_gtp);

                if($cardCredited == false){
                    return sendError('Probleme lors du credit de la carte', [], 500);                    
                }else{
                    $referenceGtpCredit = $cardCredited->transactionId;

                    $retrait->reference_gtp_credit = $referenceGtpCredit;
                    $retrait->libelle = 'Cashout vers '.$libelle;
                    $retrait->status = 'completed';
                    $retrait->save();

                    $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                    sendSms($partner->telephone,$message);

                    $email = $partner->email;
                    try{
                        $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                        Mail::to([$email,])->send(new MailAlerte($arr));
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                        writeLog($message);
                    }

                    return sendResponse($retrait, 'Cashout effectué avec succes');
                }
            }else {
                if($wallet->type == 'bcv'){
                    $libelle = 'le compte BCV +'.$wallet->phone_code.$wallet->phone;

                    $receiver =  UserClient::where('deleted',0)->where('username',$wallet->phone_code.$wallet->phone)->first(); 
                    if(!$receiver){
                        $message = ['success' => false, 'status' => 500,'message' => 'Ce compte n\'existe pas','timestamp' => Carbon::now(),'user_id' => $userPartenaire->id]; 
                        writeLog($message);
                        return sendError('Ce compte n\'existe pas', [], 500); 
                    }                      
                    $receiverFirstCard =  $receiver->userCard->first();
                    
                    $reference_memo_gtp = unaccent('Cashout vers '.$libelle);
                    $bcvCredited = $paiementService->cardCredited($receiverFirstCard->customer_id, $receiverFirstCard->last_digits,$montant, $userPartenaire,$reference_memo_gtp);

                    if($bcvCredited == false){
                        return sendError('Probleme lors du compte BCV', [], 500);                    
                    }else{
                        $referenceGtpCredit = $bcvCredited->transactionId;
                        $retrait->reference_gtp_credit = $referenceGtpCredit;
                        $retrait->libelle = 'Cashout vers '.$libelle;
                        $retrait->status = 'completed';
                        $retrait->save();
                            
                        $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                        sendSms($partner->telephone,$message);

                        $email = $partner->email;
                        try{
                            $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                            Mail::to([$email,])->send(new MailAlerte($arr));
                        } catch (\Exception $e) {
                            $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                            writeLog($message);
                        }
                    }
                }else if($wallet->type == 'bmo'){ 
                    $libelle = 'le compte '.$wallet->type.' '.$wallet->phone_code.$wallet->phone ;                       
                    try{  
                        $bmoCredited = $paiementService->bmoCredited('+'.$wallet->phone_code.$wallet->phone,  'Cashout', $partner->libelle, $montant, $userPartenaire);
    
                        if($bmoCredited == false){
                            return sendError('Probleme lors du credit du compte BMO', [], 500);                    
                        }else{
                            $retrait->reference = $bmoCredited->reference;
                            $retrait->libelle = 'Cashout vers '.$libelle;
                            $retrait->status = 'completed';
                            $retrait->save();
                                
                            $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                            sendSms($partner->telephone,$message);

                            $email = $partner->email;
                            try{
                                $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                                Mail::to([$email,])->send(new MailAlerte($arr));
                            } catch (\Exception $e) {
                                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                                writeLog($message);
                            }
                        }
            
                    } catch (BadResponseException $e) {
                        return sendError($e->getMessage(), [], 401);
                    }
                }else{
                    $libelle = 'le compte '.$wallet->type.' '.$wallet->phone_code.$wallet->phone ;
                    $momoCredited = $paiementService->momoCredited($wallet->phone_code.$wallet->phone, $montant);
                    if($momoCredited == "FAILED"){
                        return sendError('Echec lors du remboursement de la transaction', [], 500); 
                    }else if($momoCredited == "FAILED_TIME"){
                        return sendError('Echec du a un temps d\'attente trop long', [], 500);
                    }else{
                        $retrait->libelle = 'Cashout vers '.$libelle;
                        $retrait->reference = $momoCredited->transactionId;
                        $retrait->status = 'completed';
                        $retrait->save();
                            
                        $message = getSms('cashout', null, $montant, null, null, $libelle, $userPartenaire->lastname.' '.$userPartenaire->name);
                        sendSms($partner->telephone,$message);

                        $email = $partner->email;
                        try{
                            $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                            Mail::to([$email,])->send(new MailAlerte($arr));
                        } catch (\Exception $e) {
                            $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                            writeLog($message);
                        }
                    }
                }      
            }    
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function withdrawPartnerToDistributionAccount(Request $request){
        try {
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            $partenaire = $userPartenaire->partenaire;

            $accountCommission = AccountCommission::where('deleted',0)->where('partenaire_id',$partenaire->id)->first();            
            $accountDistribution = AccountDistribution::where('deleted',0)->where('partenaire_id',$partenaire->id)->first();
            $montant = (int)$request->montant;
            if($accountCommission->solde < $montant){
                return sendError('Votre solde commission est insuffisant pour cet opération', [], 500);
            }

            AccountCommissionOperation::create([
                'id' => Uuid::uuid4()->toString(),
                'solde_avant' => $accountCommission->solde,
                'montant' => $montant,
                'solde_apres' => $accountCommission->solde - $montant,
                'libelle' => 'Transfert vers le compte distribution',
                'type' => 'debit',
                'account_commission_id' => $accountCommission->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $accountCommission->solde -= $montant;
            $accountCommission->save();

            AccountDistributionOperation::create([
                'id' => Uuid::uuid4()->toString(),
                'solde_avant' => $accountDistribution->solde,
                'montant' => $montant,
                'solde_apres' => $accountDistribution->solde + $montant,
                'libelle' => 'Transfert depuis le compte de commission',
                'type' => 'credit',
                'deleted' => 0,
                'account_distribution_id' => $accountDistribution->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $accountDistribution->solde += $montant;
            $accountDistribution->save();
            
            
            $message = getSms('cashout', null, $montant, null, null, 'votre compte de distribution', $userPartenaire->lastname.' '.$userPartenaire->name);
            sendSms($partenaire->telephone,$message);

            $email = $partenaire->email;
            try{
                $arr = ['messages'=> $message,'objet'=>'Alerte cashout sur compte de commission','from'=>'bmo-uba-noreply@bestcash.me'];
                Mail::to([$email,])->send(new MailAlerte($arr));
            } catch (\Exception $e) {
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                writeLog($message);
            }
            return sendResponse([], 'Transfert effectué avec succes');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function cessionBetweenPartner(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'receiver_telephone' => ["required" , "string"],
                'user_partenaire_id' => ["required" , "string"],
                'partenaire_id' => ["required" , "string"],
                'amount' => ["required" , "int"],
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            $partenaire = Partenaire::where('id',$request->partenaire_id)->first();
            $receiver = Partenaire::where('telephone','+'.$request->receiver_telephone)->first();

            if(!$receiver){
                return sendError('Il n\'existe pas de compte partenaire avec ce numero de telephone', [], 404);
            }
         
            $accountDistributionSender = AccountDistribution::where('deleted',0)->where('partenaire_id',$partenaire->id)->first();
            $accountDistributionReceiver = AccountDistribution::where('deleted',0)->where('partenaire_id',$receiver->id)->first();
            $montant = (int)$request->amount;

            if($accountDistributionSender->solde < $montant){
                return sendError('Votre solde de distribution est insuffisant pour cette opération', [], 500);
            }

            $transfert = PartnerCession::create([
                'id' => Uuid::uuid4()->toString(),
                'partenaire_id' => $partenaire->id,
                'receiver_id'=> $receiver->id,
                'user_partenaire_id'=> $userPartenaire->id,
                'montant' => $montant,
                'status' => 'pending',
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),    
            ]);

            $accountDistributionSender->solde -= $montant;
            $accountDistributionSender->save();
            
            $accountDistributionReceiver->solde += $montant;
            $accountDistributionReceiver->save();
            
            $transfert->status = 'completed';
            $transfert->save();

            
            // message a faire
            return sendResponse($transfert, 'Transfert effectué avec succes');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
}
