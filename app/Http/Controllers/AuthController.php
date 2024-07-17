<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $data = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()],
            'confirm_password' => ['required', 'same:password']
        ]);

        unset($data['confirm_password']);

        try {
            User::create($data);

            $credentials = [
                'email' => $data['email'],
                'password' => $data['password']
            ];

            if (Auth::attempt($credentials)) {
                return to_route('users.dashboard')->with('success', 'Login Successfully.');
            } else {
                return to_route('users.signup')->with('failure', 'Your credentials does not match our records.');
            }
        } catch (\Exception $e) {
            return to_route('users.signup')->with('failure', 'Not sign up: ' . $e->getMessage());
        }
    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            if (Auth::attempt($data)) {
                return to_route('users.dashboard')->with('success', 'Login Successfully.');
            } else {
                return to_route('users.signup')->with('failure', 'Your credentials does not match our records.');
            }
        } catch (\Exception $e) {
            return to_route('users.signup')->with('failure', 'Not sign up: ' . $e->getMessage());
        }
    }

    public function logout(){
        Auth::logout();

        return to_route('users.loginPage');
    }
}
