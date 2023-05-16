<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;

class BinanceAccountService{

    protected $binanceSignatureService;

    public function __construct(BinanceSignatureService $binanceSignatureService){
        $this->binanceSignatureService = $binanceSignatureService;
    }

    /* gets data to access binance account api */
    public function getBinanceData($binanceAPIKey, $binanceSecretKey){
        $timestampRequest = json_decode(Http::get('https://api.binance.com/api/v3/time'));
        $timestamp = $timestampRequest -> serverTime;
        $timestampString = "timestamp=" . $timestamp;
        $signature = $this->binanceSignatureService->generateSignature($timestampString, $binanceSecretKey);
        $response = Http::withHeaders(['X-MBX-APIKEY' => $binanceAPIKey])->get('https://api.binance.com/api/v3/account', ['timestamp' => $timestamp, 'signature' => $signature]);
        return $response;
    }
}
?>