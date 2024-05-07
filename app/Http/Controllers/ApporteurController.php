<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apporteur;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class ApporteurController extends Controller
{
    public function index(Request $request){
        $apporteurs =  Apporteur::where('deleted',0)->get();
        //dd($apporteurs);
        return view('apporteurs.index', compact('apporteurs'));
    }

    public function add(Request $request){
        $apporteur = Apporteur::where('telephone',$request->telephone)->where('deleted',0)->first();
        if($apporteur){
            return back()->withError("Un apporteur existe deja avec ce numero de telephone");
        }
        $password = generateRandomString(8);
        $code_promo = generateRandomCode(6);

        Apporteur::create([
            'id' => Uuid::uuid4()->toString(),
            'name' => $request->name,
            'lastname' => $request->lastname,
            'telephone' => $request->telephone,
            'password' => Hash::make($password),
            'code_promo' => $code_promo,
            'solde_commission' => 0,
            'status' => 1,
            'deleted' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $message = 'Votre compte BCC apporteur a été créer avec succes. Votre mot de passe est: '.$password.'. Vous etes prier de changer ce mot de passe a votre connexion.';
        sendSms($request->telephone,$message);

        return back()->withSuccess("Apporteur crée avec succès");
    }

    public function edit(Request $request){
        try{
            $apporteur = Apporteur::where('id',$request->id)->where('deleted',0)->first();

            if($apporteur->telephone != $request->telephone){
                $apporteur = Apporteur::where('telephone',$request->telephone)->where('deleted',0)->first();
                if($apporteur){
                    return back()->withError("Un apporteur existe deja avec ce numero de telephone");
                }
            }

            $apporteur->name = $request->name;
            $apporteur->lastname = $request->lastname;
            $apporteur->telephone = $request->telephone;
            $apporteur->save();
            return back()->withSuccess("Modification effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function delete(Request $request){
        try{
            $apporteur = Apporteur::where('id',$request->id)->where('deleted',0)->first();

            $apporteur->deleted = 1;
            $apporteur->save();
            return back()->withSuccess("Supression effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function activate(Request $request){
        try{
            $apporteur = Apporteur::where('id',$request->id)->where('deleted',0)->first();

            $apporteur->status = 1;
            $apporteur->save();
            return back()->withSuccess("Supression effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function desactivate(Request $request){
        try{
            $apporteur = Apporteur::where('id',$request->id)->where('deleted',0)->first();

            $apporteur->status = 0;
            $apporteur->save();
            return back()->withSuccess("Supression effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function resetPassword(Request $request){
        try{
            $password = generateRandomString(8);
            $apporteur = Apporteur::where('id',$request->id)->where('deleted',0)->first();

            $apporteur->password = Hash::make($password);
            $apporteur->save();
            $message = 'Votre mot de passe BCC apporteur a été modifier avec succes. Votre  nouveau mot de passe est: '.$password.'. Vous etes prier de changer ce mot de passe a votre connexion.';
            sendSms($apporteur->telephone,$message);
            return back()->withSuccess("Reinitialisation effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function resetCode(Request $request){
        try{
            $code_promo = generateRandomCode(6);
            $apporteur = Apporteur::where('id',$request->id)->where('deleted',0)->first();

            $apporteur->code_promo = $code_promo;
            $apporteur->save();
            return back()->withSuccess("Reinitialisation effectuée avec succès");
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }
}
