<?php

namespace App\Http\Controllers;

use App\Services\TradeService;

class HomeController extends Controller{
    protected $tradeService;

    public function __construct(TradeService $tradeService){
        $this->tradeService = $tradeService;
    }

    /*Generates the list of years that have trade history */
    public function generateListOfTradeYears(){
        $listOfTradeYears = $this -> tradeService -> getListOfTradeYears();
        if (count($listOfTradeYears) <= 0) {
            return[
                "success" => false,
                "response" => ["error" => "No trade years found"]
            ];
        }
        else{
            rsort($listOfTradeYears);
            return[
                "success" => true,
                "response" => ["listOfTradeYears" => $listOfTradeYears]
            ];
        }
    }
}