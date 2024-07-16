<?php

namespace App\Http\Controllers;

use App\Mail\MailAlerte;
use App\Models\AccountDistribution;
use App\Models\AccountDistributionOperation;
use App\Models\FrontPayment;
use Illuminate\Http\Request;
use App\Models\Service;
use Ramsey\Uuid\Uuid;
use App\Models\Info;
use App\Models\kkiapayRecharge;
use App\Models\PartnerAllWallet;
use App\Models\PartnerAllWalletDetail;
use App\Models\PartnerWallet;
use App\Models\PartnerWalletDeposit;
use App\Models\PasswordResetQuestion;
use App\Models\TransfertAdmin;
use App\Models\UserPartenaire;
use App\Models\RetraitKkp;
use App\Services\PaiementService;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AppController extends Controller
{
    public function appClient(Request $request){
        try {
            $info_card = Info::where('deleted',0)->first();
            $services = Service::where('deleted',0)->where('type','client')->get();
            $questions = PasswordResetQuestion::where('deleted',0)->get();
            return view('app.client.client',compact('info_card','services','questions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }

    public function serviceClientAdd(Request $request){
        try {
            Service::create([
                'id' => Uuid::uuid4()->toString(),
                'type' => 'client',
                'slug' => Str::slug($request->libelle , "-"),
                'status' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return back()->withSuccess('Module ajouté avec success');
        } catch (\Exception $e) {
            return  back()->withError($e->getMessage());
        }
    }

    public function cardInfosUpdate(Request $request){
        try {
            $info_card = Info::where('deleted',0)->first();

            if(!$info_card){
                Info::create([
                    'id' => Uuid::uuid4()->toString(),
                    'card_max' => $request->nb_card,
                    'card_price' => $request->pu_card,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }else{
                $info_card->card_max = $request->nb_card;
                $info_card->card_price = $request->pu_card;
                $info_card->save();
            }
            return back()->withSuccess('Informations modifiées avec succes');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function questionAdd(Request $request){
        try {
            PasswordResetQuestion::create([
                'id' => Uuid::uuid4()->toString(),
                'libelle' => $request->libelle,
                'status' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return back()->withSuccess('Question ajouté avec success');
        } catch (\Exception $e) {
            return  back()->withError($e->getMessage());
        }
    }

    public function questionDelete(Request $request){
        try {
            $password = PasswordResetQuestion::where('id',$request->id)->where('deleted',0)->first();
            $password->deleted = 1;
            $password->save();
            return 'success';
        } catch (\Exception $e) {
            return  $e->getMessage();
        };
    }

    public function appPartenaire(Request $request){
        try {
            $services = Service::where('deleted',0)->where('type','partenaire')->get();
            return view('app.partenaire',compact('services'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function servicePartenaireAdd(Request $request){
        try {
            Service::create([
                'id' => Uuid::uuid4()->toString(),
                'type' => 'partenaire',
                'slug' => Str::slug($request->libelle , "-"),
                'status' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return back()->withSuccess('Module ajouté avec success');
        } catch (\Exception $e) {
            return  back()->withError($e->getMessage());
        }
    }



    public function serviceDelete(Request $request){
        try {
            $service = Service::where('id',$request->id)->where('deleted',0)->first();
            $service->deleted = 1;
            $service->save();
            return 'success';
        } catch (\Exception $e) {
            return  $e->getMessage();
        };
    }

    public function serviceActivate(Request $request){
        try {
            $service = Service::where('id',$request->id)->where('deleted',0)->first();
            $service->status = 1;
            $service->save();
            return 'success';

        } catch (\Exception $e) {
            return  $e->getMessage();
        };
    }

    public function serviceDesactivate(Request $request){
        try {
            $service = Service::where('id',$request->id)->where('deleted',0)->first();
            $service->status = 0;
            $service->save();
            return 'success';

        } catch (\Exception $e) {
            return  $e->getMessage();
        };
    }



    public function appAdmin(Request $request){
        try {
            $services = Service::where('deleted',0)->where('type','partenaire')->get();
            return view('app.partenaire',compact('services'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function serviceAdminAdd(Request $request){
        try {
            Service::create([
                'id' => Uuid::uuid4()->toString(),
                'type' => 'partenaire',
                'slug' => Str::slug($request->libelle , "-"),
                'status' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return back()->withSuccess('Module ajouté avec success');
        } catch (\Exception $e) {
            return  back()->withError($e->getMessage());
        }
    }

    public function transfertAdmin(Request $request){
        try {
            $transferts = TransfertAdmin::where('deleted',0)->get();
            return view('admin.transfert',compact('transferts'));
        } catch (\Exception $e) {
            dd($e);
            return  $e->getMessage();
        };
    }

    public function transfertAdminAdd(Request $request){
        try {
            
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');

            $client = new Client();
            $url =  $base_url."accounts/".$request->customer_id."/transactions";

            $body = [
                "transferType" => $request->sens == 'debit' ? "WalletToCard" : "CardToWallet",
                "transferAmount" => $request->montant,
                "currencyCode" => "XOF",
                "referenceMemo" => 'Transfert admin de '.$request->montant.' XOF. par '. Auth::user()->name.' '.Auth::user()->lastname, 
                "last4Digits" => $request->last_digits
            ];

            $body = json_encode($body);
            
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
                'accountId' => $accountId,
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];
        
            $auth = [
                $authLogin,
                $authPass
            ];

            $response = $client->request('POST', $url, [
                'auth' => $auth,
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);
            

            $client = new Client();
            $url =  $base_url."accounts/".$request->compte."/transactions";

            $body = [
                "transferType" => $request->sens == 'debit' ? "CardToWallet" : "WalletToCard" ,
                "transferAmount" => $request->montant,
                "currencyCode" => "XOF",
                "referenceMemo" => 'Transfert admin de '.$request->montant.' XOF. par '. Auth::user()->name.' '.Auth::user()->lastname,
                "last4Digits" => $request->compte_last
            ];

            $body = json_encode($body);
            
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
                'accountId' => $accountId,
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];
        
            $auth = [
                $authLogin,
                $authPass
            ];
            
            $response = $client->request('POST', $url, [
                'auth' => $auth,
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);

            TransfertAdmin::create([
                'id' => Uuid::uuid4()->toString(),
                'compte' => $request->compte,
                'program' => $request->compte_last,
                'sens' => $request->sens,
                'customer_id' => $request->customer_id,
                'last_digits' => $request->last_digits,
                'montant' => $request->montant,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            return back()->withSuccess('Transfert effectué avec succes');
            
        } catch (\Exception $e) {
            dd($e);
            return  $e->getMessage();
        };
    }

    public function retraitKkp(Request $request){
        try {
            $retraits = RetraitKkp::where('deleted',0)->orderBy('created_at', 'desc')->get();
            return view('admin.retraitKkp',compact('retraits'));
        } catch (\Exception $e) {
            dd($e);
            return  $e->getMessage();
        };
    }

    public function retraitKkpAdd(Request $request){
        try {

            $base_url_kkp = "https://api.kkiapay.me";

            $client = new Client();
            $url = $base_url_kkp . "/api/v1/payments/deposit";

            $telephone = env("WITHDRAW_KKP");
            $montant = (int)$request->montant;

            $partner_reference = substr($telephone, -4) . time();
            $body = [
                "phoneNumber" => $telephone,
                "amount" => $montant,
                "reason" => 'Transfert de ' . $montant . ' XOF vers le compte momo/flooz ' . $telephone . '.',
                "partnerId" => $partner_reference
            ];

            $body = json_encode($body);
            $headers = [
                'x-private-key' =>"pk_20b69f7e83a417345810e281fd71bbe43d908484455ba01d384d992ba6f8a853",
                'x-secret-key' => "sk_f069c954304d0ff5522c5b1055a38b8640994d87681e855d28eebc19a569ba24",
                'x-api-key' => "653a4b85df3c403ad1fb39a64cc9a9ef874432db"
            ];

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body
            ]);

            $resultat = json_decode($response->getBody());
            
            $status = "PENDING";
            $starttime = time();

            while ($status == "PENDING") {
                $externalTransaction = resultat_check_status_kkp($resultat->transactionId);
                if ($externalTransaction->status == "SUCCESS") {
                    $status = "SUCCESS";
                    $message = ['success' => true, 'status' => 200, 'message' => 'Retrait kkp effectué avec succes', 'timestamp' => Carbon::now(), 'user' => 1];
                    writeLog($message);
                } else if ($externalTransaction->status == "FAILED") {
                    $status = "FAILED";
                    $message = ['success' => false, 'status' => 500, 'message' => 'Echec retrait kkp', 'timestamp' => Carbon::now()];
                    writeLog($message);
                    dd($externalTransaction);
                } 
            }

            RetraitKkp::create([
                'id' => Uuid::uuid4()->toString(),
                'reference' => $externalTransaction->transactionId,
                'amount' => $request->montant,
                'status' => "success",
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            //dd($externalTransaction);
            return redirect()->route('retrait.kkp')->withSuccess('Retrait effectué avec success');
            
        } catch (\Exception $e) {
            dd($e);
            return  $e->getMessage();
        };
    }

    

    public function rechargeKkp(Request $request){
        try {
            kkiapayRecharge::create([
                'id' => Uuid::uuid4()->toString(),
                'montant' => $request->montant,
                'reference' => $request->reference,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return back()->withSuccess('Rechargement effectué avec success');

        } catch (\Exception $e) {
            return  back()->withError($e->getMessage());
        };
    }

    

    public function initTransactionKkiapay(Request $request){
        try{
            $id = $request->payment_id;
            $payment = PartnerWalletDeposit::where('id',$id)->first();
            
            return view('initKkp', compact('payment'));
        } catch (\Exception $e) {
            return redirect()->route('welcome')->withError($e->getMessage());
        }
    }

    public function validationTransactionKkiapay(Request $request, PaiementService $paiementService){
        
        try {
            $encrypt_Key = env('ENCRYPT_KEY');

            $reference = $request->transaction_id;
            $payment = PartnerWalletDeposit::where('id',$request->payment_id)->first();
            
            $wallet = PartnerWallet::where('id',$payment->wallet_id)->first();
            $partner = $wallet->partenaire;
            $montant = $payment->montant;

            $distributionAccount = AccountDistribution::where('partenaire_id',$partner->id)->first();
            $soldeAvDepot = $distributionAccount->solde;
            $soldeApDepot = $soldeAvDepot + $montant;

            $checkPaiement = $paiementService->paymentVerification($wallet->type, $reference, $montant);
            
            $payment->solde_avant = $soldeAvDepot;
            $payment->solde_apres = $soldeApDepot;
            $payment->reference = $reference;
            $payment->save();

            if($checkPaiement == true){
                $payment->is_debited = 1;
                $payment->save();
                
                $compteMirroirChezElg = PartnerAllWallet::where('deleted',0)->first();
                $compteMirroirChezElg->solde -= $montant;
                $compteMirroirChezElg->save();

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
    
                $distributionAccount->solde += $montant;
                $distributionAccount->save();
    
                AccountDistributionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'solde_avant' => $soldeAvDepot,
                    'solde_apres' => $soldeApDepot,
                    'montant' => $montant,
                    'libelle' => 'Rechargement du compte de distribution',
                    'type' => 'credit',
                    'deleted' => 0,
                    'account_distribution_id' =>  $distributionAccount->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
        
                if($wallet->type == 'card'){
                    $libelle = 'la carte '.decryptData($wallet->customer_id, $encrypt_Key).', ****'.decryptData($wallet->last_digits, $encrypt_Key);
                    $payment->libelle = 'Depot depuis '.$libelle;
                }else if($wallet->type == 'bcv'){   
                    $libelle = 'le compte BCV '.$wallet->phone_code.$wallet->phone ;
                    $payment->libelle = 'Depot depuis '.$libelle;
                }else{       
                    $libelle = 'le compte '.$wallet->type.' '.$wallet->phone_code.$wallet->phone ;
                    $payment->libelle = 'Depot depuis '.$libelle;
                }
                
                $payment->status = 'completed';
                $payment->save();

                $userPartenaire = UserPartenaire::where('id',$payment->user_partenaire_id)->first();

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
        
                return view('initKkp', compact('payment'));
            }else{
                return sendError('Probleme lors de la verification du paiement', [], 500);
            }
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function rejetTransactionKkiapay(Request $request){
        
        try {
            $depot = PartnerWalletDeposit::where('id',$request->payment_id)->first();
                
            $depot->status = 'failed';
            $depot->motif = 'Ce paiement n\'a pas pu bien s\'effectuer';
            $depot->save();
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
}
