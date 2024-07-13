<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserClient;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class OtherController extends Controller
{
    public function searchCustomerWithAccount(Request $request){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');
           
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');

            $validator = Validator::make($request->all(), [
                'accountId' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }

            $customerId = decryptData($request->accountId, $encrypt_Key);       

            try {  
                $client = new Client();
                $url = $base_url."accounts/".$customerId;
            
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
            
                $data = json_decode($response->getBody());

                return sendResponse($data, 'Liste');

            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return sendError($error, [], 500);
            }
            
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function transfertCardToCard(Request $request){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            
            $validator = Validator::make($request->all(), [
                'amount' => 'required',
                'fromAccountId' => 'required',
                'toAccountId' => 'required',
                'toLastDigit' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }

            try {
                $client = new Client();
                $url =  $base_url." /accounts/fund-transfer";
                
                $body = [
                    "paymentType" => "C2C",
                    "fromAccountId" =>  decryptData($request->fromAccountId, $encrypt_Key),
                    "transferAmount" =>  $request->amount,
                    "currencyCode" => "XOF",
                    "toAccountId" =>  decryptData($request->toAccountId, $encrypt_Key),
                    "last4Digits" => decryptData($request->toLastDigit, $encrypt_Key),
                ];

                $body = json_encode($body);
                
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
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
                $data = json_decode($response->getBody());

                return sendResponse($data, 'Liste');

            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return sendError($error, [], 500);
            }
            
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function transactionActivity(Request $request){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            
            $validator = Validator::make($request->all(), [
                'startDate' => 'required',
                'endDate' => 'required',
                'accountId' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $customerId = decryptData($request->accountId, $encrypt_Key);

            try {        
                $client = new Client();
                $url =  $base_url."accounts/".$customerId."/transactions";
                
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                    'Content-Type' => 'application/json', 'Accept' => 'application/json'
                ];
            
                $auth = [
                    $authLogin,
                    $authPass
                ];
    
                $response = Http::withHeaders($headers)
                ->withBasicAuth($authLogin, $authPass)
                ->get($url, [
                    'StartDate' => $request->startDate,
                    'EndDate' => $request->endDate
                ]);

                $data = json_decode($response->getBody());
                return sendResponse($data, 'Liste');
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return sendError($error, [], 500);
            }
            
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function updateStatus(Request $request){
        try {
            $encrypt_Key = env('ENCRYPT_KEY');
            
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            
            $auth = [
                $authLogin,
                $authPass
            ];
            
            $validator = Validator::make($request->all(), [
                'accountId' => 'required',
                'lastDigits' => 'required',
                'mobilePhoneNumber' => 'required',
                'status' => 'required'
            ]);
            if ($validator->fails()) {
                return  sendError($validator->errors(), [],422);
            }
            $customerId = decryptData($request->accountId, $encrypt_Key);

            try {       
                $client = new Client();
                $url = $base_url."accounts/".$customerId."/status";
                
                $body = [
                    "last4Digits" => decryptData($request->toLastDigit, $encrypt_Key),
                    "mobilePhoneNumber" => $request->mobilePhoneNumber,
                    "newCardStatus" => $request->status
                ];
            
                $headers = [
                    'programId' => $programID,
                    'requestId' => Uuid::uuid4()->toString(),
                ];
                
                $response = $client->request('PATCH', $url, [
                    'auth' => $auth,
                    'body' => $body,
                    'headers' => $headers,
                ]);
            
                $data = json_decode($response->getBody());

                return sendResponse($data, 'Liste');
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $error = $json->title.'.'.$json->detail;
                return sendError($error, [], 500);
            }
            
        } catch (\Exception $e) {
            return sendError($e->getMessage(), [], 500);
        };
    }

    public function ping(Request $request){
        try {       
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $base_url = env('BASE_GTP_API');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');
            $programID = env('PROGRAM_ID');
            
            $auth = [
                $authLogin,
                $authPass
            ];

            $client = new Client();
            $url = $base_url."ping";
            
            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
            ];
            
            $response = $client->request('GET', $url, [
                'auth' => $auth,
                'headers' => $headers,
            ]);
        
            $data = json_decode($response->getBody());

            return sendResponse($data, 'Liste');
        } catch (BadResponseException $e) {
            $json = json_decode($e->getResponse()->getBody()->getContents());
            $error = $json->title.'.'.$json->detail;
            return sendError($error, [], 500);
        }
    }
}
