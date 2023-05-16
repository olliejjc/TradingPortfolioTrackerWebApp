<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTradeRequest;
use App\Http\Requests\CloseTradeRequest;
use App\Models\Trade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Services\UserService;
use App\Services\TradeService;
use App\Services\ScreenshotsService;
use App\Services\MonthlyBalanceService;

class TradesController extends Controller{

    protected $userService;
    protected $tradeService;
    protected $screenshotsService;
    protected $monthlyBalanceService;

    public function __construct(UserService $userService, TradeService $tradeService, ScreenshotsService $screenshotsService, MonthlyBalanceService $monthlyBalanceService){
        $this->userService = $userService;
        $this->tradeService = $tradeService;
        $this->screenshotsService = $screenshotsService;
        $this->monthlyBalanceService = $monthlyBalanceService;
    }

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

    public function store(StoreTradeRequest $request){
        $trade = new Trade();
        $trade->symbol = "";
        $trade->holding_name = $request->input('asset_name');
        $trade->trade_size = $request->input('trade_size');
        $trade->trade_value = $request->input('trade_value');
        $trade->date_trade_opened = $request->input('date_trade_opened');
        $trade->date_trade_closed = null;
        $trade->price_purchased_at = $request->input('price_purchased_at');
        $trade->price_closed_at = null;
        $trade->trade_opened = true;
        $trade->profit_loss = null;
        $trade->has_screenshots = false;
        $userId = Auth::id();
        $user = User::find($userId);
        $images = $request->file('screenshots');
        $trade->save();
        $user->trades()->save($trade);
        
        if (is_array($images)) {
            if (count($images) > 1) {
                $trade->has_screenshots = true;
                foreach ($images as $key => $image) {
                    $screenshot = $this->screenshotsService->createScreenshot($image);
                    $trade->existingScreenshots()->save($screenshot);
                }
            }
            else if(count($images) === 1){
                $trade->has_screenshots = true;
                $oneImage = $images[0];
                $screenshot = $this->screenshotsService->createScreenshot($oneImage);
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

    public function delete($id){
        $trade = Trade::findOrFail($id);
        $screenshots = $this -> screenshotsService -> getScreenshotsByTrade($id);
        if(!empty($trade)){
            if(!empty($screenshots)){
                $this -> screenshotsService -> deleteScreenshots($screenshots);
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
        $trades = $this -> tradeService -> getTradesByLatestMonth();
        if(!empty($trades)){
            $tradeMonth = $this -> tradeService -> getTradeMonth($trades[0]);
            $tradeYear = $this -> tradeService -> getTradeYear($trades[0]);
            $monthlyBalance = $this -> monthlyBalanceService -> getUserMonthlyBalance($tradeMonth, $tradeYear);
            $monthlyProfitLoss = $this -> userService -> getUserMonthlyProfitLoss($tradeMonth, $tradeYear);
            $listOfTradeYears = $this -> tradeService -> getListOfTradeYears();
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
    //     $monthlyBalance = $this -> monthlyBalanceService -> getUserMonthlyBalance($monthSelected, $yearSelected);
    //     $monthlyProfitLoss =  $this -> userService -> getUserMonthlyProfitLoss($monthSelected, $yearSelected);
    //     $monthlyTotals = array($monthlyBalance, $monthlyProfitLoss);
    //     return[
    //         "success" => true,
    //         "response" => ["monthlyTotals" => $monthlyTotals]
    //     ];
    //     // return json_encode($monthlyTotals);
    // }

    const THREE_MONTH_VIEW = "3 Month View";
    const SIX_MONTH_VIEW = "6 Month View";
    const YEARLY_VIEW = "Yearly View";
    const OVERALL_VIEW = "Overall View";     

    public function generateTradeChartDataSets(Request $request){
        $timePeriod = $request->timePeriod;
        switch ($timePeriod) {
            case self::THREE_MONTH_VIEW:
                $months = 3;
                $tradeChartDataSets = $this->tradeService->generateTradeChartDataSetsForMonths($months);
                break;
            case self::SIX_MONTH_VIEW:
                $months = 6;
                $tradeChartDataSets = $this->tradeService->generateTradeChartDataSetsForMonths($months);
                break;
            case self::YEARLY_VIEW:
                $yearSelected = $request->yearPeriod;
                $tradeChartDataSets = $this->tradeService->generateTradeChartDataSetsForYear($yearSelected);
                break;
            case self::OVERALL_VIEW:
                $tradeChartDataSets = $this->tradeService->generateTradeChartDataSetsOverall();
                break;
            default:
                return [
                    "success" => false,
                    "response" => ["error" => "Invalid time period selected"],
                ];
        }
    
        if (count($tradeChartDataSets) > 0) {
            return [
                "success" => true,
                "response" => ["tradeChartDataSets" => $tradeChartDataSets],
            ];
        } else {
            return [
                "success" => false,
                "response" => ["error" => "The trade chart datasets could not be generated"],
            ];
        }
    }

    public function closeTrade(CloseTradeRequest $request){
        $trade = Trade::findOrFail($request->id);
        $dateTradeClosed = $request->date_trade_closed;
        $priceClosedAt = $request->price_closed_at;
        $profitLossTotal = $request->profit_loss;
        $userId = Auth::id();
        $user = User::find($userId);
        $trade->date_trade_closed = $dateTradeClosed;
        $trade->price_closed_at = $priceClosedAt;
        $trade->profit_loss = $profitLossTotal;
        $trade->trade_opened = false;
        $trade->save();
        $user->trades()->save($trade);

        $trade = Trade::findOrFail($request->id);

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

    public function retrieveListOfTradeYears(){
        $listOfYears = $this->tradeService->getListOfTradeYears();
        return $listOfYears;
    }

    /* For trade history will update the trades displayed if month is changed*/
    public function getTradeDataForSelectedDate(Request $request){
        $monthSelected = $request->selectedMonth;
        $yearSelected = $request->selectedYear;
        $tradeYears = $this -> tradeService -> getListOfTradeYears();

        if (count($tradeYears) === 0) {
            return [
                "success" => false,
                "response" => "No trades exist",
            ];
        }

        if ($yearSelected === -1) {
            $yearSelected = $tradeYears[0];
        }

        if ($monthSelected === "Month") {
            $latestTrades = $this->tradeService->getTradesByLatestMonth();
            if (!empty($latestTrades)) {
                $monthSelected = $this->tradeService->getTradeMonth($latestTrades[0]);
            }
        }    

        $tradesWithMatchingDate = $this -> tradeService -> getTradesBySelectedDate($monthSelected, $yearSelected);
        $monthlyBalance = $this -> monthlyBalanceService -> getUserMonthlyBalance($monthSelected, $yearSelected);
        $monthlyProfitLoss = $this -> userService -> getUserMonthlyProfitLoss($monthSelected, $yearSelected);

        return[
            "success" => true,
            "response" => [
                "tradesWithMatchingDate" => $tradesWithMatchingDate, 
                "monthlyBalance" => $monthlyBalance, 
                "monthlyProfitLoss" => $monthlyProfitLoss
            ]
        ];

        //return json_encode($tradesAndDateData);
    }
}