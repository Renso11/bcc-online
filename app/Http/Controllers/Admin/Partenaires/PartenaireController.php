<?php

namespace App\Http\Controllers\Admin\Partenaires;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Partenaire;
use App\Models\AccountCommission;
use App\Models\AccountDistribution;
use App\Models\AccountVente;
use App\Models\AccountCommissionOperation;
use App\Models\AccountDistributionOperation;
use App\Models\UserPartenaire;
use App\Models\RechargementPartenaire;
use App\Models\Depot;
use App\Models\Retrait;
use Illuminate\Support\Facades\Hash;
use App\Mail\MailAlerte;
use App\Models\ApiPartenaireAccount;
use App\Models\ApiPartenaireFee;
use App\Models\ApiPartenaireTransaction;
use App\Models\PartnerAllWallet;
use App\Models\PartnerAllWalletDetail;
use App\Models\PartnerCession;
use App\Models\PartnerWalletDeposit;
use App\Models\PartnerWalletWithdraw;
use App\Models\UserCardBuy;
use App\Models\Role;
use App\Services\CardService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use App\Services\PaiementService;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class PartenaireController extends Controller
{
    public function partenaires(Request $request){
        try{
            $partenaires = Partenaire::where('deleted',0)->get();
            $roles = Role::where('deleted',0)->where('type','partner')->get();  
            return view('partenaires.index',compact('partenaires','roles'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }
    
    public function partenaireDetails(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->first();
            $roles = Role::where('deleted',0)->where('type','partner')->get();  

            $depots = Depot::where('partenaire_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at', 'desc')->get();
            $retraits = Retrait::where('partenaire_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at', 'desc')->get();
            $appros = PartnerWalletDeposit::where('partenaire_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at', 'desc')->get();
            $cashouts = PartnerWalletWithdraw::where('partenaire_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at', 'desc')->get();
            $cessions = PartnerCession::where('partenaire_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at', 'desc')->get();
            $cards = UserCardBuy::where('partenaire_id',$request->id)->where('deleted',0)->where('status','completed')->orderBy('created_at', 'desc')->get();
            return view('partenaires.detail',compact('partenaire','roles','depots','retraits','appros','cashouts','cessions','cards'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireNew(){
        try{
            return view('partenaires.new');
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireAdd(Request $request){
        try{
            $nbCompte = Partenaire::where('deleted',0)->count();
            $codeBcc = 'BCC-ELG-'.$nbCompte+1;

            if($request->rccm){
                $rccm = Uuid::uuid4()->toString().'.'.$request->rccm->getClientOriginalExtension();
                $request->rccm->move('storage/partenaire/rccm/', $rccm);
                $url_rccm = 'storage/partenaire/rccm/'.$rccm;
            }
            if($request->ifu){
                $ifu = Uuid::uuid4()->toString().'.'.$request->ifu->getClientOriginalExtension();
                $request->ifu->move('storage/partenaire/ifu/', $ifu);
                $url_ifu = 'storage/partenaire/ifu/'.$ifu;
            }
            
            $partenaire = Partenaire::create([
                'id' => Uuid::uuid4()->toString(),
                'libelle' => $request->libelle,
                'email' => $request->email,
                'telephone' => $request->phone_full,
                'rccm' => $url_rccm,
                'ifu' => $url_ifu,
                'num_rccm' => $request->num_rccm,
                'num_ifu' => $request->num_ifu,
                'code_partenaire' => $codeBcc,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            AccountCommission::create([
                'id' => Uuid::uuid4()->toString(),
                'solde' => 0,
                'partenaire_id' => $partenaire->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            AccountDistribution::create([
                'id' => Uuid::uuid4()->toString(),
                'solde' => 0,
                'partenaire_id' => $partenaire->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            try{
                $message = 'Votre compte BCC partenaire a ete cree avec succes. Patientez le temps de la creation de vos comptes utilisateurs pour vous connectez.';
                sendSms($request->phone_full,$message);

                $arr = ['messages'=> $message,'objet'=>'Compte partenaire crée','from'=>'bmo-uba-noreply@bestcash.me'];
                Mail::to($request->email)->send(new MailAlerte($arr));
            } catch (\Exception $e) {
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de mail.', 'timestamp' => Carbon::now()];  
                writeLog($message);
            }

            return redirect()->route('admin.partenaires')->withSuccess("Partenaire enregistré avec succès");

        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireEdit(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->where('deleted',0)->first();
            return view('partenaires.edit',compact('partenaire'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireUpdate(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->where('deleted',0)->first();

            $partenaire->libelle = $request->libelle;
            $partenaire->email = $request->email;
            $partenaire->telephone = $request->phone_full;
            $partenaire->num_rccm = $request->num_rccm;
            $partenaire->num_ifu = $request->num_ifu;

            if($request->rccm){
                $rccm = time().'.'.$request->rccm->getClientOriginalExtension();
                $request->rccm->move('storage/partenaire/rccm/', $rccm);
                $url_rccm = 'storage/partenaire/rccm/'.$rccm;
                $partenaire->rccm = $url_rccm;
            }
            if($request->ifu){
                $ifu = time().'.'.$request->ifu->getClientOriginalExtension();
                $request->ifu->move('storage/partenaire/ifu/', $ifu);
                $url_ifu = 'storage/partenaire/ifu/'.$ifu;
                $partenaire->ifu = $url_ifu;
            }
            $partenaire->save();
            return redirect()->route('admin.partenaires')->withSuccess("Modification effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireDelete(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->where('deleted',0)->first();

            $partenaire->deleted = 1;
            $partenaire->updated_at = Carbon::now();
            $partenaire->save();
            return redirect()->route('admin.partenaires')->withSuccess("Suppression effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }




    public function viewPartenaireReleve(Request $request,CardService $cardService){
        try{
            $partenaire = Partenaire::where('id',$request->partenaire_id)->first();
            $debut = $request->debut.' 00:00:00';
            $fin = $request->fin.' 23:59:59';


            $transactions = $cardService->getPartenaireOperation($partenaire->id, $debut, $fin);
            return view('partenaires.releve.view',compact('transactions','debut','fin','partenaire'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function downloadPartenaireReleve(Request $request,CardService $cardService){
        try{
            $partenaire = Partenaire::where('id',$request->partenaire_id)->first();
            $debut = $request->debut.' 00:00:00';
            $fin = $request->fin.' 23:59:59';

            $transactions = $cardService->getPartenaireOperation($partenaire->id, $debut, $fin);
            $pdf = FacadePdf::loadView('partenaires.releve.pdf',compact('transactions','debut','fin','partenaire'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download ('Releve de '.$partenaire->libelle.' du '.$debut.' au '.$fin.'.pdf');

        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function partenaireRechargeInit(Request $request){
        $partenaire = Partenaire::where('id',$request->id)->where('deleted',0)->first();

        RechargementPartenaire::create([
            'id' => Uuid::uuid4()->toString(),
            'partenaire_id' => $partenaire->id,
            'montant' => $request->montant,
            'user_id' => Auth::user()->id,
            'deleted' => 0,
            'status' => 'pending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('admin.partenaires')->withSuccess("Rechargement initié avec succès");
    }

    public function partenaireRechargeAttentes(Request $request){
        try {
            $recharges = RechargementPartenaire::where('deleted',0)->where('status','pending')->orderBy('created_at','desc')->get();
            return view('partenaires.rechargeAttentes',compact('recharges'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        } 
    }
    
    public function partenaireRechargeValidation(Request $request){
        try{
            $rechargement = RechargementPartenaire::where('id',$request->id)->where('deleted',0)->first();
            $montant = $rechargement->montant;
            $compteSolde = PartnerAllWallet::first();
    
            if($compteSolde->solde < (int)$montant){
                return redirect()->route('admin.partenaires')->withWarning('Solde du compte de mouvements des partenaires insuffisant!!');
            }

            $partenaire = $rechargement->partenaire;
            $compteDistribution = $partenaire->accountDistribution;

            $compteSolde->solde -= $montant;
            $compteSolde->save();
            PartnerAllWalletDetail::create([
                'id' => Uuid::uuid4()->toString(),
                'libelle' => 'Approvisionnement du compte du partenaire '.$partenaire->code_partenaire.'.',
                'sens' => 'debit',
                'amount' => $montant,
                'partenaire_id' => $partenaire->id,
                'reference' => $rechargement->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);   

            $compteDistribution->solde += $montant;
            $compteDistribution->save();
            AccountDistributionOperation::create([
                'id' => Uuid::uuid4()->toString(),
                'solde_avant' => $compteDistribution->solde,
                'montant' => $montant,
                'solde_apres' => $compteDistribution->solde + $montant,
                'libelle' => 'Rechargement du compte de distribution',
                'type' => 'credit',
                'deleted' => 0,
                'rechargement_partenaire_id' =>  $rechargement->id,
                'account_distribution_id' =>  $compteDistribution->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $rechargement->status = 'completed';
            $rechargement->save();
            
            return redirect()->route('admin.partenaires')->withSuccess("Rechargement validé avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireOperationsAttentes(Request $request){
        try{
            $transactions = Depot::with('partenaire')->with('userClient')
            ->select('id','created_at', 'partenaire_id','user_client_id', DB::raw("'Depot' as type"), 'montant', 'frais')
            ->where('status', 'pending')->where('is_debited', 1)->where('deleted', 0)

            ->union(PartnerWalletWithdraw::with('partenaire')->with('wallet')
            ->select('id','created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Cashout' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'pending')->where('is_debited', 1)->where('deleted', 0))

            ->union(PartnerWalletDeposit::with('partenaire')->with('wallet')
            ->select('id','created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Approvisionnement' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'pending')->where('is_debited', 1)->where('deleted', 0))

            ->union(PartnerCession::with('partenaire')
            ->select('id','created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Cession' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'pending')->where('deleted', 0))

            ->orderBy('created_at','desc')->get();
            
            return view('partenaires.operations.attentes',compact('transactions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireOperationsAttentesRefund(Request $request, PaiementService $paiementService){
        try{            
            if($request->type_operation == 'Depot'){
                $depot = Depot::where('id', $request->id)->first();
    
                $distribution_account = AccountDistribution::where('partenaire_id',$depot->partenaire_id)->where('deleted',0)->first();
                $soldeAvDepot = $distribution_account->solde;     
                $distribution_account->solde += $depot->montant;
                $distribution_account->save();
                $soldeApDepot = $distribution_account->solde;

                AccountDistributionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'solde_avant' => $soldeAvDepot,
                    'montant' => $depot->montant,
                    'solde_apres' => $soldeApDepot,
                    'libelle' => 'Remboursement de depot client en attente.',
                    'type' => 'credit',
                    'account_distribution_id' => $distribution_account->id,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $depot->status = 'refunded';
                $depot->refunder_id = Auth::user()->id;
                $depot->refunded_at = Carbon::now();
                $depot->save();
                
            }else if($request->type_operation == 'Approvisionnement'){
                $appro = PartnerWalletDeposit::where('id', $request->id)->first();                
                
                $response = $paiementService->getPayment($appro->wallet->type,$appro->reference);
                
                if(!$response){
                    return back()->withWarning('Le paiment n\'existe pas');
                }
                
                if($response->amount != $appro->montant){
                    return back()->withWarning('Le montant que vous essayez de rembourser ne correspond pas à celui de la transaction');
                }
                
                if ($appro->wallet->type == 'bmo') {
                    $bmoCredited = $paiementService->bmoCredited('+'.$appro->wallet->phone_code.$appro->wallet->phone, 'part', 'part', $response->amount,Auth::user());

                    if($bmoCredited != false){                        
                        $appro->status = 'refunded';
                        $appro->refunded_at = Carbon::now();
                        $appro->refunder_id = Auth::user()->id;
                        $appro->refunded_reference = $response->reference;
                        $appro->save();
                            
                        //Voir que message envoyé au client a l'annulation

                        $message = ['success' => true, 'status' => 200,'message' => 'Remboursement du rechargement client','timestamp' => Carbon::now(),'user' => Auth::user()->id]; 
                        writeLog($message);  
                    }else{
                        return back()->withWarning("Echec lors du remboursement de la transaction");                        
                    }
                }else{
                    $momoCredited = $paiementService->momoCredited($response->client->phone, $response->amount);
                    if($momoCredited == "FAILED"){
                        return back()->withWarning("Echec lors du remboursement de la transaction");
                    }else if($momoCredited == "FAILED_TIME"){
                        return back()->withWarning("Echec du a un temps d'attente trop long");
                    }else{
                        $appro->status = 'refunded';
                        $appro->refunded_at = Carbon::now();
                        $appro->refunder_id = Auth::user()->id;
                        $appro->refunded_reference = $momoCredited->transactionId;
                        $appro->save();
                            
                        //Voir que message envoyé au client a l'annulation

                        $message = ['success' => true, 'status' => 200,'message' => 'Remboursement du rechargement client','timestamp' => Carbon::now(),'user' => Auth::user()->id]; 
                        writeLog($message); 
                    }
                }

            }else if($request->type_operation == 'Withdraw'){
                $withdraw = PartnerWalletWithdraw::where('id', $request->id)->first();
    
                $commission_account = AccountCommission::where('partenaire_id',$withdraw->partenaire_id)->where('deleted',0)->first();      
                $soldeAvDepot = $commission_account->solde;     
                $commission_account->solde += $withdraw->montant;
                $commission_account->save();
                $soldeApDepot = $commission_account->solde ;

                AccountDistributionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'solde_avant' => $soldeAvDepot,
                    'montant' => $withdraw->montant,
                    'solde_apres' => $soldeApDepot,
                    'libelle' => 'Remboursement de cashout partenaire en attente.',
                    'type' => 'credit',
                    'account_distribution_id' => $commission_account->id,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $withdraw->status = 'refunded';
                $withdraw->refunder_id = Auth::user()->id;
                $withdraw->refunded_at = Carbon::now();
                $withdraw->save();
            }else{
                $cession = PartnerCession::where('id', $request->id)->first();
    
                $commission_account = AccountDistribution::where('partenaire_id',$cession->partenaire_id)->where('deleted',0)->first();      
                $soldeAvDepot = $commission_account->solde;     
                $commission_account->solde += $cession->montant;
                $commission_account->save();
                $soldeApDepot = $commission_account->solde ;

                AccountDistributionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'solde_avant' => $soldeAvDepot,
                    'montant' => $cession->montant,
                    'solde_apres' => $soldeApDepot,
                    'libelle' => 'Remboursement de cession partenaire en attente.',
                    'type' => 'credit',
                    'account_commission_id' => $commission_account->id,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $cession->status = 'refunded';
                $cession->refunder_id = Auth::user()->id;
                $cession->refunded_at = Carbon::now();
                $cession->save();

            }
            return redirect()->route('admin.partenaires')->withSuccess('Remboursement effectué avec succes');
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireOperationsAttentesCancel(Request $request){
        try{
            if($request->type_operation == 'Depot'){
                $depot = Depot::where('id', $request->id)->first();                   
                $depot->status = 'cancelled';
                $depot->cancel_motif = $request->motif_cancel;
                $depot->cancelled_at = Carbon::now();
                $depot->canceller_id = Auth::user()->id;
                $depot->save();                
            }else if($request->type_operation == 'Approvisionnement'){
                $appro = PartnerWalletDeposit::where('id', $request->id)->first();                   
                $appro->status = 'cancelled';
                $appro->cancel_motif = $request->motif_cancel;
                $appro->cancelled_at = Carbon::now();
                $appro->canceller_id = Auth::user()->id;
                $appro->save();
            }else if($request->type_operation == 'Withdraw'){
                $withdraw = PartnerWalletWithdraw::where('id', $request->id)->first();                   
                $withdraw->status = 'cancelled';
                $withdraw->cancel_motif = $request->motif_cancel;
                $withdraw->cancelled_at = Carbon::now();
                $withdraw->canceller_id = Auth::user()->id;
                $withdraw->save();
            }else{
                $cession = PartnerCession::where('id', $request->id)->first();                   
                $cession->status = 'cancelled';
                $cession->cancel_motif = $request->motif_cancel;
                $cession->cancelled_at = Carbon::now();
                $cession->canceller_id = Auth::user()->id;
                $cession->save();
            }
            return redirect()->route('admin.partenaires')->withSuccess('Annulation effectué avec succes');
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    /*public function partenaireOperationsAttentesCompleted(Request $request){
        try{
            
            $depots = Depot::where('status','pending')->where('deleted',0)->orderBy('created_at','desc')->get();
            foreach($depots as $depot){
                $depot->date = $depot->created_at->format('d-m-Y H:i:s');
                $depot->type = 'Depot';
                $depot->partenaire = $depot->partenaire;
                $depot->userClient = $depot->userClient;
            }

            $retraits = Retrait::where('status','pending')->where('deleted',0)->orderBy('created_at','desc')->get();
            foreach($retraits as $retrait){
                $retrait->date = $retrait->created_at->format('d-m-Y H:i:s');
                $retrait->type = 'Retrait';
                $retrait->partenaire = $retrait->partenaire;
                $retrait->userClient = $retrait->userClient;
            }
            
            $transactions = array_merge($depots->toArray(), $retraits->toArray());
            
        
            usort($transactions, 'date_compare');
            return view('partenaires.operations.attentes',compact('transactions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }*/


    public function partenaireOperationsFinalises(Request $request){
        try{
            $transactions = Depot::with('partenaire')->with('userClient')
            ->select('id', 'created_at', 'partenaire_id','user_client_id', DB::raw("'Depot' as type"), 'montant', 'frais')
            ->where('status', 'completed')->where('deleted', 0)

            ->union(Retrait::with('partenaire')->with('userClient')
            ->select('id', 'created_at', 'partenaire_id','user_client_id', DB::raw("'Retrait' as type"), 'montant', 'frais')
            ->where('status', 'completed')->where('deleted', 0))

            ->union(PartnerWalletWithdraw::with('partenaire')->with('userClient')
            ->select('id', 'created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Withdrawl' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('deleted', 0))

            ->union(PartnerWalletDeposit::with('partenaire')
            ->select('id', 'created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Approvisionnement' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('deleted', 0))

            ->union(PartnerCession::with('partenaire')
            ->select('id', 'created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Cession' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('deleted', 0))

            ->orderBy('created_at','desc')->get();
            return view('partenaires.operations.finalises',compact('transactions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireOperationsRemboursees(Request $request){
        try{
            $transactions = Depot::with('partenaire')->with('userClient')
            ->select('id','created_at', 'partenaire_id','user_client_id', DB::raw("'Depot' as type"), 'montant', 'frais')
            ->where('status', 'refunded')
            ->where('deleted', 0)

            ->union(PartnerWalletWithdraw::with('partenaire')->with('userClient')
            ->select('id','created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Withdrawl' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'refunded')->where('deleted', 0))

            ->union(PartnerWalletDeposit::with('partenaire')
            ->select('id','created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Approvisionnement' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'refunded')->where('deleted', 0))

            ->union(PartnerCession::with('partenaire')
            ->select('id','created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Cession' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'refunded')->where('deleted', 0))

            ->orderBy('created_at','desc')->get();

            return view('partenaires.operations.refunds',compact('transactions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireOperationsAnnulees(Request $request){
        try{            
            $transactions = Depot::with('partenaire')->with('userClient')
            ->select('id', 'created_at', 'partenaire_id','user_client_id', DB::raw("'Depot' as type"), 'montant', 'frais')
            ->where('status', 'cancelled')->where('deleted', 0)

            ->union(PartnerWalletWithdraw::with('partenaire')->with('userClient')
            ->select('id', 'created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Withdrawl' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'cancelled')->where('deleted', 0))

            ->union(PartnerWalletDeposit::with('partenaire')
            ->select('id', 'created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Approvisionnement' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'cancelled')->where('deleted', 0))

            ->union(PartnerCession::with('partenaire')
            ->select('id', 'created_at', 'partenaire_id', DB::raw('null as user_client_id'), DB::raw("'Cession' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'cancelled')->where('deleted', 0))

            ->orderBy('created_at','desc')->get();

            return view('partenaires.operations.annulees',compact('transactions'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    } 

    public function partenaireCompteCommission(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->first();

            $compteCommission = $partenaire->accountCommission;
            $operationsCompteCommission = AccountCommissionOperation::where('deleted',0)->where('account_commission_id',$partenaire->accountCommission->id)->orderBy('id','desc')->get()->all();   
            
            return view('partenaires.comptes.commission',compact('compteCommission','operationsCompteCommission'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireCompteDistribution(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->first();
            
            $compteDistribution = $partenaire->accountDistribution;
            $operationsCompteDistribution = AccountDistributionOperation::where('deleted',0)->where('account_distribution_id',$partenaire->accountDistribution->id)->orderBy('id','desc')->get()->all();   
            
            return view('partenaires.comptes.distribution',compact('compteDistribution','operationsCompteDistribution'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    
    
    public function partenaireUsers(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->first();
            $users = UserPartenaire::where('partenaire_id',$partenaire->id)->where('deleted',0)->get();
            return view('partenaires.users.index',compact('partenaire','users'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }
    
    public function partenaireNewUser(Request $request){
        try{
            $partenaire = Partenaire::where('id',$request->id)->first();
            $roles = Role::where('deleted',0)->where('type','partner')->get();  
            return view('partenaires.users.new',compact('partenaire','roles'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireUserAdd(Request $request){
        try{
            $password = generateRandomString();
            $partenaire = Partenaire::where('id',$request->id)->first();
            $username = strtolower(unaccent($request->name)[0].''.explode(' ',unaccent($request->lastname))[0]);

            $usernameExist = UserPartenaire::where('username',$username)->first();
            $i = 1;
            while($usernameExist != null){
                $username = $usernameExist->username.''.$i;
                $usernameExist = UserPartenaire::where('username',$username)->first();
                $i++;
            }
            
            UserPartenaire::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $request->name,
                'lastname' => $request->lastname,
                'telephone' => $request->phone_full,
                'role_id' => $request->role,
                'partenaire_id' => $partenaire->id,
                'username' => $username,
                'password' => Hash::make($password),
                'status' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            try{
                $message = 'Votre compte utilisateur lie au partenaire '.$partenaire->libelle.' a ete cree avec succes. Votre nom d\'utilisateur est: '.$username.' et votre mot de passe: '.$password.'. Vous etes prier de changer ce mot de passe à votre connexion.';
                sendSms($request->phone_full,$message);
            } catch (\Exception $e) {
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de sms a la creation du compte utilisateur.', 'timestamp' => Carbon::now()];  
                writeLog($message);
            }
            return redirect()->route('admin.partenaires')->withSuccess("Utilisateur ajouté avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }
    
    public function partenaireUserEdit(Request $request){
        try{
            $user = UserPartenaire::where('id',$request->id)->where('deleted',0)->first();

            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->role_partenaire_id = $request->role;
            $user->updated_at = Carbon::now();
            $user->save();
            return redirect()->route('admin.partenaires')->withSuccess("Modification effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireUserDelete(Request $request){
        try{
            $user = UserPartenaire::where('id',$request->id)->where('deleted',0)->first();
            $user->deleted = 1;
            $user->updated_at = Carbon::now();
            $user->save();
            return redirect()->route('admin.partenaires')->withSuccess("Supression effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireUserResetPassword(Request $request){
        try{
            $password = generateRandomString();
            $user = UserPartenaire::where('id',$request->id)->where('deleted',0)->first();

            $user->password = Hash::make($password);
            $user->updated_at = Carbon::now();
            $user->save();
            
            try{
                $message = 'Le mot de passe de votre compte utilisateur lie au partenaire '.$user->partenaire->libelle.' a ete reinitialiser. Votre nouveau mot de passe est: '.$password.'. Vous etes prier de changer ce mot de passe a votre connexion.';
                sendSms($user->telephone,$message);
            } catch (\Exception $e) {
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec d\'envoi de sms a la creation du compte utilisateur.', 'timestamp' => Carbon::now()];  
                writeLog($message);
            }
            return redirect()->route('admin.partenaires')->withSuccess("Reinitialisation effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireUserActivation(Request $request){
        try{
            $user = UserPartenaire::where('id',$request->id)->where('deleted',0)->first();

            $user->status = 1;
            $user->updated_at = Carbon::now();
            $user->save();
            return redirect()->route('admin.partenaires')->withSuccess("Activation effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireUserDesactivation(Request $request){
        try{
            $user = UserPartenaire::where('id',$request->id)->where('deleted',0)->first();

            $user->status = 0;
            $user->updated_at = Carbon::now();
            $user->save();
            return redirect()->route('admin.partenaires')->withSuccess("Desactivation effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }




    
    public function partenairesApi(Request $request)
    {
        try{
            $partenaires = ApiPartenaireAccount::where('deleted',0)->get();
            return view('partenairesApi.index',compact('partenaires'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireApiAdd(Request $request)
    {
        try{            
            $apiKey = bin2hex(random_bytes(24));
            $secretApiKey = 'sk_'.bin2hex(random_bytes(32));
            $privateApiKey = 'pk_'.bin2hex(random_bytes(32));

            ApiPartenaireAccount::create([
                        'id' => Uuid::uuid4()->toString(),
                'libelle' => $request->libelle,
                'name' => $request->name,
                'lastname' => $request->lastname,
                'address' => $request->address,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'balance' => 0,
                'api_key' => $apiKey,
                'secret_api_key' => $secretApiKey,
                'public_api_key' => $privateApiKey,
                'status' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('admin.partenaires')->withSuccess("Partenaire enregistré avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireApiEdit(Request $request)
    {   
        try{
            $partenaire = Partenaire::where('id',$request->id)->where('deleted',0)->first();

            $partenaire->libelle = $request->libelle;
            $partenaire->code = $request->code;
            $partenaire->last = $request->last;
            $partenaire->email = $request->email;
            $partenaire->telephone = $request->telephone;

            if($request->rccm){
                $rccm = time().'.'.$request->rccm->getClientOriginalExtension();
                $request->rccm->move('storage/partenaire/rccm/', $rccm);
                $url_rccm = 'storage/partenaire/rccm/'.$rccm;
                $partenaire->rccm = $url_rccm;
            }
            if($request->ifu){
                $ifu = time().'.'.$request->ifu->getClientOriginalExtension();
                $request->ifu->move('storage/partenaire/ifu/', $ifu);
                $url_ifu = 'storage/partenaire/ifu/'.$ifu;
                $partenaire->ifu = $url_ifu;
            }
            
            $partenaire->updated_at = Carbon::now();
            $partenaire->save();
            return redirect()->route('admin.partenaires')->withSuccess("Modification effectuée avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireApiRecharge(Request $request)
    {   
        try{            
            ApiPartenaireTransaction::create([
                'id' => Uuid::uuid4()->toString(),
                'api_partenaire_account_id' => $request->id,
                'type' => 'Appro',
                'reference' => 'APP-'.time(),
                'montant' => $request->montant,
                'frais' => 0,
                'commission' => 0,
                'libelle' => 'Approvisionnement du compte',
                'user_id' => Auth::user()->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return redirect()->route('admin.partenaires')->withSuccess("Approvisionnement initié avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireApiRechargeAttente(Request $request){
        try {
            $appros = ApiPartenaireTransaction::where('type','Appro')->where('deleted',0)->where('status',null)->get();
            return view('partenairesApi.approAttentes',compact('appros'));

        } catch (\Exception $e) {
            return  redirect()->route('admin.partenaires')->withError($e->getMessage());
        };
    }

    public function partenaireApiRechargeValidate(Request $request)
    {   
        try{     
            $appro = ApiPartenaireTransaction::where('id',$request->id)->first();

            $partenaire = ApiPartenaireAccount::where('id',$appro->apiPartenaireAccount->id)->first();
            $soldeAvant = $partenaire->balance;
            $partenaire->balance += $appro->montant;
            $partenaire->save();

            $appro->solde_avant = $soldeAvant;
            $appro->solde_apres = $partenaire->balance;
            $appro->status = 1;
            $appro->validate_id = Auth::user()->id;
            $appro->validate_time = Carbon::now();
            $appro->save();
            return redirect()->route('admin.partenaires')->withSuccess("Approvisionnement validé avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireApiRechargeUnvalidate(Request $request)
    {   
        try{     
            $appro = ApiPartenaireTransaction::where('id',$request->id)->first();
            $appro->status = 0;
            $appro->comment = $request->comment;
            $appro->validate_id = Auth::user()->id;
            $appro->validate_time = Carbon::now();
            $appro->save();
            return redirect()->route('admin.partenaires')->withSuccess("Approvisionnement rejetté avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenairesApiFee(Request $request)
    {   
        try{
            $fees = ApiPartenaireFee::where('deleted',0)->get();
            $partenaires = ApiPartenaireAccount::where('deleted',0)->get();
            return view('partenairesApi.fee',compact('fees','partenaires'));
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }

    public function partenaireApiFeeAdd(Request $request)
    {
        try{
            ApiPartenaireFee::create([
                        'id' => Uuid::uuid4()->toString(),
                'api_partenaire_account_id' => $request->partenaire,
                'type_fee' => $request->type_fee,
                'beguin' => $request->beguin,
                'end' => $request->end,
                'value' => $request->value,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return redirect()->route('admin.partenaires')->withSuccess("Frais enregistré avec succès");
        } catch (\Exception $e) {
            return redirect()->route('admin.partenaires')->withError($e->getMessage());
        }
    }    

    public function partenaireApiFeeEdit(Request $request){
        try {
            $fee = ApiPartenaireFee::where('id',$request->id)->where('deleted',0)->first();
            $fee->api_partenaire_account_id = $request->partenaire;
            $fee->type_fee = $request->type_fee;
            $fee->beguin = $request->beguin;
            $fee->end = $request->end;
            $fee->value = $request->value;
            $fee->save();
            return redirect()->route('admin.partenaires')->withSuccess('Modification effectué avec success');
        } catch (\Exception $e) {
            return  redirect()->route('admin.partenaires')->withError($e->getMessage());
        };
    }

    public function partenaireApiFeeDelete(Request $request){
        try {
            $fee = ApiPartenaireFee::where('id',$request->id)->where('deleted',0)->first();
            $fee->deleted = 1;
            $fee->save();
            return redirect()->route('admin.partenaires')->withSuccess('Suppression effectuée avec success');

        } catch (\Exception $e) {
            return  redirect()->route('admin.partenaires')->withError($e->getMessage());
        };
    }

    public function partenairesApiTransactions(Request $request){
        try {
            $transactions = ApiPartenaireTransaction::where('deleted',0)->get();
            $partners = ApiPartenaireAccount::where('deleted',0)->get();
            return view('partenairesApi.transactions',compact('transactions','partners'));

        } catch (\Exception $e) {
            return  redirect()->route('admin.partenaires')->withError($e->getMessage());
        };
    }

    public function partenairesApiFilterTransactions(Request $request){
        try {
            $transactions = ApiPartenaireTransaction::where('deleted',0);

            if($request->partner != 'all'){
                $transactions->where('api_partenaire_account_id',$request->partner);
            }

            if($request->type != 'all'){
                $transactions->where('type',$request->type);
            }

            if($request->status != 'all'){
                $request->status == 'null' ? $status = null : $status = (int)$request->status;
                $transactions->where('status',$status);
            }

            if($request->date != null){
                $debut = date($request->date.' 00:00:00');
                $fin = date($request->date.' 23:59:59');
                $transactions->whereBetween('created_at',[$debut, $fin]);
            }
            $transactions = $transactions->get();
            return view('partenairesApi.filterTransactions',compact('transactions'));

        } catch (\Exception $e) {
            return  redirect()->route('admin.partenaires')->withError($e->getMessage());
        };
    }
    
    public $countries = array(
        'AD'=>array('name'=>'ANDORRA','code'=>'376'),
        'AE'=>array('name'=>'UNITED ARAB EMIRATES','code'=>'971'),
        'AF'=>array('name'=>'AFGHANISTAN','code'=>'93'),
        'AG'=>array('name'=>'ANTIGUA AND BARBUDA','code'=>'1268'),
        'AI'=>array('name'=>'ANGUILLA','code'=>'1264'),
        'AL'=>array('name'=>'ALBANIA','code'=>'355'),
        'AM'=>array('name'=>'ARMENIA','code'=>'374'),
        'AN'=>array('name'=>'NETHERLANDS ANTILLES','code'=>'599'),
        'AO'=>array('name'=>'ANGOLA','code'=>'244'),
        'AQ'=>array('name'=>'ANTARCTICA','code'=>'672'),
        'AR'=>array('name'=>'ARGENTINA','code'=>'54'),
        'AS'=>array('name'=>'AMERICAN SAMOA','code'=>'1684'),
        'AT'=>array('name'=>'AUSTRIA','code'=>'43'),
        'AU'=>array('name'=>'AUSTRALIA','code'=>'61'),
        'AW'=>array('name'=>'ARUBA','code'=>'297'),
        'AZ'=>array('name'=>'AZERBAIJAN','code'=>'994'),
        'BA'=>array('name'=>'BOSNIA AND HERZEGOVINA','code'=>'387'),
        'BB'=>array('name'=>'BARBADOS','code'=>'1246'),
        'BD'=>array('name'=>'BANGLADESH','code'=>'880'),
        'BE'=>array('name'=>'BELGIUM','code'=>'32'),
        'BF'=>array('name'=>'BURKINA FASO','code'=>'226'),
        'BG'=>array('name'=>'BULGARIA','code'=>'359'),
        'BH'=>array('name'=>'BAHRAIN','code'=>'973'),
        'BI'=>array('name'=>'BURUNDI','code'=>'257'),
        'BJ'=>array('name'=>'BENIN','code'=>'229'),
        'BL'=>array('name'=>'SAINT BARTHELEMY','code'=>'590'),
        'BM'=>array('name'=>'BERMUDA','code'=>'1441'),
        'BN'=>array('name'=>'BRUNEI DARUSSALAM','code'=>'673'),
        'BO'=>array('name'=>'BOLIVIA','code'=>'591'),
        'BR'=>array('name'=>'BRAZIL','code'=>'55'),
        'BS'=>array('name'=>'BAHAMAS','code'=>'1242'),
        'BT'=>array('name'=>'BHUTAN','code'=>'975'),
        'BW'=>array('name'=>'BOTSWANA','code'=>'267'),
        'BY'=>array('name'=>'BELARUS','code'=>'375'),
        'BZ'=>array('name'=>'BELIZE','code'=>'501'),
        'CA'=>array('name'=>'CANADA','code'=>'1'),
        'CC'=>array('name'=>'COCOS (KEELING) ISLANDS','code'=>'61'),
        'CD'=>array('name'=>'CONGO, THE DEMOCRATIC REPUBLIC OF THE','code'=>'243'),
        'CF'=>array('name'=>'CENTRAL AFRICAN REPUBLIC','code'=>'236'),
        'CG'=>array('name'=>'CONGO','code'=>'242'),
        'CH'=>array('name'=>'SWITZERLAND','code'=>'41'),
        'CI'=>array('name'=>'COTE D IVOIRE','code'=>'225'),
        'CK'=>array('name'=>'COOK ISLANDS','code'=>'682'),
        'CL'=>array('name'=>'CHILE','code'=>'56'),
        'CM'=>array('name'=>'CAMEROON','code'=>'237'),
        'CN'=>array('name'=>'CHINA','code'=>'86'),
        'CO'=>array('name'=>'COLOMBIA','code'=>'57'),
        'CR'=>array('name'=>'COSTA RICA','code'=>'506'),
        'CU'=>array('name'=>'CUBA','code'=>'53'),
        'CV'=>array('name'=>'CAPE VERDE','code'=>'238'),
        'CX'=>array('name'=>'CHRISTMAS ISLAND','code'=>'61'),
        'CY'=>array('name'=>'CYPRUS','code'=>'357'),
        'CZ'=>array('name'=>'CZECH REPUBLIC','code'=>'420'),
        'DE'=>array('name'=>'GERMANY','code'=>'49'),
        'DJ'=>array('name'=>'DJIBOUTI','code'=>'253'),
        'DK'=>array('name'=>'DENMARK','code'=>'45'),
        'DM'=>array('name'=>'DOMINICA','code'=>'1767'),
        'DO'=>array('name'=>'DOMINICAN REPUBLIC','code'=>'1809'),
        'DZ'=>array('name'=>'ALGERIA','code'=>'213'),
        'EC'=>array('name'=>'ECUADOR','code'=>'593'),
        'EE'=>array('name'=>'ESTONIA','code'=>'372'),
        'EG'=>array('name'=>'EGYPT','code'=>'20'),
        'ER'=>array('name'=>'ERITREA','code'=>'291'),
        'ES'=>array('name'=>'SPAIN','code'=>'34'),
        'ET'=>array('name'=>'ETHIOPIA','code'=>'251'),
        'FI'=>array('name'=>'FINLAND','code'=>'358'),
        'FJ'=>array('name'=>'FIJI','code'=>'679'),
        'FK'=>array('name'=>'FALKLAND ISLANDS (MALVINAS)','code'=>'500'),
        'FM'=>array('name'=>'MICRONESIA, FEDERATED STATES OF','code'=>'691'),
        'FO'=>array('name'=>'FAROE ISLANDS','code'=>'298'),
        'FR'=>array('name'=>'FRANCE','code'=>'33'),
        'GA'=>array('name'=>'GABON','code'=>'241'),
        'GB'=>array('name'=>'UNITED KINGDOM','code'=>'44'),
        'GD'=>array('name'=>'GRENADA','code'=>'1473'),
        'GE'=>array('name'=>'GEORGIA','code'=>'995'),
        'GH'=>array('name'=>'GHANA','code'=>'233'),
        'GI'=>array('name'=>'GIBRALTAR','code'=>'350'),
        'GL'=>array('name'=>'GREENLAND','code'=>'299'),
        'GM'=>array('name'=>'GAMBIA','code'=>'220'),
        'GN'=>array('name'=>'GUINEA','code'=>'224'),
        'GQ'=>array('name'=>'EQUATORIAL GUINEA','code'=>'240'),
        'GR'=>array('name'=>'GREECE','code'=>'30'),
        'GT'=>array('name'=>'GUATEMALA','code'=>'502'),
        'GU'=>array('name'=>'GUAM','code'=>'1671'),
        'GW'=>array('name'=>'GUINEA-BISSAU','code'=>'245'),
        'GY'=>array('name'=>'GUYANA','code'=>'592'),
        'HK'=>array('name'=>'HONG KONG','code'=>'852'),
        'HN'=>array('name'=>'HONDURAS','code'=>'504'),
        'HR'=>array('name'=>'CROATIA','code'=>'385'),
        'HT'=>array('name'=>'HAITI','code'=>'509'),
        'HU'=>array('name'=>'HUNGARY','code'=>'36'),
        'ID'=>array('name'=>'INDONESIA','code'=>'62'),
        'IE'=>array('name'=>'IRELAND','code'=>'353'),
        'IL'=>array('name'=>'ISRAEL','code'=>'972'),
        'IM'=>array('name'=>'ISLE OF MAN','code'=>'44'),
        'IN'=>array('name'=>'INDIA','code'=>'91'),
        'IQ'=>array('name'=>'IRAQ','code'=>'964'),
        'IR'=>array('name'=>'IRAN, ISLAMIC REPUBLIC OF','code'=>'98'),
        'IS'=>array('name'=>'ICELAND','code'=>'354'),
        'IT'=>array('name'=>'ITALY','code'=>'39'),
        'JM'=>array('name'=>'JAMAICA','code'=>'1876'),
        'JO'=>array('name'=>'JORDAN','code'=>'962'),
        'JP'=>array('name'=>'JAPAN','code'=>'81'),
        'KE'=>array('name'=>'KENYA','code'=>'254'),
        'KG'=>array('name'=>'KYRGYZSTAN','code'=>'996'),
        'KH'=>array('name'=>'CAMBODIA','code'=>'855'),
        'KI'=>array('name'=>'KIRIBATI','code'=>'686'),
        'KM'=>array('name'=>'COMOROS','code'=>'269'),
        'KN'=>array('name'=>'SAINT KITTS AND NEVIS','code'=>'1869'),
        'KP'=>array('name'=>'KOREA DEMOCRATIC PEOPLES REPUBLIC OF','code'=>'850'),
        'KR'=>array('name'=>'KOREA REPUBLIC OF','code'=>'82'),
        'KW'=>array('name'=>'KUWAIT','code'=>'965'),
        'KY'=>array('name'=>'CAYMAN ISLANDS','code'=>'1345'),
        'KZ'=>array('name'=>'KAZAKSTAN','code'=>'7'),
        'LA'=>array('name'=>'LAO PEOPLES DEMOCRATIC REPUBLIC','code'=>'856'),
        'LB'=>array('name'=>'LEBANON','code'=>'961'),
        'LC'=>array('name'=>'SAINT LUCIA','code'=>'1758'),
        'LI'=>array('name'=>'LIECHTENSTEIN','code'=>'423'),
        'LK'=>array('name'=>'SRI LANKA','code'=>'94'),
        'LR'=>array('name'=>'LIBERIA','code'=>'231'),
        'LS'=>array('name'=>'LESOTHO','code'=>'266'),
        'LT'=>array('name'=>'LITHUANIA','code'=>'370'),
        'LU'=>array('name'=>'LUXEMBOURG','code'=>'352'),
        'LV'=>array('name'=>'LATVIA','code'=>'371'),
        'LY'=>array('name'=>'LIBYAN ARAB JAMAHIRIYA','code'=>'218'),
        'MA'=>array('name'=>'MOROCCO','code'=>'212'),
        'MC'=>array('name'=>'MONACO','code'=>'377'),
        'MD'=>array('name'=>'MOLDOVA, REPUBLIC OF','code'=>'373'),
        'ME'=>array('name'=>'MONTENEGRO','code'=>'382'),
        'MF'=>array('name'=>'SAINT MARTIN','code'=>'1599'),
        'MG'=>array('name'=>'MADAGASCAR','code'=>'261'),
        'MH'=>array('name'=>'MARSHALL ISLANDS','code'=>'692'),
        'MK'=>array('name'=>'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF','code'=>'389'),
        'ML'=>array('name'=>'MALI','code'=>'223'),
        'MM'=>array('name'=>'MYANMAR','code'=>'95'),
        'MN'=>array('name'=>'MONGOLIA','code'=>'976'),
        'MO'=>array('name'=>'MACAU','code'=>'853'),
        'MP'=>array('name'=>'NORTHERN MARIANA ISLANDS','code'=>'1670'),
        'MR'=>array('name'=>'MAURITANIA','code'=>'222'),
        'MS'=>array('name'=>'MONTSERRAT','code'=>'1664'),
        'MT'=>array('name'=>'MALTA','code'=>'356'),
        'MU'=>array('name'=>'MAURITIUS','code'=>'230'),
        'MV'=>array('name'=>'MALDIVES','code'=>'960'),
        'MW'=>array('name'=>'MALAWI','code'=>'265'),
        'MX'=>array('name'=>'MEXICO','code'=>'52'),
        'MY'=>array('name'=>'MALAYSIA','code'=>'60'),
        'MZ'=>array('name'=>'MOZAMBIQUE','code'=>'258'),
        'NA'=>array('name'=>'NAMIBIA','code'=>'264'),
        'NC'=>array('name'=>'NEW CALEDONIA','code'=>'687'),
        'NE'=>array('name'=>'NIGER','code'=>'227'),
        'NG'=>array('name'=>'NIGERIA','code'=>'234'),
        'NI'=>array('name'=>'NICARAGUA','code'=>'505'),
        'NL'=>array('name'=>'NETHERLANDS','code'=>'31'),
        'NO'=>array('name'=>'NORWAY','code'=>'47'),
        'NP'=>array('name'=>'NEPAL','code'=>'977'),
        'NR'=>array('name'=>'NAURU','code'=>'674'),
        'NU'=>array('name'=>'NIUE','code'=>'683'),
        'NZ'=>array('name'=>'NEW ZEALAND','code'=>'64'),
        'OM'=>array('name'=>'OMAN','code'=>'968'),
        'PA'=>array('name'=>'PANAMA','code'=>'507'),
        'PE'=>array('name'=>'PERU','code'=>'51'),
        'PF'=>array('name'=>'FRENCH POLYNESIA','code'=>'689'),
        'PG'=>array('name'=>'PAPUA NEW GUINEA','code'=>'675'),
        'PH'=>array('name'=>'PHILIPPINES','code'=>'63'),
        'PK'=>array('name'=>'PAKISTAN','code'=>'92'),
        'PL'=>array('name'=>'POLAND','code'=>'48'),
        'PM'=>array('name'=>'SAINT PIERRE AND MIQUELON','code'=>'508'),
        'PN'=>array('name'=>'PITCAIRN','code'=>'870'),
        'PR'=>array('name'=>'PUERTO RICO','code'=>'1'),
        'PT'=>array('name'=>'PORTUGAL','code'=>'351'),
        'PW'=>array('name'=>'PALAU','code'=>'680'),
        'PY'=>array('name'=>'PARAGUAY','code'=>'595'),
        'QA'=>array('name'=>'QATAR','code'=>'974'),
        'RO'=>array('name'=>'ROMANIA','code'=>'40'),
        'RS'=>array('name'=>'SERBIA','code'=>'381'),
        'RU'=>array('name'=>'RUSSIAN FEDERATION','code'=>'7'),
        'RW'=>array('name'=>'RWANDA','code'=>'250'),
        'SA'=>array('name'=>'SAUDI ARABIA','code'=>'966'),
        'SB'=>array('name'=>'SOLOMON ISLANDS','code'=>'677'),
        'SC'=>array('name'=>'SEYCHELLES','code'=>'248'),
        'SD'=>array('name'=>'SUDAN','code'=>'249'),
        'SE'=>array('name'=>'SWEDEN','code'=>'46'),
        'SG'=>array('name'=>'SINGAPORE','code'=>'65'),
        'SH'=>array('name'=>'SAINT HELENA','code'=>'290'),
        'SI'=>array('name'=>'SLOVENIA','code'=>'386'),
        'SK'=>array('name'=>'SLOVAKIA','code'=>'421'),
        'SL'=>array('name'=>'SIERRA LEONE','code'=>'232'),
        'SM'=>array('name'=>'SAN MARINO','code'=>'378'),
        'SN'=>array('name'=>'SENEGAL','code'=>'221'),
        'SO'=>array('name'=>'SOMALIA','code'=>'252'),
        'SR'=>array('name'=>'SURINAME','code'=>'597'),
        'ST'=>array('name'=>'SAO TOME AND PRINCIPE','code'=>'239'),
        'SV'=>array('name'=>'EL SALVADOR','code'=>'503'),
        'SY'=>array('name'=>'SYRIAN ARAB REPUBLIC','code'=>'963'),
        'SZ'=>array('name'=>'SWAZILAND','code'=>'268'),
        'TC'=>array('name'=>'TURKS AND CAICOS ISLANDS','code'=>'1649'),
        'TD'=>array('name'=>'CHAD','code'=>'235'),
        'TG'=>array('name'=>'TOGO','code'=>'228'),
        'TH'=>array('name'=>'THAILAND','code'=>'66'),
        'TJ'=>array('name'=>'TAJIKISTAN','code'=>'992'),
        'TK'=>array('name'=>'TOKELAU','code'=>'690'),
        'TL'=>array('name'=>'TIMOR-LESTE','code'=>'670'),
        'TM'=>array('name'=>'TURKMENISTAN','code'=>'993'),
        'TN'=>array('name'=>'TUNISIA','code'=>'216'),
        'TO'=>array('name'=>'TONGA','code'=>'676'),
        'TR'=>array('name'=>'TURKEY','code'=>'90'),
        'TT'=>array('name'=>'TRINIDAD AND TOBAGO','code'=>'1868'),
        'TV'=>array('name'=>'TUVALU','code'=>'688'),
        'TW'=>array('name'=>'TAIWAN, PROVINCE OF CHINA','code'=>'886'),
        'TZ'=>array('name'=>'TANZANIA, UNITED REPUBLIC OF','code'=>'255'),
        'UA'=>array('name'=>'UKRAINE','code'=>'380'),
        'UG'=>array('name'=>'UGANDA','code'=>'256'),
        'US'=>array('name'=>'UNITED STATES','code'=>'1'),
        'UY'=>array('name'=>'URUGUAY','code'=>'598'),
        'UZ'=>array('name'=>'UZBEKISTAN','code'=>'998'),
        'VA'=>array('name'=>'HOLY SEE (VATICAN CITY STATE)','code'=>'39'),
        'VC'=>array('name'=>'SAINT VINCENT AND THE GRENADINES','code'=>'1784'),
        'VE'=>array('name'=>'VENEZUELA','code'=>'58'),
        'VG'=>array('name'=>'VIRGIN ISLANDS, BRITISH','code'=>'1284'),
        'VI'=>array('name'=>'VIRGIN ISLANDS, U.S.','code'=>'1340'),
        'VN'=>array('name'=>'VIET NAM','code'=>'84'),
        'VU'=>array('name'=>'VANUATU','code'=>'678'),
        'WF'=>array('name'=>'WALLIS AND FUTUNA','code'=>'681'),
        'WS'=>array('name'=>'SAMOA','code'=>'685'),
        'XK'=>array('name'=>'KOSOVO','code'=>'381'),
        'YE'=>array('name'=>'YEMEN','code'=>'967'),
        'YT'=>array('name'=>'MAYOTTE','code'=>'262'),
        'ZA'=>array('name'=>'SOUTH AFRICA','code'=>'27'),
        'ZM'=>array('name'=>'ZAMBIA','code'=>'260'),
        'ZW'=>array('name'=>'ZIMBABWE','code'=>'263')
    );
}