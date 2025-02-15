<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendRequestController extends Controller
{
    /**
     * Send a friend request.
     */
    public function sendRequest(User $user)
    {
        if (Auth::id() == $user->id) {
            return back()->with('error', 'You cannot send a friend request to yourself.');
        }

        // Check if a request already exists
        $exists = FriendRequest::where([
            ['sender_id', Auth::id()],
            ['receiver_id', $user->id]
        ])->exists();

        if ($exists) {
            return back()->with('error', 'Friend request already sent.');
        }

        // Create a new friend request
        FriendRequest::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $user->id
        ]);

        return back()->with('success', 'Friend request sent.');
    }

    /**
    * Display received friend requests.
    */
    public function receivedRequests()
    {
    $requests = FriendRequest::where('receiver_id', Auth::id())
        ->where('status', 'pending')
        ->with('sender:id,name,display_name,avatar') // Load sender details
        ->get();

        return view('friends.requests', compact('requests'));
    }

    /**
     * Accept a friend request.
     */
    public function acceptRequest(FriendRequest $friendRequest)
    {
        if ($friendRequest->receiver_id != Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        // Add both users to the friends table
        Friend::create([
            'user_id' => $friendRequest->sender_id,
            'friend_id' => $friendRequest->receiver_id
        ]);

        Friend::create([
            'user_id' => $friendRequest->receiver_id,
            'friend_id' => $friendRequest->sender_id
        ]);

        // Remove the friend request from the database permanently
        $friendRequest->delete();

        return back()->with('status', 'friend-request-removed');
    }

    /**
     * Decline a friend request.
     */
    public function declineRequest(FriendRequest $friendRequest)
    {
        if ($friendRequest->receiver_id != Auth::id()) {
            return back()->with('error', 'Unauthorized.');
        }

        // Remove the friend request from the database permanently
        $friendRequest->delete();

        return back()->with('status', 'friend-request-removed');
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        // Exclude the currently logged-in user
        $users = User::where('id', '!=', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                ->orWhere('display_name', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->get();

        return view('friends.search', compact('users'));
    }
}