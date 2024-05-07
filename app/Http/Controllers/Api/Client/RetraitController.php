<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retrait;
use App\Models\AccountCommission;
use App\Models\AccountCommissionOperation;
use App\Models\AccountDistribution;
use App\Models\AccountDistributionOperation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailAlerte;
use App\Models\CompteCommission;
use App\Models\CompteCommissionOperation;
use App\Services\PaiementService;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Ramsey\Uuid\Uuid;

class RetraitController extends Controller
{
    public function __construct() {
        $this->middleware('is-auth', ['except' => ['addContact','createCompteClient', 'loginCompteClient', 'sendCode', 'checkCodeOtp', 'resetPassword','verificationPhone', 'verificationInfoPerso','verificationInfoPiece','saveFile','sendCodeTelephoneRegistration','getServices','sendCodeTelephone']]);
    }

    public function getClientPendingWithdraws(Request $request){
        $withdrawls = Retrait::where('deleted',0)->where('user_client_id',$request->id)->where('status','pending')->get();
        return sendResponse($withdrawls, 'Liste transactions.');
    }
    
    public function validationRetraitAttenteClient(Request $request, PaiementService $paiementService){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');
                
            $validator = Validator::make($request->all(), [
                'user_card_id' => 'required',
                'transaction_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $token = JWTAuth::getToken();
            $userId = JWTAuth::getPayload($token)->toArray()['sub'];

      
            $retrait = Retrait::where('id',$request->transaction_id)->where('deleted',0)->where('status',0)->first();
            
            if($userId != $retrait->user_client_id){
                return  sendError('Vous n\'etes pas autorisé à faire cette opération', [$userId,$retrait->user_client_id],401);
            }

            $reference = unaccent("Retrait de  : " . $retrait->montant . " XOF de la carte " . decryptData((string) $retrait->userCard->customer_id, $encrypt_Key));
            $cardDebited = $paiementService->cardDebited($retrait->userCard->customer_id, $retrait->userCard->last_digits, $retrait->montant+$retrait->frais, 0, $retrait->userClient, $reference);

            if($cardDebited == false){
                return sendError('Erreur lors du debit de la carte principale', [], 401);
            }else{
            
                $soldeAvantRetrait = getCardSolde($retrait->userCard);
                
                $fraisAndRepartition = getFeeAndRepartition('retrait', $retrait->montant);
    
                
                $referenceGtp = $cardDebited->transactionId;
    
                $comptePartenaire = AccountDistribution::where('partenaire_id',$retrait->userPartenaire->partenaire->id)->where('deleted',0)->first();
    
                AccountDistributionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'solde_avant' => $comptePartenaire->solde,
                    'montant' => $retrait->montant,
                    'solde_apres' => $comptePartenaire->solde + $retrait->montant,
                    'libelle' => 'Retrait effectué sur le compte '.$retrait->userClient->telephone,
                    'type' => 'credit',
                    'account_distribution_id' => $comptePartenaire->id,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
    
                $soldeApresRetrait = $soldeAvantRetrait - $retrait->montant - $retrait->frais;
                $retrait->status = 'completed';
                $retrait->solde_avant = $soldeAvantRetrait;
                $retrait->solde_apres = $soldeApresRetrait;                
                $retrait->reference_gtp = $cardDebited->transactionId;
                $retrait->save();
    
                $comptePartenaire->solde += $retrait->montant;
                $comptePartenaire->save(); 
    
                $message = ['success' => true, 'status' => 200,'message' => 'Retrait effectué avec succes','timestamp' => Carbon::now(),'user' => $retrait->userClient->id]; 
                writeLog($message); 
    
                
                $compteCommissionPartenaire = AccountCommission::where('partenaire_id',$retrait->userPartenaire->partenaire->id)->where('deleted',0)->first();
                $this->repartitionCommission($compteCommissionPartenaire,$comptePartenaire,$fraisAndRepartition,$retrait->frais,$retrait->montant,$referenceGtp);
    
                $message = getSms('retrait_finalise_client', null, $retrait->montant, $retrait->frais, $soldeApresRetrait, null, $retrait->partenaire->libelle);
                
                if($retrait->userClient->sms == 1){
                    sendSms($retrait->userClient->username,$message);
                }else{
                    try{
                        $arr = ['messages'=> $message,'objet'=>'Confirmation du retrait','from'=>'bmo-uba-noreply@bestcash.me'];
                        Mail::to([$retrait->userClient->kycClient->email,])->send(new MailAlerte($arr));
                    } catch (\Exception $e) {
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $retrait->userClient->id];  
                        writeLog($message);
                    }
                }
    
                
                $message = getSms('retrait_finalise_partenaire', null, $retrait->montant, $retrait->frais, $soldeApresRetrait, $retrait->userClient->name.' '.$retrait->userClient->lastname.' - Tel : '.$retrait->userClient->username, null);
                
                sendSms($retrait->userPartenaire->partenaire->telephone,$message);
                
                return sendResponse($retrait, 'Votre opération de retrait a été confirmé avec succès');
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }
    
    public function annulationRetraitAttenteClient(Request $request){
        try {                
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $token = JWTAuth::getToken();
            $userId = JWTAuth::getPayload($token)->toArray()['sub'];

      
            $retrait = Retrait::where('id',$request->transaction_id)->where('deleted',0)->where('status','pending')->first();
            
            if($userId != $retrait->user_client_id){
                return  sendError('Vous n\'etes pas autorisé à faire cette opération', [$userId,$retrait->user_client_id],401);
            }

            $retrait->status = 'canceled';
            $retrait->deleted = 1;
            $retrait->save();
            $message = ['success' => true, 'status' => 200,'message' => 'Retrait annulé avec succes','timestamp' => Carbon::now(),'user' => $retrait->userClient->id]; 
            writeLog($message); 
            
            return sendResponse($retrait, 'Succès');
        } catch (\Exception $e) {
            $message = ['success' => true, 'status' => 200,'message' => 'Retrait effectué avec succes','timestamp' => Carbon::now(),'user' => $retrait->userClient->id]; 
            writeLog($message); 
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        };
    }

    private function repartitionCommission($compteCommissionPartenaire,$compteDistributionPartenaire,$fraisOperation,$frais,$montant,$referenceGtp){
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
                    'type_operation'=> 'retrait',
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
                    'libelle' => 'Commission sur retrait',
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
