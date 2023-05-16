<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use App\Models\User;
use App\Models\Screenshot;
use App\Services\ScreenshotsService;
use Illuminate\Support\Facades\Auth;

class ScreenshotsController extends Controller
{
    protected $screenshotsService;

    public function __construct(ScreenshotsService $screenshotsService){
        $this->screenshotsService = $screenshotsService;
    }

    public function store(Request $request){
        $images = $request->file('screenshots');
        $userId = Auth::id();
        $user = User::find($userId);
        $trade = Trade::findOrFail($request->tradeId);
        /* Multiple screenshots uploaded*/
        if (is_array($images)) {
            if (count($images) > 1) {
                $trade->has_screenshots = true;
                foreach ($images as $key => $image) {
                    $screenshot = $this->screenshotsService->createScreenshot($image);
                    $trade->existingScreenshots()->save($screenshot);
                }
                $response = ["screenshots" => $images];
            }
            else if(count($images) === 1){
                $trade->has_screenshots = true;
                $oneImage = $images[0];
                $screenshot = $this->screenshotsService->createScreenshot($oneImage);
                $trade->existingScreenshots()->save($screenshot);
                $response = ["screenshots" => $images];
            }
        }
        $trade->save();
        $user->trades()->save($trade);
        return [
            "success" => true,
            "response" => $response
        ];
    }

    public function delete($activeScreenshotPath){
        $id = $this -> screenshotsService -> parsePathForID($activeScreenshotPath, "_", ".png");
        $screenshot = Screenshot::findOrFail($id);
        $tradeID = $screenshot -> trade_id;
        if(!is_null($activeScreenshotPath) && !empty($activeScreenshotPath)){
            if (file_exists(public_path() . "//screenshots//" . $activeScreenshotPath)) {
                unlink(public_path() . "//screenshots//" . $activeScreenshotPath);
                $screenshot->delete();
                $screenshots = Screenshot::where('trade_id', $tradeID)->get();
                if(count($screenshots) === 0){
                    // Log::info(print_r($tradeID, true));
                    $trade = Trade::findOrFail($tradeID);
                    $trade->has_screenshots = false;
                    $trade->save();
                }
                return[
                    "success" => true,
                    "response" => ["screenshot" => $screenshot]
                ];
            }
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "The screenshot or screenshot path does not exist"]
            ];
        }
    }

    public function retrieveScreenshotsByTrade($tradeId){
        $screenshots = $this -> screenshotsService -> getScreenshotsByTrade($tradeId);
        if(!is_null($screenshots)){
            return[
                "success" => true,
                "response" => ["screenshots" => $screenshots]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "No screenshots are available for this trade"]
            ];
        }
    }
}