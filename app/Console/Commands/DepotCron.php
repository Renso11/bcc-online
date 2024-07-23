<?php

namespace App\Console\Commands;

use App\Mail\MailAlerte;
use App\Models\Recharge;
use App\Models\UserCard;
use App\Models\UserClient;
use App\Services\PaiementService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DepotCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'depot:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron pour executer les depots';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(PaiementService $paiementService)
    {
        try {
            $recharge = Recharge::where('is_debited',1)->where('status', 'on_going_payment')->orderBy('created_at','asc')->first();
            if($recharge){
                $recharge->status =  'on_payment';
                $recharge->save();
    
                $user = UserClient::where('id',$recharge->user_client_id)->first();
                $card = UserCard::where('id',$recharge->user_card_id)->first();
                
                $montant = $recharge->montant;
    
                $fraisAndRepartition = getFeeAndRepartition('rechargement', $montant);
                $frais = getFee($fraisAndRepartition,$montant);
                $montantWithoutFee = $montant - $frais;
    
                $soldeAvantDepot = getCardSolde($card);
                
                $reference_memo_gtp = unaccent("Rechargement de " . $montantWithoutFee . " XOF sur votre carte. Frais de rechargement : " . $frais . " XOF");
                $cardCredited = $paiementService->cardCredited($card->customer_id, $card->last_digits, $montantWithoutFee, $user, $reference_memo_gtp); 
                
                return sendResponse($cardCredited, 'Rechargement complété avec succes. Consulter votre solde');
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
                    
                    sendSms($user->username,$message);
                    
                    $paiementService->repartitionCommission($fraisAndRepartition,$frais,$montant,$referenceGtp, 'rechargement');
                    return sendResponse($recharge, 'Rechargement complété avec succes. Consulter votre solde');
                }
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
