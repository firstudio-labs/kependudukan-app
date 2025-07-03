<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\KepalaDesa;

class ProfileDesaController extends Controller
{
    /**
     * Display admin desa profile page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = Auth::user();

        return view('admin.desa.profile.index', compact('user'));
    }

    /**
     * Show the form for editing the profile
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function edit()
    {
        $user = Auth::user();

        return view('admin.desa.profile.edit', compact('user'));
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
            'nama' => 'required|string|max:255',
            'tanda_tangan' => 'nullable|image|max:2048',
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

        // Handle kepala desa data
        $kepalaDesa = KepalaDesa::firstOrNew(['user_id' => $user->id]);
        $kepalaDesa->nama = $request->nama;

        // Handle tanda tangan upload
        if ($request->hasFile('tanda_tangan')) {
            // Delete old tanda tangan if exists
            if ($kepalaDesa->tanda_tangan && Storage::disk('public')->exists($kepalaDesa->tanda_tangan)) {
                Storage::disk('public')->delete($kepalaDesa->tanda_tangan);
            }

            // Save new tanda tangan
            $tandaTangan = $request->file('tanda_tangan');
            $filename = 'tanda_tangan_' . $user->id . '_' . time() . '.' . $tandaTangan->getClientOriginalExtension();
            $path = $tandaTangan->storeAs('images/tanda_tangan', $filename, 'public');
            $kepalaDesa->tanda_tangan = $path;
        }

        $kepalaDesa->save();

        return redirect()->route('admin.desa.profile.index')
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

            return redirect()->route('admin.desa.profile.index')
                ->with('success', 'Foto profil berhasil diperbarui.');
        }

        return redirect()->route('admin.desa.profile.index')
            ->with('error', 'Gagal mengupload foto profil.');
    }
}
