<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::paginate(15);
        $roles = ['admin', 'agent', 'client'];
        return view('users.index', compact('users', 'roles'));
    }

    // Show edit form
    public function edit(User $user)
    {
        $roles = ['admin', 'agent', 'client'];
        return view('users.edit', compact('user', 'roles'));
    }

    // Update user (roles, status)
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required|in:admin,agent,client',
            'status' => 'required|in:active,inactive',
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        $user->job_title = $validated['job_title'] ?? null;
        $user->department = $validated['department'] ?? null;
        $user->company = $validated['company'] ?? null;
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->save();
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Deactivate user
    public function deactivate(User $user)
    {
        $user->status = 'inactive';
        $user->save();
        return redirect()->route('users.index')->with('success', 'User deactivated.');
    }
} 