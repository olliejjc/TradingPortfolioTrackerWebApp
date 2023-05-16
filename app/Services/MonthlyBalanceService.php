<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trade;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MonthlyBalanceService{
    public function getUserMonthlyBalance($monthSelected, $yearSelected){
        $user = User::where('username', Auth::user()->username)->first();
        $portfolioSize = $user -> portfolio_size;
        $trades = Trade::where('user_id', $user->id)->get();
        foreach($trades as $trade){
            $tradeDate = $trade -> date_trade_opened;
            $date = new Carbon($tradeDate);
            $year = strval($date -> year);
            $month = $date->format('F');
            $tradeProfitLoss = $trade -> profit_loss;
            //Need to add the first day to parse as if you don't define it it uses the 31st which doesnt exist for some months
            $monthValue = Carbon::parse('1 ' . $month)->month;
            /* checks if there's profit/loss on the trade and then add remove it from the portfolio balance */
            if($tradeProfitLoss != null){
                if($monthSelected != "All Months"){
                    if($year < $yearSelected){
                        $portfolioSize += $tradeProfitLoss;
                    }
                    else if($year == $yearSelected){
                        $monthSelectedValue = Carbon::parse('1 ' . $monthSelected)->month;
                        if($monthValue <= $monthSelectedValue){
                            $portfolioSize += $tradeProfitLoss;
                        }
                    }
                }
                else{
                    if($year < $yearSelected || $year == $yearSelected){
                        $portfolioSize += $tradeProfitLoss;
                    }
                }
            }
        }
        $portfolioSize = number_format((float)$portfolioSize, 2, '.', '');
        return $portfolioSize;
    }
}