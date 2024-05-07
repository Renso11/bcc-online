<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promotion;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Auth as Auth;

class PromotionController extends Controller
{
    
    public function partenaires(Request $request){
        try {
            $promotions = Promotion::where('deleted',0)->get();
            return view('promotions.partenaires',compact('promotions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function partenairePromoAdd(Request $request){
        try {           
            Promotion::create([
                'id' => Uuid::uuid4()->toString(),
                'operation' => $request->operation,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_promo_client' => $request->type_promo_client,
                'type_gain_client' => $request->type_gain_client,
                'gain_client' => $request->gain_client,
                'type_promo_partenaire' => $request->type_promo_partenaire,
                'type_gain_partenaire' => $request->type_gain_partenaire,
                'gain_partenaire' => $request->gain_partenaire,
                'status' => 1,
                'user_id' => Auth::user()->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return back()->withSuccess('Promotion partenaire ajouté avec succes');
        } catch (\Exception $e) {
            dd($e);
            return back()->withError($e->getMessage());
        };
    }
    
    public function partenairePromoEdit(Request $request){
        try {           
            Promotion::create([
                'id' => Uuid::uuid4()->toString(),
                'operation' => $request->operation,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_promo_client' => $request->type_promo_client,
                'type_gain_client' => $request->type_gain_client,
                'gain_client' => $request->gain_client,
                'type_promo_partenaire' => $request->type_promo_partenaire,
                'type_gain_partenaire' => $request->type_gain_partenaire,
                'gain_partenaire' => $request->gain_partenaire,
                'status' => 1,
                'user_id' => Auth::user()->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return back()->withSuccess('Promotion partenaire ajouté avec succes');
        } catch (\Exception $e) {
            dd($e);
            return back()->withError($e->getMessage());
        };
    }
    
    public function partenairePromoDelete(Request $request){
        try {           
            Promotion::create([
                'id' => Uuid::uuid4()->toString(),
                'operation' => $request->operation,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
                'type_promo_client' => $request->type_promo_client,
                'type_gain_client' => $request->type_gain_client,
                'gain_client' => $request->gain_client,
                'type_promo_partenaire' => $request->type_promo_partenaire,
                'type_gain_partenaire' => $request->type_gain_partenaire,
                'gain_partenaire' => $request->gain_partenaire,
                'status' => 1,
                'user_id' => Auth::user()->id,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            return back()->withSuccess('Promotion partenaire ajouté avec succes');
        } catch (\Exception $e) {
            dd($e);
            return back()->withError($e->getMessage());
        };
    }
    
    public function clients(Request $request){
        try {
            $promotions = Promotion::where('deleted',0)->get();
            return view('promotions.clients',compact('promotions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }

    public function clientPromoAdd(Request $request){
        try {
            $promotions = Promotion::where('deleted',0)->get();
            return view('promotions.clients',compact('promotions'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
}
