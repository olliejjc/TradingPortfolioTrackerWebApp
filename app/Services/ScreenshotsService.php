<?php

namespace App\Services;

use App\Models\Screenshot;

class ScreenshotsService{

    public function createScreenshot($image){
        $name = $image->getClientOriginalName();
        $screenshot = new Screenshot();
        $screenshot -> save();
        $screenshotNameWithId = str_replace(['.png', '.jpg'], '_' . $screenshot->id, $name);
        $screenshotFileName = $screenshotNameWithId . '.png';
        $screenshot->screenshot_image = $screenshotFileName;
        $screenshot->save();
        $image->move('screenshots', $screenshotFileName);
        return $screenshot;
    }

    public function parsePathForID($string, $start, $end){
        $string = ' ' . $string;
        $ini = strrpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function getScreenshotsByTrade($tradeId){
        $screenshots = Screenshot::where('trade_id', $tradeId)->get();
        return $screenshots;
    }

    public function deleteScreenshots($screenshots) {
        foreach($screenshots as $screenshot){
            if (file_exists(public_path() . "//screenshots//" . $screenshot->screenshot_image)) {
                unlink(public_path() . "//screenshots//" . $screenshot->screenshot_image);
                $screenshot->delete();
            }
        }
    }
}