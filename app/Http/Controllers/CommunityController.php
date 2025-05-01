<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friend;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use App\Gamify\Points\CommunityVisited;


class CommunityController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        /**Friendly Badge stuff */
        // Award visit point only once
        $alreadyGiven = $user->reputations()->where('name', 'community_visited')->exists();

        if (!$alreadyGiven) {
            $user->givePoint(new CommunityVisited($user));
            session()->flash('badge_awarded', 'You got a new badge!');
        }
        /** end */

        return view('community.index', [
            'userGroups' => $user->groups()->get(),
            'publicGroups' => Group::withCount('members')->where('is_private', false)->orderByDesc('members_count')->get(),
            'friends' => Friend::where('user_id', $user->id)->with('user')->get(),
        ]);
    }
}
