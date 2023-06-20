<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function correctHomePage()
    {
        // Check if user is logged in
        if (auth()->check()) {
            return view('homepage-feed');
        } else {
            return view('home');
        }
    }

    public function login(Request $request)
    {
        $loginFields = $request->validate([
            "loginusername" => "required",
            "loginpassword" => "required",
        ]);

        if (auth()->attempt(
            [
                'username' => $loginFields['loginusername'],
                "password" => $loginFields['loginpassword']
            ]
        )) {
            $request->session()->regenerate();
            return redirect('/')->with('success', 'You have successfully logged in.');
        } else {
            return redirect('/')->with('failure', 'Login failed. Invalid username or password.');
        }
    }

    public function register(Request $request)
    {
        $incomingFields = $request->validate([
            "username" => [
                "required", "min:3", "max:20", Rule::unique('users', 'username')
            ],
            "email" => [
                "required", "email", Rule::unique('users', 'email')
            ],
            "password" => [
                "required", "min:5", "confirmed"
            ]
        ]);

        $user = User::create($incomingFields);
        auth()->login($user);
        return redirect('/')->with('success', 'You have successfully registered.');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/')->with('success', 'You have successfully logged out.');
    }

    public function viewProfile(User $user)
    {
        // return $pizza;
        // Username - Find user depending on the username(its unique)

        return view('user-profile', ['username' => $user->username, 'posts' => $user->posts()->latest()->get(), 'postCount' => $user->posts()->count()]);
    }
}
