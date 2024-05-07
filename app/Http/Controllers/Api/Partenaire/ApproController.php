<?php

namespace App\Http\Controllers\Api\Partenaire;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPartenaire;
use App\Models\PartnerWallet;
use App\Models\AccountDistribution;
use App\Models\AccountDistributionOperation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\MailAlerte;
use App\Models\PartnerAllWallet;
use App\Models\PartnerAllWalletDetail;
use App\Models\PartnerWalletDeposit;
use App\Services\PaiementService;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ApproController extends Controller
{
    public function depositPartnerFromWallet(Request $request, PaiementService $paiementService){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required',
                'montant' => 'required',
                'user_partenaire_id' => 'required',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $reference = $request->transaction_id;
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            $wallet = PartnerWallet::where('id',$request->walletId)->first();
            $partner = $wallet->partenaire;
            $montant = $request->montant;
            $distributionAccount = AccountDistribution::where('partenaire_id',$partner->id)->first();
            $soldeAvDepot = $distributionAccount->solde;
            $soldeApDepot = $soldeAvDepot + $montant;

            $deposit = PartnerWalletDeposit::create([
                'id' => Uuid::uuid4()->toString(),
                'montant' => $montant,
                'partenaire_id'=> $partner->id,
                'user_partenaire_id'=> $request->user_partenaire_id,
                'libelle' => 'Approvisionnement du compte partenaire',
                'wallet_id' => $wallet->id,
                'solde_avant' => $soldeAvDepot,
                'status' => 'pending',
                'reference' => $reference,
                'is_debited' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),    
            ]);
            
            // Debit du compte qui permet d'approvisionner les compte de distribution des partenaires

            $compteApprovisionnementPartenaire = PartnerAllWallet::where('deleted',0)->first();
            $compteApprovisionnementPartenaire->solde -= $montant;
            $compteApprovisionnementPartenaire->save();
            $deposit->is_debited = 1;
            $deposit->save();

            PartnerAllWalletDetail::create([
                'id' => Uuid::uuid4()->toString(),
                'libelle' => 'Rechargment de compte',
                'sens' => 'debit',
                'amount' => $montant,
                'partenaire_id' => $partner->id,
                'reference' => $reference,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]); 

            // Verification et completion de la transaction

            $checkPaiement = $paiementService->paymentVerification($wallet->type, $reference, $montant);

            if($checkPaiement == true){
                $deposit->is_paid = 1;
                $deposit->save();
    
                $distributionAccount->solde += $montant;
                $distributionAccount->save();
    
                AccountDistributionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'solde_avant' => $soldeAvDepot,
                    'solde_apres' => $soldeApDepot,
                    'montant' => $montant,
                    'libelle' => 'Approvisionnement du compte de distribution',
                    'type' => 'credit',
                    'deleted' => 0,
                    'account_distribution_id' =>  $distributionAccount->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
        
                if($wallet->type == 'card'){
                    $libelle = 'la carte '.decryptData($wallet->customer_id, $encrypt_Key).', ****'.decryptData($wallet->last_digits, $encrypt_Key);
                }else if($wallet->type == 'bcv'){   
                    $libelle = 'le compte BCV '.$wallet->phone_code.$wallet->phone ;
                }else{       
                    $libelle = 'le compte '.$wallet->type.' '.$wallet->phone_code.$wallet->phone ;
                }
                
                $deposit->libelle = 'Depot depuis '.$libelle;
                $deposit->solde_apres = $soldeApDepot;
                $deposit->status = 'completed';
                $deposit->save();

                $message = getSms('rechargement_partenaire', null, $montant, null, null, null, $userPartenaire->lastname.' '.$userPartenaire->name);
                sendSms($partner->telephone,$message);
    
                $email = $partner->email;
                try{
                    $arr = ['messages'=> $message,'objet'=>'Alerte depot sur compte de distribution','from'=>'bmo-uba-noreply@bestcash.me'];
                    Mail::to([$email,])->send(new MailAlerte($arr));
                } catch (\Exception $e) {
                    $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                    writeLog($message);
                }
        
                return sendResponse($deposit, 'Success');
            }else{
                return sendError('Probleme lors de la verification du paiement', [], 500);
            }
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
}
