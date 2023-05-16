<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BinanceHoldingsService{
    public function getBinanceHoldings($binanceData){
        $binanceDataDecoded = json_decode($binanceData);
        $balances = $binanceDataDecoded -> balances;
        $binanceSymbolStrings = [];
        $binanceHoldings = [];
        $binanceTotalDollarHoldings = 0;

        // $dollarPriceOfHoldingResponse = json_decode(Http::get('https://api.binance.com/api/v3/ticker/price', ['symbol' => $symbol]));
        $dollarPricesOfHoldingsResponse = json_decode(Http::get('https://api.binance.com/api/v3/ticker/price'));
        /* Loop through each possible binance token */
        foreach($balances as $balance){
            /* only check tokens where balance > 0 */
            if($balance->free > 0){
                $cryptoType = $balance->asset;
                $dollarValueOfHolding = 0;
                $dollarPriceOfHolding = 0;
                if($balance->asset !== "USDT"){
                    $symbolString = $balance->asset . "USDT";
                    $binanceSymbolStrings [] = $symbolString;
                    foreach ( $dollarPricesOfHoldingsResponse as $dollarPriceOfHoldingResponse ) {
                        if($dollarPriceOfHoldingResponse->symbol === $symbolString) {
                            if(isset($dollarPriceOfHoldingResponse -> price)){
                                $dollarPriceOfHolding = $dollarPriceOfHoldingResponse -> price;
                                $dollarValueOfHolding = $dollarPriceOfHolding * $balance->free;
                                $binanceTotalDollarHoldings += number_format($dollarValueOfHolding, 2, '.', '');
                                $binanceHoldings [] = [$cryptoType, $cryptoType, $balance->free, "$" . number_format($dollarPriceOfHolding, 2, '.', ''), "$" . number_format($dollarValueOfHolding, 2, '.', '')]; 
                            }
                        }
                    }
                }
                else{
                    $dollarValueOfHolding = $balance->free;
                    $dollarPriceOfHolding = 1;
                    $binanceTotalDollarHoldings += number_format($dollarValueOfHolding, 2, '.', '');
                    // $cryptoSymbol = LivePortfolioController::generateCryptoSymbol($cryptoType);
                    $binanceHoldings [] = [$cryptoType, $cryptoType, $balance->free, "$" . number_format($dollarPriceOfHolding, 2, '.', ''), "$" . number_format($dollarValueOfHolding, 2, '.', '')]; 
                }
                // if($dollarValueOfHolding > 1){
                // }
            }
        }
        /* Builds list of binance holdings in the traders account with a value greater than 1 dollar*/
        return $binanceHoldings;
    }
}
?>