<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;

class interactiveBrokersService{
    public function getInteractiveBrokersData(){
        $process = new Process(['C:\laragon\www\tradingportfoliotracker\resources\IBC\StartGateway.bat']);
        // $process->setWorkingDirectory('C:\IBC');
        // $process->setEnv(getenv());
        // $process->start();
        // $process->waitUntil(function ($type, $output) {
        //     return $output === 'Ready. Waiting for commands...';
        // });        
        // echo $process->getOutput();
        $accountDataResponse = Http::withoutVerifying()->get('https://localhost:5000/v1/portal/portfolio/accounts');
        $response = Http::withoutVerifying()->get('https://localhost:5000/v1/portal/portfolio/U2479245/summary');
        $response = Http::withoutVerifying()->get('https://localhost:5000/v1/portal/iserver/accounts');
        return $response;
    }
}
?>