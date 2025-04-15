<?php

namespace App\Http\Controllers\adminKabupaten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileKabController extends Controller
{
    /**
     * Display admin kabupaten profile page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = Auth::user();

        return view('admin.kabupaten.profile.index', compact('user'));
    }

    /**
     * Show the form for editing the profile
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit()
    {
        $user = Auth::user();

        return view('admin.kabupaten.profile.edit', compact('user'));
    }

    /**
     * Update the profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
            'image' => 'nullable|image|max:2048',
        ]);

        $user->username = $request->username;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;

        // Handle password update
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        // Handle profile photo upload
        if ($request->hasFile('image')) {
            // Delete old photo if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Save new photo
            $photo = $request->file('image');
            $filename = 'user_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('images/users', $filename, 'public');
            $user->image = $path;
        }

        $user->save();

        return redirect()->route('admin.kabupaten.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update profile photo
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePhoto(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('image')) {
            // Delete old photo if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Save new photo
            $photo = $request->file('image');
            $filename = 'user_' . $user->id . '_' . time() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('images/users', $filename, 'public');
            $user->image = $path;
            $user->save();

            return redirect()->route('admin.kabupaten.profile.index')
                ->with('success', 'Foto profil berhasil diperbarui.');
        }

        return redirect()->route('admin.kabupaten.profile.index')
            ->with('error', 'Gagal mengupload foto profil.');
    }
}
