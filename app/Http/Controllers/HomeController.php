<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\UserClient;
use App\Models\UserPartenaire;
use App\Models\Depot;
use App\Models\Retrait;
use App\Models\AccountDistribution;
use App\Models\KycClient;
use App\Models\Partenaire;
use App\Models\Recharge;
use App\Models\TransfertOut;
use App\Models\UserCard;
use App\Services\PaiementService;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon as Carbon;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');    
    }

    public function index()
    {
        return redirect(route('welcome'));
    }

    
    
    public function recovery(){
        return view('recover');
    }

    public function recoveryDatabase(Request $request){
        $revovery = $request->file('revovery');
        $extension = $revovery->getClientOriginalExtension();
        $fileSize = $revovery->getSize();
        $this->checkUploadedFileProperties($extension, $fileSize);
        
        $spreadsheet = IOFactory::load($revovery->getRealPath());
        $sheet        = $spreadsheet->getActiveSheet();
        $row_limit    = $sheet->getHighestDataRow();
        $column_limit = $sheet->getHighestDataColumn();
        
        $row_range    = range(2, $row_limit );        
        $column_range = range('A', $column_limit );
        $arr = [];
        $i = 0;
        foreach ($row_range as $row) {
            $i++;
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['date_activation'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCell('F' . $row )->getValue());
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['customer_id'] = (int)$sheet->getCell('G' . $row )->getValue();
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['last_four'] = $sheet->getCell('H' . $row )->getValue();
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['name'] = $sheet->getCell('J' . $row )->getValue();
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['last_name'] = $sheet->getCell('K' . $row )->getValue();
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['telephone'] = $sheet->getCell('L' . $row )->getValue();
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['mail'] = $sheet->getCell('M' . $row )->getValue();
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['naissance'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCell('N' . $row )->getValue());
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['piece_type'] = $sheet->getCell('O' . $row )->getValue();
            $arr[$sheet->getCell('L' . $row )->getValue()][$i]['date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($sheet->getCell('S' . $row )->getValue());
        };

        foreach ($arr as $key => $value) {            
            $base_url = 'https://gtpportal.com/rest/api/v1/';
            $programID = 66;
            $authLogin = '5404b9d0-15a5-448f-9664-40fab9082621';
            $authPass = 'z^MN0X2]kMm6!6993^}{';
            $encrypt_Key = env('ENCRYPT_KEY');
    

            try {    
                $client = new Client();
                $url = $base_url."accounts/".$value[array_key_last($value)]['customer_id'];
        
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                ];
        
                $auth = [
                    $authLogin,
                    $authPass
                ];
    
                $response = $client->request('GET', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                ]);
        
                $infosKyc = json_decode($response->getBody());
                //dd($infosKyc);
                
                $kyc = KycClient::create([
                    'id' => Uuid::uuid4()->toString(),
                    'name' => $infosKyc->firstName,
                    'lastname' => $infosKyc->lastName,
                    'email' => $infosKyc->emailAddress,
                    'telephone' => $infosKyc->mobilePhoneNumber,
                    'birthday' => $infosKyc->birthDate,
                    'departement' => 'LI',
                    'city' => $infosKyc->city,
                    'country' => $infosKyc->country,
                    'address' => $infosKyc->address1,
                    'piece_type' => $infosKyc->idType,
                    'piece_id' => $infosKyc->idValue,
                    'piece_file' => 'Fichier a rechercher',
                    'user_with_piece' => 'Fichier a rechercher',
                    'job' => 'Autres',
                    'salary' => 'SMIG',
                    'agreement' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);

                // Creation de l'utilisateur
                $client = UserClient::create([
                    'id' => Uuid::uuid4()->toString(),
                    'name' => $infosKyc->firstName,
                    'lastname' => $infosKyc->lastName,
                    'username' => $infosKyc->mobilePhoneNumber,
                    'password' => Hash::make(12345678),                    
                    'status' => 1,
                    'phone_code' => substr((string)$infosKyc->mobilePhoneNumber, 0, 3),
                    'phone' => substr((string)$infosKyc->mobilePhoneNumber, -8),
                    'double_authentification' => 0,
                    'sms' => 1,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'verification' => 1,
                    'verification_step_one' => 1,
                    'verification_step_two' => 1,
                    'verification_step_three' => 1,
                    'kyc_client_id' => $kyc->id,
                    'pin' => encryptData('12345',$encrypt_Key),
                ]);

                foreach ($value as $key => $item) {
                    if ($key === array_key_first($value)) {
                        $firstly = 1;
                    }else{
                        $firstly = 0;
                    }

                    $card = UserCard::create([
                        'id' => Uuid::uuid4()->toString(),
                        'user_client_id' => $client->id,
                        'customer_id' => encryptData((string)$item['customer_id'],$encrypt_Key),
                        'last_digits' => encryptData((string)$item['last_four'],$encrypt_Key),
                        'type' => 'virtuelle',
                        'is_first' => $firstly,
                        'is_buy' => 1,
                        'deleted' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
                
            } catch (BadResponseException $e) {
                dd($e->getResponse()->getBody()->getContents());
            }
        }
        dd('done');        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome(){
        try{
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
    
            $comptesClientsEnAttentes = UserClient::where('deleted',0)->where('verification',0)->where('is_rejected','<>',1)->orderBy('created_at','desc')->limit(5)->get();
    
            $recharges = Recharge::where('status','pending')->where('deleted',0)->get();
            foreach($recharges as $recharge){
                $recharge->date = $recharge->created_at->format('d-m-Y H:i:s');
                $recharge->type = 'Recharge';
                $recharge->partenaire = $recharge->partenaire;
                $recharge->userClient = $recharge->userClient;
            }

            $transferts = TransfertOut::where('status','pending')->where('deleted',0)->get();
            foreach($transferts as $transfert){
                $transfert->date = $transfert->created_at->format('d-m-Y H:i:s');
                $transfert->type = 'Transfert';
                $transfert->partenaire = $transfert->partenaire;
                $transfert->userClient = $transfert->userClient;
            }
            $operationsClientsEnAttentes = array_merge($recharges->toArray(), $transferts->toArray());
            
            usort($operationsClientsEnAttentes, 'date_compare');
                
            $depots = Depot::where('status','pending')->where('deleted',0)->get();
            foreach($depots as $depot){
                $depot->date = $depot->created_at->format('d-m-Y H:i:s');
                $depot->type = 'Depot';
                $depot->partenaire = $depot->partenaire;
                $depot->userClient = $depot->userClient;
            }
            $retraits = Retrait::where('status','pending')->where('deleted',0)->get();
            foreach($retraits as $retrait){
                $retrait->date = $retrait->created_at->format('d-m-Y H:i:s');
                $retrait->type = 'Retrait';
                $retrait->partenaire = $retrait->partenaire;
                $retrait->userClient = $retrait->userClient;
            }
            $operationsPartenairesEnAttentes = array_merge($depots->toArray(), $retraits->toArray());
            usort($operationsPartenairesEnAttentes, 'date_compare');
    
            $solde = [
                'gtp' => 0,
                'bmo_debit' => 0,
                'bmo_credit' => 0,
                'kkiapay' => 0,
                'compte_partenaire' => 0
            ];
    
            $nbClients = count(UserClient::where('deleted',0)->where('status',1)->get());
            $nbPartenaires = count(Partenaire::where('deleted',0)->get());
    
            try {    
                $client = new Client();
                $url = $base_url."accounts/".$accountId."/balance";
        
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                ];
        
                $auth = [
                    $authLogin,
                    $authPass
                ];
    
                $response = $client->request('GET', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                ]);
        
                $solde['gtp'] = json_decode($response->getBody())->balance;
                $solde['compte_partenaire'] = AccountDistribution::where('deleted',0)->sum('solde');
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return back()->withError($error);
            }

            
            $base_url_kkp = env('BASE_KKIAPAY');    
            $client = new Client();
            $url = $base_url_kkp."/api/v1/account/info";            
            $headers = [
                'x-private-key' => env('PRIVATE_KEY_KKIAPAY'),
                'x-secret-key' => env('SECRET_KEY_KKIAPAY'),
                'x-api-key' => env('API_KEY_KKIAPAY')
            ];
            $response = $client->request('GET', $url, [
                'headers' => $headers
            ]);            
            $solde['kkiapay'] = json_decode($response->getBody())->waiting_payout_amount;
            //$solde['kkiapay'] = 0;
    
            return view('welcome',compact('solde','nbClients','nbPartenaires','comptesClientsEnAttentes','operationsClientsEnAttentes','operationsPartenairesEnAttentes'));
        } catch (\Exception $e) {
            dd($e);
            return back()->withError($e->getMessage());
        }
    }

    public function searchData(Request $request){
        $debut = date("Y-m-d", strtotime(str_replace(' ','',explode('-',$request->periode)[0])));
        $debut = $debut.' 00:00:00';
        $fin = date("Y-m-d", strtotime(str_replace(' ','',explode('-',$request->periode)[1])));
        $fin = $fin.' 23:59:59';
        
        $data = [];
        $data['nbDepots'] = count(Depot::where('deleted',0)->where('status',1)->whereBetween('created_at', [$debut, $fin])->orderBy('created_at', 'desc')->get());
        $data['nbRetraits'] = count(Retrait::where('deleted',0)->where('status',1)->whereBetween('created_at', [$debut, $fin])->orderBy('created_at', 'desc')->get());
        $data['nbTransferts'] = count(TransfertOut::where('deleted',0)->where('status',1)->whereBetween('created_at', [$debut, $fin])->orderBy('created_at', 'desc')->get());
        return json_encode($data);
    }

    
    public function test(PaiementService $paiementService){
        //dd('ok');
        /*try {
            $base_url = 'https://gtpportal.com/rest/api/v1/';
            $programID = 66;
            $authLogin = '5404b9d0-15a5-448f-9664-40fab9082621';
            $authPass = 'z^MN0X2]kMm6!6993^}{';
            $accountId = 17225124;
    
            $client = new Client();
            $url =  $base_url."accounts/fund-transfer";
            
            $body = [
                "paymentType" => "C2C",
                "fromAccountId" => 19358089,
                "transferAmount" => 216,
                "currencyCode" => "XOF",
                "toAccountId" => 13173334,
                "last4Digits" => "3229",
                "fromCardReferenceMemo" => "Transfer fees 14/07/2024"
            ];

            $body = json_encode($body);
            
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
                'accountId' => $accountId,
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];
        
            $auth = [
                $authLogin,
                $authPass
            ];

            $response = $client->request('POST', $url, [
                'auth' => $auth,
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);

            dd(json_decode($response->getBody()));
        } catch (\Exception $e) {
            dd($e);
        }

        
        try {
            $base_url = 'https://sandbox.gtpportal.com/rest/api/v1/';
            $programID = 53;
            $authLogin = '598501ef-6ab6-42b3-8132-56c11bf6ada4';
            $authPass = '|YmqHUkcm&s/6$?h;Pb4';
            $accountId = 12703952;            

            $client = new Client();
            $url = $base_url."accounts/phone-number";

            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
            ];
    
            $query = [
                'phoneNumber' => "22962617848"
            ];
    
            $auth = [
                $authLogin,
                $authPass
            ];
            $response = $client->request('GET', $url, [
                'auth' => $auth,
                'headers' => $headers,
                'query' => $query
            ]);
            
            $accountInfo = null;
            $accountInfoLists = json_decode($response->getBody())->accountInfoList;
            dd($accountInfoLists);
            return $accountInfo;
        } catch (BadResponseException $e) {
            $json = json_decode($e->getResponse()->getBody()->getContents());   
            $error = $json->title.'.'.$json->detail;
            return sendError($error, [], 500);
        }*/

        try {
            $base_url_bmo = env('BASE_BMO');

            $client = new Client();
            $url = "https://svc.bmo.bestcash.me/external/operations-collect/cancel";
            
            $body = [
                "operation" => "OPATRCO20240719105338841900350"
            ];

            $body = json_encode($body);

            $headers = [
                'X-Auth-ApiKey' => 22960608820,
                'X-Auth-ApiSecret' => "MxLeESK29JUmew==",
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);

            dd(json_decode($response->getBody()));
        } catch (BadResponseException $e) {
            dd($e);
        }
    }

    public function checkUploadedFileProperties($extension, $fileSize){
        $valid_extension = array("csv", "xlsx", "xls"); //Only want csv and excel files
        $maxFileSize = 2097152; // Uploaded file size limit is 2mb
        if (in_array(strtolower($extension), $valid_extension)) {
            if ($fileSize <= $maxFileSize) {
            } else {
                throw new \Exception('No file was uploaded', Response::HTTP_REQUEST_ENTITY_TOO_LARGE); //413 error
            }
        } else {
            throw new \Exception('Invalid file extension', Response::HTTP_UNSUPPORTED_MEDIA_TYPE); //415 error
        }
    }
    
}