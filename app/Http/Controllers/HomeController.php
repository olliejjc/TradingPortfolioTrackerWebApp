<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /*Generates the list of years that have trade history */
    public function generateListOfTradeYears(){
        $listOfTradeYears = TradesController::getListOfTradeYears();
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
