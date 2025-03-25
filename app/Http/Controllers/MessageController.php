<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request, Group $group)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        if (!$group->members->contains(Auth::id())) {
            return back()->with('error', 'You must be a member to post messages.');
        }

        $group->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Message posted!');
    }
}
