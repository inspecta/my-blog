<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FollowController extends Controller
{
    //
    public function createFollow(User $user)
    {
        // var_dump($user);
        // Add follow details into the user table
        // A user can't follow himself
        if (auth()->user()->id == $user->id) {
            return back()->with('failure', 'You can not follow yourself.');
        }
        // You can not follow someone more than once
        $alreadyFollowing = Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->count();
        if ($alreadyFollowing) {
            return back()->with('failure', 'You are already following that user.');
        }

        $newFollow = Follow::create(
            [
                'user_id' => auth()->user()->id,
                'followeduser' => $user->id
            ]
        );
        $newFollow->save();
        return back()->with('success', 'You have successfully followed the user.');
    }

    public function removeFollow(User $user)
    {
        // Removing a follow - delete a row where the user_id is for the currently logged in user
        // and the followeduser is for the user
        Follow::where([['user_id', '=', auth()->user()->id], ['followeduser', '=', $user->id]])->delete();
        return back()->with('success', 'You have successfully unfollowed the user.');
    }
}
