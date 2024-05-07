<?php

namespace App\Services;

use App\Models\BccPayment;
use App\Models\CompteCommission;
use App\Models\CompteCommissionOperation;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Carbon;
use Ramsey\Uuid\Uuid;

class PaiementService
{
    public function getPayment($methode, $reference)
    {
        if ($methode == 'bmo') {
            $base_url_bmo = env('BASE_BMO');

            $headers = [
                'X-Auth-ApiKey' => env('APIKEY_BMO'),
                'X-Auth-ApiSecret' => env('APISECRET_BMO'),
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];

            $client = new Client();
            $url = $base_url_bmo . "/operation?partnerReference=" . $reference;

            $response = $client->request('GET', $url, [
                'headers' => $headers
            ]);
            return json_decode($response->getBody());
        } else {
            $public_key = env('API_KEY_KKIAPAY');
            $private_key = env('PRIVATE_KEY_KKIAPAY');
            $secret = env('SECRET_KEY_KKIAPAY');

            $kkiapay = new \Kkiapay\Kkiapay($public_key, $private_key, $secret);

            return $kkiapay->verifyTransaction($reference);
        }
    }

    public function paymentVerification($moyen_paiement, $reference, $montant, $user = null)
    {
        try {
            if (checkPayment($moyen_paiement, $reference, $montant) == 'bad_amount') {
                $reason = 'Echec pour montant incorrespondant';
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec pour montant incorrespondant', 'timestamp' => Carbon::now(), 'user' => $user];
                writeLog($message);
                return $reason;
            } else if (checkPayment($moyen_paiement, $reference, $montant) == 'not_success') {
                $reason = 'Echec du paiement. Reference introuvable';
                $message = ['success' => false, 'status' => 500, 'message' => 'Echec du paiement. Reference introuvable', 'timestamp' => Carbon::now(), 'user' => $user];
                writeLog($message);
                return $reason;
            } else {
                return true;
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500, 'message' => $e->getMessage(), 'timestamp' => Carbon::now(), 'user' => $user];
            writeLog($message);
            return false;
        };
    }

    public function cardCredited($customer_id, $last_digits, $montant, $user, $reference_memo_gtp)
    {
        try {
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');
            $accountId = env('AUTH_DISTRIBUTION_ACCOUNT');

            $client = new Client();
            $encrypt_Key = env('ENCRYPT_KEY');
            $url =  $base_url . "accounts/" . decryptData((string)$customer_id, $encrypt_Key) . "/transactions";

            $body = [
                "transferType" => "WalletToCard",
                "transferAmount" => round($montant, 2),
                "currencyCode" => "XOF",
                "referenceMemo" => $reference_memo_gtp,
                "last4Digits" => decryptData((string)$last_digits, $encrypt_Key)
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
            $message = ['success' => true, 'status' => 200, 'message' => 'Rechargement effectué avec succes', 'timestamp' => Carbon::now(), 'user' => $user->id];
            writeLog($message);
            $responseBody = json_decode($response->getBody());
            return $responseBody;
        } catch (BadResponseException $e) {
            $json = json_decode($e->getResponse()->getBody()->getContents());
            $error = $json->title . '.' . $json->detail;
            $message = ['success' => false, 'status' => 500, 'message' => $error, 'timestamp' => Carbon::now(), 'user' => $user->id];
            writeLog($message);
            return false;
        }
    }

    public function repartitionCommission($fraisOperation, $frais, $montant, $referenceGtp, $type)
    {
        if ($fraisOperation) {
            $fraiCompteCommissions = $fraisOperation->fraiCompteCommissions;

            foreach ($fraiCompteCommissions as $value) {
                $compteCommission = CompteCommission::where('id', $value->compte_commission_id)->first();

                if ($value->type == 'pourcentage') {
                    $commission = $frais * $value->value / 100;
                } else {
                    $commission = $value->value;
                }

                $compteCommission->solde += $commission;
                $compteCommission->save();

                CompteCommissionOperation::create([
                    'id' => Uuid::uuid4()->toString(),
                    'compte_commission_id' => $compteCommission->id,
                    'type_operation' => $type,
                    'montant' => $montant,
                    'frais' => $frais,
                    'commission' => $commission,
                    'reference_gtp' => $referenceGtp,
                    'status' => 0,
                    'deleted' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }

    public function cardDebited($customer_id, $last_digits, $montant, $frais = 0, $user, $reference_memo_gtp = null)
    {
        try {
            $base_url = env('BASE_GTP_API');
            $programID = env('PROGRAM_ID');
            $authLogin = env('AUTH_LOGIN');
            $authPass = env('AUTH_PASS');

            $client = new Client();
            $encrypt_Key = env('ENCRYPT_KEY');
            $url =  $base_url . "accounts/" . decryptData((string)$customer_id, $encrypt_Key) . "/transactions";

            $body = [
                "transferType" => "CardToWallet",
                "transferAmount" => round(($montant + $frais), 2),
                "currencyCode" => "XOF",
                "last4Digits" => decryptData($last_digits, $encrypt_Key),
                "referenceMemo" => $reference_memo_gtp
            ];

            $body = json_encode($body);

            $headers = [
                'programId' => $programID,
                'requestId' => Uuid::uuid4()->toString(),
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];

            $auth = [
                $authLogin,
                $authPass
            ];

            try {
                $response = $client->request('POST', $url, [
                    'auth' => $auth,
                    'headers' => $headers,
                    'body' => $body,
                    'verify'  => false,
                ]);

                $message = ['success' => true, 'status' => 200, 'message' => 'Retrait de la carte ' . decryptData((string)$customer_id, $encrypt_Key) . ' effectué.', 'timestamp' => Carbon::now(), 'user' => $user->id];
                writeLog($message);
                $responseBody = json_decode($response->getBody());
                return $responseBody;
            } catch (BadResponseException $e) {
                $json = json_decode($e->getResponse()->getBody()->getContents());
                $message = ['success' => false, 'status' => 500, 'message' => $json, 'timestamp' => Carbon::now(), 'user' => $user->id];
                writeLog($message);
                return false;
            }
        } catch (\Exception $e) {
            $message = ['success' => false, 'status' => 500, 'message' => $e->getMessage(), 'timestamp' => Carbon::now(), 'user' => $user->id];
            writeLog($message);
            return false;
        }
    }

    public function bmoCredited($telephone,  $name, $lastname, $montant, $user)
    {
        try {

            $base_url_bmo = env('BASE_BMO');

            $client = new Client();
            $url = $base_url_bmo . "/operations/credit";

            $body = [
                "amount" => $montant,
                "customer" => [
                    "phone" => $telephone,
                    "firstname" => $name,
                    "lastname" => $lastname
                ]
            ];

            $body = json_encode($body);

            $headers = [
                'X-Auth-ApiKey' => env('APIKEY_BMO_CREDIT'),
                'X-Auth-ApiSecret' => env('APISECRET_BMO_CREDIT'),
                'Content-Type' => 'application/json', 'Accept' => 'application/json'
            ];

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body,
                'verify'  => false,
            ]);

            $resultat_credit_bmo = json_decode($response->getBody());

            $message = ['success' => true, 'status' => 200, 'message' => 'Transfert effectué avec succes', 'timestamp' => Carbon::now(), 'user' => $user->id];
            writeLog($message);

            return $resultat_credit_bmo;
        } catch (BadResponseException $e) {
            $message = ['success' => false, 'status' => 500, 'message' => $e->getMessage(), 'timestamp' => Carbon::now()];
            writeLog($message);
            return false;
        }

        //Check bmo status;
    }

    public function momoCredited($telephone, $montant, $user)
    {
        try {
            $base_url_kkp = env('BASE_KKIAPAY');

            $client = new Client();
            $url = $base_url_kkp . "/api/v1/payments/deposit";

            $partner_reference = substr($telephone, -4) . time();
            $body = [
                "phoneNumber" => $telephone,
                "amount" => $montant,
                "reason" => 'Transfert de ' . $montant . ' XOF vers le compte momo/flooz ' . $telephone . '.',
                "partnerId" => $partner_reference
            ];

            $body = json_encode($body);
            $headers = [
                'x-private-key' => env('PRIVATE_KEY_KKIAPAY'),
                'x-secret-key' => env('SECRET_KEY_KKIAPAY'),
                'x-api-key' => env('API_KEY_KKIAPAY')
            ];

            $response = $client->request('POST', $url, [
                'headers' => $headers,
                'body' => $body
            ]);

            $resultat = json_decode($response->getBody());
            //return resultat_check_status_kkp($resultat->transactionId);
            // check momo status
            $status = "PENDING";
            $starttime = time();

            while ($status == "PENDING") {
                $externalTransaction = resultat_check_status_kkp($resultat->transactionId);
                if ($externalTransaction->status == "SUCCESS") {
                    $status = "SUCCESS";
                    $message = ['success' => true, 'status' => 200, 'message' => 'Paiement momo effectué avec succes', 'timestamp' => Carbon::now(), 'user' => $user];
                    writeLog($message);
                    return $externalTransaction;
                } else if ($externalTransaction->status == "FAILED") {
                    $status = "FAILED";
                    $message = ['success' => false, 'status' => 500, 'message' => 'Echec lors du paiement du transfert', 'timestamp' => Carbon::now()];
                    writeLog($message);
                    return false;
                } else {
                    $now = time() - $starttime;
                    if ($now > 125) {
                        $status = "FAILED";
                        $message = ['success' => false, 'status' => 500, 'message' => 'Echec de confirmation du transfert', 'timestamp' => Carbon::now()];
                        writeLog($message);
                        return false;
                    }
                    $status = $externalTransaction->status;
                }
            }
            return $resultat;
        } catch (BadResponseException $e) {
            $message = ['success' => false, 'status' => 500, 'message' => $e->getMessage(), 'timestamp' => Carbon::now()];
            writeLog($message);
            return false;
        }
    }
}
