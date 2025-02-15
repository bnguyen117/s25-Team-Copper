<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    /**
     * Add a friend.
     */
    public function addFriend(User $user)
    {
        $authUser = Auth::user();

        // Prevent adding oneself as a friend
        if ($authUser->id === $user->id) {
            return back()->with('error', 'You cannot add yourself as a friend.');
        }

        // Check if they are already friends
        if (Friend::where('user_id', $authUser->id)->where('friend_id', $user->id)->exists()) {
            return back()->with('error', 'You are already friends.');
        }

        // Add friendship
        Friend::create([
            'user_id' => $authUser->id,
            'friend_id' => $user->id
        ]);

        Friend::create([
            'user_id' => $user->id,
            'friend_id' => $authUser->id
        ]);

        return back()->with('success', 'Friend added successfully!');
    }

    /**
     * Remove a friend.
     */
    public function removeFriend(User $user)
    {
        $authUser = Auth::user();

        // Remove friendship from both users' lists
        Friend::where('user_id', $authUser->id)->where('friend_id', $user->id)->delete();
        Friend::where('user_id', $user->id)->where('friend_id', $authUser->id)->delete();

        return back()->with('success', 'Friend removed successfully.');
    }
}