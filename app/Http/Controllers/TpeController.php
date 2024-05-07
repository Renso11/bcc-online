<?php

namespace App\Http\Controllers;

use App\Models\Partenaire;
use App\Models\Tpe;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

class TpeController extends Controller
{
    
    public function index(Request $request){
        try {
            $tpes = Tpe::where('deleted',0)->get();
            $partenaires = Partenaire::where('deleted',0)->get();
            return view('tpes.index',compact('tpes','partenaires'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function tpeAdd(Request $request){
        try {
            Tpe::create([                
                'id' => Uuid::uuid4()->toString(),
                'type' => $request->type,
                'code' => $request->code,
                'partenaire_id' => $request->partenaire,
                'status' => 'on',
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            return redirect()->back()->withSuccess('TPE enregistré avec succes.');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function tpeEdit(Request $request){
        try {
            $tpe = Tpe::where('id',$request->id)->first();

            $tpe->type = $request->type;
            $tpe->code = $request->code;
            $tpe->partenaire_id = $request->partenaire;
            $tpe->save();
            
            return redirect()->back()->withSuccess('TPE modifié avec succes.');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function tpeDelete(Request $request){
        try {
            $tpe = Tpe::where('id',$request->id)->first();            
            $tpe->deleted = 1;
            $tpe->status = 'off';
            $tpe->save();
            
            return redirect()->back()->withSuccess('TPE supprimé avec succes.');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function tpeActivation(Request $request){
        try {
            $tpe = Tpe::where('id',$request->id)->first();            
            $tpe->status = 'on';
            $tpe->save();
            
            return redirect()->back()->withSuccess('TPE activé avec succes.');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
    
    public function tpeDesactivation(Request $request){
        try {
            $tpe = Tpe::where('id',$request->id)->first();
            $tpe->status = 'off';
            $tpe->save();
            
            return redirect()->back()->withSuccess('TPE désactivé avec succes.');
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        };
    }
}
