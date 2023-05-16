<?php

namespace App\Services;

class BinanceSignatureService{
    public function generateSignature($timestampString, $secretKey){
        return hash_hmac('sha256', $timestampString, $secretKey);
    }
}

?>