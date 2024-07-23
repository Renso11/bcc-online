<?php

namespace App\Console;

use App\Mail\MailAlerte;
use App\Mail\VenteVirtuelle;
use App\Models\AccountCommission;
use App\Models\AccountCommissionOperation;
use App\Models\Apporteur;
use App\Models\ApporteurOperation;
use App\Models\Recharge;
use App\Models\UserCard;
use App\Models\UserCardBuy;
use App\Models\UserClient;
use App\Services\PaiementService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('achat:cron')->everyMinutes();
        //$schedule->command('depot:cron')->everyMinutes();
        $schedule->call(function (PaiementService $paiementService) {
            try {
                $recharges = Recharge::where('is_debited',1)->where('status', 'on_going_payment')->orderBy('created_at','asc')->get();
                if(count($recharges) > 0){
                    foreach ($recharges as $key => $recharge) {
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
                                    $arr = ['messages'=> $message,'objet'=> 'Confirmation du rechargement','from'=>'bmo-uba-noreply@bestcash.me'];
                                    Mail::to([$user->kycClient->email,])->send(new MailAlerte($arr));
                                } catch (\Exception $e) {
                                    return "error";
                                }
                            }
                            
                            $paiementService->repartitionCommission($fraisAndRepartition,$frais,$montant,$referenceGtp, 'rechargement');
                            return sendResponse($recharge, 'Rechargement complété avec succes. Consulter votre solde');
                        }
                    }
                }else{
                    return "pas de recharge";
                }
            } catch (BadResponseException $e) {        
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return $error;
            }
        })->everyMinute();

        $schedule->call(function (PaiementService $paiementService) {
            try{
                $base_url = env('BASE_GTP_API');
                $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
                $programID = env('PROGRAM_ID');
                $authLogin = env('AUTH_LOGIN');
                $authPass = env('AUTH_PASS');
                $encrypt_Key = env('ENCRYPT_KEY');
                
                $userCardBuys = UserCardBuy::where('is_debited',1)->where('status', 'on_going_payment')->orderBy('created_at','asc')->get();
                
                if(count($userCardBuys) > 0){
                    foreach ($userCardBuys as $key => $userCardBuy) {
                        $userCardBuy->status =  'on_payment';
                        $userCardBuy->save();
            
                        $user = UserClient::where('id',$userCardBuy->user_client_id)->first();
            
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
                                'type' => "virtuelle",
                                'pass_code' => encryptData((string)$responseBody->registrationPassCode,$encrypt_Key),
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
                                Mail::to([$user->kycClient->email,])->send(new VenteVirtuelle(['registrationAccountId' => $responseBody->registrationAccountId,'registrationLast4Digits' => $responseBody->registrationLast4Digits,'registrationPassCode' => $responseBody->registrationPassCode,'type' => 'virtuelle'])); 
                            } catch (\Exception $e) {
                                return 'Echec d\'envoi de mail.';
                            }
                            
                            return sendResponse($card, 'Achat terminé avec succes');
                        } catch (BadResponseException $e) {
                            $json = json_decode($e->getResponse()->getBody()->getContents());
                            $error = $json->title.'.'.$json->detail;
                            
                            return sendError($error, [], 500);
                        }
                    }
                }else{
                    return "pas d'achat";
                }
            }catch (\Exception $e) {
                return sendError($e->getMessage(), [], 500);
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        /*$this->commands([
            Commands\AchatCron::class,
            Commands\DepotCron::class,
        ]);*/
        require base_path('routes/console.php');
    }
}
