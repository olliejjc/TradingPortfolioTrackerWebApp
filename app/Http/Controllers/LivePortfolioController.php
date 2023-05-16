<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\BinanceAccountService;
use App\Services\BinanceHoldingsService;
use App\Services\BinanceSignatureService;
use Illuminate\Support\Facades\Auth;

class LivePortfolioController extends Controller
{
    protected $binanceAccountService;
    protected $binanceHoldingsService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(BinanceAccountService $binanceAccountService, BinanceHoldingsService $binanceHoldingsService){
        $this->middleware('auth');
        $this->binanceAccountService = $binanceAccountService;
        $this->binanceHoldingsService = $binanceHoldingsService;

    }

    protected $binanceTotalDollarHoldings = 0;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBinanceAccountData(){
        $user = User::where('username', Auth::user()->username)->first();
        $binanceAPIKey  = $user -> binance_apikey;
        $binanceSecretKey  = $user -> binance_secretkey;
        //checks that the binance api keys are set, if they are will retrieve your binance exchange data
        if(isset($binanceAPIKey) && isset($binanceSecretKey)){
            $binanceData =  $this -> binanceAccountService -> getBinanceData($binanceAPIKey, $binanceSecretKey);
            $binanceDataDecoded = json_decode($binanceData);
            //check that the binance api returns valid data about holdings, will return valid data if api on binance is not set up to allow retrieval of data from your account
            if(!isset( $binanceDataDecoded -> code)){
                $binanceHoldings = $this->binanceHoldingsService -> getBinanceHoldings($binanceData);
                $interactiveBrokersData = "";
                $this->binanceTotalDollarHoldings = number_format($this->binanceTotalDollarHoldings, 2, '.', '');
                return[
                    "success" => true,
                    "response" => ['binanceHoldings' => $binanceHoldings, 'binanceTotalDollarHoldings' => $this->binanceTotalDollarHoldings, 'interactiveBrokersData' => $interactiveBrokersData]
                ];
            }
            else{
                return[
                    "success" => false,
                    "response" => ["error" => "Binance API has returned invalid data"]
                ];
            }
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "Binance API keys have not been set"]
            ];
        }
        /* This gets data from the Interactive Brokers API *TO BE DONE* */
        //$interactiveBrokersData = LivePortfolioController::getInteractiveBrokersData();
    }

    /* Retrieves crypto token symbol for portfolio display for each crypto token holding */
    // public function generateCryptoSymbol($cryptoType){
    //     $cryptoSymbol = "";

    //     /* Symbol exists already so don't need to retrieve it again from the cryptoicons api*/
    //     if (File::exists("symbols/" . strtolower($cryptoType) . ".png")) {
    //         $cryptoSymbol = "symbols/" . strtolower($cryptoType) . ".png";
    //     }
    //     else{

    //         $cryptoIconsSymbol = @file_get_contents("https://cryptoicons.org/api/icon/" . strtolower($cryptoType) . "/30");
    //         /* Check if the cryptoicons api returns data for specific crypto token */
    //         if($cryptoIconsSymbol !== FALSE){
    //             file_put_contents(public_path("symbols/" . strtolower($cryptoType) . ".png"), $cryptoIconsSymbol);
    //             $cryptoSymbol = "symbols/" . strtolower($cryptoType) . ".png";
    //         }
    //         /* When the cryptoicons api doesn't return data for specific crypto token we try and retrieve a generic crypto symbol from the api instead */
    //         else{
    //             if (File::exists("symbols/generic.png")) {
    //                 $cryptoSymbol = "symbols/generic.png";
    //             }
    //             else{
    //                 $cryptoIconsGenericSymbol = @file_get_contents("https://cryptoicons.org/api/icon/generic/30");
    //                 if($cryptoIconsGenericSymbol !== FALSE){
    //                     file_put_contents(public_path("symbols/generic.png"), $cryptoIconsGenericSymbol);
    //                     $cryptoSymbol = "symbols/generic.png";
    //                 }
    //                 else{
    //                     $cryptoSymbol = "";
    //                 }
    //             }
    //         }
    //     }        
    //     return $cryptoSymbol;
    // }
}