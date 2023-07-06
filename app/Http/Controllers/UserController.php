<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

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
        // Add a flag that check whether a user is following another user or not
        $currentlyFollowing = 0;

        if (auth()->check()) {
            $currentlyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        }

        // Username - Find user depending on the username(its unique)
        return view('user-profile', [
            'username' => $user->username,
            'posts' => $user->posts()->latest()->get(),
            'postCount' => $user->posts()->count(),
            'currentlyFollowing' => $currentlyFollowing,
        ]);
    }

    /*
        * Manage Avatars
    */

    public function showManageAvatarForm()
    {
        return view('manage-avatar');
    }

    public function uploadAvatar(Request $request)
    {
        // Validate the avatar file
        $request->validate([
            'avatar' => 'required|max:3000|image'
        ]);

        // Use the intervention/image library to set dimensions of the avatar
        // Give it 120x120 pixels dimensions
        // Encode it into a jpg
        $avatarData = Image::make($request->file('avatar'))->fit(120)->encode('jpg');

        // Create a unique name for the avatar
        // Username name and a unique string
        $user = auth()->user();
        $avatarName = $user->username . '-' . uniqid() . '.jpg';

        // Store the enoded avatar image data in the public folder
        Storage::put('public/avatars/' . $avatarName, $avatarData);

        $oldAvatar = $user->avatar;

        // Saving the avatar into the database
        $user->avatar = $avatarName;
        $user->save();

        // string(42) "/storage/avatars/derrick-6492cf2c6209e.jpg"
        // Replace /storage/ with /public/ and then delete photo
        if ($oldAvatar != "/default-avatar.jpg") {
            Storage::delete(str_replace("/storage/", "public/", $oldAvatar));
        }

        return back()->with('success', 'Avatar uploaded successfully!');
    }
}
