<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['seller', 'orders', 'affiliate', 'wishlists']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:customer,seller,cashier,admin,superadmin',
            'status' => 'required|in:active,suspended',
            'phone' => 'nullable|string|max:20',
        ]);

        // Prevent self-demotion from superadmin
        if ($user->id === auth()->id() && $request->role !== 'superadmin') {
            return back()->with('error', 'You cannot change your own superadmin role.');
        }

        $user->update($request->only(['name', 'email', 'role', 'status', 'phone']));

        return redirect()->route('admin.users.index')->with('success', 'User updated');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();
        return back()->with('success', 'User deleted');
    }

    public function impersonate(User $user)
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);
        session(['impersonate' => $user->id]);
        return redirect()->route('home');
    }

    public function stopImpersonate()
    {
        session()->forget('impersonate');
        return redirect()->route('admin.users.index');
    }
}