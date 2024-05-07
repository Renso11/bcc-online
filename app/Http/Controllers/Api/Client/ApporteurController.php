<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use App\Models\Apporteur;
use App\Models\UserCardBuy;
use App\Models\ApporteurOperation;
use Illuminate\Http\Request;
use App\Services\PaiementService;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ApporteurController extends Controller
{ 
    public function login(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'telephone' => 'required|string',
                'password' => 'required|string|min:8',
            ]);
    
            if ($validator->fails())
            {
                return response()->json([
                    "error" => $validator->errors()->first()
                ], 422);
            }
    
            $apporteur = Apporteur::where('telephone', $request->telephone)->first();
    
            if ($apporteur) {
                if($apporteur->status == 0){
                    $message = ['success' => false, 'status' => 401, 'message' => 'Echec de connexion : Compte apporteur inactif', 'timestamp' => Carbon::now(), 'user' => $apporteur->id]; 
                    writeLog($message);
                    return response()->json([
                        'message' => 'Ce compte est désactivé',
                    ], 401);
                }
                
                if (!Hash::check($request->password, $apporteur->password)) {
                    return sendError('Identifiants incorrectes', [], 401);
                }

                $apporteur->makeHidden(['password']);

                return response()->json([
                    'apporteur' => $apporteur
                ],200);
            } else {
                $message = ['success' => false, 'status' => 404,'message' => 'Echec de connexion : l\'apporteur n\'exite pas','timestamp' => Carbon::now()];  
                writeLog($message);
                return response()->json([
                    "message" =>'L\'apporteur n\'existe pas'
                ], 404);
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500,'message' => $e->getMessage(),'timestamp' => Carbon::now()]; 
            writeLog($message);
            return sendError($e->getMessage(), [], 500);
        }
    }

    public function getBalance(Request $request){
        $apporteur =  Apporteur::where('id', $request->id)->first();

        if (!$apporteur) {
            return sendError("Apporteur introuvable, verifier l'ID", [], 404);
        }

        $solde = $apporteur->solde_commission;

        return sendResponse($solde, 'Solde');
    }

    public function getOperations(Request $request){
        $apporteur =  Apporteur::where('id', $request->id)->first();

        if (!$apporteur) {
            return sendError("Apporteur introuvable, verifier l'ID", [], 404);
        }

        $operations =  ApporteurOperation::where('deleted', 0)->where('apporteur_id', $apporteur->id)->paginate(10);

        return sendResponse($operations, 'Operations');
    }

    public function getActivations(Request $request){
        $apporteur =  Apporteur::where('id', $request->id)->first();

        if (!$apporteur) {
            return sendError("Apporteur introuvable, verifier l'ID", [], 404);
        }

        $activations =  UserCardBuy::with('apporteur')->with('userClient')->with('userCard')->where('deleted', 0)->where('apporteur_id', $apporteur->id)->paginate(10);

        return sendResponse($activations, 'Activations');
    }

    public function regenerateCode(Request $request){
        $code_promo = generateRandomCode(6);
        $apporteur =  Apporteur::where('id', $request->id)->first();

        if (!$apporteur) {
            return sendError("Apporteur introuvable, verifier l'ID", [], 404);
        }

        $apporteur->code_promo = $code_promo;
        $apporteur->save();

        return sendResponse($code_promo, 'Code promo regeneré avec succes');
    }

    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                "error" => $validator->errors()->first()
            ], 422);
        }

        $apporteur =  Apporteur::where('id', $request->id)->first();
        $apporteur->makeHidden(['password']);

        if (!$apporteur) {
            return sendError("Apporteur introuvable", [], 404);
        }
        
        $apporteur->password = Hash::make($request->password);
        $apporteur->save();
        return sendResponse($apporteur, 'Mot de passe modifié avec succes');
    }

    public function withdrawCommission(Request $request, PaiementService $paiementService){
        $validator = Validator::make($request->all(), [
            'amount' => 'required|int',
            'moyen' => 'required|string',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                "error" => $validator->errors()->first()
            ], 422);
        }

        $apporteur =  Apporteur::where('id', $request->id)->first();
        $montant = $request->amount;

        if (!$apporteur) {
            return sendError("Apporteur introuvable, verifier l'ID", [], 404);
        }

        if ($apporteur->solde_commission < $montant) {
            return sendError("Solde insuffisant pour cette operation", [], 401);
        }

        if($request->moyen == 'bcv'){
            $encrypt_Key = env('ENCRYPT_KEY');
            $receiver =  UserClient::where('deleted',0)->where('username',$apporteur->telephone)->first(); 
            if(!$receiver){
                return sendError('Ce compte BCV n\'existe pas', [], 404); 
            }else if($receiver->verification != 1){
                return sendError('Ce compte BCV est inactif', [], 500); 
            }else if(!$receiver->userCard){
                return sendError('Ce compte BCV n\'a pas de carte liée', [], 500);
            }

            $receiverFirstCard =  $receiver->userCard->first();

            $reference_memo_gtp = unaccent('Retrait de commission de l\'apporteur '. $apporteur->lastname . ' ' . $apporteur->name . '. Montant : '. $montant .' XOF');
            $bcvCredited = $paiementService->cardCredited($receiverFirstCard->customer_id, $receiverFirstCard->last_digits, $montant, $apporteur, $reference_memo_gtp);

            if($bcvCredited == false){
                return sendError('Probleme lors du credit de la carte', [], 500);                    
            }else{
                $apporteur->solde_commission -= $montant;
                $apporteur->save();
                
    
                ApporteurOperation::insert([
                    'id' => Uuid::uuid4()->toString(),
                    'apporteur_id' => $amount->id,
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
                return sendError('Probleme lors du credit du compt BMO', [], 500);                    
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

        return sendResponse($apporteur, 'Retrait effectuée avec succes');
    }
}
