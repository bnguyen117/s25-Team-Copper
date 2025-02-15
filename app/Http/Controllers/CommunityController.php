<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friend;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;


class CommunityController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('community.index', [
            'userGroups' => $user->groups()->get(),
            'publicGroups' => Group::withCount('members')->where('is_private', false)->orderByDesc('members_count')->get(),
            'friends' => Friend::where('user_id', $user->id)->with('user')->get(),
        ]);
    }
}
