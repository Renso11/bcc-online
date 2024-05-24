<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apporteur;
use App\Models\ApporteurOperation;
use App\Models\UserCardBuy;
use App\Models\UserClient;
use App\Services\PaiementService;
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
        $message = 'Votre compte BCC apporteur a ete creer avec succes. Votre username est : '.$request->telephone.'  et votre mot de passe est: '.$password.'. Vous etes prié de changer ce mot de passe a votre connexion.';
        sendSms($request->telephone,$message);

        return back()->withSuccess("Apporteur crée avec succès");
    }
    
    public function operations(Request $request, PaiementService $paiementService){

        try {
            $apporteur =  Apporteur::where('id', $request->id)->where('deleted', 0)->first();
            $operations =  ApporteurOperation::where('deleted', 0)->where('apporteur_id', $apporteur->id)->get();
            return view('apporteurs.operations', compact('apporteur', 'operations'));
        } catch (\Exception $e) {
            return back()->withError($e->getMessage());
        }
    }

    public function edit(Request $request){
        try{
            $apporteur = Apporteur::where('id',$request->id)->where('deleted',0)->first();

            if($apporteur->telephone != $request->telephone){
                $app = Apporteur::where('telephone',$request->telephone)->where('deleted',0)->first();
                if($app){
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




    
    public function login(Request $request){
        try {
            if($request->session()->get('apporteur')){
                return redirect(route('getDashboardApporteur'));
            }
            return view('apporteurs.login');
        } catch (\Exception $e) {
            return redirect(route('loginApporteur'))->withError($e->getMessage());
        }
    }
    
    public function loginCheck(Request $request){
        try {    
            $apporteur = Apporteur::where('telephone', $request->telephone)->first();
            if ($apporteur) {
                if($apporteur->status == 0){
                    return back()->withError('Echec de connexion : Compte apporteur inactif');
                }
                
                if (!Hash::check($request->password, $apporteur->password)) {
                    return back()->withError('Identifiants incorrectes');
                }

                $apporteur->makeHidden(['password']);
                $request->session()->push('apporteur', $apporteur);
                
                return redirect(route('getDashboardApporteur'))->withSuccess("Connexion effectuée avec succès");
            } else {
                return redirect(route('loginApporteur'))->withError('Echec de connexion : l\'apporteur n\'existe pas');
            }
        } catch (\Exception $e) {
            return redirect(route('loginApporteur'))->withError($e->getMessage());
        }
    }

    public function logout(Request $request){
        try {
            $request->session()->forget('apporteur');
            return redirect(route('loginApporteur'));
        } catch (\Exception $e) {
            return redirect(route('loginApporteur'))->withError($e->getMessage());
        }
    }

    public function dashboard(Request $request){
        try {
            if($request->session()->get('apporteur')){
                $apporteur = $request->session()->get('apporteur')[0];
                $apporteur =  Apporteur::where('id', $apporteur->id)->where('deleted', 0)->first();
                $solde = $apporteur->solde_commission;
                $operations =  ApporteurOperation::where('deleted', 0)->where('apporteur_id', $apporteur->id)->get();
                $activations =  UserCardBuy::with('apporteur')->with('userClient')->with('userCard')->where('deleted', 0)->where('apporteur_id', $apporteur->id)->get();
                return view('apporteurs.dashboard', compact('apporteur', 'solde', 'operations', 'activations'));
            }else{
                $request->session()->forget('apporteur');
                return redirect(route('loginApporteur'));
            }
        } catch (\Exception $e) {
            return redirect(route('getDashboardApporteur'))->withError($e->getMessage());
        }
    }
    
    public function profile(Request $request, PaiementService $paiementService){

        try {
            if($request->session()->get('apporteur')){
                $apporteur = $request->session()->get('apporteur')[0];
                $apporteur =  Apporteur::where('id', $apporteur->id)->where('deleted', 0)->first();
                return view('apporteurs.profile', compact('apporteur'));
            }else{
                $request->session()->forget('apporteur');
                return redirect(route('loginApporteur'));
            }
        } catch (\Exception $e) {
            return redirect(route('getDashboardApporteur'))->withError($e->getMessage());
        }
    }

    public function changeProfile(Request $request){
        try{
            if($request->session()->get('apporteur')){
                $apporteur =  Apporteur::where('id', $request->id)->where('deleted', 0)->first();
                //dd($request);
                $apporteur->name = $request->name;
                $apporteur->lastname = $request->lastname;
                $apporteur->telephone = $request->telephone;
                $apporteur->save();
                return redirect(route('getProfileApporteur'))->withSuccess("Profil modifié avec succès");
            }else{
                $request->session()->forget('apporteur');
                return redirect(route('loginApporteur'));
            }
        }catch (\Exception $e) {
            return redirect(route('getDashboardApporteur'))->withError($e->getMessage());
        }
    }

    public function changePassword(Request $request){
        try{
            if($request->session()->get('apporteur')){
                $apporteur =  Apporteur::where('id', $request->id)->first();
                
                $apporteur->password = Hash::make($request->password);
                $apporteur->save();
                return redirect(route('getProfileApporteur'))->withSuccess("Mot de passe modifié avec succès");
            }else{
                $request->session()->forget('apporteur');
                return redirect(route('loginApporteur'));
            }
        }catch (\Exception $e) {
            return redirect(route('getDashboardApporteur'))->withError($e->getMessage());
        }
    }

    public function regenerateCode(Request $request){
        try{
            if($request->session()->get('apporteur')){
                $code_promo = generateRandomCode(6);
                $apporteur =  Apporteur::where('id', $request->id)->first();
        
                $apporteur->code_promo = $code_promo;
                $apporteur->save();
        
                return redirect(route('getProfileApporteur'))->withSuccess("Code promo regeneré avec succes");
            }else{
                $request->session()->forget('apporteur');
                return redirect(route('loginApporteur'));
            }
        }catch (\Exception $e) {
            return redirect(route('getDashboardApporteur'))->withError($e->getMessage());
        }
    }
    
    public function withdrawCommission(Request $request, PaiementService $paiementService){
        try{
            if($request->session()->get('apporteur')){
                $apporteur =  Apporteur::where('id', $request->id)->first();
                $montant = $request->amount;
                
                if (!$apporteur) {
                    return redirect(route('getDashboardApporteur'))->withError("Apporteur introuvable, verifier l'ID");
                }
        
                if ($apporteur->solde_commission < $montant) {
                    return redirect(route('getDashboardApporteur'))->withError("Solde insuffisant pour cette operation");
                }

                if($request->moyen == 'bcv'){
                    $receiver =  UserClient::where('deleted',0)->where('username',$apporteur->telephone)->first(); 
                    if(!$receiver){
                        return redirect(route('getDashboardApporteur'))->withError("Ce compte BCV n\'existe pas");
                    }else if($receiver->verification != 1){
                        return redirect(route('getDashboardApporteur'))->withError("Ce compte BCV est inactif");
                    }else if(!$receiver->userCard){
                        return redirect(route('getDashboardApporteur'))->withError("Ce compte BCV n\'a pas de carte liée");
                    }
        
                    $receiverFirstCard =  $receiver->userCard->first();
        
                    $reference_memo_gtp = unaccent('Retrait apporteur '. $apporteur->lastname . ' ' . $apporteur->name);
                    $bcvCredited = $paiementService->cardCredited($receiverFirstCard->customer_id, $receiverFirstCard->last_digits, $montant, $apporteur, $reference_memo_gtp);
        
                    if($bcvCredited == false){
                        return redirect(route('getDashboardApporteur'))->withError("Probleme lors du credit de la carte");
                    }else{
                        $apporteur->solde_commission -= $montant;
                        $apporteur->save();
                        
            
                        ApporteurOperation::insert([
                            'id' => Uuid::uuid4()->toString(),
                            'apporteur_id' => $apporteur->id,
                            'montant' => $montant,
                            'libelle' => 'Retrait sur le compte de commission vers compte BCV liée',
                            'sens' => 'debit',
                            'deleted' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),            
                        ]);
                    }
                }else if($request->moyen == 'bmo'){
                    $bmoCredited = $paiementService->bmoCredited('+'.$apporteur->telephone,  $apporteur->name, $apporteur->lastname, $montant, $apporteur);
        
                    if($bmoCredited == false){
                        return redirect(route('getDashboardApporteur'))->withError("Probleme lors du credit du compt BMO");
                    }else{
                        $apporteur->solde_commission -= $montant;
                        $apporteur->save();
                        
            
                        ApporteurOperation::insert([
                            'id' => Uuid::uuid4()->toString(),
                            'apporteur_id' => $apporteur->id,
                            'montant' => $montant,
                            'libelle' => 'Retrait sur le compte de commission vers compte BMO liée',
                            'sens' => 'debit',
                            'deleted' => 0,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),            
                        ]);
                    }
                }
        
                return redirect(route('getDashboardApporteur'))->withSuccess("Retrait effectuée avec succes");
            }else{
                $request->session()->forget('apporteur');
                return redirect(route('loginApporteur'));
            }
        }catch (\Exception $e) {
            return redirect(route('getDashboardApporteur'))->withError($e->getMessage());
        }
    }




}
