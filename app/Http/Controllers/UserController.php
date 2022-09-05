<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\TradesController;
use Illuminate\Validation\Rules;

class UserController extends Controller{

    public function showAuthenticatedUser(){
        $userId = Auth::id();
        $user = User::find($userId);
        if(empty($user)){
            return[
                "success" => false,
                "response" => ["error" => "No user found"]
            ];
        }

        return[
            "success" => true,
            "response" => ["user" => $user]
        ];
    }

    public function store(Request $request)
    {
        //return $request;

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', Rules\Password::defaults()],
            'passwordConfirm' => 'required|same:password',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
            "role" => "User",
        ]);

        if(User::find($user->id) === null) {
            return[
                "success" => false,
                "response" => ["error" => "User was not created successfully"]
            ];
        }
        else{
            return[
                "success" => true,
                "response" => ["user" => $user]
            ];
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
    
    /* Retrieve user details for use on risk calculator */
    public function showRiskCalculatorSettings(){
        $user = User::where('username', Auth::user()->username)->first();
        $currentPortfolioSize = TradesController::getCurrentPortfolioSize();
        if(!empty($user) || $currentPortfolioSize !== -1){
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

    public function showUserSettings(){
        $id = Auth::id();
        $user = User::where('id', $id)->first();
        if(!empty($user)){
            return[
                "success" => true,
                "response" => ["user" => $user]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "Authorized user does not exist"]
            ];
        }
    }

    public function update(Request $req){
        $id = Auth::id();
        $user = User::where('id', $id)->first();
        if(!empty($user)){
            $validatedData = $req->validate([
                'portfolio_size' => 'required|numeric|min:0|max:100000000',
                'risk_percentage_per_trade' => 'required|numeric|min:0.00|max:100.00',
            ]);
            $user -> portfolio_size = $req->input('portfolio_size');
            $user -> risk_percentage_per_trade = $req->input('risk_percentage_per_trade');
            $user -> binance_apikey = $req->input('binance_apikey');
            $user -> binance_secretkey = $req->input('binance_secretkey');
            $user -> save();
            return[
                "success" => true,
                "response" => ["user" => $user]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => "Authorized user does not exist or could not be updated"
            ];
        }
    }

    public static function show($username){
        $user = User::where('username', $username)->first();
        if(!empty($user)){
            return[
                "success" => true,
                "response" => ["user" => $user]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => "Authorized user does not exist"
            ];
        }
    }
}