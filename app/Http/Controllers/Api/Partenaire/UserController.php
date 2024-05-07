<?php

namespace App\Http\Controllers\Api\Partenaire;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserPartenaire;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    public function listeUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'partenaire_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }

            $req = $request->all();

            $users = UserPartenaire::where('partenaire_id',$req['partenaire_id'])->where('deleted',0)->get()->all();

            foreach ($users as $value) {
                $value['libelle'] = $value->partenaire->libelle;
                $value['role'] = $value->role->libelle;
                $value['date'] = $value->created_at->format('d-m-Y H:i');
            }
            
            return sendResponse($users, 'Liste chargée avec succes.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function showUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $req = $request->all();

            $user = UserPartenaire::where('id',$req['user_id'])->where('deleted',0)->first();
            
            return sendResponse($user, 'Liste chargée avec succes.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function addUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'partenaire_id' => 'required',
                'name' => 'required',
                'lastname' => 'required',
                "role" => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            
            $req = $request->all();
            
            $user = UserPartenaire::create([
                'id' => Uuid::uuid4()->toString(),
                'name' => $req['name'],
                'lastname' => $req['lastname'],
                'username' => strtolower($req['name'][0].''.explode(' ',$req['lastname'])[0]),
                'password' => Hash::make(12345678),
                'partenaire_id' => $req['partenaire_id'],
                'role_id' => $req['role'],
                'status' => 1,
                'deleted' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return sendResponse($user, 'Utilisateur crée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function editUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'name' => 'required',
                'lastname' => 'required',
                'role' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $req = $request->all();
            $user = UserPartenaire::where('id',$req['user_id'])->where('deleted',0)->first();

            $user->name = $request->name;
            $user->lastname = $request->lastname;
            $user->role_id = $req['role'];
            $user->updated_at = Carbon::now();
            $user->save();

            return sendResponse($user, 'Modification effectuée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function deleteUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $req = $request->all();
            $user = UserPartenaire::where('id',$req['user_id'])->where('deleted',0)->first();
            
            $user->deleted = 1;
            $user->save();

            return sendResponse($user, 'Supression effectuée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function resetUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $req = $request->all();
            $user = UserPartenaire::where('id',$req['user_id'])->where('deleted',0)->first();
            
            $user->password = Hash::make(12345678);
            $user->updated_at = Carbon::now();
            $user->save();

            return sendResponse($user, 'Reinitialisation effectuée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function activationUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $req = $request->all();
            $user = UserPartenaire::where('id',$req['user_id'])->where('deleted',0)->first();
            
            $user->status = 1;
            $user->updated_at = Carbon::now();
            $user->save();

            return sendResponse($user, 'Activation effectuée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function desactivationUserPartenaire(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $req = $request->all();
            $user = UserPartenaire::where('id',$req['user_id'])->where('deleted',0)->first();
            
            $user->status = 0;
            $user->updated_at = Carbon::now();
            $user->save();

            return sendResponse($user, 'Desactivation effectuée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function updateUserPartenairePassword(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $req = $request->all();
            $user = UserPartenaire::where('id',$req['user_id'])->where('deleted',0)->first();
            
            $user->password = Hash::make($req['password']);
            $user->updated_at = Carbon::now();
            $user->save();

            return sendResponse($user, 'Changement effectuée avec succès');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }
    
    public function getUserPartenaireInfo(Request $request){
        try{
            $user = UserPartenaire::where('id',$request->id)->first();
            $user->partenaire;
            $user->role->rolePermissions;
            $user->makeHidden(['password']);
        
            return sendResponse($user, 'Liste chargée avec succes.');
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        }
    }
}
