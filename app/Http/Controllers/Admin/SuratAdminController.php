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
use Illuminate\Support\Facades\Schema;
use Yajra\DataTables\DataTables;
use App\Models\Pejabat;
use App\Models\SuratTemplate;
use App\Models\User;
use App\Models\Skpd;

class SuratAdminController extends Controller
{
    use GetNoSurat, GeneratePDF;
    public function index()
    {
        $user = auth()->user();
        $roleId = (int) $user->role_id;

        // Kondisi umum untuk menyembunyikan surat TTD Basah yang sudah upload bukti
        // dari Pelayanan Warga agar tidak dobel dengan Beranda.
        $buktiTtdBasahEmptyWhere = "(CASE WHEN variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) THEN COALESCE(NULLIF(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.bukti_ttd_basah')), ''), '') ELSE '' END) = ''";
        $manualUploadedEmptyWhere = $buktiTtdBasahEmptyWhere;

        // Deteksi TTD Basah dibuat fleksibel karena di data real ada dua kemungkinan:
        // 1) status sudah berubah menjadi 7, atau
        // 2) status masih Warga/Admin, tetapi variable JSON sudah punya manual_signature/signature_mode manual.
        // Dengan ini, surat TTD Basah tidak ikut lagi ke notifikasi Surat Warga Masuk.
        $manualSignatureWhere = "(status = 7 OR (variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) AND (LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.signature_mode')), '')) = 'manual' OR LOWER(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(variable, '$.manual_signature')), '')) IN ('1', 'true', 'yes', 'manual'))))";
        $manualPendingWhere = "($manualSignatureWhere AND $buktiTtdBasahEmptyWhere)";
        $notManualPendingWhere = "NOT ($manualPendingWhere)";

        $buildAdminSuratCounts = function () use ($user, $roleId, $manualUploadedEmptyWhere, $manualPendingWhere, $notManualPendingWhere) {
            $statusCounts = [
                'default' => 0,
                'to_sekkel' => 0,
                'to_lurah' => 0,
                'to_camat' => 0,
                'manual_pending' => 0,
                'rejected' => 0,
                'approved' => 0,
            ];

            $wargaMasukCount = 0;
            $manualPendingCount = 0;

            if ($roleId === 1) {
                $adminBaseCountQuery = $this->scopeSuratToCurrentUser(SuratPengajuan::query())->whereRaw($manualUploadedEmptyWhere);

                $statusCounts['default']   = (clone $adminBaseCountQuery)->whereIn('status', [0, 1])->whereRaw($notManualPendingWhere)->count();
                $statusCounts['to_sekkel'] = (clone $adminBaseCountQuery)->where('status', 2)->count();
                $statusCounts['to_lurah']  = (clone $adminBaseCountQuery)->where('status', 3)->count();
                $statusCounts['to_camat']  = (clone $adminBaseCountQuery)->whereIn('status', [8, 11])->where('jenis_surat', 'sktm')->count();
                $statusCounts['manual_pending'] = $this->scopeSuratToCurrentUser(SuratPengajuan::query())->whereRaw($manualPendingWhere)->count();
                $statusCounts['rejected']  = (clone $adminBaseCountQuery)->where('status', 6)->count();
                $statusCounts['approved']  = (clone $adminBaseCountQuery)
                    ->where(function ($q) {
                        $q->where('status', 9)
                          ->orWhere(function ($qq) {
                              $qq->where('status', 4)->where('jenis_surat', '<>', 'sktm');
                          });
                    })->count();

                // Notifikasi keras khusus surat murni dari warga yang belum disentuh Admin.
                $wargaMasukCount = (clone $adminBaseCountQuery)->where('status', 0)->whereRaw($notManualPendingWhere)->count();

                // Notifikasi terpisah khusus TTD Basah yang belum upload bukti.
                $manualPendingCount = $statusCounts['manual_pending'];
            }

            return [
                'statusCounts' => $statusCounts,
                'wargaMasukCount' => $wargaMasukCount,
                'manualPendingCount' => $manualPendingCount,
            ];
        };

        if (request()->ajax() && request()->boolean('notif_counts')) {
            return response()->json($buildAdminSuratCounts())
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache');
        }

        if (request()->ajax()) {
            // Query ke tabel tunggal
            $query = $this->scopeSuratToCurrentUser(SuratPengajuan::with('penduduk'))
                ->orderByRaw('COALESCE(updated_at, created_at) DESC')
                ->orderByDesc('id');

            // Filter wilayah sudah diterapkan oleh scopeSuratToCurrentUser().
            // Jangan menambah where id_kel = id_instansi lagi di sini,
            // karena Camat/Sekcam memakai id_instansi kecamatan dan harus membaca semua kelurahan dalam id_kec yang sama.

            if ($roleId === 1) {
                // Hilangkan surat TTD Basah yang sudah upload bukti dari Pelayanan Warga.
                // Data selesai seperti ini cukup tampil di Beranda agar laporan tidak dobel/menumpuk.
                $query->whereRaw($manualUploadedEmptyWhere);

                // Default Admin: hanya surat yang belum diproses/belum dinaikkan.
                // Jika dropdown filter dipilih, status akan mengikuti filter di bawah.
                if (!request()->filled('filter_status')) {
                    $query->whereIn('status', [0, 1])
                        ->whereRaw($notManualPendingWhere);
                }
            } elseif ($roleId === 4) {
                // Sekkel hanya fokus pada surat yang menunggu verifikasi Sekkel.
                $query->where('status', 2);
            } elseif ($roleId === 3) {
                // Lurah melihat surat yang menunggu TTE Lurah (status 3).
                // Khusus SKTM yang sudah TTE Lurah (status 4) tetap tampil agar Lurah bisa klik Naikkan ke Kecamatan/Camat.
                $query->where(function ($q) {
                    $q->where('status', 3)
                      ->orWhere(function ($qq) {
                          $qq->where('status', 4)->where('jenis_surat', 'sktm');
                      });
                });
            } elseif ($roleId === 6) {
                // Sekretaris/Adm Camat melihat SKTM yang sudah dinaikkan dari Lurah ke Kecamatan.
                $query->where('jenis_surat', 'sktm')->where('status', 11);
            } elseif ($roleId === 5) {
                // Camat melihat SKTM yang sudah dinaikkan oleh Sekretaris/Adm Camat.
                $query->where('jenis_surat', 'sktm')->where('status', 8);
            }

            // Filter berdasarkan Jenis Surat (Opsional jika ingin difilter via dropdown)
            if (request()->filled('jenis')) {
                $query->where('jenis_surat', request()->jenis);
            }

            // Filter Status Pengajuan dari dropdown daftar pengajuan surat.
            // KHUSUS Admin role_id 1 saja.
            // Role Sekkel/Lurah/Sekcam/Camat tidak memakai filter ini karena datanya sudah dibatasi otomatis dari backend.
            if ($roleId === 1 && request()->filled('filter_status')) {
                switch (request()->filter_status) {
                    case 'approved':
                        $query->where(function ($q) {
                            $q->where('status', 9)
                              ->orWhere(function ($qq) {
                                  $qq->where('status', 4)->where('jenis_surat', '<>', 'sktm');
                              });
                        });
                        break;

                    case 'rejected':
                        $query->where('status', 6);
                        break;


                    case 'to_sekkel':
                        $query->where('status', 2);
                        break;

                    case 'to_lurah':
                        $query->where('status', 3);
                        break;

                    case 'to_camat':
                        $query->where('jenis_surat', 'sktm')->whereIn('status', [8, 11]);
                        break;

                    case 'manual_pending':
                        $query->whereRaw($manualPendingWhere);
                        break;
                }
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

                    $variableRow = $this->decodeFlexibleValue($row->variable ?? []);
                    $submitter_type = $this->resolveSubmitterType($row);
                    $manual_signature = !empty($variableRow['manual_signature']) || (($variableRow['signature_mode'] ?? null) === 'manual');
                    $bukti_ttd_basah = $variableRow['bukti_ttd_basah'] ?? null;

                    if ($role == 1) {
                        return view('includes.button-admin', compact('id', 'route', 'status', 'submitter_type', 'manual_signature', 'bukti_ttd_basah'));
                    } elseif (in_array($role, [3, 5])) {
                        return view('includes.button-kaopd', compact('id', 'status', 'nomorSurat', 'jenis', 'role', 'route', 'submitter_type', 'manual_signature', 'bukti_ttd_basah'));
                    } else {
                        return view('includes.button-verifikator', compact('id', 'status', 'nomorSurat', 'jenis', 'role', 'route', 'submitter_type', 'manual_signature', 'bukti_ttd_basah'));
                    }
                })
                ->rawColumns(['action', 'tipe'])
                ->make(true);
        }

        $title = "Daftar Pengajuan Surat";

        $adminCounts = $buildAdminSuratCounts();
        $statusCounts = $adminCounts['statusCounts'];
        $wargaMasukCount = $adminCounts['wargaMasukCount'];
        $manualPendingCount = $adminCounts['manualPendingCount'];

        return view('admin.surat.index', compact('title', 'statusCounts', 'wargaMasukCount', 'manualPendingCount'));
    }

    /**
     * Role E-SUKET:
     * 1 = Admin kelurahan, 3 = Lurah, 4 = Sekkel, 5 = Camat, 6 = Sekcam,
     * 7 = Super Admin/Template, 8 = RT/RW, 9 = Admin kelurahan tambahan.
     *
     * Prinsip keamanan:
     * - Akun kelurahan hanya boleh mengakses surat dengan surat_pengajuans.id_kel = users.id_instansi.
     * - Akun Camat/Sekcam hanya boleh mengakses kelurahan dalam kecamatan yang sama berdasarkan skpds.id_kec.
     * - Super Admin role 7 boleh melihat semua wilayah.
     */
    protected function isSuperAdminUser($user = null): bool
    {
        $user = $user ?: auth()->user();
        return $user && (int) $user->role_id === 7;
    }

    protected function isKecamatanRole($user = null): bool
    {
        $user = $user ?: auth()->user();
        return $user && in_array((int) $user->role_id, [5, 6], true);
    }

    protected function currentUserKelurahanIds($user = null): ?array
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return [];
        }

        if ($this->isSuperAdminUser($user)) {
            return null; // null = boleh semua wilayah
        }

        $roleId = (int) $user->role_id;
        $idInstansi = (int) ($user->id_instansi ?? 0);

        if ($idInstansi <= 0) {
            return [];
        }

        if (in_array($roleId, [5, 6], true)) {
            $skpdInstansi = Skpd::find($idInstansi);

            if (!$skpdInstansi) {
                return [];
            }

            // Akun kecamatan/Sekcam bisa memakai id_instansi baris kecamatan atau baris kelurahan.
            // Baris kecamatan biasanya punya id_region seperti 35.71.01 dan id_kec bisa kosong.
            // Baris kelurahan biasanya punya id_region seperti 35.71.01.1001 dan id_kec berisi 35.71.01.
            $candidateKecRegions = [];
            $idKec = trim((string) ($skpdInstansi->id_kec ?? ''));
            $idRegion = trim((string) ($skpdInstansi->id_region ?? ''));

            if ($idKec !== '') {
                $candidateKecRegions[] = $idKec;
            }

            if ($idRegion !== '' && substr_count($idRegion, '.') === 2) {
                $candidateKecRegions[] = $idRegion;
            }

            // Fallback aman: jika id_instansi adalah baris kecamatan tetapi id_region/id_kec tidak rapi,
            // cari kelurahan yang punya id_kec sama dengan id_region baris tersebut.
            $candidateKecRegions = array_values(array_unique(array_filter($candidateKecRegions)));

            if (empty($candidateKecRegions)) {
                return [];
            }

            return Skpd::query()
                ->where(function ($q) use ($candidateKecRegions) {
                    $q->whereIn('id_kec', $candidateKecRegions)
                      ->orWhereIn('id_region', $candidateKecRegions);
                })
                ->get(['id', 'id_region'])
                ->filter(function ($skpd) {
                    // Kelurahan punya id_region seperti 35.71.01.1001 (minimal 3 titik).
                    // Kecamatan punya id_region seperti 35.71.01 (2 titik), jadi tidak ikut.
                    return substr_count((string) $skpd->id_region, '.') >= 3;
                })
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return [$idInstansi];
    }

    protected function scopeSuratToCurrentUser($query, $user = null)
    {
        $user = $user ?: auth()->user();
        $kelurahanIds = $this->currentUserKelurahanIds($user);

        if ($kelurahanIds === null) {
            return $query;
        }

        if (empty($kelurahanIds)) {
            return $query->whereRaw('1 = 0');
        }

        $query->whereIn('id_kel', $kelurahanIds);

        if ((int) $user->role_id === 8) {
            if (!is_null($user->id_rw) && $user->id_rw !== '') {
                $query->where('id_rw', $user->id_rw);
            }
            if (!is_null($user->id_rt) && $user->id_rt !== '') {
                $query->where('id_rt', $user->id_rt);
            }
        }

        return $query;
    }

    protected function findSuratForCurrentUser($id)
    {
        return $this->scopeSuratToCurrentUser(SuratPengajuan::query())
            ->whereKey($id)
            ->first();
    }

    protected function findSuratForCurrentUserOrFail($id)
    {
        return $this->scopeSuratToCurrentUser(SuratPengajuan::query())
            ->whereKey($id)
            ->firstOrFail();
    }

    protected function findPejabatBySkpdAndJabatan($idSkpd, int $idJabatan): ?Pejabat
    {
        $idSkpd = (int) $idSkpd;
        if ($idSkpd <= 0) {
            return null;
        }

        return Pejabat::with(['jabatan', 'pangkat', 'skpd.kecamatan'])
            ->where('id_skpd', $idSkpd)
            ->where('id_jabatan', $idJabatan)
            ->latest('id')
            ->first();
    }

    protected function resolveLurahForKelurahanId($idKel): ?Pejabat
    {
        // Lurah wajib diambil dari tabel pejabats sesuai wilayah kelurahan + id_jabatan = 1.
        // Jangan pakai first() berdasarkan id_skpd saja karena tabel pejabats juga berisi Kasi/Sekkel/Admin.
        return $this->findPejabatBySkpdAndJabatan($idKel, 1);
    }

    protected function resolveCamatForKelurahanId($idKel): ?Pejabat
    {
        $kelurahanSkpd = Skpd::with('kecamatan')->find((int) $idKel);
        if (!$kelurahanSkpd) {
            return null;
        }

        $idKec = trim((string) $kelurahanSkpd->id_kec);
        $kecamatanSkpd = null;

        if ($idKec !== '') {
            // Di tabel skpds, baris kecamatan memakai id_region = id_kec, contoh 35.71.01.
            $kecamatanSkpd = Skpd::where('id_region', $idKec)->first();
        }

        if (!$kecamatanSkpd) {
            $kecamatanSkpd = Skpd::whereIn('nama', [
                strtoupper((string) optional($kelurahanSkpd->kecamatan)->nama),
                optional($kelurahanSkpd->kecamatan)->nama,
            ])->first();
        }

        if (!$kecamatanSkpd) {
            return $this->resolveCamatByDistrictName(optional($kelurahanSkpd->kecamatan)->nama);
        }

        // Camat wajib diambil dari pejabats sesuai wilayah kecamatan + id_jabatan = 2.
        // Jangan fallback ke first() karena bisa mengambil Sekcam/Kasi/Admin kecamatan.
        return $this->findPejabatBySkpdAndJabatan($kecamatanSkpd->id, 2);
    }

    /**
     * Mengambil nama kecamatan untuk header template Word/PDF.
     * Dibuat fallback berlapis karena pada beberapa data relasi skpds->kecamatan
     * bisa kosong, sementara kelurahan tetap terbaca dari skpds.nama.
     */
    protected function resolveSkpdKecamatanName($skpd, array $residentData = []): string
    {
        $name = optional(optional($skpd)->kecamatan)->nama;

        if (!$name && !empty($skpd->id_kec)) {
            $kecamatanSkpd = Skpd::where('id_region', trim((string) $skpd->id_kec))->first();
            $name = optional($kecamatanSkpd)->nama;
        }

        if (!$name && !empty($residentData['kecamatan_nm'])) {
            $name = $residentData['kecamatan_nm'];
        }

        return strtoupper(trim((string) $name));
    }

    protected function assertRoleCanTransition(SuratPengajuan $surat, int $nextStatus): ?string
    {
        $roleId = (int) auth()->user()->role_id;
        $currentStatus = (int) $surat->status;

        // Role 7 adalah Super Admin: boleh override transisi dari rumah kontrol baru tanpa mengubah alur role lain.
        if ($roleId === 7) {
            return null;
        }

        if (in_array($roleId, [1, 8, 9], true) && in_array($currentStatus, [0, 1], true) && $nextStatus === 2) {
            return null;
        }

        if ($roleId === 4 && $currentStatus === 2 && $nextStatus === 3) {
            return null;
        }

        // Khusus SKTM: setelah TTE Lurah status 4, Lurah baru boleh menaikkan ke Kecamatan/Sekcam (status 11).
        if ($roleId === 3 && $currentStatus === 4 && $nextStatus === 11 && strtolower((string) $surat->jenis_surat) === 'sktm') {
            return null;
        }

        // Sekretaris/Adm Camat menaikkan SKTM dari meja kecamatan ke Camat untuk TTE.
        if ($roleId === 6 && $currentStatus === 11 && $nextStatus === 8 && strtolower((string) $surat->jenis_surat) === 'sktm') {
            return null;
        }

        // Camat hanya final setelah TTE Camat, bukan dari tombol naik biasa.
        if ($roleId === 5 && $currentStatus === 8 && $nextStatus === 9) {
            return null;
        }

        return 'Role Anda tidak memiliki akses untuk mengubah status surat ini.';
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
                        'kepada_tempat_lhr',
                        'kepada_tgl_lhr',
                        'kepada_gender',
                        'kepada_gender_nm',
                        'kepada_hubungan',
                        'kepada_sekolah',
                        'kepada_kelas',
                        'kepada_alamat_sekolah',
                        'penghasilan',
                        'terbilang',
                        'keperluan',
                        'surat_keperluan',
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
                    'kepada'         => 'nullable|string',
                    'tgl_surat'      => 'nullable|date',
                ];

                if ((int) auth()->user()->role_id === 7) {
                    $rules['id_kel'] = ['required', 'integer', 'exists:skpds,id'];
                }

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
                        $rules['register_as'] = 'required|in:perorangan,sekolah';
                        $rules['kategori'] = 'required|string|max:255';
                        $rules['keterangan'] = 'required|string';
                        $rules['kepada'] = 'nullable|string|max:255';
                        $rules['kepada_tempat_lhr'] = 'required_if:register_as,sekolah|nullable|string|max:255';
                        $rules['kepada_tgl_lhr'] = 'required_if:register_as,sekolah|nullable|string|max:255';
                        $rules['kepada_gender'] = 'required_if:register_as,sekolah|nullable|string|max:50';
                        $rules['kepada_hubungan'] = 'required_if:register_as,sekolah|nullable|string|max:255';
                        $rules['kepada_sekolah'] = 'required_if:register_as,sekolah|nullable|string|max:255';
                        $rules['kepada_kelas'] = 'required_if:register_as,sekolah|nullable|string|max:50';
                        $rules['kepada_alamat_sekolah'] = 'required_if:register_as,sekolah|nullable|string';
                    } elseif ($request->jenis_surat !== 'skboro') {
                        $rules['kepada'] = 'required|string|max:255';
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
                    $rules['kepada'] = 'required|string|max:255';
                    $rules['kepada_tempat_lhr'] = 'required|string|max:255';
                    $rules['kepada_tgl_lhr'] = 'required|date';
                    $rules['kepada_gender'] = 'required|string|max:50';
                    $rules['kepada_hubungan'] = 'required|string|max:255';
                    $rules['kepada_sekolah'] = 'required|string|max:255';
                    $rules['kepada_kelas'] = 'required|string|max:100';
                    $rules['kepada_alamat_sekolah'] = 'required|string';
                    $rules['penghasilan'] = 'required|string|max:255';
                    $rules['terbilang'] = 'required|string|max:255';
                    $rules['keperluan'] = 'nullable|string|max:255';
                    $rules['surat_keperluan'] = 'nullable|string|max:255';
                    $rules['keterangan_tambahan'] = 'nullable|string';
                }

                if ($request->jenis_surat === 'skboro') {
                    $rules['tgl_awal'] = 'required|date';
                    $rules['tgl_akhir'] = 'required|date|after_or_equal:tgl_awal';
                    $rules['provinsi_boro'] = 'required|string|max:255';
                    $rules['kabko_boro'] = 'required|string|max:255';
                    $rules['kecamatan_boro'] = 'required|string|max:255';
                    $rules['kelurahan_boro'] = 'required|string|max:255';
                    $rules['alamat_boro'] = 'required|string';
                    $rules['jumlah_pengikut'] = 'nullable|integer|min:0';
                    $rules['kepada'] = 'nullable|string|max:255';
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
                        $isSuperAdminStore = ((int) $user->role_id === 7);
                        $targetSkpd = $isSuperAdminStore
                            ? Skpd::findOrFail((int) $request->id_kel)
                            : Skpd::find($user->id_instansi);
                        $targetInstansiId = $targetSkpd->id ?? $user->id_instansi;
                        $targetRw = $request->filled('rw') ? $request->rw : $user->id_rw;
                        $targetRt = $request->filled('rt') ? $request->rt : $user->id_rt;

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
						
						if ($request->jenis_surat === 'sktm') {
						$registerAs = strtolower(trim((string) $request->register_as));
						$allInput['register_as'] = $registerAs;
					
						if ($registerAs !== 'sekolah') {
							$allInput['kepada'] = strtoupper($request->name ?? '');
							$allInput['kepada_tempat_lhr'] = null;
							$allInput['kepada_tgl_lhr'] = null;
							$allInput['kepada_gender'] = null;
							$allInput['kepada_gender_nm'] = null;
							$allInput['kepada_hubungan'] = null;
							$allInput['kepada_sekolah'] = null;
							$allInput['kepada_kelas'] = null;
							$allInput['kepada_alamat_sekolah'] = null;
						}
					}

                        $allInput['name'] = strtoupper($request->name ?? '');
                        $allInput['tempat_lhr'] = strtoupper($request->tempat_lhr ?? '');
                        $allInput['alamat'] = strtoupper($request->alamat ?? '');

                        if ($request->jenis_surat === 'skhsl') {
                            $allInput['kepada'] = strtoupper($request->kepada ?? '');
                            $allInput['kepada_tempat_lhr'] = strtoupper($request->kepada_tempat_lhr ?? '');
                            $allInput['kepada_gender_nm'] = $request->kepada_gender;
                            $allInput['keperluan'] = $request->peruntukan;
                            $allInput['surat_keperluan'] = $request->peruntukan;
                        }

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
                        $variableData['submitter_type'] = 'admin';

                        if ($request->jenis_surat === 'skhsl') {
                            $penghasilanAngka = preg_replace('/[^0-9]/', '', (string) $request->penghasilan);
                            $penghasilanDisplay = $penghasilanAngka !== ''
                                ? 'Rp. ' . number_format((int) $penghasilanAngka, 2, ',', '.')
                                : (string) $request->penghasilan;

                            $variableData['kepada'] = strtoupper($request->kepada ?? '');
                            $variableData['kepada_tempat_lhr'] = strtoupper($request->kepada_tempat_lhr ?? '');
                            $variableData['kepada_tgl_lhr'] = $request->kepada_tgl_lhr;
                            $variableData['kepada_gender'] = $request->kepada_gender;
                            $variableData['kepada_gender_nm'] = $request->kepada_gender;
                            $variableData['kepada_hubungan'] = $request->kepada_hubungan;
                            $variableData['kepada_sekolah'] = $request->kepada_sekolah;
                            $variableData['kepada_kelas'] = $request->kepada_kelas;
                            $variableData['kepada_alamat_sekolah'] = $request->kepada_alamat_sekolah;
                            $variableData['penghasilan'] = $request->penghasilan;
                            $variableData['penghasilan_display'] = $penghasilanDisplay;
                            $variableData['terbilang'] = $request->terbilang;
                            $variableData['keperluan'] = $request->peruntukan;
                            $variableData['surat_keperluan'] = $request->peruntukan;
                            $variableData['surat_keterangan'] = 'Adalah benar-benar dengan penghasilan perbulan sebesar ' . $penghasilanDisplay . ' (' . $request->terbilang . ').';
                        }

                        if ($request->jenis_surat === 'skboro') {
                            $tujuanBoro = collect([
                                $request->kelurahan_boro ? 'Desa / Kelurahan : ' . $request->kelurahan_boro : null,
                                $request->kecamatan_boro ? 'Kecamatan : ' . $request->kecamatan_boro : null,
                                $request->kabko_boro ? 'Kabupaten/Kota : ' . $request->kabko_boro : null,
                                $request->provinsi_boro ? 'Provinsi : ' . $request->provinsi_boro : null,
                                $request->alamat_boro ? 'Alamat : ' . $request->alamat_boro : null,
                            ])->filter()->implode(' ');

                            $variableData['surat_tgl_berlaku'] = trim(($request->tgl_awal ?? '') . ' s/d ' . ($request->tgl_akhir ?? ''));
                            $variableData['surat_tujuan'] = $tujuanBoro;
                            $variableData['surat_keperluan'] = $request->peruntukan;
                            $variableData['surat_jml_pengikut'] = (string) ($request->jumlah_pengikut ?? '0');
                        }
						
						if ($request->jenis_surat === 'sktm') {
						$registerAs = strtolower(trim((string) $request->register_as));
						$variableData['register_as'] = $registerAs;
					
						if ($registerAs !== 'sekolah') {
							unset(
								$variableData['kepada'],
								$variableData['kepada_tempat_lhr'],
								$variableData['kepada_tgl_lhr'],
								$variableData['kepada_gender'],
								$variableData['kepada_gender_nm'],
								$variableData['kepada_hubungan'],
								$variableData['kepada_sekolah'],
								$variableData['kepada_kelas'],
								$variableData['kepada_alamat_sekolah']
							);
						}
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

                        $fileUrl = null;
                        if ($request->hasFile('pengantar')) {
                            $path = $request->file('pengantar')->store(
                                'public/pengantar/' . date('Y') . '/' . $request->jenis_surat
                            );
                            $fileUrl = str_replace('public/', '/storage/', $path);
                        }

                        $surat = SuratPengajuan::create([
							'kepada' => $request->jenis_surat === 'sktm'
							? (strtolower((string) $request->register_as) === 'sekolah'
								? $request->kepada
								: strtoupper($request->name ?? ''))
							: $request->kepada,
                            'jenis_surat'    => $request->jenis_surat,
                            'kd_jenis_surat' => $request->kd_jenis_surat,
                            'no_urut_surat'  => $request->no_urut_surat,
                            'nik'            => $request->nik,
                            'id_kel'         => $targetInstansiId,
                            'id_rw'          => $targetRw,
                            'id_rt'          => $targetRt,
                            'tahun'          => date('Y'),
                            'tgl_surat'      => $request->tgl_surat ?: now(),
                            'peruntukan'     => $request->peruntukan,
                            'kepada'         => $request->jenis_surat === 'skboro' ? null : $request->kepada,
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
                            ->route($isSuperAdminStore ? 'super-admin.pelayanan.index' : 'admin.surat.index', $isSuperAdminStore ? ['id_kel' => $targetInstansiId] : [])
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
            $surat = $this->findSuratForCurrentUserOrFail($id);
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
                'kepada'         => 'nullable|string',
                'pengantar'      => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
                'bukti_ttd_basah' => 'nullable|file|mimes:jpg,jpeg,png,pdf,webp|max:5120',
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
					$rules['register_as'] = 'required|in:perorangan,sekolah';
					$rules['kategori'] = 'required|string|max:255';
					$rules['keterangan'] = 'required|string';
				
					$rules['kepada'] = 'required_if:register_as,sekolah|nullable|string|max:255';
					$rules['kepada_tempat_lhr'] = 'required_if:register_as,sekolah|nullable|string|max:255';
					$rules['kepada_tgl_lhr'] = 'required_if:register_as,sekolah|nullable|string|max:255';
					$rules['kepada_gender'] = 'required_if:register_as,sekolah|nullable|string|max:20';
					$rules['kepada_hubungan'] = 'required_if:register_as,sekolah|nullable|string|max:255';
					$rules['kepada_sekolah'] = 'required_if:register_as,sekolah|nullable|string|max:255';
					$rules['kepada_kelas'] = 'required_if:register_as,sekolah|nullable|string|max:50';
					$rules['kepada_alamat_sekolah'] = 'required_if:register_as,sekolah|nullable|string';
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
                $rules['kepada'] = 'required|string|max:255';
                $rules['kepada_tempat_lhr'] = 'required|string|max:255';
                $rules['kepada_tgl_lhr'] = 'required|date';
                $rules['kepada_gender'] = 'required|string|max:50';
                $rules['kepada_hubungan'] = 'required|string|max:255';
                $rules['kepada_sekolah'] = 'required|string|max:255';
                $rules['kepada_kelas'] = 'required|string|max:100';
                $rules['kepada_alamat_sekolah'] = 'required|string';
                $rules['penghasilan'] = 'required|string|max:255';
                $rules['terbilang'] = 'required|string|max:255';
                $rules['keperluan'] = 'nullable|string|max:255';
                $rules['surat_keperluan'] = 'nullable|string|max:255';
                $rules['keterangan_tambahan'] = 'nullable|string';
            }

            if ($request->jenis_surat === 'skboro') {
                // BORO memakai field tujuan dan masa berlaku, bukan nama_ayah/nama_ibu/alamat_asal.
                $rules['tgl_awal'] = 'required|date';
                $rules['tgl_akhir'] = 'required|date|after_or_equal:tgl_awal';
                $rules['provinsi_boro'] = 'required|string|max:255';
                $rules['kabko_boro'] = 'required|string|max:255';
                $rules['kecamatan_boro'] = 'required|string|max:255';
                $rules['kelurahan_boro'] = 'required|string|max:255';
                $rules['alamat_boro'] = 'required|string';
                $rules['jumlah_pengikut'] = 'nullable|integer|min:0';
                $rules['kepada'] = 'nullable|string|max:255';
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
                    $surat = $this->findSuratForCurrentUserOrFail($id);

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

                    $existingVariable = $this->decodeFlexibleValue($surat->variable);

                    $allInput = $request->except(['_token', '_method']);
                    $allInput['name'] = strtoupper($request->name ?? '');
                    $allInput['tempat_lhr'] = strtoupper($request->tempat_lhr ?? '');
                    $allInput['alamat'] = strtoupper($request->alamat ?? '');

                    if ($request->jenis_surat === 'skhsl') {
                        $allInput['kepada'] = strtoupper($request->kepada ?? '');
                        $allInput['kepada_tempat_lhr'] = strtoupper($request->kepada_tempat_lhr ?? '');
                        $allInput['kepada_gender_nm'] = $request->kepada_gender;
                        $allInput['keperluan'] = $request->peruntukan;
                        $allInput['surat_keperluan'] = $request->peruntukan;
                    }

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

                    if ($request->jenis_surat === 'sktm') {
                        $registerAs = strtolower(trim((string) $request->register_as));
                        $allInput['register_as'] = $registerAs;

                        if ($registerAs !== 'sekolah') {
                            $allInput['kepada'] = null;
                            $allInput['kepada_tempat_lhr'] = null;
                            $allInput['kepada_tgl_lhr'] = null;
                            $allInput['kepada_gender'] = null;
                            $allInput['kepada_gender_nm'] = null;
                            $allInput['kepada_hubungan'] = null;
                            $allInput['kepada_sekolah'] = null;
                            $allInput['kepada_kelas'] = null;
                            $allInput['kepada_alamat_sekolah'] = null;
                        }
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
                    $variableData = array_merge($existingVariable, $variableData);
                    $variableData['submitter_type'] = $existingVariable['submitter_type'] ?? $this->resolveSubmitterType($surat);

                    if ($request->jenis_surat === 'skhsl') {
                        $penghasilanAngka = preg_replace('/[^0-9]/', '', (string) $request->penghasilan);
                        $penghasilanDisplay = $penghasilanAngka !== ''
                            ? 'Rp. ' . number_format((int) $penghasilanAngka, 2, ',', '.')
                            : (string) $request->penghasilan;

                        $variableData['kepada'] = strtoupper($request->kepada ?? '');
                        $variableData['kepada_tempat_lhr'] = strtoupper($request->kepada_tempat_lhr ?? '');
                        $variableData['kepada_tgl_lhr'] = $request->kepada_tgl_lhr;
                        $variableData['kepada_gender'] = $request->kepada_gender;
                        $variableData['kepada_gender_nm'] = $request->kepada_gender;
                        $variableData['kepada_hubungan'] = $request->kepada_hubungan;
                        $variableData['kepada_sekolah'] = $request->kepada_sekolah;
                        $variableData['kepada_kelas'] = $request->kepada_kelas;
                        $variableData['kepada_alamat_sekolah'] = $request->kepada_alamat_sekolah;
                        $variableData['penghasilan'] = $request->penghasilan;
                        $variableData['penghasilan_display'] = $penghasilanDisplay;
                        $variableData['terbilang'] = $request->terbilang;
                        $variableData['keperluan'] = $request->peruntukan;
                        $variableData['surat_keperluan'] = $request->peruntukan;
                        $variableData['surat_keterangan'] = 'Adalah benar-benar dengan penghasilan perbulan sebesar ' . $penghasilanDisplay . ' (' . $request->terbilang . ').';
                    }

                    if ($request->jenis_surat === 'skboro') {
                        $tujuanBoro = collect([
                            $request->kelurahan_boro ? 'Desa / Kelurahan : ' . $request->kelurahan_boro : null,
                            $request->kecamatan_boro ? 'Kecamatan : ' . $request->kecamatan_boro : null,
                            $request->kabko_boro ? 'Kabupaten/Kota : ' . $request->kabko_boro : null,
                            $request->provinsi_boro ? 'Provinsi : ' . $request->provinsi_boro : null,
                            $request->alamat_boro ? 'Alamat : ' . $request->alamat_boro : null,
                        ])->filter()->implode(' ');

                        $variableData['surat_tgl_berlaku'] = trim(($request->tgl_awal ?? '') . ' s/d ' . ($request->tgl_akhir ?? ''));
                        $variableData['surat_tujuan'] = $tujuanBoro;
                        $variableData['surat_keperluan'] = $request->peruntukan;
                        $variableData['surat_jml_pengikut'] = (string) ($request->jumlah_pengikut ?? '0');
                    }

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

                    if ($request->jenis_surat === 'sktm') {
                        $registerAs = strtolower(trim((string) $request->register_as));
                        $variableData['register_as'] = $registerAs;

                        if ($registerAs !== 'sekolah') {
                            unset(
                                $variableData['kepada_tempat_lhr'],
                                $variableData['kepada_tgl_lhr'],
                                $variableData['kepada_gender'],
                                $variableData['kepada_gender_nm'],
                                $variableData['kepada_hubungan'],
                                $variableData['kepada_sekolah'],
                                $variableData['kepada_kelas'],
                                $variableData['kepada_alamat_sekolah']
                            );
                        }
                    }

                    $fileUrl = $surat->pengantar;
                    if ($request->hasFile('pengantar')) {
                        $path = $request->file('pengantar')->store(
                            'public/pengantar/' . date('Y') . '/' . $request->jenis_surat
                        );
                        $fileUrl = str_replace('public/', '/storage/', $path);
                    }

                    if ($request->hasFile('bukti_ttd_basah')) {
                        $proofPath = $request->file('bukti_ttd_basah')->store(
                            'public/bukti_ttd_basah/' . date('Y') . '/' . $request->jenis_surat
                        );
                        $variableData['bukti_ttd_basah'] = str_replace('public/', '/storage/', $proofPath);
                        $variableData['manual_signature'] = true;
                        $variableData['signature_mode'] = 'manual';
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
                        'kepada'         => $request->jenis_surat === 'skboro' ? null : ($request->jenis_surat === 'sktm' && strtolower((string) $request->register_as) !== 'sekolah' ? null : $request->kepada),
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




            protected function resolveSubmitterType(?SuratPengajuan $surat): string
            {
                if (!$surat) {
                    return 'admin';
                }

                $variable = $this->decodeFlexibleValue($surat->variable ?? []);
                $submitterType = strtolower(trim((string) ($variable['submitter_type'] ?? '')));

                if (in_array($submitterType, ['warga', 'admin'], true)) {
                    return $submitterType;
                }

                $firstLog = Log_surat::query()
                    ->where('tabel_surat', 'surat_pengajuans')
                    ->where('id_surat', $surat->id)
                    ->orderBy('id', 'asc')
                    ->first();

                if ($firstLog) {
                    $firstStatus = (int) $firstLog->status_surat;

                    if ($firstStatus === 0) {
                        return 'warga';
                    }

                    if ($firstStatus === 1) {
                        return 'admin';
                    }
                }

                return ((int) $surat->status === 0) ? 'warga' : 'admin';
            }

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

                    $unserialized = decode_json_data($value);
                    if ($unserialized !== false && is_array($unserialized)) {
                        return $unserialized;
                    }
                }

                return [];
            }

				protected function resolveTemplateFileAdmin(string $jenis, int $idKel, ?\App\Models\SuratPengajuan $surat = null): string
			{
				if ($jenis === 'sktm') {
					$registerAs = strtolower((string) data_get($surat?->variable, 'register_as', 'perorangan'));
			
					$path = $registerAs === 'sekolah'
						? public_path('templates/SKTM_SEKOLAH.docx')
						: public_path('templates/SKTM_PERORANGAN.docx');
			
					if (!file_exists($path)) {
						abort(404, 'Template SKTM tidak ditemukan.');
					}
			
					return $path;
				}
			
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

                $skpdSurat = Skpd::with('kecamatan')->find((int) $surat->id_kel);
                $pejabat = $this->resolveLurahForKelurahanId((int) $surat->id_kel);
                $camat = $this->resolveCamatForKelurahanId((int) $surat->id_kel);


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
                    'skpd_kec'            => $this->resolveSkpdKecamatanName($skpdSurat, $residentData),
                    'skpd_kel'            => strtoupper(optional($skpdSurat)->nama ?? ''),
                    'skpd_alamat'         => optional($skpdSurat)->instansi_alamat ?? '',
                    'skpd_telp'           => optional($skpdSurat)->instansi_telp ?? '',
                    'skpd_pos'            => optional($skpdSurat)->instansi_kode_pos ?? '',
                    'skpd_kepala'         => $pejabat->nama ?? '',
                    'skpd_nip_kepala'     => $pejabat->nip ?? '',
                    'skpd_jabatan'        => trim(ucfirst(optional(optional($pejabat)->jabatan)->nama ?? '') . ' ' . ucfirst(strtolower(optional($skpdSurat)->nama ?? ''))),
                    'skpd_camat'          => optional($camat)->nama ?? '',
                    'skpd_nip_camat'      => optional($camat)->nip ?? '',
                    'skpd_jabatan_camat'  => strtoupper(optional(optional($camat)->jabatan)->nama ?? 'CAMAT'),

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
                    // Marker kosong untuk Lurah tetap menyisakan tanda ~ di template.
                    // Marker Camat tetap disiapkan agar PDF sebelum TTE Camat masih punya titik tag.
                    'qr'                  => '',
                    'qr_camat'            => '[[qr_camat]]',

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

                if ($surat->jenis_surat === 'sktm') {
                    $data['surat_kepada'] = $surat->kepada ?? ($variableData['kepada'] ?? '');
                    $data['surat_kepada_tempat_lhr'] = $variableData['kepada_tempat_lhr'] ?? '';
                    $data['surat_kepada_tgl_lhr'] = $variableData['kepada_tgl_lhr'] ?? '';
                    $data['surat_kepada_sekolah'] = $variableData['kepada_sekolah'] ?? '';
                    $data['surat_kepada_kelas'] = $variableData['kepada_kelas'] ?? '';
                    $data['surat_kepada_gender'] = $variableData['kepada_gender'] ?? '';
                    $data['surat_kepada_gender_nm'] = $variableData['kepada_gender_nm'] ?? ($variableData['kepada_gender'] ?? '');
                    $data['surat_kepada_hubungan'] = $variableData['kepada_hubungan'] ?? '';
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
                        $penghasilanRaw = $variableData['penghasilan'] ?? '';
                        $penghasilanAngka = preg_replace('/[^0-9]/', '', (string) $penghasilanRaw);
                        $penghasilanDisplay = $variableData['penghasilan_display'] ?? (
                            $penghasilanAngka !== ''
                                ? 'Rp. ' . number_format((int) $penghasilanAngka, 2, ',', '.')
                                : (string) $penghasilanRaw
                        );

                        $data['surat_kepada'] = $surat->kepada ?? ($variableData['kepada'] ?? '');
                        $data['surat_kepada_tempat_lhr'] = $variableData['kepada_tempat_lhr'] ?? '';
                        $data['surat_kepada_tgl_lhr'] = $variableData['kepada_tgl_lhr'] ?? '';
                        $data['surat_kepada_sekolah'] = $variableData['kepada_sekolah'] ?? '';
                        $data['surat_kepada_kelas'] = $variableData['kepada_kelas'] ?? '';
                        $data['surat_kepada_alamat_sekolah'] = $variableData['kepada_alamat_sekolah'] ?? '';
                        $data['surat_kepada_gender_nm'] = $variableData['kepada_gender_nm'] ?? ($variableData['kepada_gender'] ?? '');
                        $data['surat_kepada_hubungan'] = $variableData['kepada_hubungan'] ?? '';
                        $data['surat_keperluan'] = $variableData['surat_keperluan'] ?? ($variableData['keperluan'] ?? $surat->peruntukan ?? '');
                        $data['surat_keterangan'] = $variableData['surat_keterangan']
                            ?? ('Adalah benar-benar dengan penghasilan perbulan sebesar ' . $penghasilanDisplay . ' (' . ($variableData['terbilang'] ?? '') . ').');
                        break;
                    case 'skboro':
                        $data['header'] = $variableData['header'] ?? '';
                        $data['block'] = $variableData['block'] ?? '';
                        $data['detail_pengikut'] = $variableData['detail_pengikut'] ?? '';
                        $data['qr'] = $data['show_qr'] ? ($data['qr'] ?? '') : '';
                        $data['surat_tgl_berlaku'] = $variableData['surat_tgl_berlaku'] ?? trim(($variableData['tgl_awal'] ?? '') . ' s/d ' . ($variableData['tgl_akhir'] ?? ''));
                        $data['surat_tujuan'] = $variableData['surat_tujuan'] ?? collect([
                            !empty($variableData['kelurahan_boro']) ? 'Desa / Kelurahan : ' . $variableData['kelurahan_boro'] : null,
                            !empty($variableData['kecamatan_boro']) ? 'Kecamatan : ' . $variableData['kecamatan_boro'] : null,
                            !empty($variableData['kabko_boro']) ? 'Kabupaten/Kota : ' . $variableData['kabko_boro'] : null,
                            !empty($variableData['provinsi_boro']) ? 'Provinsi : ' . $variableData['provinsi_boro'] : null,
                            !empty($variableData['alamat_boro']) ? 'Alamat : ' . $variableData['alamat_boro'] : null,
                        ])->filter()->implode(' ');
                        $data['surat_keperluan'] = $variableData['surat_keperluan'] ?? ($surat->peruntukan ?? '');
                        $data['surat_jml_pengikut'] = $variableData['surat_jml_pengikut'] ?? (string) ($variableData['jumlah_pengikut'] ?? '0');
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['alamat_boro'] ?? '');
                        break;
                    case 'suket':
                        $data['surat_keterangan'] = $data['surat_keterangan'] ?: ($variableData['keterangan_tambahan'] ?? '');
                        break;
                }

                return $data;
            }

           	protected function generateAdminPdfFile(\App\Models\SuratPengajuan $surat, bool $manualSignature = false): array
    {
        $data = $this->buildPdfDataAdmin($surat);
        $templateFile = $this->resolveTemplateFileAdmin($surat->jenis_surat, (int) $surat->id_kel, $surat);

        if ($manualSignature) {
            $data = $this->applyManualSignatureData($data);
            $templateFile = $this->buildManualSignatureTemplate($templateFile);

            $variable = $this->decodeFlexibleValue($surat->variable);
            $variable['manual_signature'] = true;
            $variable['signature_mode'] = 'manual';
            $variable['manual_signature_previewed_at'] = now()->toDateTimeString();
            $surat->update(['variable' => $variable]);
        }

        $suffix = $manualSignature ? '_BASAH' : '';
        $outputPdf = hash('sha256', strtoupper($surat->jenis_surat) . '_' . $surat->id . $suffix);
        $pdfPath = $this->generatePdf($data, $templateFile, $outputPdf);

        return [$pdfPath, $data];
    }

            /**
             * Percepat download: jika PDF final sudah pernah dibuat dan datanya belum berubah,
             * langsung kirim file PDF yang ada tanpa generate DOCX/PDF ulang.
             */
            protected function getCachedAdminPdfFile(\App\Models\SuratPengajuan $surat, bool $manualSignature = false): array
            {
                $suffix = $manualSignature ? '_BASAH' : '';
                $outputPdf = hash('sha256', strtoupper($surat->jenis_surat) . '_' . $surat->id . $suffix);

                $candidates = [
                    storage_path('app/public/pdf/' . $outputPdf . '.pdf'),
                    public_path('storage/pdf/' . $outputPdf . '.pdf'),
                    storage_path('app/pdf/' . $outputPdf . '.pdf'),
                ];

                $updatedAt = $surat->updated_at ? strtotime((string) $surat->updated_at) : null;

                foreach ($candidates as $path) {
                    if (is_file($path) && (!$updatedAt || filemtime($path) >= $updatedAt)) {
                        return [$path, []];
                    }
                }

                return $this->generateAdminPdfFile($surat, $manualSignature);
            }

            protected function getSignedPdfPathForTteFlow(\App\Models\SuratPengajuan $surat): ?string
            {
                $status = (int) $surat->status;
                if (empty($surat->file) || !in_array($status, [4, 9, 11, 8], true)) {
                    return null;
                }

                $variable = $this->decodeFlexibleValue($surat->variable);
                $isManualSignature = !empty($variable['manual_signature'])
                    || (($variable['signature_mode'] ?? null) === 'manual');

                // Untuk surat yang sudah TTE, preview/cetak wajib membuka file hasil TTE dari kolom file.
                // Kalau digenerate ulang dari template, QR/img TTE hilang dan nama pejabat bisa kembali salah.
                if ($isManualSignature) {
                    return null;
                }

                $candidate = public_path($surat->file);
                return is_file($candidate) ? $candidate : null;
            }

            public function preview($id)
            {
                $surat = $this->findSuratForCurrentUserOrFail($id);

                if ($signedPath = $this->getSignedPdfPathForTteFlow($surat)) {
                    return response()->file($signedPath, [
                        'Content-Type' => 'application/pdf',
                        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                    ]);
                }

                [$pdfPath, $data] = $this->generateAdminPdfFile($surat);

                return response()->file($pdfPath);
            }

            public function cetak($id)
            {
                $surat = $this->findSuratForCurrentUserOrFail($id);
                $pdfPath = $this->getSignedPdfPathForTteFlow($surat);

                if (!$pdfPath) {
                    [$pdfPath, $data] = $this->getCachedAdminPdfFile($surat, false);
                }

                $namaFile = strtoupper($surat->jenis_surat) . '_' . preg_replace('/[^A-Za-z0-9\-]+/', '_', $this->getNoSrt($surat)) . '.pdf';

                return response()->download($pdfPath, $namaFile);
            }
			
			public function previewBasah($id)
			{
				$surat = $this->findSuratForCurrentUserOrFail($id);
			
				[$pdfPath, $data] = $this->getCachedAdminPdfFile($surat, true);
			
				return response()->file($pdfPath, [
					'Content-Type' => 'application/pdf',
					'Cache-Control' => 'public, max-age=3600',
				]);
			}
			
			public function cetakBasah($id)
			{
				$surat = $this->findSuratForCurrentUserOrFail($id);
				[$pdfPath, $data] = $this->getCachedAdminPdfFile($surat, true);
			
				$namaFile = strtoupper($surat->jenis_surat)
					. '_TTD_BASAH_'
					. preg_replace('/[^A-Za-z0-9\-]+/', '_', $this->getNoSrt($surat))
					. '.pdf';
			
				return response()->download($pdfPath, $namaFile);
			}

            protected function normalizeRegistrasiKecamatan(Request $request): string
            {
                $directValue = trim((string) $request->input('register_kecamatan', $request->input('register', '')));
                if ($directValue !== '') {
                    return preg_replace('/\s+/', '', $directValue);
                }

                $nomor = trim((string) $request->input('register_nomor', ''));
                $kode = trim((string) $request->input('register_kode', ''));
                $instansi = trim((string) $request->input('register_instansi', ''));
                $unit = trim((string) $request->input('register_unit', ''));
                $tahun = trim((string) $request->input('register_tahun', ''));

                if ($nomor === '' && $kode === '' && $instansi === '' && $unit === '' && $tahun === '') {
                    return '';
                }

                if ($nomor === '' || $kode === '' || $instansi === '' || $unit === '' || $tahun === '') {
                    return '';
                }

                return preg_replace('/\s+/', '', "{$nomor}/{$kode}/{$instansi}.{$unit}/{$tahun}");
            }


            // Naikan Ke Atasan Yang Lebih tinggi Web Admin

        public function naik(Request $request, $id)
                {
                    $surat = $this->findSuratForCurrentUser($id);

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
                    // 0/1 -> 2 = Admin naik ke Sekkel
                    // 2   -> 3 = Sekkel naik ke Lurah
                    // Non-SKTM: status 3 menunggu TTE Lurah, setelah TTE langsung final status 4.
                    // SKTM: status 3 TTE Lurah -> status 4, lalu Lurah klik Naikkan ke Kecamatan (11),
                    //       Sekretaris/Adm Camat klik Naikkan ke Camat (8), Camat TTE -> final status 9.
                    $isSktm = strtolower((string) $surat->jenis_surat) === 'sktm';
                    if (in_array($currentStatus, [0, 1], true)) {
                        $nextStatus = 2;
                        $message = 'Pengajuan berhasil diajukan ke Sekkel.';
                    } elseif ($currentStatus === 2) {
                        $nextStatus = 3;
                        $message = 'Pengajuan berhasil diajukan ke Lurah.';
                    } elseif ($currentStatus === 4 && $isSktm) {
                        $nextStatus = 11;
                        $message = 'SKTM berhasil dinaikkan ke Kecamatan/Sekretaris Camat.';
                    } elseif ($currentStatus === 11 && $isSktm) {
                        $nextStatus = 8;
                        $message = 'SKTM berhasil dinaikkan ke Camat untuk TTE.';
                    }

                    if ($nextStatus === null) {
                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Status surat ini tidak bisa diajukan ke level berikutnya.'
                        ], 422);
                    }

                    if ($error = $this->assertRoleCanTransition($surat, $nextStatus)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $error,
                        ], 403);
                    }

                    $variable = $this->clearManualSignatureFlags($this->decodeFlexibleValue($surat->variable));
            $variable['submitter_type'] = $variable['submitter_type'] ?? $this->resolveSubmitterType($surat);

                    $updateData = [
                        'status' => $nextStatus,
                    ];

                    // Khusus Role Sekcam: sebelum SKTM naik ke Camat wajib mengisi nomor registrasi kecamatan.
                    if ((int) auth()->user()->role_id === 6 && $currentStatus === 11 && $nextStatus === 8 && $isSktm) {
                        $registrasiKecamatan = $this->normalizeRegistrasiKecamatan($request);

                        if ($registrasiKecamatan === '') {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Silahkan isi Data Registrasi Kecamatan Anda terlebih dahulu.',
                            ], 422);
                        }

                        if (!preg_match('/^[0-9A-Za-z.\-\/]+$/', $registrasiKecamatan)) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Format Registrasi Kecamatan tidak valid. Contoh: 145/14/419.407/2026.',
                            ], 422);
                        }

                        $duplicateRegistrasi = SuratPengajuan::query()
                            ->where('id', '!=', $surat->id)
                            ->where(function ($query) use ($registrasiKecamatan) {
                                if (Schema::hasColumn('surat_pengajuans', 'no_register')) {
                                    $query->where('no_register', $registrasiKecamatan);
                                }

                                $query->orWhereRaw(
                                    "(variable IS NOT NULL AND variable <> '' AND JSON_VALID(variable) AND (JSON_UNQUOTE(JSON_EXTRACT(variable, '$.no_register_kecamatan')) = ? OR JSON_UNQUOTE(JSON_EXTRACT(variable, '$.register_kecamatan')) = ?))",
                                    [$registrasiKecamatan, $registrasiKecamatan]
                                );
                            })
                            ->exists();

                        if ($duplicateRegistrasi && !$request->boolean('force_register_duplicate')) {
                            return response()->json([
                                'status' => 'warning',
                                'duplicate' => true,
                                'message' => 'Nomor Registrasi yang anda cantumkan sudah pernah digunakan. Apakah anda tetap ingin lanjut atau ganti registrasi anda?',
                            ], 409);
                        }

                        $variable['no_register_kecamatan'] = $registrasiKecamatan;
                        $variable['register_kecamatan'] = $registrasiKecamatan;

                        if (Schema::hasColumn('surat_pengajuans', 'no_register')) {
                            $updateData['no_register'] = $registrasiKecamatan;
                        }
                    }

                    $updateData['variable'] = $variable;

                    $surat->update($updateData);

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
                    $surat = $this->findSuratForCurrentUser($id);

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

                    if ($error = $this->assertRoleCanTransition($surat, $nextStatus)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => $error,
                        ], 403);
                    }

                    $variable = $this->clearManualSignatureFlags($this->decodeFlexibleValue($surat->variable));
                    $variable['submitter_type'] = $variable['submitter_type'] ?? $this->resolveSubmitterType($surat);


                    $surat->update([
                        'status' => $nextStatus,
                        'variable' => $variable,
                    ]);

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

    			public function tolak(Request $request, $id)
    {
        $request->validate([
            'komentar' => 'required|string|max:1000',
        ], [
            'komentar.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $surat = $this->findSuratForCurrentUser($id);

        if (!$surat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan atau gagal diperbarui.'
            ], 404);
        }

        $variable = $this->decodeFlexibleValue($surat->variable);
        $variable['submitter_type'] = $variable['submitter_type'] ?? $this->resolveSubmitterType($surat);
        $variable['alasan_penolakan'] = trim((string) $request->komentar);
        $variable['ditolak_pada'] = now()->toDateTimeString();

        $surat->update([
            'status'   => 6,
            'variable' => $variable,
        ]);

        Log_surat::create([
            'nik'          => $surat->nik,
            'tabel_surat'  => 'surat_pengajuans',
            'nama_surat'   => strtoupper($surat->jenis_surat),
            'id_surat'     => $surat->id,
            'status_surat' => 6,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Pengajuan berhasil ditolak dan alasan penolakan sudah tersimpan.',
            'data'    => [
                'id'       => $id,
                'jenis'    => $surat->jenis_surat,
                'komentar' => $variable['alasan_penolakan'] ?? null,
            ]
        ]);
    }


    protected function resolveCamatByDistrictName(?string $districtName): ?\App\Models\Pejabat
    {
        $districtName = strtoupper(trim((string) $districtName));
        if ($districtName === '') {
            return null;
        }

        $map = [
            'MOJOROTO'  => 64,
            'KOTA'      => 65,
            'PESANTREN' => 66,
        ];

        $idSkpd = $map[$districtName] ?? null;
        if (!$idSkpd) {
            return null;
        }




        return $this->findPejabatBySkpdAndJabatan($idSkpd, 2);
    }

    protected function applyManualSignatureData(array $data): array
    {
        // Khusus TTD Basah: yang dihilangkan hanya marker TTE/QR.
        // Data pejabat seperti skpd_kepala dan skpd_jabatan tetap wajib hidup
        // karena dipakai di badan surat: "Yang bertanda tangan dibawah ini".
        $data['qr'] = '';
        $data['qr_camat'] = '';
        $data['show_qr'] = false;

        return $data;
    }

    protected function replaceLastTextOccurrence(string $text, string $search, string $replace = ''): string
    {
        $pos = strrpos($text, $search);
        if ($pos === false) {
            return $text;
        }

        return substr_replace($text, $replace, $pos, strlen($search));
    }


    protected function removeTextRunAndOptionalFollowingComma(string $xml, string $literal): string
    {
        $quoted = preg_quote($literal, '/');
        $pattern = '/<w:r\b[^>]*>(?:(?!<\/w:r>).)*<w:t\b[^>]*>\s*' . $quoted . '\s*<\/w:t>(?:(?!<\/w:r>).)*<\/w:r>\s*(?:<w:r\b[^>]*>(?:(?!<\/w:r>).)*<w:t\b[^>]*>\s*,\s*<\/w:t>(?:(?!<\/w:r>).)*<\/w:r>)?/s';
        $updated = preg_replace($pattern, '', $xml, 1);

        return is_string($updated) ? $updated : $xml;
    }

    protected function buildManualSignatureTemplate(string $templateFile): string
    {
        if (!file_exists($templateFile)) {
            throw new \RuntimeException("Template tidak ditemukan: {$templateFile}");
        }

        if (strtolower(pathinfo($templateFile, PATHINFO_EXTENSION)) !== 'docx') {
            return $templateFile;
        }

        $tempDir = storage_path('app/manual_signature_templates');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0777, true);
        }

        $newFile = $tempDir . DIRECTORY_SEPARATOR . uniqid('manual_', true) . '.docx';
        copy($templateFile, $newFile);

        $zip = new \ZipArchive();
        if ($zip->open($newFile) === true) {
            $documentXml = $zip->getFromName('word/document.xml');
            if ($documentXml !== false) {
                // Bersihkan hanya area tanda tangan untuk TTD Basah.
                // Jangan hapus ${skpd_kepala}/${skpd_jabatan} secara global,
                // karena placeholder itu juga dipakai pada badan surat:
                // "Yang bertanda tangan dibawah ini".
                $hasCamatSignatureArea = str_contains($documentXml, '${skpd_jabatan_camat}')
                    || str_contains($documentXml, '${skpd_camat}')
                    || str_contains($documentXml, '${skpd_nip_camat}')
                    || str_contains($documentXml, '~camat~')
                    || str_contains($documentXml, '[[qr_camat]]')
                    || str_contains($documentXml, '${qr_camat}');

                $documentXml = str_replace([
                    '${qr}~',
                    '${qr}',
                    '[[qr_camat]]',
                    '${qr_camat}',
                    '~camat~',
                ], '', $documentXml);

                // Khusus TTD Basah: teks di bawah "Mengetahui," tidak boleh menampilkan
                // LURAH/Kelurahan. Jadi "Mengetahui, LURAH CAMPUREJO" menjadi hanya "Mengetahui,".
                // Ini hanya menghapus run tanda tangan bawah: "LURAH ${skpd_kel}".
                $documentXml = $this->removeTextRunAndOptionalFollowingComma($documentXml, 'LURAH ${skpd_kel}');

                // Hapus identitas tanda tangan bawah saja dengan mengambil kemunculan terakhir.
                // Kemunculan pertama ${skpd_kepala}/${skpd_jabatan} pada badan surat tetap dipertahankan.
                $documentXml = $this->replaceLastTextOccurrence($documentXml, 'NIP. ${skpd_nip_kepala}', '');
                $documentXml = $this->replaceLastTextOccurrence($documentXml, 'NIP.${skpd_nip_kepala}', '');
                $documentXml = $this->replaceLastTextOccurrence($documentXml, '${skpd_nip_kepala}', '');
                $documentXml = $this->replaceLastTextOccurrence($documentXml, '${skpd_kepala}', '');

                // SKTM/area Camat: setelah "Mengetahui," tidak boleh menyisakan CAMAT/MOJOROTO/NIP.
                // ${skpd_kec} di header tetap aman karena yang dihapus hanya kemunculan terakhir
                // dan hanya jika template memang punya area tanda tangan Camat.
                if ($hasCamatSignatureArea) {
                    $documentXml = $this->replaceLastTextOccurrence($documentXml, '${skpd_jabatan_camat}', '');
                    $documentXml = $this->replaceLastTextOccurrence($documentXml, '${skpd_kec}', '');
                    $documentXml = $this->replaceLastTextOccurrence($documentXml, 'NIP. ${skpd_nip_camat}', '');
                    $documentXml = $this->replaceLastTextOccurrence($documentXml, 'NIP.${skpd_nip_camat}', '');
                    $documentXml = $this->replaceLastTextOccurrence($documentXml, '${skpd_nip_camat}', '');
                    $documentXml = $this->replaceLastTextOccurrence($documentXml, '${skpd_camat}', '');
                }

                // Setelah placeholder NIP dikosongkan, beberapa template masih menyisakan teks label "NIP."
                // di area tanda tangan bawah. Khusus TTD Basah, label ini juga harus hilang.
                // Fungsi ini hanya dipakai untuk mode TTD Basah, sehingga proses TTE digital tetap aman.
                $documentXml = str_replace(['NIP. ', 'NIP.'], '', $documentXml);

                $zip->addFromString('word/document.xml', $documentXml);
            }
            $zip->close();
        }

        return $newFile;
    }

    protected function clearManualSignatureFlags(array $variable): array
    {
        unset(
            $variable['manual_signature'],
            $variable['manual_signature_previewed_at'],
            $variable['signature_mode']
        );

        return $variable;
    }


    public function proses($id)
    {
        // 1. Cari data di tabel tunggal (surat_pengajuans)
        $surat = $this->findSuratForCurrentUser($id);

        if ($surat) {
            // 2. Update status ke 1 (Proses)
            $variable = $this->clearManualSignatureFlags($this->decodeFlexibleValue($surat->variable));
            $variable['submitter_type'] = $variable['submitter_type'] ?? $this->resolveSubmitterType($surat);


            $surat->update([
                'status' => 1,
                'variable' => $variable,
            ]);

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
        $surat = $this->findSuratForCurrentUser($id);

        if (!$surat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        $currentStatus = (int) $surat->status;
        $variableNow = $this->decodeFlexibleValue($surat->variable);
        $isFromWarga = $this->resolveSubmitterType($surat) === 'warga';

        $nextStatus = match ($currentStatus) {
            2 => ($isFromWarga ? 0 : 1),
            3 => 2,
            11 => 4,
            8 => 11,
            default => null,
        };

        if ($nextStatus === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Status surat ini tidak bisa diturunkan.'
            ], 422);
        }

        $variable = $this->clearManualSignatureFlags($this->decodeFlexibleValue($surat->variable));
                    $variable['submitter_type'] = $variable['submitter_type'] ?? $this->resolveSubmitterType($surat);


        $surat->update([
            'status' => $nextStatus,
            'variable' => $variable,
        ]);

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
        $surat = $this->findSuratForCurrentUser($id);

        if (!$surat) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if (!in_array((int) $surat->status, [0, 1], true)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya surat warga atau draft yang belum diajukan yang bisa dihapus.'
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
            'message' => 'Surat berhasil dihapus.'
        ]);
    }
}
