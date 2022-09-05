<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use App\Models\User;
use Auth;
use App\Models\Screenshot;
use App\Http\Controllers\ScreenshotsController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use DateTime;
use DatePeriod;
use DateInterval;

class TradesController extends Controller{

    public static function index(){
        $userId = Auth::id();
        $user = User::find($userId);
        $trades = Trade::where('user_id', $user->id)->get();
        if(count($trades) === 0){
            return[
                "success" => false,
                "response" => ["error" => "No trades found"]
            ];
        }
        else{
            return[
                "success" => true,
                "response" => ["trades" => $trades]
            ];
        }
    }

    public function delete($id){
        $trade = Trade::findOrFail($id);
        $screenshotsResponse = ScreenshotsController::getScreenshotsByTrade($id);
        $screenshots = $screenshotsResponse['response']['screenshots'];
        if(!empty($trade)){
            //**NEED TO UPDATE THIS CHECK LATER */
            if(!empty($screenshots)){
                // Log::info(print_r($screenshots, true));
                foreach($screenshots as $screenshot){
                    if (file_exists(public_path() . "//screenshots//" . $screenshot->screenshot_image)) {
                        unlink(public_path() . "//screenshots//" . $screenshot->screenshot_image);
                        $screenshot->delete();
                    }
                }
            }
            $trade->delete();
            return[
                "success" => true,
                "response" => ["trade" => $trade]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "The trade could not be deleted."]
            ];
        }
    }

    public function generateTradeHistory(){
        $trades = array();
        $trades = TradesController::getTradesByLatestMonth();
        if(!empty($trades)){
            $tradeMonth = TradesController::getTradeMonth($trades[0]);
            $tradeYear = TradesController::getTradeYear($trades[0]);
            $monthlyBalance = TradesController::getMonthlyBalance($tradeMonth, $tradeYear);
            $monthlyProfitLoss = TradesController::getMonthlyProfitLoss($tradeMonth, $tradeYear);
            $listOfTradeYears = TradesController::getListOfTradeYears();
            return[
                "success" => true,
                "response" => ["tradehistory" => ['trades' => $trades, 'listOfTradeYears' => $listOfTradeYears, 'tradeMonth' => $tradeMonth, 'tradeYear' => $tradeYear, 
                'monthlyBalance' => $monthlyBalance, 'monthlyProfitLoss' => $monthlyProfitLoss]]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "The trades do not exist and could not be generated"]
            ];
        }
    }

    // public function calculateMonthlyBalanceAndProfitLoss(Request $request){
    //     $monthSelected = $request->month;
    //     $yearSelected = $request->year;
    //     $monthlyBalance = TradesController::getMonthlyBalance($monthSelected, $yearSelected);
    //     $monthlyProfitLoss = TradesController::getMonthlyProfitLoss($monthSelected, $yearSelected);
    //     $monthlyTotals = array($monthlyBalance, $monthlyProfitLoss);
    //     return[
    //         "success" => true,
    //         "response" => ["monthlyTotals" => $monthlyTotals]
    //     ];
    //     // return json_encode($monthlyTotals);
    // }

    public static function getMonthlyBalance($monthSelected, $yearSelected){
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

    public function generateTradeChartDataSets(Request $request){
        $monthSelected;
        $yearSelected;
        $tradeChartDataSets = array();
        $timePeriod = $request->timePeriod;
        if($timePeriod == ("3 Month View") || $timePeriod == ("6 Month View")){
            $lastMonthChecked = "";
            $date = Carbon::now();
            $yearSelected = $date->year;
            $i = 0;
            $j = 0;
            if($timePeriod == ("3 Month View") ){
                /* check starts at 3 months ago, gets final balance of each month up to and not including the current month */
                $i=3;
            }
            else if($timePeriod == ("6 Month View") ){
                /* check starts at 6 months ago, gets final balance of each month up to and not including the current month  */
                $i=6;
            }

            $currentMonth = $date->format('m');
            $earliestMonth = $date->subMonth($i)->format('m');
            /* if earliest month is greater than the current month it means the time period check has crossed multiple years and the earliestMonth belongs to the year before */
            if($earliestMonth > $currentMonth){
                $yearSelected--;
            }
            $date = Carbon::now();
            /* loop through each month to be checked depending on time period */
            for($i; $i > 0; $i--){
                $monthSelected = $date->subMonth($i)->format('F');
                /* check has passed into the next year so increment the year, e.g December 2019 -> January 2020 */
                if($lastMonthChecked == "December" && $monthSelected == "January"){
                    $yearSelected++;
                }
                $lastMonthChecked = $monthSelected;
                $monthlyBalance = TradesController::getMonthlyBalance($monthSelected, $yearSelected);
                $tradeChartDataSets[$j] = $monthlyBalance;
                $date = Carbon::now();
                $j++;
            }
            if (count($tradeChartDataSets) > 0) {
                return[
                    "success" => true,
                    "response" => ["tradeChartDataSets" => $tradeChartDataSets]
                ];
            }
            else{
                return[
                    "success" => false,
                    "response" => ["error" => "The trade chart datasets could not be generated"]
                ];
            }
        }
        else if($timePeriod == ("Yearly View")){
            $monthsInYear = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $date = Carbon::now();
            $currentYear= $date->year;
            $currentMonth = $date->format('F');
            $yearSelected = $request->yearPeriod;
            //$tradeMonths = TradesController::getTradeMonthsByYear($yearSelected);
            foreach($monthsInYear as $month){
                if($currentYear == $yearSelected){
                    //If its December the final balance will be next years opening balance so we don't need it for current year dataset
                    if($month!=="December"){
                        //run first time to get previous years final balance, will be Januarys opening balance
                        if($month == "January"){
                            $previousYearDecemberFinalBalance = TradesController::getMonthlyBalance("December", $yearSelected-1);
                            $tradeChartDataSets[] = $previousYearDecemberFinalBalance;
                        }
                        $monthlyBalance = TradesController::getMonthlyBalance($month, $yearSelected);
                        $tradeChartDataSets[] = $monthlyBalance;
                        /* If we reach the current month we can't get data for the months after that so we break the loop */
                        if($month == $currentMonth){
                            break;
                        }
                    }
                }
                else{
                    if($month!=="December"){
                        if($month == "January"){
                            $previousYearDecemberFinalBalance = TradesController::getMonthlyBalance("December", $yearSelected-1);
                            $tradeChartDataSets[] = $previousYearDecemberFinalBalance;
                        }
                        $monthlyBalance = TradesController::getMonthlyBalance($month, $yearSelected);
                        $tradeChartDataSets[] = $monthlyBalance;
                    }
                }
            }
            if (count($tradeChartDataSets) > 0) {
                return[
                    "success" => true,
                    "response" => ["tradeChartDataSets" => $tradeChartDataSets]
                ];
            }
            else{
                return[
                    "success" => false,
                    "response" => ["error" => "The trade chart datasets could not be generated"]
                ];
            }
        }
        else if($timePeriod == ("Overall View")){
            $tradeYears = TradesController::getListOfTradeYears();
            $allYears = TradesController::getListOfAllYearsFromInitialTrade($tradeYears);
            $user = User::where('username', Auth::user()->username)->first();
            /* Adds starting balance and first trade year, will have same balance */
            $tradeChartDataSets[] = $user -> portfolio_size;
            $lastYearsBalance = $user -> portfolio_size;
            /* Loops through all years and gets the latest balance of each year, if trades don't exist in a year it'll use last years balance */
            foreach($allYears as $year){
                if(in_array($year, $tradeYears)){
                    $tradeMonths = TradesController::getTradeMonthsByYear($year);
                    $lastTradeMonth = $tradeMonths[count($tradeMonths)-1];
                    $monthlyBalance = TradesController::getMonthlyBalance($lastTradeMonth, $year);
                    $tradeChartDataSets[] = $monthlyBalance;
                    $lastYearsBalance = $monthlyBalance;
                }
                else{
                    $tradeChartDataSets[] = $lastYearsBalance;
                }
            }
            $trades = TradesController::getTradesByLatestMonth();
            if(!empty($trades)){
                $tradeMonth = TradesController::getTradeMonth($trades[0]);
                $tradeYear = TradesController::getTradeYear($trades[0]);
                $monthlyBalance = TradesController::getMonthlyBalance($tradeMonth, $tradeYear);
                $tradeChartDataSets[] = $monthlyBalance;
            }
            if (count($tradeChartDataSets) > 0) {
                return[
                    "success" => true,
                    "response" => ["tradeChartDataSets" => $tradeChartDataSets]
                ];
            }
            else{
                return[
                    "success" => false,
                    "response" => ["error" => "The trade chart datasets could not be generated"]
                ];
            }
        }
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

    public function getMonthlyProfitLoss($monthSelected, $yearSelected){
        $totalMonthlyProfitLoss = 0;
        $tradesWithMatchingDate = TradesController::getTradesBySelectedDate($monthSelected, $yearSelected);
        foreach($tradesWithMatchingDate as $tradeWithMatchingDate){
            $tradeProfitLoss = $tradeWithMatchingDate -> profit_loss; 
            if($tradeProfitLoss != null){
                $totalMonthlyProfitLoss += $tradeProfitLoss;
            }
        }
        $totalMonthlyProfitLoss = number_format((float)$totalMonthlyProfitLoss, 2, '.', '');
        return $totalMonthlyProfitLoss;
    }

    public static function getTradesByLatestMonth(){
        $tradesFromLatestMonth = array();
        $userId = Auth::id();
        $trades = Trade::all()->where('user_id', $userId);
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

    public static function getListOfTradeYears(){
        $listOfYears = array();
        $userId = Auth::id();
        $trades = Trade::all()->where('user_id', $userId);
        if(count($trades) !== 0){
            foreach($trades as $trade){
                $tradeDate = $trade -> date_trade_opened;
                $date = new Carbon( $tradeDate );
                $year = $date -> year;
                /* check so don't add same year twice */
                if(!in_array($year, $listOfYears)){
                    $listOfYears [] = $year;
                }
            }
            rsort($listOfYears);
        }
        return $listOfYears;
    }

    public static function getListOfTradeYearsWithClosedTrades(){
        $listOfYears = array();
        $userId = Auth::id();
        $trades = Trade::all()->where('user_id', $userId);
        foreach($trades as $trade){
            $tradeDate = $trade -> date_trade_opened;
            $isTradeOpened = $trade -> trade_opened;
            $date = new Carbon( $tradeDate );
            $year = $date -> year;
            if($isTradeOpened == false){
                if(!in_array($year, $listOfYears)){
                    $listOfYears [] = $year;
                }
            }
        }
        sort($listOfYears);
        return $listOfYears;
    }

    /* Builds a list of trades by a selected month and year*/
    public function getTradesBySelectedDate($monthSelected, $yearSelected){
        $tradesWithMatchingDate = array();
        $userId = Auth::id();
        $trades = Trade::all()->where('user_id', $userId);
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

    public function addNewTrade(Request $req){
        $validatedData = $req->validate([
            'asset_name' => 'required',
            'trade_size' => 'required|numeric|min:0.00|max:10000000.00',
            'trade_value' => 'required|numeric|min:0.00|max:100000000.00',
            'date_trade_opened' => 'required|date_format:Y-m-d|before:tomorrow|after_or_equal:2017-01-01',
            'price_purchased_at' => 'required|numeric|min:0.00|max:10000000.00',
            'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            // 'screenshots.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $trade = new Trade();
        $trade->symbol = "";
        $trade->holding_name = $req->input('asset_name');
        $trade->trade_size = $req->input('trade_size');
        $trade->trade_value = $req->input('trade_value');
        $trade->date_trade_opened = $req->input('date_trade_opened');
        $trade->date_trade_closed = null;
        $trade->price_purchased_at = $req->input('price_purchased_at');
        $trade->price_closed_at = null;
        $trade->trade_opened = true;
        $trade->profit_loss = null;
        $trade->has_screenshots = false;
        $userId = Auth::id();
        $user = User::find($userId);
        $images = $req->file('screenshots');
        
        if(is_array($images)){
            if(count($images) > 1){
                $trade->has_screenshots = true;
                $trade->save();
                $user->trades()->save($trade);
                /* Loops through each screenshot uploaded and associates it with a trade */
                foreach($images as $key => $image){
                    $name=$image->getClientOriginalName();
                    $screenshot = new Screenshot();
                    $screenshot->save();
                    $screenshotNameWithId = str_replace(".png", "_" . $screenshot->id, $name);
                    $screenshotFileName =  $screenshotNameWithId . ".png";
                    $screenshot -> screenshot_image = $screenshotFileName;
                    $screenshot->save();
                    $image->move('screenshots', $screenshotFileName);
                    $trade->existingScreenshots()->save($screenshot);
                }
            }
            /* Handles single screenshot upload */
            else if(count($images) === 1){
                $trade->has_screenshots = true;
                $trade->save();
                $user->trades()->save($trade);
                $oneImage = $images[0];
                $name=$oneImage->getClientOriginalName();
                $screenshot = new Screenshot();
                $screenshot->save();
                $screenshotNameWithId = str_replace(".png", "_" . $screenshot->id, $name);
                $screenshotFileName =  $screenshotNameWithId . ".png";
                $screenshot -> screenshot_image = $screenshotFileName;
                $screenshot->save();
                $oneImage->move('screenshots', $screenshotFileName);
                $trade->existingScreenshots()->save($screenshot);
            }
        }

        $trade->save();
        $user->trades()->save($trade);

        if(empty($trade)) {
            return[
                "success" => false,
                "response" => ["error" => "Trade was not created successfully"]
            ];
        }
        else{
            return[
                "success" => true,
                "response" => ["trade" => $trade]
            ];
        }
    }

    public function closeTrade(Request $req){
        TradesController::validateCloseTradeInputData($req);
        $trade = Trade::findOrFail($req->id);
        $dateTradeOpened = $trade->date_trade_opened;
        $dateTradeClosed = $req->date_trade_closed;
        $priceClosedAt = $req->price_closed_at;
        $profitLossTotal = $req->profit_loss;
        $userId = Auth::id();
        $user = User::find($userId);
        $trade->date_trade_closed = $dateTradeClosed;
        $trade->price_closed_at = $priceClosedAt;
        $trade->profit_loss = $profitLossTotal;
        $trade->trade_opened = false;
        $trade->save();
        $user->trades()->save($trade);

        $trade = Trade::findOrFail($req->id);

        if($trade->trade_opened === false) {
            return[
                "success" => false,
                "response" => ["error" => "Trade was not closed successfully"]
            ];
        }
        else{
            return[
                "success" => true,
                "response" => ["trade" => $trade]
            ];
        }
    }

    /* get all months with a trade in it in a specific year */
    public function getTradeMonthsByYear($yearSelected){
        $tradeMonthsWithMatchingYear = array();
        $userId = Auth::id();
        $trades = Trade::all()->where('user_id', $userId);
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

    /* For trade history will update the trades displayed if month is changed*/
    public function getTradeDataForSelectedDate(Request $request){
        $monthSelected = $request->selectedMonth;
        $yearSelected = $request->selectedYear;
        if(count(TradesController::getListOfTradeYears()) !== 0){
            if($yearSelected === -1){
                $yearSelected = TradesController::getListOfTradeYears()[0];
            }
            if($monthSelected === "Month"){
                $trades = array();
                $trades = TradesController::getTradesByLatestMonth();
                if(!empty($trades)){
                    $monthSelected = TradesController::getTradeMonth($trades[0]);
                }
            }

            $tradesWithMatchingDate = TradesController::getTradesBySelectedDate($monthSelected, $yearSelected);
            $monthlyBalance = TradesController::getMonthlyBalance($monthSelected, $yearSelected);
            $monthlyProfitLoss = TradesController::getMonthlyProfitLoss($monthSelected, $yearSelected);

            return[
                "success" => true,
                "response" => ["tradesWithMatchingDate" => $tradesWithMatchingDate, "monthlyBalance" => $monthlyBalance, "monthlyProfitLoss" => $monthlyProfitLoss]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => "No trades exist"
            ];
        }
        //return json_encode($tradesAndDateData);
    }

    public static function getCurrentPortfolioSize(){
        $trades = array();
        $trades = TradesController::getTradesByLatestMonth();
        if(!empty($trades)){
            $tradeMonth = TradesController::getTradeMonth($trades[0]);
            $tradeYear = TradesController::getTradeYear($trades[0]);
            $monthlyBalance = TradesController::getMonthlyBalance($tradeMonth, $tradeYear);
            return $monthlyBalance;
        }
        else{
            $userId = Auth::id();
            $user = User::find($userId);
            $portfolioSize = $user->portfolio_size;
            return $portfolioSize;
        }
    }

    public static function getTradeMonth($trade){
        $tradeDate = $trade -> date_trade_opened;
        $date = new Carbon( $tradeDate );
        $month = $date->format('F');
        return $month;
    }

    public static function getTradeYear($trade){
        $tradeDate = $trade -> date_trade_opened;
        $date = new Carbon( $tradeDate );
        $year = strval($date -> year);
        return $year;
    }

    public function validateCloseTradeInputData(Request $req){
        $validatedData = $req->validate([
            'date_trade_closed' =>'required|date_format:Y-m-d|before:tomorrow|after_or_equal:' . $req->date_trade_opened,
            'price_closed_at' => 'required|numeric|min:0.00|max:10000000.00',
            'profit_loss' => 'required|numeric|between:-9999999999.99,9999999999.99',
        ], 
        [
            'date_trade_closed.required' => 'The date trade closed field is required.',
            'date_trade_closed.date_format' => 'The date trade closed field does not match the format Y-m-d.',
            'date_trade_closed.before' => 'The date trade closed field must be a date before tomorrow.',
            'date_trade_closed.after' => 'The date trade closed field must be a date after ' . $req->date_trade_opened,
            'price_closed_at.required' => 'The priced closed at field is required.',
            'price_closed_at.numeric' =>  'The price closed at field must be a number.',
            'price_closed_at.min' => 'The price closed at field must be at least 0.00.',
            'price_closed_at.max' =>  'The price closed at field may not be greater than 1000000.00.',
            'profit_loss.required' => 'The profit/loss field is required.',
            'profit_loss.numeric' => 'The profit/loss field must be a number.',
            // 'profit_loss.between' => 'The profit/loss field must be within two decimal places.',
        ]
        );
    }
}