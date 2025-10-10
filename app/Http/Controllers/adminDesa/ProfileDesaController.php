<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\KepalaDesa;
use App\Models\PerangkatDesa;
use App\Models\DataWilayah;

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

        $dataWilayah = DataWilayah::firstOrCreate(['user_id' => $user->id], [
            'luas_wilayah' => null,
            'foto_peta' => null,
            'batas_wilayah' => null,
            'jumlah_dusun' => null,
            'jumlah_rt' => null,
        ]);

        return view('admin.desa.profile.index', compact('user', 'dataWilayah'));
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

    // Halaman perangkat desa dihapus, tetap di edit profil

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
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'tag_lokasi' => 'nullable|string',
            'nama_kepala_desa' => 'nullable|string|max:255',
            'foto_kepala_desa' => 'nullable|image|max:2048',
            'tanda_tangan' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
            'image' => 'nullable|image|max:2048',
            'foto_pengguna' => 'nullable|image|max:2048',
        ]);

        $user->nama = $request->nama;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->no_hp = $request->no_hp;
        $user->alamat = $request->alamat;
        $user->tag_lokasi = $request->tag_lokasi;

        // Handle password update
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
            }

            $user->password = Hash::make($request->new_password);
        }

        // Handle logo upload (image field)
        if ($request->hasFile('image')) {
            // Delete old logo if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }

            // Save new logo
            $logo = $request->file('image');
            $filename = 'user_' . $user->id . '_' . time() . '_logo.' . $logo->getClientOriginalExtension();
            $path = $logo->storeAs('images/users', $filename, 'public');
            $user->image = $path;
        }

        // Handle foto pengguna upload
        if ($request->hasFile('foto_pengguna')) {
            // Delete old foto if exists
            if ($user->foto_pengguna && Storage::disk('public')->exists($user->foto_pengguna)) {
                Storage::disk('public')->delete($user->foto_pengguna);
            }

            // Save new foto pengguna
            $fotoPengguna = $request->file('foto_pengguna');
            $filename = 'user_' . $user->id . '_' . time() . '_foto.' . $fotoPengguna->getClientOriginalExtension();
            $path = $fotoPengguna->storeAs('images/users', $filename, 'public');
            $user->foto_pengguna = $path;
        }

        $user->save();

        // Handle kepala desa data
        $kepalaDesa = KepalaDesa::firstOrNew(['user_id' => $user->id]);
        $kepalaDesa->nama = $request->nama_kepala_desa;

        // Handle foto kepala desa upload
        if ($request->hasFile('foto_kepala_desa')) {
            if ($kepalaDesa->foto && Storage::disk('public')->exists($kepalaDesa->foto)) {
                Storage::disk('public')->delete($kepalaDesa->foto);
            }
            $file = $request->file('foto_kepala_desa');
            $filename = 'kepala_desa_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images/kepala_desa', $filename, 'public');
            $kepalaDesa->foto = $path;
        }

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

        // Perangkat desa dipisah ke endpoint khusus

        return redirect()->route('admin.desa.profile.index')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update perangkat desa (form terpisah)
     */
    public function updatePerangkat(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'perangkat' => 'nullable|array',
            'perangkat.*.id' => 'nullable|integer|exists:perangkat_desas,id',
            'perangkat.*.nama' => 'required_with:perangkat|string|max:255',
            'perangkat.*.jabatan' => 'required_with:perangkat|string|max:255',
            'perangkat.*.alamat' => 'nullable|string',
            'perangkat.*.foto' => 'nullable|image|max:2048',
        ]);

        $submittedPerangkat = collect($request->input('perangkat', []));
        $submittedIds = $submittedPerangkat->pluck('id')->filter()->map(function ($v) { return (int) $v; })->all();

        // Hapus perangkat yang tidak diajukan lagi
        if (!empty($submittedIds)) {
            PerangkatDesa::where('user_id', $user->id)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        } else {
            // Jika tidak ada yang diajukan, hapus semua perangkat user
            PerangkatDesa::where('user_id', $user->id)->delete();
        }

        foreach ($submittedPerangkat as $index => $data) {
            if (!isset($data['nama']) || !isset($data['jabatan'])) {
                continue;
            }

            $perangkat = null;
            if (!empty($data['id'])) {
                $perangkat = PerangkatDesa::where('user_id', $user->id)->where('id', (int)$data['id'])->first();
            }
            if (!$perangkat) {
                $perangkat = new PerangkatDesa();
                $perangkat->user_id = $user->id;
            }

            $perangkat->nama = $data['nama'];
            $perangkat->jabatan = $data['jabatan'];
            $perangkat->alamat = $data['alamat'] ?? null;

            if ($request->hasFile("perangkat.$index.foto")) {
                $file = $request->file("perangkat.$index.foto");
                if ($perangkat->foto && Storage::disk('public')->exists($perangkat->foto)) {
                    Storage::disk('public')->delete($perangkat->foto);
                }
                $filename = 'perangkat_' . $user->id . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('images/perangkat_desa', $filename, 'public');
                $perangkat->foto = $path;
            }

            $perangkat->save();
        }

        return redirect()->route('admin.desa.profile.edit', '#perangkat-desa-section')
            ->with('success', 'Perangkat desa berhasil diperbarui.');
    }

    /** Simple CRUD Perangkat Desa */
    public function storePerangkat(Request $request)
    {
        $user = Auth::user();
        // Dukung tambah banyak item sekaligus (perangkat[0..n]) atau single field lama
        $isBatch = is_array($request->input('perangkat'));
        if ($isBatch) {
            $request->validate([
                'perangkat' => 'required|array|min:1',
                'perangkat.*.nama' => 'required|string|max:255',
                'perangkat.*.jabatan' => 'required|string|max:255',
                'perangkat.*.alamat' => 'nullable|string',
                'perangkat.*.foto' => 'nullable|image|max:2048',
            ]);

            foreach ($request->input('perangkat') as $index => $data) {
                $perangkat = new PerangkatDesa();
                $perangkat->user_id = $user->id;
                $perangkat->nama = $data['nama'];
                $perangkat->jabatan = $data['jabatan'];
                $perangkat->alamat = $data['alamat'] ?? null;

                if ($request->hasFile("perangkat.$index.foto")) {
                    $file = $request->file("perangkat.$index.foto");
                    $filename = 'perangkat_' . $user->id . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('images/perangkat_desa', $filename, 'public');
                    $perangkat->foto = $path;
                }

                $perangkat->save();
            }
        } else {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'jabatan' => 'required|string|max:255',
                'alamat' => 'nullable|string',
                'foto' => 'nullable|image|max:2048',
            ]);

            $perangkat = new PerangkatDesa();
            $perangkat->user_id = $user->id;
            $perangkat->nama = $validated['nama'];
            $perangkat->jabatan = $validated['jabatan'];
            $perangkat->alamat = $validated['alamat'] ?? null;

            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'perangkat_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('images/perangkat_desa', $filename, 'public');
                $perangkat->foto = $path;
            }

            $perangkat->save();
        }

        return redirect()->route('admin.desa.profile.index')->with('success', 'Perangkat desa berhasil ditambahkan.');
    }

    public function updatePerangkatItem(Request $request, $id)
    {
        $user = Auth::user();
        $perangkat = PerangkatDesa::where('user_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|max:2048',
        ]);

        $perangkat->nama = $validated['nama'];
        $perangkat->jabatan = $validated['jabatan'];
        $perangkat->alamat = $validated['alamat'] ?? null;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            if ($perangkat->foto && Storage::disk('public')->exists($perangkat->foto)) {
                Storage::disk('public')->delete($perangkat->foto);
            }
            $filename = 'perangkat_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images/perangkat_desa', $filename, 'public');
            $perangkat->foto = $path;
        }

        $perangkat->save();

        return redirect()->route('admin.desa.profile.index')->with('success', 'Perangkat desa diperbarui.');
    }

    public function destroyPerangkat($id)
    {
        $user = Auth::user();
        $perangkat = PerangkatDesa::where('user_id', $user->id)->findOrFail($id);
        if ($perangkat->foto && Storage::disk('public')->exists($perangkat->foto)) {
            Storage::disk('public')->delete($perangkat->foto);
        }
        $perangkat->delete();
        return redirect()->route('admin.desa.profile.index')->with('success', 'Perangkat desa dihapus.');
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

    /**
     * Update Data Wilayah
     */
    public function updateDataWilayah(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'luas_wilayah' => 'nullable|string|max:255',
            'foto_peta' => 'nullable|image|max:4096',
            'batas_wilayah.utara' => 'nullable|string',
            'batas_wilayah.timur' => 'nullable|string',
            'batas_wilayah.barat' => 'nullable|string',
            'batas_wilayah.selatan' => 'nullable|string',
            'jumlah_dusun' => 'nullable|string|max:255',
            'jumlah_rt' => 'nullable|string|max:255',
        ]);

        $dataWilayah = \App\Models\DataWilayah::firstOrCreate(['user_id' => $user->id]);
        $dataWilayah->luas_wilayah = $request->luas_wilayah;
        $dataWilayah->jumlah_dusun = $request->jumlah_dusun;
        $dataWilayah->jumlah_rt = $request->jumlah_rt;
        $dataWilayah->batas_wilayah = $request->input('batas_wilayah');

        if ($request->hasFile('foto_peta')) {
            $file = $request->file('foto_peta');
            if ($dataWilayah->foto_peta && Storage::disk('public')->exists($dataWilayah->foto_peta)) {
                Storage::disk('public')->delete($dataWilayah->foto_peta);
            }
            $filename = 'foto_peta_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images/wilayah', $filename, 'public');
            $dataWilayah->foto_peta = $path;
        }

        $dataWilayah->save();

        return redirect()->route('admin.desa.profile.index')->with('success', 'Data wilayah berhasil disimpan.');
    }
}
