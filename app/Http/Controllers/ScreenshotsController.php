<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;
use App\Models\User;
use App\Models\Screenshot;
use Auth;
use Illuminate\Support\Facades\Log;

class ScreenshotsController extends Controller
{

    public function store(Request $req){
        $images = $req->file('screenshots');
        $userId = Auth::id();
        $user = User::find($userId);
        $trade = Trade::findOrFail($req->tradeId);
        /* Multiple screenshots uploaded*/
        if(is_array($images)){
            $trade->has_screenshots = true;
            $trade->save();
            $user->trades()->save($trade);
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

            return[
                "success" => true,
                "response" => ["screenshots" => $images]
            ];
            
        }
        /* One screenshot uploaded*/
        else{
            $trade->has_screenshots = true;
            $trade->save();
            $user->trades()->save($trade);
            $oneImage = $images;
            $name=$oneImage->getClientOriginalName();
            $screenshot = new Screenshot();
            $screenshot->save();
            $screenshotNameWithId = str_replace(".png", "_" . $screenshot->id, $name);
            $screenshotFileName =  $screenshotNameWithId . ".png";
            $screenshot -> screenshot_image = $screenshotFileName;
            $screenshot->save();
            $image->move('screenshots', $screenshotFileName);
            $trade->existingScreenshots()->save($screenshot);

            return[
                "success" => true,
                "response" => ["screenshots" => $oneImage]
            ];
        }
        $trade->save();
        $user->trades()->save($trade);
    }

    public static function getScreenshotsByTrade($tradeID){
        $screenshotPathData = array();
        $screenshots = Screenshot::where('trade_id', $tradeID)->get();
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

    public function delete($activeScreenshotPath){
        $id = ScreenShotsController::parsePathForID($activeScreenshotPath, "_", ".png");
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

    public function parsePathForID($string, $start, $end){
        $string = ' ' . $string;
        $ini = strrpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}