<?php

namespace App\Http\Controllers\Api\Partenaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Depot;
use App\Models\UserClient;
use App\Models\UserPartenaire;
use GuzzleHttp\Client;
use App\Models\AccountCommission;
use App\Models\AccountDistribution;
use App\Models\AccountDistributionOperation;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use App\Mail\MailAlerte;
use App\Models\AccountCommissionOperation;
use App\Models\CompteCommission;
use App\Models\CompteCommissionOperation;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use App\Services\PaiementService;

class DepotController extends Controller
{
    
    public function addDepotPartenaire(Request $request, PaiementService $paiementService){
        try {        
            $validator = Validator::make($request->all(), [
                'username' => ["required" , "string"],
                'montant' => ["required" , "integer"],
                'user_partenaire_id' => ["required" , "string"]
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
    
            $client = UserClient::where('username',$request->username)->where('deleted',0)->first();
            if(!$client){
                return sendError('Ce compte client n\'exite pas. Verifier le numero de telephone et recommencer');
            }else{
                if($client->status == 0){
                    return sendError('Ce compte client est inactif');
                }
                if($client->verification == 0){
                    return sendError('Ce compte client n\'est pas encore verifié');
                }
            }
    
            $card = $client->userCard->first();
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();        
            $distribution_account = AccountDistribution::where('partenaire_id',$userPartenaire->partenaire->id)->where('deleted',0)->first();
    
            $montant = $request->montant;        
            $fraisAndRepartition = getFeeAndRepartition('depot', $montant);
            $frais = getFee($fraisAndRepartition,$montant);
            $montantWithoutFee = $montant - $frais;
            
            if($distribution_account->solde < $montantWithoutFee || $montantWithoutFee <= 0){
                return sendError('Solde insuffisant ou montant inferieur aux frais de la transaction',403);
            }
            
            $depot = Depot::create([
                'id' => Uuid::uuid4()->toString(),
                'user_client_id' => $client->id,
                'user_partenaire_id' => $userPartenaire->id,
                'partenaire_id' => $userPartenaire->partenaire->id,
                'libelle' => 'Depot du compte BCV '.$client->username. ' chez le marchand ' .$userPartenaire->partenaire->code_partenaire,
                'montant' => $montant,
                'frais' => $frais,
                'status' => 'pending',
                'is_debited' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
    
            $soldeAvDepot = $distribution_account->solde;            
            $distribution_account->solde -= $montant;
            $distribution_account->save();
            $soldeApDepot = $distribution_account->solde ;

            AccountDistributionOperation::create([
                'id' => Uuid::uuid4()->toString(),
                'solde_avant' => $soldeAvDepot,
                'montant' => $montant,
                'solde_apres' => $soldeApDepot,
                'libelle' => 'Depot sur le compte BCV '.$client->username.'.',
                'type' => 'debit',
                'account_distribution_id' => $distribution_account->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $depot->is_debited = 1;
            $depot->save();
    
            
            $reference_memo_gtp = unaccent("Depot de ".$montantWithoutFee.". Frais: ".$frais.". Client: ".$client->username.". Partenaire: ".$userPartenaire->partenaire->libelle.".");
            $cardCredited = $paiementService->cardCredited($card->customer_id, $card->last_digits, $montantWithoutFee, $userPartenaire, $reference_memo_gtp);
    
            if($cardCredited == false){
                return sendError('Probleme lors du credit de la carte', [], 500);                    
            }else{
                $referenceGtp = $cardCredited->transactionId;           
                
                $soldeAvantRetrait = getCardSolde($card);
                $soldeApresRetrait = $soldeAvantRetrait + $montantWithoutFee;
    
                $depot->reference_gtp = $referenceGtp;
                $depot->montant_recu = $montantWithoutFee;
                $depot->solde_avant = $soldeAvantRetrait;
                $depot->solde_apres = $soldeApresRetrait;
                $depot->save();
                
                $commission_account = AccountCommission::where('partenaire_id',$userPartenaire->partenaire->id)->where('deleted',0)->first();
    
                $this->repartitionCommission($commission_account,$fraisAndRepartition,$frais,$montant,$referenceGtp);
                
                $depot->is_credited = 1;
                $depot->status = 'completed';
                $depot->save();
    
                $message = getSms('depot_client', null, $montant, $frais, null, null, $userPartenaire->partenaire->libelle);
                if($client->sms == 1){
                    sendSms($client->username,$message);
                }else{
                    try{
                        $arr = ['messages'=> $message,'objet'=>'Confirmation du depot','from'=>'bmo-uba-noreply@bestcash.me'];
                        Mail::to([$client->kycClient->email,])->send(new MailAlerte($arr));
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                        writeLog($message);
                    }
                }
                
                $message = getSms('depot_partenaire', null, $montant, $frais, null, $client->username, $userPartenaire->partenaire->libelle);
                sendSms($depot->partenaire->telephone,$message);
            }
        } catch (\Exception $e) {
            return sendError($e->getMessage(), 500);
        }
    }

    public function completeDepotPartenaire(Request $request, PaiementService $paiementService){$validator = Validator::make($request->all(), [
            'transaction_id' => ["required" , "string"],
            'user_partenaire_id' => ["required" , "string"]
        ]);

        if ($validator->fails()) {
            return  sendError($validator->errors(), [],422);
        }

        $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
        $depot = Depot::where('id',$request->transaction_id)->first();
        $card = $depot->userClient->userCard->first();
        $client = $depot->userClient;
        $montant = $depot->montant;
        
        $fraisAndRepartition = getFeeAndRepartition('depot', $montant);
        $frais = getFee($fraisAndRepartition,$montant);
        $montantWithoutFee = $montant - $frais;
            
        $distribution_account = $depot->partenaire->accountDistribution;

        if($distribution_account->solde < $montant){
            return sendError('Votre solde ne suffit pas pour cette opération',403);
        }

        if($depot->is_debited == 0 || $depot->is_debited == null){
            $soldeAvDepot = $distribution_account->solde;
            $soldeApDepot = $distribution_account->solde - $montant;

            AccountDistributionOperation::create([
                'id' => Uuid::uuid4()->toString(),
                'solde_avant' => $soldeAvDepot,
                'montant' => $montant,
                'solde_apres' => $soldeApDepot,
                'libelle' => 'Depot sur le compte BCV '.$client->username.'.',
                'type' => 'debit',
                'account_distribution_id' => $distribution_account->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $distribution_account->solde -= $montant;
            $distribution_account->save();

            $depot->is_debited == 1;
            $depot->save();
        }        
        
        if($depot->is_credited == 1){
            
            $reference_memo_gtp = unaccent("Depot de ".$montantWithoutFee.". Frais: ".$frais.". Client: ".$client->username.". Partenaire: ".$userPartenaire->partenaire->libelle.".");
            $cardCredited = $paiementService->cardCredited($card->customer_id, $card->last_digits, $montantWithoutFee, $userPartenaire, $reference_memo_gtp);
    
            if($cardCredited == false){
                return sendError('Probleme lors du credit de la carte', [], 500);                    
            }else{
                $referenceGtp = $cardCredited->transactionId;           
                
                $soldeAvantRetrait = getCardSolde($card);
                $soldeApresRetrait = $soldeAvantRetrait + $montantWithoutFee;
    
                $depot->reference_gtp = $referenceGtp;
                $depot->montant_recu = $montantWithoutFee;
                $depot->solde_avant = $soldeAvantRetrait;
                $depot->solde_apres = $soldeApresRetrait;
                $depot->save();
                
                $commission_account = AccountCommission::where('partenaire_id',$userPartenaire->partenaire->id)->where('deleted',0)->first();
    
                $this->repartitionCommission($commission_account,$fraisAndRepartition,$frais,$montant,$referenceGtp);
                
                $depot->is_credited = 1;
                $depot->status = 'completed';
                $depot->save();
    
                $message = getSms('depot_client', null, $montant, $frais, null, null, $userPartenaire->partenaire->libelle);
                if($client->sms == 1){
                    sendSms($client->username,$message);
                }else{
                    try{
                        $arr = ['messages'=> $message,'objet'=>'Confirmation du depot','from'=>'bmo-uba-noreply@bestcash.me'];
                        Mail::to([$client->kycClient->email,])->send(new MailAlerte($arr));
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $userPartenaire->id];  
                        writeLog($message);
                    }
                }
                
                $message = getSms('depot_partenaire', null, $montant, $frais, null, $client->username, $userPartenaire->partenaire->libelle);
                sendSms($depot->partenaire->telephone,$message);
            }
        }
        return sendResponse($depot, 'Succès');
    }

    private function repartitionCommission($compteCommissionPartenaire,$fraisOperation,$frais,$montant,$referenceGtp){
        if($fraisOperation){        
            $fraiCompteCommissions = $fraisOperation->fraiCompteCommissions;
            
            foreach ($fraiCompteCommissions as $value) {
                $compteCommission = CompteCommission::where('id',$value->compte_commission_id)->first();
    
                if($value->type == 'pourcentage'){
                    $commission = $frais * $value->value / 100;
                }else{
                    $commission = $value->value;
                }
    
                $compteCommission->solde += $commission;
                $compteCommission->save();
                
                CompteCommissionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'compte_commission_id'=> $compteCommission->id,
                    'type_operation'=>'depot',
                    'montant'=> $montant,
                    'frais'=> $frais,
                    'commission'=> $commission,
                    'reference_gtp'=> $referenceGtp,
                    'status'=> 0,
                    'deleted'=> 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()                
                ]);
            }
    
            if($fraisOperation->value_commission_partenaire > 0){
                if($fraisOperation->type_commission_partenaire == 'pourcentage'){
                    $commissionPartenaire = $frais * $fraisOperation->value_commission_partenaire / 100;
                }else{
                    $commissionPartenaire = $fraisOperation->value_commission_partenaire;
                }
    
                $soldeAvIncr = $compteCommissionPartenaire->solde;
                $compteCommissionPartenaire->solde += $commissionPartenaire;
                $compteCommissionPartenaire->save();
                
                
                $soldeApIncr = $compteCommissionPartenaire->solde + $commissionPartenaire;
    
                AccountCommissionOperation::insert([
                    'id' => Uuid::uuid4()->toString(),
                    'reference_gtp'=> $referenceGtp,
                    'solde_avant' => $soldeAvIncr,
                    'montant' => $commissionPartenaire,
                    'solde_apres' => $soldeApIncr,
                    'libelle' => 'Commission sur depot',
                    'type' => 'credit',
                    'account_commission_id' => $compteCommissionPartenaire->id,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),            
                ]);
            }
    
        }
    }
}
