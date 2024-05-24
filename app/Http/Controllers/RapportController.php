<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Depot;
use App\Models\Retrait;
use App\Models\Partenaire;
use App\Models\PartnerCession;
use App\Models\PartnerWalletDeposit;
use App\Models\PartnerWalletWithdraw;
use App\Models\Recharge;
use App\Models\RechargementPartenaire;
use App\Models\TransfertOut;
use App\Models\Apporteur;
use App\Models\UserCardBuy;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class RapportController extends Controller
{
    public function rapportTransactionClient(){
        return view('rapport.transactions.client');
    }

    public function searchTransactionClient(Request $request){
        $debut = $request->debut ? explode('T',$request->debut)[0].' '.explode('T',$request->debut)[1].':00' : null;
        $fin = $request->fin ? explode('T',$request->fin)[0].' '.explode('T',$request->fin)[1].':00' : null;

        $status = in_array('all', $request->status) ? ['pending','completed','cancelled','refunded'] : $request->status;
        //dump($request);die();
        $transactions = [];

        $nbDepots = $sumDepots = $sumFraisDepots = 0;
        if(($request->type_operations && in_array('depot',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $depots = Depot::whereIn('status',$status)->where('is_debited',1)->where('deleted',0);
            if($debut != null){
                $depots = $depots->where('created_at','>',$debut);
            }
            if($fin != null){
                $depots = $depots->where('created_at','<',$fin);
            }
            $sumDepots = $depots->sum('montant');
            $sumFraisDepots = $depots->sum('frais');
            $depots = $depots->orderBy('created_at','desc')->get();
            $nbDepots = count($depots);
            foreach($depots as $depot){
                $depot->date = $depot->created_at->format('d-m-Y H:i:s');
                $depot->type = 'Depot';
                $depot->partenaire = $depot->partenaire;
                $depot->userClient = $depot->userClient;
            }
            $transactions = array_merge($transactions, $depots->toArray());
        }

        $nbRetraits = $sumRetraits = $sumFraisRetraits = 0;
        if(($request->type_operations && in_array('retrait',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $retraits = Retrait::whereIn('status',$status)->where('deleted',0);
            if($debut != null){
                $retraits = $retraits->where('created_at','>',$debut);
            }
            if($fin != null){
                $retraits = $retraits->where('created_at','<',$fin);
            }
            $sumRetraits = $retraits->sum('montant');
            $sumFraisRetraits = $retraits->sum('frais');
            $retraits = $retraits->orderBy('created_at','desc')->get();
            $nbRetraits = count($retraits);
            foreach($retraits as $retrait){
                $retrait->date = $retrait->created_at->format('d-m-Y H:i:s');
                $retrait->type = 'Retrait';
                $retrait->partenaire = $retrait->partenaire;
                $retrait->userClient = $retrait->userClient;
            }
            $transactions = array_merge($transactions, $retraits->toArray());
        }

        $nbRecharges = $sumRecharges = $sumFraisRecharges = 0;
        if(($request->type_operations && in_array('rechargement',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $recharges = Recharge::whereIn('status',$status)->where('is_debited',1)->where('deleted',0);
            
            if($debut != null){
                $recharges = $recharges->where('created_at','>',$debut);
            }
            if($fin != null){
                $recharges = $recharges->where('created_at','<',$fin);
            }

            $sumRecharges = $recharges->sum('montant');
            $sumFraisRecharges = $recharges->sum('frais');
            $recharges = $recharges->orderBy('created_at','desc')->get();
            $nbRecharges = count($recharges);
            foreach($recharges as $recharge){
                $recharge->date = $recharge->created_at->format('d-m-Y H:i:s');
                $recharge->type = 'Recharge';
                $recharge->userClient = $recharge->userClient;
            }
            $transactions = array_merge($transactions, $recharges->toArray());
        }

        $nbTransferts = $sumTransferts = $sumFraisTransferts = 0;
        if(($request->type_operations && in_array('transfert',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $transferts = TransfertOut::whereIn('status',$status)->where('is_debited',1)->where('deleted',0);
            
            if($debut != null){
                $transferts = $transferts->where('created_at','>',$debut);
            }
            if($fin != null){
                $transferts = $transferts->where('created_at','<',$fin);
            }

            $sumTransferts = $transferts->sum('montant');
            $sumFraisTransferts = $transferts->sum('frais');
            $transferts = $transferts->get();
            $nbTransferts = count($transferts);
            foreach($transferts as $transfert){
                $transfert->date = $transfert->created_at->format('d-m-Y H:i:s');
                $transfert->type = 'Transfert';
                $transfert->userClient = $transfert->userClient;
            }
            $transactions = array_merge($transactions, $transferts->toArray());
        }
        
        usort($transactions, 'date_compare');

        $statNb = $statSum = $statFrais = [];

        $statNb['retrait'] = $nbRetraits;
        $statNb['transfert'] = $nbTransferts;
        $statNb['depot'] = $nbDepots;
        $statNb['recharge'] = $nbRecharges;
        
        $statSum['retrait'] = $sumRetraits;
        $statSum['transfert'] = $sumTransferts;
        $statSum['depot'] = $sumDepots;
        $statSum['recharge'] = $sumRecharges;
        
        $statFrais['retrait'] = $sumFraisRetraits;
        $statFrais['transfert'] = $sumFraisTransferts;
        $statFrais['depot'] = $sumFraisDepots;
        $statFrais['recharge'] = $sumFraisRecharges;

        session()->push('transaction_clients',compact('transactions','statNb','statSum','statFrais'));        
        return view('rapport.etat_transactions.client',compact('transactions','statNb','statSum','statFrais'));
    }

    public function downloadTransactionClient(Request $request){
        
        $lastKey = array_key_last(session()->get('transaction_clients'));
        $transaction_clients = session()->get('transaction_clients')[$lastKey];
        
        $pdf = FacadePdf::loadView('rapport.pdf_transactions.client',$transaction_clients);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download ('Rapport des transactions clients.pdf');
    }


    public function rapportAchatCarte(){
        return view('rapport.transactions.achat_carte');
    }

    public function searchAchatCarte(Request $request){
        $debut = $request->debut ? explode('T',$request->debut)[0].' '.explode('T',$request->debut)[1].':00' : null;
        $fin = $request->fin ? explode('T',$request->fin)[0].' '.explode('T',$request->fin)[1].':00' : null;


        if ($request->status == null){
            $status = ['pending','completed','cancelled','refunded'];
        }else{
            $status = in_array('all', $request->status) ? ['pending','completed','cancelled','refunded'] : $request->status;
        }

        $transactions = [];

        $nbBuys = $sumBuys = 0;

        $buys = UserCardBuy::whereIn('status',$status)->where('deleted',0);
        if($debut != null){
            $buys = $buys->where('created_at','>',$debut);
        }
        if($fin != null){
            $buys = $buys->where('created_at','<',$fin);
        }

        
        if ($request->promos !== null){
            if (in_array('with', $request->promos)){
                $buys = $buys->where('partenaire_id','<>',null);
            }else if (in_array('without', $request->promos)){
                $buys = $buys->where('partenaire_id',null);
            }
        }

        $sumBuys = $buys->sum('montant');
        $buys = $buys->orderBy('created_at','DESC')->get();
        $nbBuys = count($buys);

        foreach($buys as $buy){
            $buy->date = $buy->created_at->format('d-m-Y H:i:s');
            $buy->userClient;
        }

        session()->push('achat_cards',compact('buys','sumBuys','nbBuys'));        
        return view('rapport.etat_transactions.achat_carte',compact('buys','sumBuys','nbBuys'));
    }

    public function downloadAchatCarte(Request $request){
        
        $lastKey = array_key_last(session()->get('achat_cards'));
        $achat_cards = session()->get('achat_cards')[$lastKey];
        
        $pdf = FacadePdf::loadView('rapport.pdf_transactions.achat_carte',$achat_cards);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download ('Rapport des achats de cartes.pdf');
    }

    public function rapportTransactionPartenaire(){
        $partenaires = Partenaire::where('deleted',0)->get();
        return view('rapport.transactions.partenaire',compact('partenaires'));
    }

    public function searchTransactionPartenaire(Request $request){
        $debut = $request->debut ? explode('T',$request->debut)[0].' '.explode('T',$request->debut)[1].':00' : null;
        $fin = $request->fin ? explode('T',$request->fin)[0].' '.explode('T',$request->fin)[1].':00' : null;

        $status = in_array('all', $request->status) ? ['pending','completed','cancelled','refunded'] : $request->status;
        $partenaires = in_array('all', $request->partenaires) ? Partenaire::where('deleted',0)->pluck('id') : $request->partenaires;
        
        $transactions = [];

        $nbDepots = $sumDepots = $sumFraisDepots = 0;
        if(($request->type_operations && in_array('depot',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $depots = Depot::whereIn('status',$status)->where('deleted',0)->whereIn('partenaire_id',$partenaires);
            if($debut != null){
                $depots = $depots->where('created_at','>',$debut);
            }
            if($fin != null){
                $depots = $depots->where('created_at','<',$fin);
            }
            $sumDepots = $depots->sum('montant');
            $sumFraisDepots = $depots->sum('frais');
            $depots = $depots->orderBy('created_at','desc')->get();
            $nbDepots = count($depots);
            foreach($depots as $depot){
                $depot->date = $depot->created_at->format('d-m-Y H:i:s');
                $depot->type = 'Depot';
                $depot->partenaire = $depot->partenaire;
                $depot->userClient = $depot->userClient;
                $depot->userCard = $depot->userCard;
            }
            $transactions = array_merge($transactions, $depots->toArray());
        }

        $nbRetraits = $sumRetraits = $sumFraisRetraits = 0;
        if(($request->type_operations && in_array('retrait',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $retraits = Retrait::whereIn('status',$status)->where('deleted',0)->whereIn('partenaire_id',$partenaires);
            if($debut != null){
                $retraits = $retraits->where('created_at','>',$debut);
            }
            if($fin != null){
                $retraits = $retraits->where('created_at','<',$fin);
            }
            $sumRetraits = $retraits->sum('montant');
            $sumFraisRetraits = $retraits->sum('frais');
            $retraits = $retraits->orderBy('created_at','desc')->get();
            $nbRetraits = count($retraits);
            foreach($retraits as $retrait){
                $retrait->date = $retrait->created_at->format('d-m-Y H:i:s');
                $retrait->type = 'Retrait';
                $retrait->partenaire = $retrait->partenaire;
                $retrait->userClient = $retrait->userClient;
                $retrait->userCard = $retrait->userCard;
            }
            $transactions = array_merge($transactions, $retraits->toArray());
        }

        $nbApprovisionnements = $sumApprovisionnements = $sumFraisApprovisionnements = 0;
        if(($request->type_operations && in_array('approvisionnement',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $approvisionnements = RechargementPartenaire::whereIn('status',$status)->whereIn('partenaire_id',$partenaires)->where('deleted',0);
            
            if($debut != null){
                $approvisionnements = $approvisionnements->where('created_at','>',$debut);
            }
            if($fin != null){
                $approvisionnements = $approvisionnements->where('created_at','<',$fin);
            }

            $sumApprovisionnements = $approvisionnements->sum('montant');
            $sumFraisApprovisionnements = 0;
            $approvisionnements = $approvisionnements->orderBy('created_at','desc')->get();
            $nbApprovisionnements = count($approvisionnements);
            foreach($approvisionnements as $approvisionnement){
                $approvisionnement->date = $approvisionnement->created_at->format('d-m-Y H:i:s');
                $approvisionnement->type = 'Approvisionnement';
            }
            $transactions = array_merge($transactions, $approvisionnements->toArray());
        }

        $nbRecharges = $sumRecharges = $sumFraisRecharges = 0;
        if(($request->type_operations && in_array('rechargement',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $recharges = PartnerWalletDeposit::whereIn('status',$status)->whereIn('partenaire_id',$partenaires)->where('deleted',0);
            
            if($debut != null){
                $recharges = $recharges->where('created_at','>',$debut);
            }
            if($fin != null){
                $recharges = $recharges->where('created_at','<',$fin);
            }

            $sumRecharges = $recharges->sum('montant');
            $sumFraisRecharges = 0;
            $recharges = $recharges->orderBy('created_at','desc')->get();
            $nbRecharges = count($recharges);
            foreach($recharges as $recharge){
                $recharge->date = $recharge->created_at->format('d-m-Y H:i:s');
                $recharge->type = 'Recharge';
                $recharge->moyen_paiement = $recharge->wallet->type;
            }
            $transactions = array_merge($transactions, $recharges->toArray());
        }

        $nbCashouts = $sumCashouts = $sumFraisCashouts = 0;
        if(($request->type_operations && in_array('cashout',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $cashouts = PartnerWalletWithdraw::whereIn('status',$status)->whereIn('partenaire_id',$partenaires)->where('deleted',0);
            
            if($debut != null){
                $cashouts = $cashouts->where('created_at','>',$debut);
            }
            if($fin != null){
                $cashouts = $cashouts->where('created_at','<',$fin);
            }

            $sumCashouts = $cashouts->sum('montant');
            $sumFraisCashouts = 0;
            $cashouts = $cashouts->orderBy('created_at','desc')->get();
            $nbCashouts = count($cashouts);

            foreach($cashouts as $cashout){
                $cashout->date = $cashout->created_at->format('d-m-Y H:i:s');
                $cashout->type = 'Cashout';
                $cashout->moyen_paiement = $cashout->wallet->type;
            }
            $transactions = array_merge($transactions, $cashouts->toArray());
        }

        $nbCessions = $sumCessions = $sumFraisCessions = 0;
        if(($request->type_operations && in_array('cession',$request->type_operations)) || ($request->type_operations && in_array('all',$request->type_operations))){
            $cessions = PartnerCession::whereIn('status',$status)->whereIn('partenaire_id',$partenaires)->where('deleted',0);
            
            if($debut != null){
                $cessions = $cessions->where('created_at','>',$debut);
            }
            if($fin != null){
                $cessions = $cessions->where('created_at','<',$fin);
            }

            $sumCessions = $cessions->sum('montant');
            $sumFraisCessions = 0;
            $cessions = $cessions->orderBy('created_at','desc')->get();
            $nbCessions = count($cessions);

            foreach($cessions as $cession){
                $cession->date = $cession->created_at->format('d-m-Y H:i:s');
                $cession->type = 'Cession';
            }
            $transactions = array_merge($transactions, $cessions->toArray());
        }
        //dump($transactions);die();
        usort($transactions, 'date_compare');

        $statNb = $statSum = $statFrais = [];

        $statNb['retrait'] = $nbRetraits;
        $statNb['depot'] = $nbDepots;
        $statNb['recharge'] = $nbRecharges;
        $statNb['approvisionnement'] = $nbApprovisionnements;
        $statNb['cashout'] = $nbCashouts;
        $statNb['cession'] = $nbCessions;
        
        $statSum['retrait'] = $sumRetraits;
        $statSum['depot'] = $sumDepots;
        $statSum['recharge'] = $sumRecharges;
        $statSum['approvisionnement'] = $sumApprovisionnements;
        $statSum['cashout'] = $sumCashouts;
        $statSum['cession'] = $sumCessions;
        
        $statFrais['retrait'] = $sumFraisRetraits;
        $statFrais['depot'] = $sumFraisDepots;
        $statFrais['recharge'] = $sumFraisRecharges;
        $statFrais['approvisionnement'] = $sumFraisApprovisionnements;
        $statFrais['cashout'] = $sumFraisCashouts;
        $statFrais['cession'] = $sumFraisCessions;

        session()->push('transaction_partenaires',compact('transactions','statNb','statSum','statFrais'));        
        return view('rapport.etat_transactions.partenaire',compact('transactions','statNb','statSum','statFrais'));
    }

    public function downloadTransactionPartenaire(Request $request){
        
        $lastKey = array_key_last(session()->get('transaction_partenaires'));
        $transaction_partenaires = session()->get('transaction_partenaires')[$lastKey];
        
        $pdf = FacadePdf::loadView('rapport.pdf_transactions.partenaire',$transaction_partenaires);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download ('Rapport des transactions partenaires.pdf');
    }

    public function rapportTransactionApporteur(){
        $apporteurs = Apporteur::where('deleted',0)->get();
        return view('rapport.transactions.apporteur',compact('apporteurs'));
    }

    public function searchTransactionApporteur(Request $request){
        try{
            $debut = $request->debut ? explode('T',$request->debut)[0].' '.explode('T',$request->debut)[1].':00' : null;
            $fin = $request->fin ? explode('T',$request->fin)[0].' '.explode('T',$request->fin)[1].':00' : null;
    
    
            if ($request->status == null){
                $status = ['pending','completed','cancelled','refunded'];
            }else{
                $status = in_array('all', $request->status) ? ['pending','completed','cancelled','refunded'] : $request->status;
            }
    
            $apporteurs = in_array('all', $request->apporteurs) ? Apporteur::where('deleted',0)->pluck('id') : $request->apporteurs;

            $buys = UserCardBuy::whereIn('status',$status)->where('deleted',0)->whereIn('apporteur_id',$apporteurs);
            $operations = Apporteur::where('deleted',0)->whereIn('apporteur_id',$apporteurs);
    
            if($debut != null){
                $buys = $buys->where('created_at','>',$debut);
            }
            if($fin != null){
                $buys = $buys->where('created_at','<',$fin);
            }
            
    
            $sumBuys = $buys->sum('montant');
            $buys = $buys->orderBy('created_at','DESC')->get();
            $nbBuys = count($buys);
    
            foreach($buys as $buy){
                $buy->date = $buy->created_at->format('d-m-Y H:i:s');
                $buy->userClient;
            }
            //dump($buys);die();
    
            session()->push('transaction_apporteurs',compact('buys','nbBuys','sumBuys'));        
            return view('rapport.etat_transactions.apporteur',compact('buys','nbBuys','sumBuys'));
        } catch (\Exception $e) {
            dump($e);die();
            return back()->withError($e->getMessage());
        }
    }

    public function downloadTransactionApporteur(Request $request){
        
        $lastKey = array_key_last(session()->get('transaction_apporteurs'));
        $transaction_apporteurs = session()->get('transaction_apporteurs')[$lastKey];
        
        $pdf = FacadePdf::loadView('rapport.pdf_transactions.apporteur',$transaction_apporteurs);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download('Rapport des transactions apporteurs.pdf');
    }
}
