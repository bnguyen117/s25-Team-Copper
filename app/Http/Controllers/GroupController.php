<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Redirect instead of loading a missing 'groups.index' view.
     */
    public function index()
    {
        return redirect()->route('community')->with('info', 'Group list is not available yet!');
    }

    /**
     * Show the form to create a new group.
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Store a newly created group and add creator as member.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'creator_id' => Auth::id(),
            'is_private' => $request->is_private ?? false,
        ]);

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('groups.show', $group->id)->with('success', 'Group created successfully.');
    }

    /**
     * Show a specific group and its messages.
     */
    public function show(Group $group)
    {
        if ($group->is_private && !$group->members->contains(Auth::id())) {
            return redirect()->route('community.index')->with('error', 'You do not have access to this private group.');
        }

        $messages = $group->messages()
            ->with([
                'user',
                'replies' => function ($q) {
                    $q->orderBy('created_at');
                },
                'replies.user',
                'parent.user'
            ])
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->paginate(10);

        return view('groups.show', compact('group', 'messages'));
    }

    /**
     * Show the form to edit a group.
     */
    public function edit(Group $group)
    {
        if ($group->creator_id !== Auth::id()) {
            return redirect()->route('community')->with('error', 'You do not have permission to edit this group.');
        }

        return view('groups.edit', compact('group'));
    }

    /**
     * Update group details.
     */
    public function update(Request $request, Group $group)
    {
        if ($group->creator_id !== Auth::id()) {
            return redirect()->route('community.index')->with('error', 'You do not have permission to update this group.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('group_images', 'public');
            $group->image = $imagePath;
        }

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_private' => $request->is_private ?? false,
        ]);

        return redirect()->route('community')->with('success', 'Group updated successfully.');
    }

    /**
     * Join a group.
     */
    public function join(Group $group)
    {
        if (!$group->members->contains(Auth::id())) {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('groups.show', $group->id)->with('success', 'You joined the group!');
    }

    /**
     * Leave a group.
     */
    public function leave(Group $group)
    {
        GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->delete();

        return redirect()->route('community.index')->with('success', 'You left the group.');
    }

    /**
     * Delete a group (only if you're the creator).
     */
    public function destroy(Group $group)
    {
        if ($group->creator_id !== Auth::id()) {
            return back()->with('error', 'You do not have permission to delete this group.');
        }

        $group->delete();

        return redirect()->route('community.index')->with('success', 'Group deleted successfully.');
    }
}
