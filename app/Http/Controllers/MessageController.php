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
            'body' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        if (!$group->members->contains(Auth::id())) {
            return back()->with('error', 'You must be a member to post messages.');
        }

        $group->messages()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', 'Message posted!');
    }
    public function destroy(Group $group,Message $message)
    {
        if ($message->user_id != Auth::id()) {
            return back()->with('error', 'Delete your own messages.');
        }
        $message->delete();
        return back()->with('success', 'Message deleted!');
    }
    public function update(Request $request, Group $group, Message $message)
    {
        if ($message->user_id != Auth::id()) {
            return back()->with('error', 'Edit your own messages.');
        }

        $request->validate([
            'body' => 'required|string|max:2000',
        ]);
    
        $message->update([
            'body' => $request->body,
        ]);
    
        return redirect()->route('groups.show', $group->id)->with('success', 'Message updated!');
    }
}
