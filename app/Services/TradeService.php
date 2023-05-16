<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trade;
use App\Services\MonthlyBalanceService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TradeService
{
    protected $monthlyBalanceService;

    public function __construct(MonthlyBalanceService $monthlyBalanceService){
        $this->monthlyBalanceService = $monthlyBalanceService;
    }

    public function getListOfTradeYears(){
        $listOfYears = array();
        $userId = Auth::id();
        $trades = Trade::where('user_id', $userId)->get();

        if(count($trades) !== 0){
            $listOfYears = $this -> getYearsByTradeDates($trades);
            rsort($listOfYears);
        }
        return $listOfYears;
    }

    // public function getListOfTradeYearsWithClosedTrades(){
    //     $listOfYears = array();
    //     $userId = Auth::id();
    //     $trades = Trade::where('user_id', $userId)->get();
    //     $listOfYears = $this -> getYearsByTradeDates($trades, true);
    //     sort($listOfYears);
    //     return $listOfYears;
    // }

    public function getYearsByTradeDates($trades){
        $years = [];
        foreach ($trades as $trade) {
            $tradeDate = $trade->date_trade_opened;
            //$isTradeOpened = $trade->trade_opened;
            $date = new Carbon($tradeDate);
            $year = $date->year;
            $years[] = $year;
            
            // if ($withClosedTrades && !$isTradeOpened) {
            //     $years[] = $year;
            // } elseif (!$withClosedTrades && $isTradeOpened) {
            //     $years[] = $year;
            // }
        }
        return array_unique($years);
    }

    /* Gets all years between first trade year and the current year */
    function getListOfAllYearsFromInitialTrade($tradeYears){
        $listOfYears = array();
        if(!empty($tradeYears)){
            $startYear = min($tradeYears);
            $currentYear = date("Y");
            for($year=$startYear; $year<=$currentYear; $year++) {
                $listOfYears [] = $year;
            }
        }
        return $listOfYears;
    }

    // public function getListOfTradeYears()
    // {
    //     return Trade::distinct()->pluck('year')->toArray();
    // }

    /* Builds a list of trades by a selected month and year*/
    public function getTradesBySelectedDate($monthSelected, $yearSelected){
        $tradesWithMatchingDate = array();
        $userId = Auth::id();
        $trades = Trade::where('user_id', $userId)->get();
        foreach($trades as $trade){
            $tradeDate = $trade -> date_trade_opened;
            $date = new Carbon( $tradeDate );
            $year = strval($date -> year);
            $month = $date->format('F');
            if($monthSelected == "All Months"){
                if($year==$yearSelected){
                    $tradesWithMatchingDate [] = $trade;
                }
            }
            else{
                if($year==$yearSelected && $month==$monthSelected){
                    $tradesWithMatchingDate [] = $trade;
                }
            }
        }
        $dateTradeOpenedColumn = array_column($tradesWithMatchingDate, 'date_trade_opened');
        array_multisort($dateTradeOpenedColumn, SORT_ASC, $tradesWithMatchingDate);
        return $tradesWithMatchingDate;
    }

    public static function getTradesByLatestMonth(){
        $tradesFromLatestMonth = array();
        $userId = Auth::id();
        $trades = Trade::where('user_id', $userId)->get();
        $latestMonthChecked = 0;
        $latestYearChecked = 0;
        foreach($trades as $trade){
            $tradeDate = $trade -> date_trade_opened;
            $date = new Carbon( $tradeDate );
            $year = $date -> year;
            $month = $date-> month;
            /* handles if there's only one trade in the latest year */
            if($year > $latestYearChecked){
                $tradesFromLatestMonth = array();
                $latestYearChecked = $year;
                $latestMonthChecked = $month;
                $tradesFromLatestMonth [] = $trade;
            }
            /* if the trade is in the same year as the latest year checked */
            else if($year == $latestYearChecked){
                /* If the month is greater than the latest month checked than reset tradesFromLatestMonth and start building it again */
                if($month > $latestMonthChecked){
                    $tradesFromLatestMonth = array();
                    $latestMonthChecked = $month;
                    $tradesFromLatestMonth [] = $trade;
                }
                /* If the trade is in the latest month checked add it to tradesFromLatestMonth */
                else if($month == $latestMonthChecked){
                    $tradesFromLatestMonth [] = $trade;
                }
            }
        }
        $dateTradeOpenedColumn = array_column($tradesFromLatestMonth, 'date_trade_opened');
        array_multisort($dateTradeOpenedColumn, SORT_ASC, $tradesFromLatestMonth);
        return $tradesFromLatestMonth;
    }

    /* get all months with a trade in it in a specific year */
    public function getTradeMonthsByYear($yearSelected){
        $tradeMonthsWithMatchingYear = array();
        $userId = Auth::id();
        $trades = Trade::where('user_id', $userId)->get();
        foreach($trades as $trade){
            $tradeDate = $trade -> date_trade_opened;
            $date = new Carbon( $tradeDate );
            $year = strval($date -> year);
            $month = $date->format('F');
            if($year==$yearSelected){
                if(!in_array($month, $tradeMonthsWithMatchingYear)){
                    $tradeMonthsWithMatchingYear [] = $month;
                }
            }
        }
        return $tradeMonthsWithMatchingYear;
    }

    public function getTradeMonth($trade){
        $tradeDate = $trade -> date_trade_opened;
        $date = new Carbon( $tradeDate );
        $month = $date->format('F');
        return $month;
    }

    public function getTradeYear($trade){
        $tradeDate = $trade -> date_trade_opened;
        $date = new Carbon( $tradeDate );
        $year = strval($date -> year);
        return $year;
    }

    public function generateTradeChartDataSetsForMonths($months){
        $tradeChartDataSets = [];
        $lastMonthChecked = "";
        $date = Carbon::now();
        $yearSelected = $date->year;
        $index = 0;

        $currentMonth = $date->format('m');
        $earliestMonth = $date->subMonth($index)->format('m');
        /* if earliest month is greater than the current month it means the time period check has crossed multiple years and the earliestMonth belongs to the year before */
        if($earliestMonth > $currentMonth){
            $yearSelected--;
        }
        /* loop through each month to be checked depending on time period */
        for($i = $months; $i > 0; $i--){
            $monthSelected = $date->subMonth($i)->format('F');
            /* check has passed into the next year so increment the year, e.g December 2019 -> January 2020 */
            if($lastMonthChecked == "December" && $monthSelected == "January"){
                $yearSelected++;
            }
            $lastMonthChecked = $monthSelected;
            $monthlyBalance = $this-> monthlyBalanceService -> getUserMonthlyBalance($monthSelected, $yearSelected);
            $tradeChartDataSets[$index] = $monthlyBalance;
            $date = Carbon::now();
            $index++;
        }
        return $tradeChartDataSets;
    }

    public function generateTradeChartDataSetsForYear($yearSelected){
        $tradeChartDataSets = [];
        $monthsInYear = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->format('F');
        
        foreach ($monthsInYear as $month) {
            if ($currentYear == $yearSelected && $month == $currentMonth) {
                break;
            }
            
            if ($month == "January") {
                $previousYearDecemberFinalBalance = $this->monthlyBalanceService->getUserMonthlyBalance("December", $yearSelected - 1);
                $tradeChartDataSets[] = $previousYearDecemberFinalBalance;
            }
            
            if ($month !== "December") {
                $monthlyBalance = $this->monthlyBalanceService->getUserMonthlyBalance($month, $yearSelected);
                $tradeChartDataSets[] = $monthlyBalance;
            }
        }
        
        return $tradeChartDataSets;
    }


    public function generateTradeChartDataSetsOverall(){
        $user = User::where('username', Auth::user()->username)->first();
        $startingBalance = $user -> portfolio_size;
        $tradeYears = $this->getListOfTradeYears();
        $lastYearsBalance = $startingBalance;
        /* Adds starting balance and first trade year, will have same balance */
        $tradeChartDataSets[] = $startingBalance;
        $allYears = $this->getListOfAllYearsFromInitialTrade($tradeYears);
        /* Loops through all years and gets the latest balance of each year, if trades don't exist in a year it'll use last years balance */
        foreach($allYears as $year){
            if(in_array($year, $tradeYears)){
                $tradeMonths = $this->getTradeMonthsByYear($year);
                $lastTradeMonth = $tradeMonths[count($tradeMonths)-1];
                $lastYearsBalance = $this->monthlyBalanceService->getUserMonthlyBalance($lastTradeMonth, $year);
            }

            $tradeChartDataSets[] = $lastYearsBalance;
        }
        $latestTrades = $this->getTradesByLatestMonth();
        if(!empty($latestTrades)){
            $latestTrade = $latestTrades[0];
            $tradeMonth = $this->getTradeMonth($latestTrade);
            $tradeYear = $this->getTradeYear($latestTrade);
            $monthlyBalance = $this->monthlyBalanceService->getUserMonthlyBalance($tradeMonth, $tradeYear);
            $tradeChartDataSets[] = $monthlyBalance;
        }

        return $tradeChartDataSets;
    }

}

?>