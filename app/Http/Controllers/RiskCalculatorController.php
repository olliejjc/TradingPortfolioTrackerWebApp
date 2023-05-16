<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use App\Services\TradeService;
use App\Http\Requests\CalculateRequest;

class RiskCalculatorController extends Controller{
    /* Calculates the shares to purchase, the dollar size of your position, and the dollar amount you're risking for your trade */
    protected $userService;

    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function calculate(CalculateRequest $req){
        $portfolioSizeString = str_replace("$", "", $req->input('portfolio_size'));
        $portfolioSize = floatval($portfolioSizeString);
        $req->merge(array('portfolio_size' => $portfolioSize));
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

    /* Retrieve user details for use on risk calculator */
    public function showRiskCalculatorSettings(){
        $userId = Auth::id();
        $user = User::find($userId);
        $portfolioSize = $user->portfolio_size;
        $currentPortfolioSize = $this->userService->getUserPortfolioSize();
        if(!empty($user) && $portfolioSize >= 0){
            return[
                "success" => true,
                "response" => ["riskcalculator", ['user' => $user, 'currentPortfolioSize' => $currentPortfolioSize]]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "Authorised user does not exist or portfolio value is an invalid number"]
            ];
        }
    }
}