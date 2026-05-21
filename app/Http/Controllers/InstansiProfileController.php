<?php

namespace App\Http\Controllers;

use App\Models\Skpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InstansiProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:1,3,4,5,6,7,8,9']);
    }

    public function edit()
    {
        $skpd = Skpd::findOrFail(auth()->user()->id_instansi);

        return view('profile.instansi', [
            'title' => 'Profil Instansi',
            'skpd' => $skpd,
            'hasKodePos' => Schema::hasColumn('skpds', 'instansi_kode_pos'),
        ]);
    }

    public function update(Request $request)
    {
        $skpd = Skpd::findOrFail($request->user()->id_instansi);

        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'instansi_kode' => ['nullable', 'string', 'max:100'],
            'instansi_telp' => ['nullable', 'string', 'max:100'],
            'instansi_fax' => ['nullable', 'string', 'max:100'],
            'instansi_alamat' => ['nullable', 'string', 'max:255'],
            'instansi_email' => ['nullable', 'email', 'max:255'],
        ];

        if (Schema::hasColumn('skpds', 'instansi_kode_pos')) {
            $rules['instansi_kode_pos'] = ['nullable', 'string', 'max:20'];
        }

        $validated = $request->validate($rules);

        $skpd->forceFill($validated)->save();

        return redirect()
            ->route('profile.instansi')
            ->with('status', 'Profil instansi berhasil diperbarui.');
    }
}
