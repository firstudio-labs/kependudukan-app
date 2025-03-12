<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Add search functionality if needed
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nik', 'like', "%$search%")
                  ->orWhere('no_hp', 'like', "%$search%");
            });
        }

        $users = $query->paginate(10);
        return view('superadmin.datamaster.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('superadmin.datamaster.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|unique:users,nik',
            'password' => 'required|min:6',
            'no_hp' => 'nullable|string',
            'role' => 'required|in:superadmin,admin,operator,user',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('superadmin.datamaster.user.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        return view('superadmin.datamaster.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nik' => ['required', Rule::unique('users')->ignore($user->id)],
            'no_hp' => 'nullable|string',
            'role' => 'required|in:superadmin,admin,operator,user',
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('superadmin.datamaster.user.index')->with('success', 'Data pengguna berhasil diperbarui');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('superadmin.datamaster.user.index')->with('success', 'Pengguna berhasil dihapus');
    }
}
