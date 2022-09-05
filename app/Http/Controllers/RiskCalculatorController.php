<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RiskCalculatorController extends Controller{
    /* Calculates the shares to purchase, the dollar size of your position, and the dollar amount you're risking for your trade */
    public function calculate(Request $req){
        $portfolioSizeString = str_replace("$", "", $req->input('portfolio_size'));
        $portfolioSize = floatval($portfolioSizeString);
        $req->merge(array('portfolio_size' => $portfolioSize));
        $validatedData = $req->validate([
            'portfolio_size' => 'required|gt:0',
            'entry_price' => 'required|numeric|min:0|max:1000000',
            'stop_loss' => 'required|numeric|min:0|max:1000000',
        ]);
        $riskPercentagePerTradeString = str_replace("%", "", $req->input('risk_percentage_per_trade'));
        $riskPercentagePerTrade = floatval($riskPercentagePerTradeString)/100;
        $entryPrice = floatval($req->input('entry_price'));
        $stopLoss = floatval($req->input('stop_loss'));
        $maxSharesToPurchase = number_format((float)($portfolioSize * $riskPercentagePerTrade)/($entryPrice-$stopLoss), 1, '.', '');
        $positionSize = number_format(($entryPrice * $maxSharesToPurchase), 2, '.', '');
        $riskOfPosition = number_format($maxSharesToPurchase * ($entryPrice - $stopLoss), 2, '.', '');
        $calculateResults = array("maxSharesToPurchase" => $maxSharesToPurchase,
                     "positionSize" => $positionSize, "riskOfPosition" => $riskOfPosition);
        return[
            "success" => true,
            "response" => ["calculatedResults" => $calculateResults]
        ];
        // return json_encode($calculateResults);
    }
}