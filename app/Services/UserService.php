<?php

namespace App\Services;

use App\Models\User;
use App\Services\MonthlyBalanceService;
use Illuminate\Support\Facades\Auth;

class UserService
{
    protected $tradeService;
    protected $monthlyBalanceService;

    public function __construct(TradeService $tradeService, MonthlyBalanceService $monthlyBalanceService){
        $this->tradeService = $tradeService;
        $this->monthlyBalanceService = $monthlyBalanceService;
    }

    public function getUserPortfolioSize(){
        $trades = array();
        $trades = $this->tradeService->getTradesByLatestMonth();
        if(!empty($trades)){
            $tradeMonth = $this->tradeService->getTradeMonth($trades[0]);
            $tradeYear = $this->tradeService->getTradeYear($trades[0]);
            $monthlyBalance = $this->monthlyBalanceService->getUserMonthlyBalance($tradeMonth, $tradeYear);
            return $monthlyBalance;
        }
        else{
            $userId = Auth::id();
            $user = User::find($userId);
            $portfolioSize = $user->portfolio_size;
            return $portfolioSize;
        }
    }

    public function getUserMonthlyProfitLoss($monthSelected, $yearSelected){
        $totalMonthlyProfitLoss = 0;
        $tradesWithMatchingDate = $this->tradeService->getTradesBySelectedDate($monthSelected, $yearSelected);
        foreach($tradesWithMatchingDate as $tradeWithMatchingDate){
            $tradeProfitLoss = $tradeWithMatchingDate -> profit_loss; 
            if($tradeProfitLoss != null){
                $totalMonthlyProfitLoss += $tradeProfitLoss;
            }
        }
        $totalMonthlyProfitLoss = number_format((float)$totalMonthlyProfitLoss, 2, '.', '');
        return $totalMonthlyProfitLoss;
    }
}

?>