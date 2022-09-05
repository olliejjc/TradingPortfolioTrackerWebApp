<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function getAuthenticatedUser(){
        if(Auth::check()){
            $id = Auth::id();
            $user = User::with('school')->where('id', $id)->first();
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
        else{
            return[
                "success" => false,
                "response" => ["error" => "User is not authorized"]
            ];
        }
    }

    public function getAuthenticatedUserRole(){
        if(Auth::check()){
            $id = Auth::id();
            $user = User::where('id', $id)->first();
            if(empty($user)){
                return[
                    "success" => false,
                    "response" => ["error" => "No user found"]
                ];
            }
            return[
                "success" => true,
                "response" => ["user_role" => $user->role]
            ];
        }
        else{
            return[
                "success" => false,
                "response" => ["error" => "User is not authorized"]
            ];
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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
}
