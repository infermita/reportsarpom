<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller {

    public function login(Request $request) {
        
        if ($request->all()) {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                return redirect('/');
            }


            
        }

        return view('login');
    }

    public function logout() {

        Auth::logout();
        return redirect('/');
    }
}
