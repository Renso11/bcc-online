<?php

namespace App\Http\Controllers;

use App\Models\AccountDistribution;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use App\Models\CompteCommission;
use App\Models\CompteCommissionOperation;
use App\Models\CompteMouvement;
use App\Models\CompteMouvementOperation;
use App\Models\Partenaire;
use App\Models\PartnerAllWallet;
use App\Models\PartnerAllWalletDetail;
use App\Models\UserClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class CompteController extends Controller
{

    public function compteAllPartner(Request $request)
    {
        try{
            $partnerWalletAll = PartnerAllWallet::where('deleted',0)->first();
            $partnerWalletAllDetails = PartnerAllWalletDetail::where('deleted',0)->get();
            return view('compte.partnerWallet',compact('partnerWalletAll','partnerWalletAllDetails'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteAllPartnerRecharge(Request $request)
    {
        try{
            $partnerWalletAll = PartnerAllWallet::where('deleted',0)->first();
            $partnerWalletAll->solde += $request->montant;
            $partnerWalletAll->save();

            PartnerAllWalletDetail::create([
                'id' => Uuid::uuid4()->toString(),
                'sens' => 'appro',
                'amount' => $request->montant,
                'reference' => $request->reference,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return back()->withSuccess('Compte rechargé avec succes');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteCommission(Request $request)
    {
        try{
            $compteCommissions = CompteCommission::where('deleted',0)->get();
            return view('compte.commission',compact('compteCommissions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteCommissionAdd(Request $request)
    {
        try{
            CompteCommission::create([
                'id' => Uuid::uuid4()->toString(),
                'libelle' => $request->libelle,
                'solde' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return back()->withSuccess("Type de compte enregistré avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteCommissionEdit(Request $request)
    {   
        try{
            $compteCommission = CompteCommission::where('id',$request->id)->where('deleted',0)->first();
            $compteCommission->libelle = $request->libelle;
            $compteCommission->save();
            return back()->withSuccess("Modification effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteCommissionDelete(Request $request)
    {   
        try{
            $compteCommission = CompteCommission::where('id',$request->id)->where('deleted',0)->first();
            $compteCommission->deleted = 1;
            $compteCommission->save();
            return back()->withSuccess("Suppression effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteCommissionDetail(Request $request)
    {
        try{
            $compteCommission = CompteCommission::where('id',$request->id)->where('deleted',0)->first();
            $compteCommissionOperations = CompteCommissionOperation::where('compte_commission_id',$compteCommission->id)->orderBy('created_at','desc')->get();
            return view('compte.detail_commission',compact('compteCommission','compteCommissionOperations'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteMouvement(Request $request)
    {
        try{
            $compteMvts = CompteMouvement::where('deleted',0)->get();
            return view('compte.mouvement',compact('compteMvts'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteMouvementAdd(Request $request)
    {
        try{
            CompteMouvement::create([
                'id' => Uuid::uuid4()->toString(),
                'libelle' => $request->libelle,
                'solde' => 0,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return back()->withSuccess("Type de compte enregistré avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteMouvementEdit(Request $request)
    {   
        try{
            $compteMvt = CompteMouvement::where('id',$request->id)->where('deleted',0)->first();
            $compteMvt->libelle = $request->libelle;
            $compteMvt->save();
            return back()->withSuccess("Modification effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteMouvementDelete(Request $request)
    {   
        try{
            $compteMvt = CompteMouvement::where('id',$request->id)->where('deleted',0)->first();
            $compteMvt->deleted = 1;
            $compteMvt->save();
            return back()->withSuccess("Suppression effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function searchMouvementCompte(Request $request)
    {   
        try{
            $debut = $request->debut.' 00:00:00';
            $fin = $request->fin.' 23:59:59';
            $compteCommission = CompteCommission::where('id',$request->id)->where('deleted',0)->first();
            $compteCommissionOperations = CompteCommissionOperation::where('compte_commission_id',$compteCommission->id)->orderBy('created_at','desc')->whereBetween('created_at', [$request->debut, $request->fin])->get();
            return view('compte.search_detail_commission',compact('compteCommission','debut','fin','compteCommissionOperations'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function downloadMouvementCompte(Request $request)
    {
        try{
            $debut = $request->debut;
            $fin = $request->fin;
            $compteCommission = CompteCommission::where('id',$request->id)->where('deleted',0)->first();
            $compteCommissionOperations = CompteCommissionOperation::where('compte_commission_id',$compteCommission->id)->orderBy('created_at','desc')->whereBetween('created_at', [$request->debut, $request->fin])->get();

            $pdf = FacadePdf::loadView('compte.download_detail_commission',compact('compteCommissionOperations','debut','fin','compteCommission'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download ('Releve de '.$compteCommission->libelle.' du '.$debut.' au '.$fin.'.pdf');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function compteSolde(Request $request)
    {
        try{
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
    
            $solde = [
                'gtp' => 0,
                'bmo_debit' => 0,
                'bmo_credit' => 0,
                'kkiapay' => 0,
                'compte_partenaire' => 0
            ];
    
            $nbClients = count(UserClient::where('deleted',0)->where('status',1)->get());
            $nbPartenaires = count(Partenaire::where('deleted',0)->get());
    
            try {    
                $client = new Client();
                $url = $base_url."accounts/".$accountId."/balance";
        
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                ];
        
                $auth = [
                    $authLogin,
                    $authPass
                ];
    
                $response = $client->request('GET', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                ]);
        
                $solde['gtp'] = json_decode($response->getBody())->balance;
                $solde['compte_partenaire'] = AccountDistribution::where('deleted',0)->sum('solde');
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return back()->withError($error);
            }

            
            $base_url_kkp = env('BASE_KKIAPAY');    
            $client = new Client();
            $url = $base_url_kkp."/api/v1/account/info";            
            $headers = [
                'x-private-key' => env('PRIVATE_KEY_KKIAPAY'),
                'x-secret-key' => env('SECRET_KEY_KKIAPAY'),
                'x-api-key' => env('API_KEY_KKIAPAY')
            ];
            $response = $client->request('GET', $url, [
                'headers' => $headers
            ]);            
            $solde['kkiapay'] = json_decode($response->getBody())->waiting_payout_amount;
            //$solde['kkiapay'] = 0;
    
            return view('compte.solde',compact('solde'));
        } catch (\Exception $e) {
            dd($e);
            return back()->withError($e->getMessage());
        }
    }
}