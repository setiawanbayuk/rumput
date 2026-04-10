<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SuratPengajuan;
use App\Models\Resident;
use App\Models\Log_surat;
use App\Models\Kelurahan;
use App\Traits\GeneratePDF;
use App\Traits\GetNoSurat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use App\Models\Pejabat;
use App\Models\SuratTemplate;
use App\Models\User;

class SuratAdminController extends Controller
{
    use GetNoSurat, GeneratePDF;
    public function index()
    {
        $user = auth()->user();

        if (request()->ajax()) {
            // Query ke tabel tunggal
            $query = SuratPengajuan::with('penduduk');

            // Filter berdasarkan Role (RT/RW hanya lihat wilayahnya)
            if ($user->role_id == 8) {
                $query->where('id_kel', $user->id_instansi)
                    ->where('id_rw', $user->id_rw)
                    ->where('id_rt', $user->id_rt);
            } elseif (in_array($user->role_id, [3, 4, 5, 6])) {
                $query->where('id_kel', $user->id_instansi);
            }

            // Filter berdasarkan Jenis Surat (Opsional jika ingin difilter via URL)
            if (request()->has('jenis') && request()->jenis != '') {
                $query->where('jenis_surat', request()->jenis);
            }
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('no_surat', function ($row) {
                    // Gunakan fungsi penomoran universal Anda
                    return $this->getNoSrt($row);
                })
                ->addColumn('tipe', function ($row) {
                    // Menampilkan label jenis surat (SKBN, SKTM, dll)
                    return '<span class="badge bg-info text-dark">' . strtoupper($row->jenis_surat) . '</span>';
                })->editColumn('tgl_surat', function ($row) {
                    return Carbon::parse($row->tgl_surat)->isoFormat('D MMMM Y');
                })
                ->addColumn('action', function ($row) use ($user) {
                    $id         = $row->id;
                    $status     = $row->status;
                    $role       = $user->role_id;
                    $nomorSurat = $this->getNoSrt($row);
                    $jenis      = $row->jenis_surat; // Dinamis dari kolom database
                    $route      = 'admin.surat.edit'; // Route universal

                    if ($role == 1) {
                        return view('includes.button-admin', compact('id', 'route', 'status'));
                    } elseif (in_array($role, [3, 5])) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role', 'route'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status', 'role', 'route'));
                    }
                })
                ->rawColumns(['action', 'tipe'])
                ->make(true);
        }

        $title = "Daftar Pengajuan Surat";
        return view('admin.surat.index', compact('title'));
    }

            // Form input surat baru oleh Admin
            public function create($jenis)
            {
                $title = 'Tambah Pengajuan Surat ' . strtoupper($jenis);
                $currentUser = auth()->user();

                $mapKodeJenis = [
                    'skbn'        => 'SKBN',
                    'sktm'        => 'SKTM',
                    'skdom'       => 'SKDOM',
                    'skusaha'     => 'SKUSAHA',
                    'skhsl'       => 'SKHSL',
                    'skboro'      => 'SKBORO',
                    'skkelahiran' => 'SKKELAHIRAN',
                    'skkematian'  => 'SKKEMATIAN',
                    'suket'       => 'SUKET',
                ];

                $kd_jenis_surat = $mapKodeJenis[$jenis] ?? strtoupper($jenis);

                $lastSurat = SuratPengajuan::where('jenis_surat', $jenis)
                    ->where('id_kel', $currentUser->id_instansi)
                    ->whereYear('tgl_surat', date('Y'))
                    ->orderByDesc('no_urut_surat')
                    ->first();

                $no_urut_surat = $lastSurat ? ((int) $lastSurat->no_urut_surat + 1) : 1;

                $mapVar = [
                    'skbn' => [
                        'bin_binti',
                        'nama_pasangan',
                        'nik_pasangan',
                        'tempat_lahir_pasangan',
                        'tgl_lahir_pasangan',
                        'agama_pasangan',
                        'pekerjaan_pasangan',
                        'alamat_pasangan',
                    ],
                    'sktm' => [
                        'nama_orang_tua',
                        'pekerjaan_orang_tua',
                        'alamat_orang_tua',
                        'keperluan_bantuan',
                    ],
                    'skdom' => [
                        'alamat_domisili',
                        'status_tempat_tinggal',
                        'lama_tinggal',
                    ],
                    'skusaha' => [
                        'nama_usaha',
                        'jenis_usaha',
                        'alamat_usaha',
                        'lama_usaha',
                    ],
                    'skhsl' => [
                        'keperluan',
                        'keterangan_tambahan',
                    ],
                    'skboro' => [
                        'nama_ayah',
                        'nama_ibu',
                        'alamat_asal',
                    ],
                    'skkelahiran' => [
                        'nama_bayi',
                        'jenis_kelamin_bayi',
                        'tempat_lahir',
                        'tanggal_lahir',
                        'jam_lahir',
                        'nama_ayah',
                        'nama_ibu',
                    ],
                    'skkematian' => [
                        'nama_meninggal',
                        'hari_meninggal',
                        'tanggal_meninggal',
                        'tempat_meninggal',
                        'penyebab_meninggal',
                    ],
                    'suket' => [
                        'keterangan_tambahan',
                    ],
                ];

                $var = $mapVar[$jenis] ?? [];

                return view('admin.surat.create', compact(
                    'title',
                    'jenis',
                    'currentUser',
                    'kd_jenis_surat',
                    'no_urut_surat',
                    'var'
                ));
            }

            protected function normalizePeruntukan(?string $value): string
            {
                $value = strtolower(trim((string) $value));
                $value = preg_replace('/\s+/', ' ', $value);
                return $value;
            }

            protected function buildAutoSuratMeta(?string $peruntukan, ?string $keperluanLainnya = null): array
            {
                $peruntukanNorm = $this->normalizePeruntukan($peruntukan);
                $keperluanLainnya = trim((string) ($keperluanLainnya ?? ''));

                switch ($peruntukanNorm) {
                    case 'menikah':
                        return [
                            'kategori' => 'Surat Belum Menikah',
                            'catatan' => '-',
                            'untuk' => 'Pengajuan Menikah',
                        ];
                    case 'rumah':
                        return [
                            'kategori' => 'Surat Belum Memiliki Rumah',
                            'catatan' => '-',
                            'untuk' => 'Pengajuan Rumah',
                        ];
                    case 'kendaraan':
                        return [
                            'kategori' => 'Surat Pengambilan Kendaraan',
                            'catatan' => '-',
                            'untuk' => 'Pengajuan Kendaraan',
                        ];
                    case 'lainnya':
                        return [
                            'kategori' => 'Lainnya',
                            'catatan' => $keperluanLainnya !== '' ? $keperluanLainnya : '-',
                            'untuk' => $keperluanLainnya !== '' ? 'Pengajuan ' . ucwords($keperluanLainnya) : 'Pengajuan Lainnya',
                        ];
                    default:
                        $label = $peruntukanNorm !== '' ? ucwords($peruntukanNorm) : '';
                        return [
                            'kategori' => $label !== '' ? 'Lainnya' : '-',
                            'catatan' => '-',
                            'untuk' => $label !== '' ? 'Pengajuan ' . $label : '',
                        ];
                }
            }


            // Simpan data dari Web Admin
            public function store(Request $request)
            {
                $rules = [
                    'jenis_surat'    => 'required|string',
                    'kd_jenis_surat' => 'required|string|max:50',
                    'no_urut_surat'  => 'required',
                    'nik'            => ['required', 'regex:/^[0-9]{16}$/'],
                    'kk'             => ['nullable', 'regex:/^[0-9]{16}$/'],
                    'peruntukan'     => 'required|string',
                    'keperluan_lainnya' => 'nullable|string|max:255|required_if:peruntukan,lainnya',
                    'kepada'         => 'required|string',
                    'tgl_surat'      => 'nullable|date',
                ];

                if ($request->jenis_surat === 'skbn') {
                    $rules['bin_binti'] = 'nullable|string|max:255';

                    if ($request->peruntukan === 'menikah') {
                        $rules['nama_pasangan'] = 'required|string|max:255';
                        $rules['nik_pasangan'] = ['required', 'regex:/^[0-9]{16}$/'];
                        $rules['tempat_lahir_pasangan'] = 'required|string|max:255';
                        $rules['tgl_lahir_pasangan'] = 'required|string|max:255';
                        $rules['agama_pasangan'] = 'required|string|max:255';
                        $rules['pekerjaan_pasangan'] = 'required|string|max:255';
                        $rules['alamat_pasangan'] = 'required|string';
                    } else {
                        $rules['nama_pasangan'] = 'nullable|string|max:255';
                        $rules['nik_pasangan'] = ['nullable', 'digits:16', 'regex:/^[0-9]+$/'];
                        $rules['tempat_lahir_pasangan'] = 'nullable|string|max:255';
                        $rules['tgl_lahir_pasangan'] = 'nullable|string|max:255';
                        $rules['agama_pasangan'] = 'nullable|string|max:255';
                        $rules['pekerjaan_pasangan'] = 'nullable|string|max:255';
                        $rules['alamat_pasangan'] = 'nullable|string';
                    }
                }

                if ($request->jenis_surat === 'sktm') {
                    $rules['nama_orang_tua'] = 'required|string|max:255';
                    $rules['pekerjaan_orang_tua'] = 'required|string|max:255';
                    $rules['alamat_orang_tua'] = 'required|string';
                    $rules['keperluan_bantuan'] = 'required|string';
                }

                if ($request->jenis_surat === 'skdom') {
                    $rules['alamat_domisili'] = 'required|string';
                    $rules['status_tempat_tinggal'] = 'required|string|max:255';
                    $rules['lama_tinggal'] = 'required|string|max:255';
                }

                if ($request->jenis_surat === 'skusaha') {
                    $rules['nama_usaha'] = 'required|string|max:255';
                    $rules['jenis_usaha'] = 'required|string|max:255';
                    $rules['alamat_usaha'] = 'required|string';
                    $rules['lama_usaha'] = 'required|string|max:255';
                }

                if ($request->jenis_surat === 'skhsl') {
                    $rules['keperluan'] = 'required|string';
                    $rules['keterangan_tambahan'] = 'nullable|string';
                }

                if ($request->jenis_surat === 'skboro') {
                    $rules['nama_ayah'] = 'required|string|max:255';
                    $rules['nama_ibu'] = 'required|string|max:255';
                    $rules['alamat_asal'] = 'required|string';
                }

                if ($request->jenis_surat === 'skkelahiran') {
                    $rules['nama_bayi'] = 'required|string|max:255';
                    $rules['jenis_kelamin_bayi'] = 'required|string|max:255';
                    $rules['tempat_lahir'] = 'required|string|max:255';
                    $rules['tanggal_lahir'] = 'required|string|max:255';
                    $rules['jam_lahir'] = 'nullable|string|max:255';
                    $rules['nama_ayah'] = 'required|string|max:255';
                    $rules['nama_ibu'] = 'required|string|max:255';
                }

                if ($request->jenis_surat === 'skkematian') {
                    $rules['nama_meninggal'] = 'required|string|max:255';
                    $rules['hari_meninggal'] = 'nullable|string|max:255';
                    $rules['tanggal_meninggal'] = 'required|string|max:255';
                    $rules['tempat_meninggal'] = 'required|string|max:255';
                    $rules['penyebab_meninggal'] = 'nullable|string';
                }

                if ($request->jenis_surat === 'suket') {
                    $rules['keterangan_tambahan'] = 'nullable|string';
                }

                $request->validate($rules, [
                    'nik.required' => 'NIK wajib diisi.',
                    'nik.regex' => 'NIK harus berupa angka dan tepat 16 digit.',
                    'kk.regex' => 'No. KK harus berupa angka dan tepat 16 digit.',
                    'nik_pasangan.regex' => 'NIK pasangan harus berupa angka dan tepat 16 digit.',
                    'nik_pasangan.required' => 'NIK pasangan wajib diisi untuk keperluan menikah.',
                    'keperluan_lainnya.required_if' => 'Keperluan lainnya wajib diisi jika memilih peruntukan lainnya.',
                ]);

                try {
                    return DB::transaction(function () use ($request) {
                        $user = auth()->user();

                        // Cari resident berdasarkan NIK
                        $resident = Resident::where('nik', $request->nik)->first();

                        // Data resident disimpan ke kolom JSON `data`
                        $residentData = [
                        'name'                => strtoupper($request->name ?? ''),
                        'gender'              => $request->gender,
                        'gender_nm'           => $request->gender_nm,
                        'status_kwn'          => $request->status_kwn,
                        'status_kwn_nm'       => $request->status_kwn_nm,
                        'kewarganegaraan'     => $request->kewarganegaraan,
                        'kewarganegaraan_nm'  => $request->kewarganegaraan_nm,
                        'tempat_lhr'          => strtoupper($request->tempat_lhr ?? ''),
                        'tgl_lhr'             => $request->tgl_lhr,
                        'agama'               => $request->agama,
                        'agama_nm'            => $request->agama_nm,
                        'pendidikan'          => $request->pendidikan,
                        'pendidikan_nm'       => $request->pendidikan_nm,
                        'pekerjaan'           => $request->pekerjaan,
                        'pekerjaan_nm'        => $request->pekerjaan_nm,
                        'provinsi'            => $request->provinsi,
                        'provinsi_nm'         => $request->provinsi_nm,
                        'kabko'               => $request->kabko,
                        'kabko_nm'            => $request->kabko_nm,
                        'kecamatan'           => $request->kecamatan,
                        'kecamatan_nm'        => $request->kecamatan_nm,
                        'kelurahan'           => $request->kelurahan,
                        'kelurahan_nm'        => $request->kelurahan_nm,
                        'rw'                  => $request->rw,
                        'rw_nm'               => $request->rw_nm,
                        'rt'                  => $request->rt,
                        'rt_nm'               => $request->rt_nm,
                        'alamat'              => strtoupper($request->alamat ?? ''),
                    ];

                        if (!$resident) {
                            // Jika belum ada → insert baru
                            $resident = Resident::create([
                                'nik'  => $request->nik,
                                'kk'   => $request->kk,
                                'data' => $residentData,
                            ]);
                        } else {
                            // Jika sudah ada → update data lama dengan data baru yang terisi
                            $existingData = is_array($resident->data) ? $resident->data : [];

                            $filteredResidentData = array_filter($residentData, function ($value) {
                                return !is_null($value) && $value !== '';
                            });

                            $mergedData = array_merge($existingData, $filteredResidentData);

                            $resident->update([
                                'kk'   => $request->kk ?: $resident->kk,
                                'data' => $mergedData,
                            ]);
                        }

                        $allInput = $request->except(['_token']);

                        $allInput['name'] = strtoupper($request->name ?? '');
                        $allInput['tempat_lhr'] = strtoupper($request->tempat_lhr ?? '');
                        $allInput['alamat'] = strtoupper($request->alamat ?? '');

                    if ($request->jenis_surat === 'skbn' && $request->peruntukan !== 'menikah') {
                        $allInput['bin_binti'] = null;
                        $allInput['nama_pasangan'] = null;
                        $allInput['nik_pasangan'] = null;
                        $allInput['tempat_lahir_pasangan'] = null;
                        $allInput['tgl_lahir_pasangan'] = null;
                        $allInput['agama_pasangan'] = null;
                        $allInput['pekerjaan_pasangan'] = null;
                        $allInput['alamat_pasangan'] = null;
                    }

                        $mainColumns = [
                            'jenis_surat',
                            'kd_jenis_surat',
                            'no_urut_surat',
                            'nik',
                            'kk',
                            'name',
                            'gender',
                            'gender_nm',
                            'status_kwn',
                            'status_kwn_nm',
                            'kewarganegaraan',
                            'kewarganegaraan_nm',
                            'tempat_lhr',
                            'tgl_lhr',
                            'agama',
                            'agama_nm',
                            'pendidikan',
                            'pendidikan_nm',
                            'pekerjaan',
                            'pekerjaan_nm',
                            'provinsi',
                            'provinsi_nm',
                            'kabko',
                            'kabko_nm',
                            'kecamatan',
                            'kecamatan_nm',
                            'kelurahan',
                            'kelurahan_nm',
                            'rw',
                            'rw_nm',
                            'rt',
                            'rt_nm',
                            'alamat',
                            'peruntukan',
                            'kepada',
                            'no_surat',
                            'pengantar',
                            'tgl_surat',
                            'tahun',
                            'id_instansi',
                        ];

                        $variableData = array_diff_key($allInput, array_flip($mainColumns));

                        if ($request->jenis_surat === 'skbn' && $request->peruntukan !== 'menikah') {
                            unset(
                                $variableData['nama_pasangan'],
                                $variableData['nik_pasangan'],
                                $variableData['tempat_lahir_pasangan'],
                                $variableData['tgl_lahir_pasangan'],
                                $variableData['agama_pasangan'],
                                $variableData['pekerjaan_pasangan'],
                                $variableData['alamat_pasangan']
                            );
                        }

                        $fileUrl = null;
                        if ($request->hasFile('pengantar')) {
                            $path = $request->file('pengantar')->store(
                                'public/pengantar/' . date('Y') . '/' . $request->jenis_surat
                            );
                            $fileUrl = str_replace('public/', '/storage/', $path);
                        }

                        $surat = SuratPengajuan::create([
                            'jenis_surat'    => $request->jenis_surat,
                            'kd_jenis_surat' => $request->kd_jenis_surat,
                            'no_urut_surat'  => $request->no_urut_surat,
                            'nik'            => $request->nik,
                            'id_kel'         => $user->id_instansi,
                            'id_rw'          => $user->id_rw,
                            'id_rt'          => $user->id_rt,
                            'tahun'          => date('Y'),
                            'tgl_surat'      => $request->tgl_surat ?: now(),
                            'peruntukan'     => $request->peruntukan,
                            'kepada'         => $request->kepada,
                            'status'         => 1,
                            'pengantar'      => $fileUrl,
                            'variable'       => $variableData,
                        ]);

                        Log_surat::create([
                            'nik'          => $request->nik,
                            'tabel_surat'  => 'surat_pengajuans',
                            'nama_surat'   => strtoupper($request->jenis_surat),
                            'id_surat'     => $surat->id,
                            'status_surat' => 1,
                        ]);

                        return redirect()
                            ->route('admin.surat.index')
                            ->with('success', 'Surat berhasil dibuat.');
                    });
                } catch (\Exception $e) {
                    return back()
                        ->withInput()
                        ->with('error', $e->getMessage());
                }
            }


        // Simpan data Edit Web Admin
        public function edit($id)
        {
            $surat = SuratPengajuan::findOrFail($id);
            $currentUser = auth()->user();
            $jenis = $surat->jenis_surat;

            $mapKodeJenis = [
                'skbn'        => 'SKBN',
                'sktm'        => 'SKTM',
                'skdom'       => 'SKDOM',
                'skusaha'     => 'SKUSAHA',
                'skhsl'       => 'SKHSL',
                'skboro'      => 'SKBORO',
                'skkelahiran' => 'SKKELAHIRAN',
                'skkematian'  => 'SKKEMATIAN',
                'suket'       => 'SUKET',
            ];

            $mapVar = [
                'skbn' => [
                    'bin_binti',
                    'nama_pasangan',
                    'nik_pasangan',
                    'tempat_lahir_pasangan',
                    'tgl_lahir_pasangan',
                    'agama_pasangan',
                    'pekerjaan_pasangan',
                    'alamat_pasangan',
                ],
                'sktm' => [
                    'nama_orang_tua',
                    'pekerjaan_orang_tua',
                    'alamat_orang_tua',
                    'keperluan_bantuan',
                ],
                'skdom' => [
                    'alamat_domisili',
                    'status_tempat_tinggal',
                    'lama_tinggal',
                ],
                'skusaha' => [
                    'nama_usaha',
                    'jenis_usaha',
                    'alamat_usaha',
                    'lama_usaha',
                ],
                'skhsl' => [
                    'keperluan',
                    'keterangan_tambahan',
                ],
                'skboro' => [
                    'nama_ayah',
                    'nama_ibu',
                    'alamat_asal',
                ],
                'skkelahiran' => [
                    'nama_bayi',
                    'jenis_kelamin_bayi',
                    'tempat_lahir',
                    'tanggal_lahir',
                    'jam_lahir',
                    'nama_ayah',
                    'nama_ibu',
                ],
                'skkematian' => [
                    'nama_meninggal',
                    'hari_meninggal',
                    'tanggal_meninggal',
                    'tempat_meninggal',
                    'penyebab_meninggal',
                ],
                'suket' => [
                    'keterangan_tambahan',
                ],
            ];

            $resident = Resident::where('nik', $surat->nik)->first();
            $residentData = [];

            if ($resident && !empty($resident->data)) {
                if (is_array($resident->data)) {
                    $residentData = $resident->data;
                } elseif (is_string($resident->data)) {
                    $decoded = json_decode($resident->data, true);
                    $residentData = is_array($decoded) ? $decoded : [];
                }
            }

            $title = 'Edit Pengajuan Surat ' . strtoupper($jenis);
            $kd_jenis_surat = $surat->kd_jenis_surat ?: ($mapKodeJenis[$jenis] ?? strtoupper($jenis));
            $no_urut_surat = $surat->no_urut_surat;
            $var = $mapVar[$jenis] ?? [];

            return view('admin.surat.edit', compact(
                'title',
                'surat',
                'jenis',
                'currentUser',
                'kd_jenis_surat',
                'no_urut_surat',
                'var',
                'resident',
                'residentData'
            ));
        }


        // Simpan data Update Web Admin
        public function update(Request $request, $id)
        {
            $rules = [
                'jenis_surat'    => 'required|string',
                'kd_jenis_surat' => 'required|string|max:50',
                'no_urut_surat'  => 'required',
                'nik'            => ['required', 'regex:/^[0-9]{16}$/'],
                'kk'             => ['nullable', 'regex:/^[0-9]{16}$/'],
                'peruntukan'     => 'required|string',
                'keperluan_lainnya' => 'nullable|string|max:255|required_if:peruntukan,lainnya',
                'kepada'         => 'required|string',
                'tgl_surat'      => 'nullable|date',
            ];

            if ($request->jenis_surat === 'skbn') {
                $rules['bin_binti'] = 'nullable|string|max:255';

                if ($request->peruntukan === 'menikah') {
                    $rules['nama_pasangan'] = 'required|string|max:255';
                    $rules['nik_pasangan'] = ['required', 'regex:/^[0-9]{16}$/'];
                    $rules['tempat_lahir_pasangan'] = 'required|string|max:255';
                    $rules['tgl_lahir_pasangan'] = 'required|string|max:255';
                    $rules['agama_pasangan'] = 'required|string|max:255';
                    $rules['pekerjaan_pasangan'] = 'required|string|max:255';
                    $rules['alamat_pasangan'] = 'required|string';
                } else {
                    $rules['nama_pasangan'] = 'nullable|string|max:255';
                    $rules['nik_pasangan'] = ['nullable', 'digits:16', 'regex:/^[0-9]+$/'];
                    $rules['tempat_lahir_pasangan'] = 'nullable|string|max:255';
                    $rules['tgl_lahir_pasangan'] = 'nullable|string|max:255';
                    $rules['agama_pasangan'] = 'nullable|string|max:255';
                    $rules['pekerjaan_pasangan'] = 'nullable|string|max:255';
                    $rules['alamat_pasangan'] = 'nullable|string';
                }
            }

            if ($request->jenis_surat === 'sktm') {
                $rules['nama_orang_tua'] = 'required|string|max:255';
                $rules['pekerjaan_orang_tua'] = 'required|string|max:255';
                $rules['alamat_orang_tua'] = 'required|string';
                $rules['keperluan_bantuan'] = 'required|string';
            }

            if ($request->jenis_surat === 'skdom') {
                $rules['alamat_domisili'] = 'required|string';
                $rules['status_tempat_tinggal'] = 'required|string|max:255';
                $rules['lama_tinggal'] = 'required|string|max:255';
            }

            if ($request->jenis_surat === 'skusaha') {
                $rules['nama_usaha'] = 'required|string|max:255';
                $rules['jenis_usaha'] = 'required|string|max:255';
                $rules['alamat_usaha'] = 'required|string';
                $rules['lama_usaha'] = 'required|string|max:255';
            }

            if ($request->jenis_surat === 'skhsl') {
                $rules['keperluan'] = 'required|string';
                $rules['keterangan_tambahan'] = 'nullable|string';
            }

            if ($request->jenis_surat === 'skboro') {
                $rules['nama_ayah'] = 'required|string|max:255';
                $rules['nama_ibu'] = 'required|string|max:255';
                $rules['alamat_asal'] = 'required|string';
            }

            if ($request->jenis_surat === 'skkelahiran') {
                $rules['nama_bayi'] = 'required|string|max:255';
                $rules['jenis_kelamin_bayi'] = 'required|string|max:255';
                $rules['tempat_lahir'] = 'required|string|max:255';
                $rules['tanggal_lahir'] = 'required|string|max:255';
                $rules['jam_lahir'] = 'nullable|string|max:255';
                $rules['nama_ayah'] = 'required|string|max:255';
                $rules['nama_ibu'] = 'required|string|max:255';
            }

            if ($request->jenis_surat === 'skkematian') {
                $rules['nama_meninggal'] = 'required|string|max:255';
                $rules['hari_meninggal'] = 'nullable|string|max:255';
                $rules['tanggal_meninggal'] = 'required|string|max:255';
                $rules['tempat_meninggal'] = 'required|string|max:255';
                $rules['penyebab_meninggal'] = 'nullable|string';
            }

            if ($request->jenis_surat === 'suket') {
                $rules['keterangan_tambahan'] = 'nullable|string';
            }

            $request->validate($rules, [
                'nik.required' => 'NIK wajib diisi.',
                'nik.regex' => 'NIK harus berupa angka dan tepat 16 digit.',
                'kk.regex' => 'No. KK harus berupa angka dan tepat 16 digit.',
                'nik_pasangan.regex' => 'NIK pasangan harus berupa angka dan tepat 16 digit.',
                'nik_pasangan.required' => 'NIK pasangan wajib diisi untuk keperluan menikah.',
                'keperluan_lainnya.required_if' => 'Keperluan lainnya wajib diisi jika memilih peruntukan lainnya.',
            ]);

            try {
                return DB::transaction(function () use ($request, $id) {
                    $user = auth()->user();
                    $surat = SuratPengajuan::findOrFail($id);

                    $resident = Resident::where('nik', $request->nik)->first();

                    $residentData = [
                        'name'               => strtoupper($request->name ?? ''),
                        'gender'             => $request->gender,
                        'gender_nm'          => $request->gender_nm,
                        'status_kwn'         => $request->status_kwn,
                        'status_kwn_nm'      => $request->status_kwn_nm,
                        'kewarganegaraan'    => $request->kewarganegaraan,
                        'kewarganegaraan_nm' => $request->kewarganegaraan_nm,
                        'tempat_lhr'         => strtoupper($request->tempat_lhr ?? ''),
                        'tgl_lhr'            => $request->tgl_lhr,
                        'agama'              => $request->agama,
                        'agama_nm'           => $request->agama_nm,
                        'pendidikan'         => $request->pendidikan,
                        'pendidikan_nm'      => $request->pendidikan_nm,
                        'pekerjaan'          => $request->pekerjaan,
                        'pekerjaan_nm'       => $request->pekerjaan_nm,
                        'provinsi'           => $request->provinsi,
                        'provinsi_nm'        => $request->provinsi_nm,
                        'kabko'              => $request->kabko,
                        'kabko_nm'           => $request->kabko_nm,
                        'kecamatan'          => $request->kecamatan,
                        'kecamatan_nm'       => $request->kecamatan_nm,
                        'kelurahan'          => $request->kelurahan,
                        'kelurahan_nm'       => $request->kelurahan_nm,
                        'rw'                 => $request->rw,
                        'rw_nm'              => $request->rw_nm,
                        'rt'                 => $request->rt,
                        'rt_nm'              => $request->rt_nm,
                        'alamat'             => strtoupper($request->alamat ?? ''),
                    ];

                    if (!$resident) {
                        $resident = Resident::create([
                            'nik'  => $request->nik,
                            'kk'   => $request->kk,
                            'data' => $residentData,
                        ]);
                    } else {
                        $existingData = is_array($resident->data) ? $resident->data : [];
                        $filteredResidentData = array_filter($residentData, function ($value) {
                            return !is_null($value) && $value !== '';
                        });

                        $resident->update([
                            'kk'   => $request->kk ?: $resident->kk,
                            'data' => array_merge($existingData, $filteredResidentData),
                        ]);
                    }

                    $allInput = $request->except(['_token', '_method']);
                    $allInput['name'] = strtoupper($request->name ?? '');
                    $allInput['tempat_lhr'] = strtoupper($request->tempat_lhr ?? '');
                    $allInput['alamat'] = strtoupper($request->alamat ?? '');

                    if ($request->jenis_surat === 'skbn' && $request->peruntukan !== 'menikah') {
                        $allInput['bin_binti'] = null;
                        $allInput['nama_pasangan'] = null;
                        $allInput['nik_pasangan'] = null;
                        $allInput['tempat_lahir_pasangan'] = null;
                        $allInput['tgl_lahir_pasangan'] = null;
                        $allInput['agama_pasangan'] = null;
                        $allInput['pekerjaan_pasangan'] = null;
                        $allInput['alamat_pasangan'] = null;
                    }

                    $mainColumns = [
                        'jenis_surat',
                        'kd_jenis_surat',
                        'no_urut_surat',
                        'nik',
                        'kk',
                        'name',
                        'gender',
                        'gender_nm',
                        'status_kwn',
                        'status_kwn_nm',
                        'kewarganegaraan',
                        'kewarganegaraan_nm',
                        'tempat_lhr',
                        'tgl_lhr',
                        'agama',
                        'agama_nm',
                        'pendidikan',
                        'pendidikan_nm',
                        'pekerjaan',
                        'pekerjaan_nm',
                        'provinsi',
                        'provinsi_nm',
                        'kabko',
                        'kabko_nm',
                        'kecamatan',
                        'kecamatan_nm',
                        'kelurahan',
                        'kelurahan_nm',
                        'rw',
                        'rw_nm',
                        'rt',
                        'rt_nm',
                        'alamat',
                        'peruntukan',
                        'kepada',
                        'no_surat',
                        'pengantar',
                        'tgl_surat',
                        'tahun',
                        'id_instansi',
                    ];

                    $variableData = array_diff_key($allInput, array_flip($mainColumns));

                    $autoMeta = $this->buildAutoSuratMeta($request->peruntukan, $request->keperluan_lainnya);
                    $variableData['surat_kategori'] = $autoMeta['kategori'];
                    $variableData['surat_catatan'] = $autoMeta['catatan'];
                    if (!empty($request->keperluan_lainnya)) {
                        $variableData['keperluan_lainnya'] = trim((string) $request->keperluan_lainnya);
                    } else {
                        unset($variableData['keperluan_lainnya']);
                    }

                    if ($request->jenis_surat === 'skbn' && $request->peruntukan !== 'menikah') {
                        unset(
                            $variableData['nama_pasangan'],
                            $variableData['nik_pasangan'],
                            $variableData['tempat_lahir_pasangan'],
                            $variableData['tgl_lahir_pasangan'],
                            $variableData['agama_pasangan'],
                            $variableData['pekerjaan_pasangan'],
                            $variableData['alamat_pasangan']
                        );
                    }

                    $fileUrl = $surat->pengantar;
                    if ($request->hasFile('pengantar')) {
                        $path = $request->file('pengantar')->store(
                            'public/pengantar/' . date('Y') . '/' . $request->jenis_surat
                        );
                        $fileUrl = str_replace('public/', '/storage/', $path);
                    }

                    $surat->update([
                        'jenis_surat'    => $request->jenis_surat,
                        'kd_jenis_surat' => $request->kd_jenis_surat,
                        'no_urut_surat'  => $request->no_urut_surat,
                        'nik'            => $request->nik,
                        'id_kel'         => $user->id_instansi,
                        'id_rw'          => $user->id_rw,
                        'id_rt'          => $user->id_rt,
                        'tahun'          => date('Y'),
                        'tgl_surat'      => $request->tgl_surat ?: now(),
                        'peruntukan'     => $request->peruntukan,
                        'kepada'         => $request->kepada,
                        'pengantar'      => $fileUrl,
                        'variable'       => $variableData,
                    ]);

                    Log_surat::create([
                        'nik'          => $request->nik,
                        'tabel_surat'  => 'surat_pengajuans',
                        'nama_surat'   => strtoupper($request->jenis_surat),
                        'id_surat'     => $surat->id,
                        'status_surat' => $surat->status,
                    ]);

                    return redirect()
                        ->route('admin.surat.index')
                        ->with('success', 'Surat berhasil diupdate.');
                });
            } catch (\Exception $e) {
                return back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        }

            // Preview Dan Cetak Template Web Admin



            protected function decodeFlexibleValue($value): array
            {
                if (is_array($value)) {
                    return $value;
                }

                if (is_string($value) && $value !== '') {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return $decoded;
                    }

                    $unserialized = @unserialize($value);
                    if ($unserialized !== false && is_array($unserialized)) {
                        return $unserialized;
                    }
                }

                return [];
            }

            protected function resolveTemplateFileAdmin(string $jenis, int $idKel): string
            {
                $custom = \App\Models\SuratTemplate::where('id_kel', $idKel)
                    ->where('jenis', $jenis)
                    ->first();

                if ($custom && !empty($custom->path_docs)) {
                    $customPath = public_path($custom->path_docs);
                    if (file_exists($customPath)) {
                        return $customPath;
                    }
                }

                $fallbackMap = [
                    'skbn'        => 'templates/SKBN.docx',
                    'sktm'        => 'templates/SKTM_PERORANGAN.docx',
                    'skdom'       => 'templates/SKDOM.docx',
                    'skusaha'     => 'templates/SKUSAHA.docx',
                    'skhsl'       => 'templates/SKHSL.docx',
                    'skboro'      => 'templates/SKBORO.docx',
                    'skkelahiran' => 'templates/SKKELAHIRAN.docx',
                    'skkematian'  => 'templates/SKKEMATIAN.docx',
                    'suket'       => 'templates/SUKET.docx',
                ];

                $relative = $fallbackMap[$jenis] ?? null;
                if ($relative) {
                    $full = public_path($relative);
                    if (file_exists($full)) {
                        return $full;
                    }
                }

                abort(404, "Template preview untuk jenis surat {$jenis} tidak ditemukan.");
            }

            protected function buildPdfDataAdmin(\App\Models\SuratPengajuan $surat): array
            {
                $resident = \App\Models\Resident::where('nik', $surat->nik)->first();
                $residentData = $this->decodeFlexibleValue(optional($resident)->data);
                $variableData = $this->decodeFlexibleValue($surat->variable);

                if (!empty($residentData['tgl_lhr'])) {
                    try {
                        $residentData['tgl_lhr'] = \Carbon\Carbon::parse($residentData['tgl_lhr'])->isoFormat('D MMMM Y');
                    } catch (\Throwable $e) {
                    }
                }

                $user = \App\Models\User::with('skpd.kecamatan')->find(auth()->id());
                $pejabat = \App\Models\Pejabat::with(['skpd.kecamatan', 'jabatan'])
                    ->where('id_skpd', $user->id_instansi)
                    ->first();

                if (!$pejabat) {
                    abort(404, 'Data pejabat penandatangan tidak ditemukan.');
                }

                $tglSurat = \Carbon\Carbon::parse($surat->tgl_surat)->isoFormat('D MMMM Y');
                $nomorSurat = $this->getNoSrt($surat);
                $verifyUrl = env('APP_URL', url('/')) . '/verify/' . $surat->jenis_surat . '/' . $surat->id;

                $alamatLengkap = trim(
                    ($residentData['alamat'] ?? '') .
                    (!empty($residentData['kelurahan_nm']) ? ' KEL. ' . $residentData['kelurahan_nm'] : '') .
                    (!empty($residentData['kecamatan_nm']) ? ' KEC. ' . $residentData['kecamatan_nm'] : '') .
                    (!empty($residentData['kabko_nm']) ? ' ' . $residentData['kabko_nm'] : '')
                );

                $data = [
                    'skpd_kec'            => strtoupper(optional(optional($pejabat->skpd)->kecamatan)->nama ?? ''),
                    'skpd_kel'            => strtoupper(optional($pejabat->skpd)->nama ?? ''),
                    'skpd_alamat'         => optional($pejabat->skpd)->instansi_alamat ?? '',
                    'skpd_telp'           => optional($pejabat->skpd)->instansi_telp ?? '',
                    'skpd_pos'            => optional($pejabat->skpd)->instansi_kode_pos ?? '',
                    'skpd_kepala'         => $pejabat->nama ?? '',
                    'skpd_nip_kepala'     => $pejabat->nip ?? '',
                    'skpd_jabatan'        => trim(ucfirst(optional($pejabat->jabatan)->nama ?? '') . ' ' . ucfirst(strtolower(optional($pejabat->skpd)->nama ?? ''))),

                    'surat_no'            => $nomorSurat,
                    'surat_tgl'           => $tglSurat,
                    'surat_nama'          => $residentData['name'] ?? '',
                    'surat_nik'           => $surat->nik ?? '',
                    'surat_tmpl'          => $residentData['tempat_lhr'] ?? '',
                    'surat_tgll'          => strtoupper($residentData['tgl_lhr'] ?? ''),
                    'surat_gender'        => $residentData['gender_nm'] ?? '',
                    'surat_perkawinan'    => $residentData['status_kwn_nm'] ?? '',
                    'surat_agama'         => $residentData['agama_nm'] ?? '',
                    'surat_pekerjaan'     => $residentData['pekerjaan_nm'] ?? '',
                    'surat_pendidikan'    => $residentData['pendidikan_nm'] ?? '',
                    'surat_alamat'        => $alamatLengkap,
                    'surat_kepada'        => $surat->kepada ?? '',
                    'surat_peruntukan'    => $this->buildAutoSuratMeta($surat->peruntukan, $variableData['keperluan_lainnya'] ?? null)['untuk'],
                    'surat_keterangan'    => $variableData['keterangan_tambahan'] ?? $variableData['keperluan'] ?? '',
                    'surat_kategori'      => $variableData['surat_kategori'] ?? $this->buildAutoSuratMeta($surat->peruntukan, $variableData['keperluan_lainnya'] ?? null)['kategori'],
                    'surat_catatan'       => $variableData['surat_catatan'] ?? $this->buildAutoSuratMeta($surat->peruntukan, $variableData['keperluan_lainnya'] ?? null)['catatan'],
                    'link'                => $verifyUrl,
                    'show_qr'             => in_array((int) $surat->status, [4, 9], true),

                    'jenis_surat'         => $surat->jenis_surat ?? '',
                    'kd_jenis_surat'      => $surat->kd_jenis_surat ?? '',
                    'no_urut_surat'       => $surat->no_urut_surat ?? '',
                    'tgl_surat'           => $surat->tgl_surat ?? '',
                    'kepada'              => $surat->kepada ?? '',
                    'peruntukan'          => $surat->peruntukan ?? '',
                    'pengantar'           => $surat->pengantar ?? '',
                ];

                foreach ($residentData as $key => $value) {
                    if (!is_array($value)) {
                        $data[$key] = $value;
                    }
                }

                foreach ($variableData as $key => $value) {
                    if (!is_array($value)) {
                        $data[$key] = $value;
                    }
                }

                switch ($surat->jenis_surat) {
                    case 'skbn':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: 'BENAR BAHWA YANG BERSANGKUTAN BELUM MENIKAH.';
                        break;
                    case 'sktm':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: 'BENAR-BENAR DALAM KEADAAN MISKIN.';
                        break;
                    case 'skdom':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: ('BERDOMISILI DI ' . ($variableData['alamat_domisili'] ?? ''));
                        break;
                    case 'skusaha':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: ('MEMILIKI USAHA ' . ($variableData['nama_usaha'] ?? ''));
                        break;
                    case 'skhsl':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['keperluan'] ?? '');
                        break;
                    case 'skboro':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['alamat_asal'] ?? '');
                        break;
                    case 'suket':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['keterangan_tambahan'] ?? '');
                        break;
                }

                return $data;
            }

            protected function generateAdminPdfFile(\App\Models\SuratPengajuan $surat): array
            {
                $data = $this->buildPdfDataAdmin($surat);
                $templateFile = $this->resolveTemplateFileAdmin($surat->jenis_surat, (int) $surat->id_kel);
                $outputPdf = hash('sha256', strtoupper($surat->jenis_surat) . '_' . $surat->id);
                $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);

                return [$pdfPath, $data];
            }

            public function preview($id)
            {
                $surat = \App\Models\SuratPengajuan::findOrFail($id);
                [$pdfPath, $data] = $this->generateAdminPdfFile($surat);

                return response()->file($pdfPath);
            }

            public function cetak($id)
            {
                $surat = \App\Models\SuratPengajuan::findOrFail($id);
                [$pdfPath, $data] = $this->generateAdminPdfFile($surat);

                $namaFile = strtoupper($surat->jenis_surat) . '_' . preg_replace('/[^A-Za-z0-9\-]+/', '_', $this->getNoSrt($surat)) . '.pdf';

                return response()->download($pdfPath, $namaFile);
            }


            // Naikan Ke Atasan Yang Lebih tinggi Web Admin

        public function naik($id)
                {
                    $surat = SuratPengajuan::find($id);

                    if (!$surat) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Data tidak ditemukan.'
                        ], 404);
                    }

                    $currentStatus = (int) $surat->status;
                    $nextStatus = null;
                    $message = 'Pengajuan berhasil diajukan ke atasan yang lebih tinggi.';

                    // Alur universal admin surat
                    // 1 = Proses -> 2 = Dinaikkan ke Sekkel
                    // 2 = Dinaikkan ke Sekkel -> 3 = Dinaikkan ke Lurah
                    // 3 = Dinaikkan ke Lurah -> 8 = Dinaikkan ke Camat
                    // 8 = Dinaikkan ke Camat -> 9 = Disetujui Camat
                    if ($currentStatus === 1) {
                        $nextStatus = 2;
                        $message = 'Pengajuan berhasil diajukan ke Sekkel.';
                    } elseif ($currentStatus === 2) {
                        $nextStatus = 3;
                        $message = 'Pengajuan berhasil diajukan ke Lurah.';
                    } elseif ($currentStatus === 3) {
                        $nextStatus = 8;
                        $message = 'Pengajuan berhasil diajukan ke Camat.';
                    } elseif ($currentStatus === 8) {
                        $nextStatus = 9;
                        $message = 'Pengajuan berhasil disetujui Camat.';
                    }

                    if ($nextStatus === null) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Status surat ini tidak bisa diajukan ke level berikutnya.'
                        ], 422);
                    }

                    $surat->update(['status' => $nextStatus]);

                    Log_surat::create([
                        'nik'          => $surat->nik,
                        'tabel_surat'  => 'surat_pengajuans',
                        'nama_surat'   => strtoupper($surat->jenis_surat),
                        'id_surat'     => $surat->id,
                        'status_surat' => $nextStatus,
                    ]);

                    return response()->json([
                        'status'  => 'success',
                        'message' => $message,
                        'data'    => [
                            'id' => $surat->id,
                            'status' => $nextStatus,
                        ]
                    ]);
                }

                public function naikLurah($id)
                {
                    $surat = SuratPengajuan::find($id);

                    if (!$surat) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Data tidak ditemukan.'
                        ], 404);
                    }

                    $currentStatus = (int) $surat->status;

                    // khusus tombol "ajukan ke lurah"
                    // izinkan dari proses/sekkel langsung ke lurah
                    if (!in_array($currentStatus, [1, 2], true)) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Status surat ini tidak bisa diajukan ke Lurah.'
                        ], 422);
                    }

                    $nextStatus = 3;

                    $surat->update(['status' => $nextStatus]);

                    Log_surat::create([
                        'nik'          => $surat->nik,
                        'tabel_surat'  => 'surat_pengajuans',
                        'nama_surat'   => strtoupper($surat->jenis_surat),
                        'id_surat'     => $surat->id,
                        'status_surat' => $nextStatus,
                    ]);

                    return response()->json([
                        'status'  => 'success',
                        'message' => 'Pengajuan berhasil diajukan ke Lurah.',
                        'data'    => [
                            'id' => $surat->id,
                            'status' => $nextStatus,
                        ]
                    ]);
                }

    public function tolak($id)
    {
        // 1. Cari data di tabel tunggal
        $surat = SuratPengajuan::find($id);

        if ($surat) {
            // 2. Update status ke 6 (Tolak) sesuai StatusSuratTrait
            $surat->update(['status' => 6]);

            // 3. Catat ke Log secara dinamis
            Log_surat::create([
                'nik'          => $surat->nik,
                'tabel_surat'  => 'surat_pengajuans', // Sekarang semua tabelnya sama
                'nama_surat'   => strtoupper($surat->jenis_surat), // Mengambil jenis surat (skbn/sktm/dll)
                'id_surat'     => $surat->id,
                'status_surat' => 6,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Pengajuan berhasil ditolak.',
                'data'    => [
                    'id'    => $id,
                    'jenis' => $surat->jenis_surat
                ]
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Data tidak ditemukan atau gagal diperbarui.'
        ], 404);
    }

    public function proses($id)
    {
        // 1. Cari data di tabel tunggal (surat_pengajuans)
        $surat = SuratPengajuan::find($id);

        if ($surat) {
            // 2. Update status ke 1 (Proses)
            $surat->update(['status' => 1]);

            // 3. Catat Log secara dinamis menggunakan jenis_surat dari database
            Log_surat::create([
                'nik'          => $surat->nik,
                'tabel_surat'  => 'surat_pengajuans',
                'nama_surat'   => strtoupper($surat->jenis_surat),
                'id_surat'     => $surat->id,
                'status_surat' => 1,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Surat berhasil diproses.',
                'data'    => $id
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal memproses: Data tidak ditemukan.'
        ], 404);
    }

    public function turunkan($id)
    {
        $surat = SuratPengajuan::find($id);

        if (!$surat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $currentStatus = (int) $surat->status;
        $nextStatus = match ($currentStatus) {
            2 => 1,
            3 => 2,
            8 => 3,
            default => null,
        };

        if ($nextStatus === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Status surat ini tidak bisa diturunkan.'
            ], 422);
        }

        $surat->update(['status' => $nextStatus]);

        Log_surat::create([
            'nik'          => $surat->nik,
            'tabel_surat'  => 'surat_pengajuans',
            'nama_surat'   => strtoupper($surat->jenis_surat),
            'id_surat'     => $surat->id,
            'status_surat' => $nextStatus,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Surat berhasil diturunkan.',
            'data'    => [
                'id'     => $surat->id,
                'status' => $nextStatus,
            ]
        ]);
    }

    public function hapus($id)
    {
        $surat = SuratPengajuan::find($id);

        if (!$surat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if ((int) $surat->status !== 1) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya draft yang belum diajukan yang bisa dihapus.'
            ], 422);
        }

        Log_surat::create([
            'nik'          => $surat->nik,
            'tabel_surat'  => 'surat_pengajuans',
            'nama_surat'   => strtoupper($surat->jenis_surat),
            'id_surat'     => $surat->id,
            'status_surat' => 7,
        ]);

        $surat->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Draft surat berhasil dihapus.'
        ]);
    }
}
