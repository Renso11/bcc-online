<?php

namespace App\Http\Controllers\Api\Partenaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Retrait;
use App\Models\UserClient;
use App\Models\UserPartenaire;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class RetraitController extends Controller
{

    public function addWithdrawPartenaire(Request $request){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');

            $validator = Validator::make($request->all(), [
                'username' => ["required" , "string"],
                'montant' => ["required" , "integer"],
                'user_partenaire_id' => ["required" , "string"]
            ]);

            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }

            $client = UserClient::where('username',$request->username)->where('deleted',0)->first();

            if(!$client){
                return sendError('Ce compte client n\'exite pas. Verifier le numero de telephone et recommencer');
            }else{
                if($client->status == 0){
                    return sendError('Ce compte client est inactif');
                }
                if($client->verification == 0){
                    return sendError('Ce compte client n\'est pas encore verifié');
                }
            }
            
            $card = $client->userCard->first();
            $userPartenaire = UserPartenaire::where('id',$request->user_partenaire_id)->first();
            
            $montant = $request->montant;

            $fraisAndRepartition = getFeeAndRepartition('retrait', $montant);
            $frais = getFee($fraisAndRepartition,$montant);
            $montantWithFee = $montant + $frais;

            
                 
            $clienthTTP = new Client();
            $url = $base_url."accounts/".decryptData($card->customer_id, $encrypt_Key)."/balance";
    
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
            ];
    
            $auth = [
                $authLogin,
                $authPass
            ];
        
            try {
                $response = $clienthTTP->request('GET', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                ]);
        
                $balance = json_decode($response->getBody());                    
            
                if($balance->balance < $montantWithFee){
                    return sendError('Le solde du client ne suffit pas pour cette opération');
                }
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return  sendError($error);
            }  
            
            $isRestrictByAdmin = isRestrictByAdmin($montant,$client->id,$userPartenaire->partenaire->id,'retrait');

            if($isRestrictByAdmin != 'ok'){
                return sendError($isRestrictByAdmin);
            }

            $isRestrictByPartenaire = isRestrictByPartenaire($montant,$userPartenaire->partenaire->id,$userPartenaire->id,'retrait');

            if($isRestrictByPartenaire != 'ok'){
                return sendError($isRestrictByPartenaire);
            }
            
            $retrait = Retrait::create([
                'id' => Uuid::uuid4()->toString(),
                'user_client_id' => $client->id,
                'partenaire_id' => $userPartenaire->partenaire->id,
                'user_partenaire_id' => $userPartenaire->id,
                'user_card_id' => $card->id,
                'libelle' => 'Retrait du compte BCV '.$client->username. ' chez le marchand ' .$userPartenaire->partenaire->libelle,
                'montant' => $montant,
                'frais' => $frais,
                'status' => 'pending',
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $message = getSms('retrait_initiation_client', null, $montant, $frais, null, null, $userPartenaire->partenaire->libelle);
            sendSms($client->username,$message);

            return sendResponse($retrait,'Retrait initié avec succes. Le client doit maintenant valider l\'opération', 'Success');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
    
    public function cancelClientWithdrawAsPartner(Request $request){
        try {                
            $validator = Validator::make($request->all(), [
                'transaction_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors()->first(), [],422);
            }

            $token = JWTAuth::getToken();
            $userId = JWTAuth::getPayload($token)->toArray()['sub'];

            $partenaire = UserPartenaire::where('id',$userId)->first()->partenaire;

      
            $retrait = Retrait::where('id',$request->transaction_id)->where('deleted',0)->where('status','pending')->first();

            if($partenaire->id != $retrait->partenaire_id && $userId != $retrait->user_partenaire_id){
                return  sendError('Vous n\'etes pas autorisé à faire cette opération', [$userId,$retrait->user_client_id],401);
            }

            $retrait->status = 'canceled';
            $retrait->deleted = 1;
            $retrait->save();
            
            return sendResponse($retrait, 'Succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
}
