<?php

namespace App\Services;

use App\Models\Depot;
use App\Models\PartnerCession;
use App\Models\PartnerWalletDeposit;
use App\Models\PartnerWalletWithdraw;
use App\Models\Recharge;
use App\Models\Retrait;
use App\Models\TransfertOut;
use App\Models\UserCardBuy;
use Illuminate\Support\Facades\DB;

class CardService
{
    public function getClientOperation($client, $debut, $fin)
    {
        try{;
            $resultats = Depot::with('partenaire')->with('userClient')->with('userCard')
            ->select('id', 'libelle', 'created_at','user_client_id', DB::raw("'Depot' as type"), DB::raw("'in' as sens"),'montant', 'frais')
            ->where('deleted', 0)->where('status','completed')
            ->whereBetween('created_at',[$debut,$fin])
            ->where('user_client_id', $client)            

            ->union(Retrait::with('partenaire')->with('userCard')->with('userClient')
            ->select('id', 'libelle', 'created_at', 'user_client_id', DB::raw("'Retrait' as type"), DB::raw("'out' as sens"), 'montant', 'frais')
            ->where('deleted', 0)->where('status','completed')
            ->whereBetween('created_at',[$debut,$fin])
            ->where('user_client_id', $client)) 

            ->union(Recharge::with('userClient')
            ->select('id', DB::raw("'Rechargement directe de compte' as libelle"),'created_at','user_client_id',DB::raw("'Rechargement' as type"), DB::raw("'in' as sens"), 'montant', 'frais')
            ->where('deleted', 0)->where('status', 'completed')
            ->whereBetween('created_at',[$debut,$fin])
            ->where('user_client_id', $client))
            
            ->union(TransfertOut::with('userClient')
            ->select('id', 'libelle', 'created_at','user_client_id', DB::raw("'Transfert' as type"), DB::raw("'out' as sens"), 'montant', 'frais')
            ->where('deleted', 0)->where('status', 'completed')
            ->whereBetween('created_at',[$debut,$fin])
            ->where('user_client_id', $client))

            ->union(UserCardBuy::with('userClient')
            ->select('id', DB::raw("'Achat de carte' as libelle"),'created_at', 'user_client_id', DB::raw("'Achat' as type"), DB::raw("'out' as sens"), 'montant', DB::raw('0 as frais'))
            ->where('deleted', 0)->where('status', 'completed')
            ->whereBetween('created_at',[$debut,$fin])
            ->where('user_client_id', $client));

            
            $transactions = DB::table(DB::raw("({$resultats->toSql()}) as sub"))
            ->mergeBindings($resultats->getQuery())->orderBy('created_at','desc')
            ->get();

            return $transactions;
            
        }catch (\Exception $e) {
            dump($e->getMessage());die();
        };
    }
    public function getPartenaireOperation($partenaire, $debut, $fin)
    {
        try{
            $resultats = Depot::with('partenaire')->with('userClient')->with('userCard')
            ->select('id', 'libelle', 'created_at','partenaire_id', DB::raw("'Depot' as type"), 'montant', 'frais')
            ->where('deleted', 0)->where('status','completed')
            ->whereBetween('created_at', [$debut, $fin])->where('partenaire_id',$partenaire)

            ->union(Retrait::with('partenaire')->with('userCard')->with('userClient')
            ->select('id', 'libelle', 'created_at', 'partenaire_id', DB::raw("'Retrait' as type"), 'montant', 'frais')
            ->where('deleted', 0)->where('status','completed')
            ->whereBetween('created_at', [$debut, $fin])->where('partenaire_id',$partenaire))
            
            ->union(PartnerWalletWithdraw::with('partenaire')->with('userClient')
            ->select('id', 'libelle', 'created_at', 'partenaire_id', DB::raw("'Withdrawl' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('deleted', 0)
            ->whereBetween('created_at', [$debut, $fin])->where('partenaire_id',$partenaire))

            ->union(PartnerWalletDeposit::with('partenaire')
            ->select('id', 'libelle', 'created_at', 'partenaire_id', DB::raw("'Approvisionnement' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('deleted', 0)
            ->whereBetween('created_at', [$debut, $fin])->where('partenaire_id',$partenaire))

            ->union(PartnerCession::with('partenaire')
            ->select('id', DB::raw("'Cession de monnaie' as user_client_id"), 'created_at', 'partenaire_id', DB::raw("'Cession' as type"), 'montant', DB::raw('0 as frais'))
            ->where('status', 'completed')->where('deleted', 0)
            ->whereBetween('created_at', [$debut, $fin])->where('partenaire_id',$partenaire));
            
            $transactions = DB::table(DB::raw("({$resultats->toSql()}) as sub"))
            ->mergeBindings($resultats->getQuery())->orderBy('created_at','desc')
            ->get();
            
            return $transactions;
            
        }catch (\Exception $e) {
            dump($e->getMessage());die();
        };
    }
}