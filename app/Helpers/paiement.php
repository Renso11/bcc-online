<?php

use App\Models\BccPayment;
use App\Models\Frai;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

function getFeeAndRepartition($type, $montant){
  $frais = Frai::where('deleted', 0)->where('type_operation', $type)->where('start', '<=', $montant)->where('end', '>=', $montant)->orderBy('id', 'DESC')->first();
  return $frais;
}

function getFee($feeAndRepartition,$montant){  
  $frais = 0;
  if($feeAndRepartition){
    if($feeAndRepartition->type == 'pourcentage'){
        $frais = $montant * $feeAndRepartition->value / 100;
    }else{
        $frais = $feeAndRepartition->value;
    }
  }
  return $frais;
}

function checkPayment($method, $reference, $amount)
{
  if ($method == 'bmo') {
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

    $response = json_decode($response->getBody());

    if ($response->status == 'CONFIRMED') {
      if ($response->amount == $amount) {
        return 'success';
      } else {
        return 'bad_amount';
      }
    } else {
      return 'not_success';
    }
  } else if ($method == 'bcc') {
    $paiement = BccPayment::where('id', $reference)->first();
    if ($paiement->status == 1) {
      if ($paiement->montant == $amount) {
        return 'success';
      } else {
        return 'bad_amount';
      }
    } else {
      return 'not_success';
    }
  }
}

function resultat_check_status_kkp($transactionId)
{
  try {
    $public_key = env('API_KEY_KKIAPAY');
    $private_key = env('PRIVATE_KEY_KKIAPAY');
    $secret = env('SECRET_KEY_KKIAPAY');

    $kkiapay = new \Kkiapay\Kkiapay($public_key, $private_key, $secret);

    return $kkiapay->verifyTransaction($transactionId);
  } catch (BadResponseException $e) {
    return $e->getMessage();
  }
}

function resultat_check_status_bmo($partnerReference)
{
  try {
    $base_url_bmo = env('BASE_BMO');

    $headers = [
      'X-Auth-ApiKey' => env('APIKEY_BMO'),
      'X-Auth-ApiSecret' => env('APISECRET_BMO'),
      'Content-Type' => 'application/json', 'Accept' => 'application/json'
    ];

    $client = new Client();
    $url = $base_url_bmo . "/operation?partnerReference=" . $partnerReference;

    $response = $client->request('GET', $url, [
      'headers' => $headers
    ]);

    $resultat_check_status_bmo = json_decode($response->getBody());

    return $resultat_check_status_bmo;
  } catch (BadResponseException $e) {
    return $e->getMessage();
  }
}
