<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display all groups (User's groups & Public groups).
     */
    public function index()
    {
        return view('groups.index', [
            'userGroups' => Auth::user()->groups()->get(),
            'publicGroups' => Group::withCount('members')->where('is_private', false)->orderByDesc('members_count')->get(),
        ]);
    }

    /**
     * Store a newly created group.
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

        // Automatically add creator as the first member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('groups.show', $group->id)->with('success', 'Group created successfully.');
    }

    /**
     * Show a specific group.
     */
    public function show(Group $group)
    {
        if ($group->is_private && !$group->members->contains(Auth::id())) {
            return redirect()->route('groups.index')->with('error', 'You do not have access to this private group.');
        }

        // Eager load user info, sort by latest, and paginate
        $messages = $group->messages()
                      ->with('user')
                      ->latest()
                      ->paginate(10);

        return view('groups.show', [
            'group' => $group,
            'messages' => $messages
        ]);
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
        GroupMember::where('group_id', $group->id)->where('user_id', Auth::id())->delete();

        return redirect()->route('groups.index')->with('success', 'You left the group.');
    }

    /**
     * Delete a group (Only the creator can delete).
     */
    public function destroy(Group $group)
    {
        if ($group->creator_id !== Auth::id()) {
            return back()->with('error', 'You do not have permission to delete this group.');
        }

        $group->delete();

        return redirect()->route('groups.index')->with('success', 'Group deleted successfully.');
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        return view('groups.create'); //leads to the create group form
    }

    /**
     * Edit Group Information
     */
    public function edit(Group $group)
    {
        if ($group->creator_id !== Auth::id()) {
            return redirect()->route('groups.index')->with('error', 'You do not have permission to edit this group.');
        }

        return view('groups.edit', ['group' => $group]);
    }

    /**
     * Update Group.
     */
    public function update(Request $request, Group $group)
    {
        if ($group->creator_id !== Auth::id()) {
            return redirect()->route('groups.index')->with('error', 'You do not have permission to update this group.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_private' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Allow image uploads
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

        return redirect()->route('groups.index')->with('success', 'Group updated successfully.');
    }


        
}