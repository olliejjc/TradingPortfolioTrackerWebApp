<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller{

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

    public function store(UserStoreRequest $request){
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
    }

    public function update(UserUpdateRequest $request){
        $id = Auth::id();
        $user = User::where('id', $id)->first();
        if(!empty($user)){
            $user -> portfolio_size = $request->input('portfolio_size');
            $user -> risk_percentage_per_trade = $request->input('risk_percentage_per_trade');
            $user -> binance_apikey = $request->input('binance_apikey');
            $user -> binance_secretkey = $request->input('binance_secretkey');
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
}