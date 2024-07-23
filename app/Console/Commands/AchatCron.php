<?php

namespace App\Console\Commands;

use App\Mail\VenteVirtuelle;
use App\Models\AccountCommission;
use App\Models\AccountCommissionOperation;
use App\Models\Apporteur;
use App\Models\ApporteurOperation;
use App\Models\UserCard;
use App\Models\UserCardBuy;
use App\Models\UserClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class AchatCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achat:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cron pour executer les achats';

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
    public function handle()
    {
        try{
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $userCardBuy = UserCardBuy::where('is_debited',1)->where('status', 'on_going_payment')->orderBy('created_at','asc')->first();
            
            if($userCardBuy){
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
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now(), 'user' => $user->id];  
                        writeLog($message);
                    }
                    
                    $message = ['success' => true, 'status' => 200,'message' => 'Achat effectué avec succes','timestamp' => Carbon::now(),'user' => $user->id]; 
                    writeLog($message);
                    return sendResponse($card, 'Achat terminé avec succes');
                } catch (BadResponseException $e) {
                    $json = json_decode($e->getResponse()->getBody()->getContents());
                    $error = $json->title.'.'.$json->detail;
                    
                    $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
                    writeLog($message);
                    return sendError($error, [], 500);
                }
            }else{
                Log::info("pas d'achat");
            }
        }catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }
}
