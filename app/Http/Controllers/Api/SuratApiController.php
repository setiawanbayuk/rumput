<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\SuratPengajuan;
use App\Models\Resident;
use App\Models\Log_surat;
use App\Models\JenisSurat;
use Carbon\Carbon;

class SuratApiController extends Controller
{
    protected function masterJenisCollection()
    {
        $columns = ['id', 'nama'];
        foreach (['assets', 'detail', 'persyaratan', 'is_active'] as $optionalColumn) {
            if (Schema::hasColumn('jenis_surats', $optionalColumn)) {
                $columns[] = $optionalColumn;
            }
        }

        $query = JenisSurat::query()->orderBy('id');

        if (Schema::hasColumn('jenis_surats', 'is_active')) {
            $query->where('is_active', 1);
        }

        return $query->get($columns)->map(function ($item) {
            $nama = trim((string) ($item->nama ?? ''));
            $jenis = $this->deriveJenisFromNama($nama, (int) $item->id);

            return [
                'id' => (int) $item->id,
                'nama' => $nama,
                'name' => $nama,
                'jenis' => $jenis,
                'kode' => strtoupper($jenis),
                'label' => $nama,
                'assets' => $item->assets ?? null,
                'detail' => $item->detail ?? null,
                'persyaratan' => $item->persyaratan ?? null,
                'is_active' => property_exists($item, 'is_active') ? (int) ($item->is_active ?? 1) : 1,
            ];
        })->values();
    }

    protected function deriveJenisFromNama(?string $nama, ?int $id = null): string
    {
        $raw = strtolower(trim((string) $nama));
        $normalized = preg_replace('/[^a-z0-9]+/i', ' ', $raw);
        $normalized = trim((string) $normalized);

        $map = [
            'suket' => 'suket',
            'surat keterangan' => 'suket',
            'skbn' => 'skbn',
            'surat keterangan belum menikah' => 'skbn',
            'surat keterangan belum nikah' => 'skbn',
            'sktm' => 'sktm',
            'surat keterangan tidak mampu' => 'sktm',
            'surat keterangan miskin' => 'sktm',
            'skdom' => 'skdom',
            'surat keterangan domisili' => 'skdom',
            'skhsl' => 'skhsl',
            'surat keterangan penghasilan' => 'skhsl',
            'skusaha' => 'skusaha',
            'surat keterangan usaha' => 'skusaha',
            'skboro' => 'skboro',
            'surat keterangan boro' => 'skboro',
            'skkelahiran' => 'skkelahiran',
            'sk kelahiran' => 'skkelahiran',
            'surat keterangan kelahiran' => 'skkelahiran',
            'skkematian' => 'skkematian',
            'sk kematian' => 'skkematian',
            'surat keterangan kematian' => 'skkematian',
        ];

        if (isset($map[$normalized])) {
            return $map[$normalized];
        }

        if (isset($map[$raw])) {
            return $map[$raw];
        }

        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '', $raw)));
        return $slug !== '' ? $slug : ('jenis_' . (int) $id);
    }

    protected function resolveJenisInput(Request $request): ?string
    {
        $master = $this->masterJenisCollection();

        if ($request->filled('jenis_surat_id')) {
            $match = $master->firstWhere('id', (int) $request->jenis_surat_id);
            if ($match) {
                return $match['jenis'];
            }
        }

        if ($request->filled('jenis_surat')) {
            $input = strtolower(trim((string) $request->jenis_surat));

            $match = $master->first(function ($item) use ($input) {
                return $input === strtolower((string) $item['jenis'])
                    || $input === strtolower((string) $item['kode'])
                    || $input === strtolower((string) $item['nama'])
                    || $input === strtolower((string) $item['name']);
            });

            if ($match) {
                return $match['jenis'];
            }

            return $input;
        }

        return null;
    }

    public function jenisSurat()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Daftar jenis surat berhasil diambil.',
            'data' => $this->masterJenisCollection(),
        ]);
    }

    public function formConfig(Request $request)
    {
        $jenis = strtolower(trim((string) $request->query('jenis_surat', '')));

        $configs = [
            'suket' => [
                'jenis_surat' => 'suket',
                'title' => 'Surat Keterangan',
                'fields' => [
                    ['name' => 'nik', 'label' => 'NIK', 'type' => 'text', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Peruntukan', 'type' => 'text', 'required' => true],
                    ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'textarea', 'required' => true],
                    ['name' => 'kepada', 'label' => 'Kepada', 'type' => 'text', 'required' => true],
                    ['name' => 'pengantar', 'label' => 'Surat Pengantar', 'type' => 'file', 'required' => true],
                ],
            ],
            'skbn' => [
                'jenis_surat' => 'skbn',
                'title' => 'Surat Keterangan Belum Menikah',
                'fields' => [
                    ['name' => 'nik', 'label' => 'NIK', 'type' => 'text', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Peruntukan', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada', 'label' => 'Kepada', 'type' => 'text', 'required' => true],
                    ['name' => 'pengantar', 'label' => 'Surat Pengantar', 'type' => 'file', 'required' => true],
                ],
            ],
            'sktm' => [
                'jenis_surat' => 'sktm',
                'title' => 'Surat Keterangan Tidak Mampu',
                'fields' => [
                    ['name' => 'nik', 'label' => 'NIK', 'type' => 'text', 'required' => true],
                    ['name' => 'register_as', 'label' => 'Jenis SKTM', 'type' => 'radio', 'required' => true, 'options' => [
                        ['id' => 'perorangan', 'nama' => 'Perorangan'],
                        ['id' => 'sekolah', 'nama' => 'Sekolah'],
                    ]],
                    ['name' => 'kategori', 'label' => 'Kategori', 'type' => 'select', 'required' => true, 'options' => [
                        ['id' => 'DTKS', 'nama' => 'DTKS'],
                        ['id' => 'Non DTKS', 'nama' => 'Non DTKS'],
                    ]],
                    ['name' => 'keterangan', 'label' => 'Keterangan', 'type' => 'textarea', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Dipergunakan Untuk', 'type' => 'text', 'required' => true],
                    ['name' => 'keperluan_lainnya', 'label' => 'Keperluan Lainnya', 'type' => 'text', 'required' => false],
                    ['name' => 'kepada', 'label' => 'Nama Siswa', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'kepada_tempat_lhr', 'label' => 'Tempat Lahir Siswa', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'kepada_tgl_lhr', 'label' => 'Tanggal Lahir Siswa', 'type' => 'date', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'kepada_gender', 'label' => 'Jenis Kelamin Siswa', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'kepada_hubungan', 'label' => 'Hubungan', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'kepada_sekolah', 'label' => 'Sekolah', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'kepada_kelas', 'label' => 'Kelas', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'kepada_alamat_sekolah', 'label' => 'Alamat Sekolah', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'sekolah']],
                    ['name' => 'pengantar', 'label' => 'Surat Pengantar', 'type' => 'file', 'required' => true],
                ],
            ],
            'skdom' => [
                'jenis_surat' => 'skdom',
                'title' => 'Surat Keterangan Domisili',
                'fields' => [
                    ['name' => 'nik', 'label' => 'NIK', 'type' => 'text', 'required' => true],
                    ['name' => 'register_as', 'label' => 'Jenis Domisili', 'type' => 'radio', 'required' => true, 'options' => [
                        ['id' => 'perorangan', 'nama' => 'Perorangan'],
                        ['id' => 'perusahaan', 'nama' => 'Perusahaan'],
                    ]],
                    ['name' => 'kepada', 'label' => 'Kepada', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_domisili', 'label' => 'Alamat Domisili', 'type' => 'text', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Peruntukan', 'type' => 'text', 'required' => true],
                    ['name' => 'pengantar', 'label' => 'Surat Pengantar', 'type' => 'file', 'required' => true],
                    ['name' => 'nama_perusahaan', 'label' => 'Nama Perusahaan', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'perusahaan']],
                    ['name' => 'status_bangunan', 'label' => 'Status Bangunan', 'type' => 'text', 'required' => false, 'show_if' => ['register_as' => 'perusahaan']],
                    ['name' => 'jumlah_karyawan', 'label' => 'Jumlah Karyawan', 'type' => 'number', 'required' => false, 'show_if' => ['register_as' => 'perusahaan']],
                ],
            ],
            'skhsl' => [
                'jenis_surat' => 'skhsl',
                'title' => 'Surat Keterangan Penghasilan',
                'fields' => [
                    ['name' => 'nik', 'label' => 'NIK', 'type' => 'text', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Peruntukan', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada', 'label' => 'Kepada', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada_tempat_lhr', 'label' => 'Tempat Lahir', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada_tgl_lhr', 'label' => 'Tanggal Lahir', 'type' => 'date', 'required' => true],
                    ['name' => 'kepada_gender', 'label' => 'Gender', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada_hubungan', 'label' => 'Hubungan', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada_sekolah', 'label' => 'Sekolah', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada_kelas', 'label' => 'Kelas', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada_alamat_sekolah', 'label' => 'Alamat Sekolah', 'type' => 'text', 'required' => true],
                    ['name' => 'penghasilan', 'label' => 'Penghasilan', 'type' => 'number', 'required' => true],
                    ['name' => 'terbilang', 'label' => 'Terbilang', 'type' => 'text', 'required' => true],
                    ['name' => 'pengantar', 'label' => 'Surat Pengantar', 'type' => 'file', 'required' => true],
                ],
            ],
            'skusaha' => [
                'jenis_surat' => 'skusaha',
                'title' => 'Surat Keterangan Usaha',
                'fields' => [
                    ['name' => 'nik', 'label' => 'NIK', 'type' => 'text', 'required' => true],
                    ['name' => 'register_as', 'label' => 'Jenis Usaha', 'type' => 'radio', 'required' => true, 'options' => [
                        ['id' => 'luar', 'nama' => 'Luar'],
                        ['id' => 'kelurahan', 'nama' => 'Kelurahan'],
                    ]],
                    ['name' => 'nama_usaha', 'label' => 'Nama Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_usaha', 'label' => 'Alamat Usaha', 'type' => 'text', 'required' => true],
                    ['name' => 'kepada', 'label' => 'Kepada', 'type' => 'text', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Peruntukan', 'type' => 'text', 'required' => true],
                    ['name' => 'pengantar', 'label' => 'Surat Pengantar', 'type' => 'file', 'required' => true],
                ],
            ],
            'skboro' => [
                'jenis_surat' => 'skboro',
                'title' => 'Surat Keterangan Boro',
                'fields' => [
                    ['name' => 'nik', 'label' => 'NIK', 'type' => 'text', 'required' => true],
                    ['name' => 'provinsi_boro', 'label' => 'Provinsi Boro', 'type' => 'text', 'required' => true],
                    ['name' => 'kabko_boro', 'label' => 'Kabupaten/Kota Boro', 'type' => 'text', 'required' => true],
                    ['name' => 'kecamatan_boro', 'label' => 'Kecamatan Boro', 'type' => 'text', 'required' => true],
                    ['name' => 'kelurahan_boro', 'label' => 'Kelurahan Boro', 'type' => 'text', 'required' => true],
                    ['name' => 'alamat_boro', 'label' => 'Alamat Boro', 'type' => 'text', 'required' => true],
                    ['name' => 'tgl_awal', 'label' => 'Tanggal Awal', 'type' => 'date', 'required' => true],
                    ['name' => 'tgl_akhir', 'label' => 'Tanggal Akhir', 'type' => 'date', 'required' => true],
                    ['name' => 'peruntukan', 'label' => 'Peruntukan', 'type' => 'text', 'required' => true],
                    ['name' => 'pengantar', 'label' => 'Surat Pengantar', 'type' => 'file', 'required' => true],
                ],
            ],
        ];

        if (!isset($configs[$jenis])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis surat tidak dikenali.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Konfigurasi form berhasil diambil.',
            'data' => $configs[$jenis],
        ]);
    }

    protected function mapKodeJenisSurat(string $jenis): string
    {
        $mapKodeJenis = [
            'skbn' => 'SKBN',
            'sktm' => 'SKTM',
            'skdom' => 'SKDOM',
            'skusaha' => 'SKUSAHA',
            'skhsl' => 'SKHSL',
            'skboro' => 'SKBORO',
            'skkelahiran' => 'SKKELAHIRAN',
            'skkematian' => 'SKKEMATIAN',
            'suket' => 'SUKET',
        ];

        return $mapKodeJenis[$jenis] ?? strtoupper($jenis);
    }

    protected function getPengajuanIdFromRequest(Request $request, ?int $routeId = null): ?int
    {
        if ($routeId) {
            return (int) $routeId;
        }

        foreach (['pengajuan_id', 'id_pengajuan', 'surat_pengajuan_id', 'id_surat', 'surat_id'] as $key) {
            if ($request->filled($key)) {
                return (int) $request->input($key);
            }
        }

        return null;
    }

    protected function getExistingVariableData(?SuratPengajuan $surat): array
    {
        if (!$surat) {
            return [];
        }

        $variable = $surat->variable ?? [];

        if (is_array($variable)) {
            return $variable;
        }

        if (is_string($variable) && trim($variable) !== '') {
            $decoded = json_decode($variable, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    protected function rejectedColumnNames(): array
    {
        return [
            'alasan_penolakan',
            'alasan_tolak',
            'alasan',
            'keterangan_penolakan',
            'catatan_penolakan',
            'ditolak_oleh',
            'ditolak_by',
            'rejected_by',
            'tanggal_ditolak',
            'tgl_ditolak',
            'rejected_at',
            'status_tolak',
        ];
    }

    protected function hasRejectionMarker(SuratPengajuan $surat): bool
			{
				// 1. Cek kolom-kolom penolakan jika memang ada di surat_pengajuans
				foreach ($this->rejectedColumnNames() as $column) {
					if (Schema::hasColumn('surat_pengajuans', $column)) {
						$value = $surat->{$column} ?? null;
			
						if ($value !== null && trim((string) $value) !== '') {
							return true;
						}
					}
				}
			
				// 2. Cek status langsung dari kolom status
				$status = strtolower(trim((string) ($surat->status ?? '')));
			
				$rejectedStatusValues = [
					'ditolak',
					'tolak',
					'rejected',
					'reject',
					'pengajuan ditolak',
					'ditolak admin',
					'dikembalikan',
					'revisi',
					'perbaikan',
					'99',
					'-1',
					'98',
					'97',
				];
			
				if (in_array($status, $rejectedStatusValues, true)) {
					return true;
				}
			
				// 3. Cek nama status dari relasi status, jika model punya relasi st
				try {
					$statusName = '';
			
					if (isset($surat->st)) {
						if (is_array($surat->st)) {
							$statusName = strtolower(trim((string) ($surat->st['name'] ?? $surat->st['nama'] ?? '')));
						} else {
							$statusName = strtolower(trim((string) ($surat->st->name ?? $surat->st->nama ?? '')));
						}
					}
			
					if (
						$statusName !== '' &&
						(
							str_contains($statusName, 'tolak') ||
							str_contains($statusName, 'reject') ||
							str_contains($statusName, 'dikembalikan') ||
							str_contains($statusName, 'revisi') ||
							str_contains($statusName, 'perbaikan')
						)
					) {
						return true;
					}
				} catch (\Throwable $e) {
					// Relasi status tidak wajib ada.
				}
			
				// 4. Cek variable JSON, karena beberapa sistem menyimpan alasan/status penolakan di variable
				$variable = $this->getExistingVariableData($surat);
			
				foreach ([
					'alasan',
					'alasan_penolakan',
					'alasan_tolak',
					'keterangan_penolakan',
					'catatan_penolakan',
					'status_penolakan',
					'status_tolak',
				] as $key) {
					if (isset($variable[$key]) && trim((string) $variable[$key]) !== '') {
						return true;
					}
				}
			
				if (isset($variable['status'])) {
					$variableStatus = strtolower(trim((string) $variable['status']));
			
					if (
						str_contains($variableStatus, 'tolak') ||
						str_contains($variableStatus, 'reject') ||
						str_contains($variableStatus, 'dikembalikan') ||
						str_contains($variableStatus, 'revisi')
					) {
						return true;
					}
				}
			
				return false;
			}

    protected function cleanInternalPayload(array $input): array
    {
        $mainColumns = [
            'id',
            'pengajuan_id',
            'id_pengajuan',
            'surat_pengajuan_id',
            'id_surat',
            'surat_id',
            'jenis_surat',
            'jenis_surat_id',
            'nik',
            'peruntukan',
            'kepada',
            'id_kel',
            'id_rw',
            'id_rt',
            'tahun',
            'tgl_surat',
            'pengantar',
            'token',
            '_method',
            'status',
            'status_label',
            'alasan',
            'alasan_penolakan',
            'alasan_tolak',
            'keterangan_penolakan',
            'catatan_penolakan',
            'ditolak_oleh',
            'ditolak_by',
            'rejected_by',
            'tanggal_ditolak',
            'tgl_ditolak',
            'rejected_at',
            'status_tolak',
        ];

        return array_diff_key($input, array_flip($mainColumns));
    }

    protected function buildAllInput(Request $request, Resident $resident, string $resolvedJenis): array
    {
        $allInput = $request->all();
        $allInput['submitter_type'] = 'warga';

        if ($resolvedJenis === 'sktm') {
            $registerAs = strtolower(trim((string) $request->register_as));
            $allInput['register_as'] = $registerAs;

            if ($registerAs !== 'sekolah') {
                $allInput['kepada'] = strtoupper($resident->name ?? $request->name ?? '');
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

        return $allInput;
    }


    protected function normalizeBoroApiText($value): string
    {
        return strtoupper(trim((string) $value));
    }

    protected function normalizeBoroApiRelation($value): string
    {
        $value = $this->normalizeBoroApiText($value);

        if ($value === 'ANAK KANDUNG') {
            return 'ANAK';
        }

        if ($value === 'KELUARGA') {
            return 'KELUARGA LAIN';
        }

        return $value;
    }

    protected function buildBoroApiDetailPengikut(Request $request): array
    {
        $names = $request->input('pengikut_nama', []);
        $niks = $request->input('pengikut_nik', []);
        $birthDates = $request->input('pengikut_tgl_lahir', []);
        $statuses = $request->input('pengikut_status_kwn', []);
        $relations = $request->input('pengikut_hubungan', []);

        $names = is_array($names) ? array_values($names) : [];
        $niks = is_array($niks) ? array_values($niks) : [];
        $birthDates = is_array($birthDates) ? array_values($birthDates) : [];
        $statuses = is_array($statuses) ? array_values($statuses) : [];
        $relations = is_array($relations) ? array_values($relations) : [];

        $jumlahPengikut = (int) $request->input('jumlah_pengikut', $request->input('pengikut', 0));
        $rowCount = max($jumlahPengikut, count($names), count($niks), count($birthDates), count($statuses), count($relations));

        $detail = [];

        for ($i = 0; $i < $rowCount; $i++) {
            $nama = $this->normalizeBoroApiText($names[$i] ?? '');
            $nik = preg_replace('/\D/', '', (string) ($niks[$i] ?? ''));
            $tglLahir = trim((string) ($birthDates[$i] ?? ''));
            $statusKawin = $this->normalizeBoroApiText($statuses[$i] ?? '');
            $hubungan = $this->normalizeBoroApiRelation($relations[$i] ?? '');

            if ($nama === '' && $nik === '' && $tglLahir === '' && $statusKawin === '' && $hubungan === '') {
                continue;
            }

            $usia = null;
            if ($tglLahir !== '') {
                try {
                    $birth = Carbon::parse($tglLahir)->startOfDay();
                    if (!$birth->isFuture()) {
                        $tglLahir = $birth->format('Y-m-d');
                        $usia = $birth->age;
                    }
                } catch (\Throwable $e) {
                    $usia = null;
                }
            }

            $detail[] = [
                'nama' => $nama,
                'nik' => $nik,
                'tgl_lahir' => $tglLahir,
                'umur' => $usia,
                'usia' => $usia,
                'status_kwn' => $statusKawin,
                'status_kwn_nm' => $statusKawin,
                'hubungan' => $hubungan,
            ];
        }

        return $detail;
    }

    protected function applyBoroApiDetailPengikut(Request $request, array $variableData): array
    {
        $detailPengikut = $this->buildBoroApiDetailPengikut($request);
        $jumlahPengikut = (int) $request->input('jumlah_pengikut', $request->input('pengikut', count($detailPengikut)));
        $jumlahPengikut = max($jumlahPengikut, count($detailPengikut));

        $variableData['jumlah_pengikut'] = $jumlahPengikut;
        $variableData['pengikut'] = $jumlahPengikut;
        $variableData['surat_jml_pengikut'] = (string) $jumlahPengikut;
        $variableData['detail_pengikut'] = $detailPengikut;

        return $variableData;
    }

    protected function storePengantarFile(Request $request, string $resolvedJenis): ?string
    {
        if (!$request->hasFile('pengantar')) {
            return null;
        }

        $file = $request->file('pengantar');
        $year = date('Y');
        $savePath = "public/pengantar/{$year}/{$resolvedJenis}";
        $fileName = $file->hashName();

        $file->storeAs($savePath, $fileName);

        return "/storage/pengantar/{$year}/{$resolvedJenis}/{$fileName}";
    }

    protected function responseData(SuratPengajuan $surat, string $resolvedJenis, ?array $master = null): array
	{
		$master = $master ?: $this->masterJenisCollection()->firstWhere('jenis', $resolvedJenis);
	
		$formatTanggal = function ($value) {
			if (!$value) {
				return null;
			}
	
			return Carbon::parse($value)->timezone('Asia/Jakarta')->format('Y-m-d');
		};
	
		$formatDateTime = function ($value) {
			if (!$value) {
				return null;
			}
	
			return Carbon::parse($value)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
		};
	
		$pdfInfo = $this->resolvePdfInfo($surat);
		$ratingInfo = $this->ratingResponseFields($surat);

		$data = [
			'id' => $surat->id,
			'pengajuan_id' => $surat->id,
			'jenis_surat' => $surat->jenis_surat,
			'jenis_surat_id' => $master['id'] ?? null,
			'jenis_surat_label' => $master['nama'] ?? strtoupper((string) $surat->jenis_surat),
			'jenis_surat_kode' => $master['kode'] ?? strtoupper((string) $surat->jenis_surat),
			'no_urut_surat' => $surat->no_urut_surat,
			'nik' => $surat->nik,
			'peruntukan' => $surat->peruntukan,
			'kepada' => $surat->kepada,
			'status' => $surat->status,
			'status_label' => $surat->status == 0 ? 'Warga' : ($surat->st['name'] ?? null),
			'pdf_url' => $pdfInfo['url'] ?? null,
			'download_pdf_url' => $pdfInfo ? url('/api/surat/' . $surat->id . '/download-pdf') : null,
			'pengantar' => $surat->pengantar,
			'variable' => $this->getExistingVariableData($surat),
			'rating' => $ratingInfo['rating'],
			'bintang' => $ratingInfo['bintang'],
			'komentar' => $ratingInfo['komentar'],
			'rating_at' => $ratingInfo['rating_at'],
			'sudah_rating' => $ratingInfo['sudah_rating'],
	
			// FIX UTAMA: jangan kirim Carbon mentah ke JSON
			'tgl_surat' => $formatTanggal($surat->tgl_surat),
			'created_at' => $formatDateTime($surat->created_at),
			'updated_at' => $formatDateTime($surat->updated_at),
		];
	
		foreach (['alasan_penolakan', 'alasan_tolak', 'alasan', 'keterangan_penolakan', 'catatan_penolakan', 'tanggal_ditolak', 'tgl_ditolak', 'rejected_at'] as $column) {
			if (Schema::hasColumn('surat_pengajuans', $column)) {
				if (in_array($column, ['tanggal_ditolak', 'tgl_ditolak'], true)) {
					$data[$column] = $formatTanggal($surat->{$column});
				} elseif ($column === 'rejected_at') {
					$data[$column] = $formatDateTime($surat->{$column});
				} else {
					$data[$column] = $surat->{$column};
				}
			}
		}
	
		return $data;
	}
	

    public function store(Request $request)
    {
        $pengajuanId = $this->getPengajuanIdFromRequest($request);

        if ($pengajuanId) {
            return $this->revisiDitolak($request, $pengajuanId);

        }

        $allowedJenis = $this->masterJenisCollection()->pluck('jenis')->filter()->values()->all();
        $resolvedJenis = $this->resolveJenisInput($request);

        $request->merge([
            'jenis_surat' => $resolvedJenis,
        ]);

        $request->validate([
            'jenis_surat' => ['required', 'string', Rule::in($allowedJenis)],
            'nik' => ['required', 'digits:16'],
            'peruntukan' => ['required', 'string', 'max:255'],
            'pengantar' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $fileUrl = null;

        try {
            return DB::transaction(function () use ($request, $resolvedJenis, &$fileUrl) {
                $user = auth()->user();
                $resident = Resident::where('nik', $request->nik)->first();

                if (!$resident) {
                    throw new \Exception('NIK tidak terdaftar dalam pangkalan data penduduk.');
                }

                $fileUrl = $this->storePengantarFile($request, $resolvedJenis);

                $allInput = $this->buildAllInput($request, $resident, $resolvedJenis);
                $variableData = $this->cleanInternalPayload($allInput);

                if ($resolvedJenis === 'skboro') {
                    $variableData = $this->applyBoroApiDetailPengikut($request, $variableData);
                }

                $tglSurat = $request->filled('tgl_surat') ? Carbon::parse($request->tgl_surat) : now();

                $noUrutSurat = ((int) SuratPengajuan::where('jenis_surat', $resolvedJenis)
                    ->where('id_kel', $user->id_instansi)
                    ->whereYear('tgl_surat', $tglSurat->format('Y'))
                    ->max('no_urut_surat')) + 1;

                $kepadaValue = $request->kepada;
                if ($resolvedJenis === 'sktm' && strtolower((string) $request->register_as) !== 'sekolah') {
                    $kepadaValue = strtoupper($resident->name ?? $request->name ?? '');
                }

                $surat = SuratPengajuan::create([
                    'jenis_surat' => $resolvedJenis,
                    'kd_jenis_surat' => $this->mapKodeJenisSurat($resolvedJenis),
                    'no_urut_surat' => $noUrutSurat,
                    'nik' => $request->nik,
                    'id_kel' => $user->id_instansi,
                    'id_rw' => $user->id_rw,
                    'id_rt' => $user->id_rt,
                    'tahun' => $tglSurat->format('Y'),
                    'tgl_surat' => $tglSurat,
                    'peruntukan' => $request->peruntukan,
                    'kepada' => $kepadaValue,
                    'status' => 0,
                    'pengantar' => $fileUrl,
                    'variable' => $variableData,
                ]);

                Log_surat::create([
                    'nik' => $request->nik,
                    'tabel_surat' => 'surat_pengajuans',
                    'nama_surat' => strtoupper($resolvedJenis),
                    'id_surat' => $surat->id,
                    'status_surat' => 0,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Pengajuan surat berhasil dikirim!',
                    'data' => $this->responseData($surat->fresh(), $resolvedJenis),
                ], 201);
            });
        } catch (\Exception $e) {
            if ($fileUrl) {
                Storage::delete(str_replace('/storage/', 'public/', $fileUrl));
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function revisiDitolak(Request $request, int $id)
    {
        $allowedJenis = $this->masterJenisCollection()->pluck('jenis')->filter()->values()->all();
        $resolvedJenis = $this->resolveJenisInput($request);

        $surat = SuratPengajuan::find($id);

        if (!$surat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan surat tidak ditemukan.',
            ], 404);
        }

        $resolvedJenis = $resolvedJenis ?: strtolower((string) $surat->jenis_surat);

        $request->merge([
            'jenis_surat' => $resolvedJenis,
            'nik' => $request->input('nik', $surat->nik),
        ]);

        $request->validate([
            'jenis_surat' => ['required', 'string', Rule::in($allowedJenis)],

            'nik' => ['required', 'digits:16'],
            'peruntukan' => ['required', 'string', 'max:255'],
            'pengantar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ((string) $surat->nik !== (string) $request->nik) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK revisi tidak sesuai dengan NIK pengajuan yang ditolak.',
            ], 422);
        }

        if (strtolower((string) $surat->jenis_surat) !== strtolower((string) $resolvedJenis)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis surat revisi tidak sesuai dengan pengajuan yang ditolak.',
            ], 422);
        }

        if (!$this->hasRejectionMarker($surat)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan ini tidak sedang dalam status ditolak, sehingga tidak dapat direvisi dari mobile.',
            ], 422);
        }

        $fileUrl = null;

        try {
            return DB::transaction(function () use ($request, $surat, $resolvedJenis, &$fileUrl) {
                $user = auth()->user();
                $resident = Resident::where('nik', $request->nik)->first();

                if (!$resident) {
                    throw new \Exception('NIK tidak terdaftar dalam pangkalan data penduduk.');
                }

                $fileUrl = $this->storePengantarFile($request, $resolvedJenis);

                $allInput = $this->buildAllInput($request, $resident, $resolvedJenis);
                $newVariableData = $this->cleanInternalPayload($allInput);
                $oldVariableData = $this->getExistingVariableData($surat);
                $variableData = array_merge($oldVariableData, $newVariableData);

                if ($resolvedJenis === 'skboro') {
                    $variableData = $this->applyBoroApiDetailPengikut($request, $variableData);
                }

                $kepadaValue = $request->input('kepada', $surat->kepada);
                if ($resolvedJenis === 'sktm' && strtolower((string) $request->register_as) !== 'sekolah') {
                    $kepadaValue = strtoupper($resident->name ?? $request->name ?? '');
                }

                $updateData = [
                    'peruntukan' => $request->peruntukan,
                    'kepada' => $kepadaValue,
                    'status' => 0,
                    'variable' => $variableData,
                ];

                if ($fileUrl) {
                    $updateData['pengantar'] = $fileUrl;
                }

                if (Schema::hasColumn('surat_pengajuans', 'kd_jenis_surat') && empty($surat->kd_jenis_surat)) {
                    $updateData['kd_jenis_surat'] = $this->mapKodeJenisSurat($resolvedJenis);
                }

                foreach (['alasan_penolakan', 'alasan_tolak', 'alasan', 'keterangan_penolakan', 'catatan_penolakan', 'ditolak_oleh', 'ditolak_by', 'rejected_by', 'tanggal_ditolak', 'tgl_ditolak', 'rejected_at', 'status_tolak'] as $column) {
                    if (Schema::hasColumn('surat_pengajuans', $column)) {
                        $updateData[$column] = null;
                    }
                }

                if (Schema::hasColumn('surat_pengajuans', 'is_revisi')) {
                    $updateData['is_revisi'] = 1;
                }

                if (Schema::hasColumn('surat_pengajuans', 'revisi_ke')) {
                    $updateData['revisi_ke'] = ((int) ($surat->revisi_ke ?? 0)) + 1;
                }

                $surat->update($updateData);

                Log_surat::create([
                    'nik' => $request->nik,
                    'tabel_surat' => 'surat_pengajuans',
                    'nama_surat' => strtoupper($resolvedJenis),
                    'id_surat' => $surat->id,
                    'status_surat' => 0,
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Revisi pengajuan berhasil dikirim ulang. Status penolakan sudah dibersihkan dan pengajuan kembali masuk ke admin.',
                    'data' => $this->responseData($surat->fresh(), $resolvedJenis),
                ], 200);
            });
        } catch (\Exception $e) {
            if ($fileUrl) {
                Storage::delete(str_replace('/storage/', 'public/', $fileUrl));
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui revisi: ' . $e->getMessage(),
            ], 500);
        }
    }


    protected function ratingResponseFields(SuratPengajuan $surat): array
    {
        $variable = $this->getExistingVariableData($surat);

        $rating = null;
        foreach (['rating', 'bintang'] as $column) {
            if (Schema::hasColumn('surat_pengajuans', $column)) {
                $value = $surat->{$column} ?? null;
                if ($value !== null && $value !== '' && is_numeric($value)) {
                    $rating = round((float) $value, 1);
                    break;
                }
            }
        }

        if ($rating === null) {
            foreach (['rating', 'bintang'] as $key) {
                $value = $variable[$key] ?? null;
                if ($value !== null && $value !== '' && is_numeric($value)) {
                    $rating = round((float) $value, 1);
                    break;
                }
            }
        }

        $komentar = null;
        foreach (['komentar', 'comment', 'coment'] as $column) {
            if (Schema::hasColumn('surat_pengajuans', $column)) {
                $value = $surat->{$column} ?? null;
                if ($value !== null && trim((string) $value) !== '') {
                    $komentar = (string) $value;
                    break;
                }
            }
        }

        if ($komentar === null) {
            foreach (['komentar', 'comment', 'coment'] as $key) {
                if (isset($variable[$key]) && trim((string) $variable[$key]) !== '') {
                    $komentar = (string) $variable[$key];
                    break;
                }
            }
        }

        $ratingAt = null;
        foreach (['rating_at', 'rated_at', 'tgl_rating'] as $column) {
            if (Schema::hasColumn('surat_pengajuans', $column)) {
                $value = $surat->{$column} ?? null;
                if ($value) {
                    $ratingAt = Carbon::parse($value)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
                    break;
                }
            }
        }

        if ($ratingAt === null) {
            foreach (['rating_at', 'rated_at', 'tgl_rating'] as $key) {
                if (!empty($variable[$key])) {
                    try {
                        $ratingAt = Carbon::parse($variable[$key])->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');
                    } catch (\Throwable $e) {
                        $ratingAt = (string) $variable[$key];
                    }
                    break;
                }
            }
        }

        return [
            'rating' => $rating,
            'bintang' => $rating,
            'komentar' => $komentar,
            'rating_at' => $ratingAt,
            'sudah_rating' => $rating !== null,
        ];
    }

    public function rating(Request $request, $id)
    {
        $identifier = trim((string) $id);

        if (preg_match('/^\d{16}$/', $identifier)) {
            $surat = SuratPengajuan::query()
                ->where('nik', $identifier)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();
        } else {
            $surat = SuratPengajuan::find($identifier);
        }

        if (!$surat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan surat tidak ditemukan untuk ID/NIK tersebut.',
            ], 404);
        }

        if ($request->filled('nik') && (string) $request->nik !== (string) $surat->nik) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK tidak sesuai dengan data pengajuan surat.',
            ], 422);
        }

        $ratingFields = $this->ratingResponseFields($surat);

        return response()->json([
            'status' => 'success',
            'message' => $ratingFields['sudah_rating'] ? 'Data penilaian ditemukan.' : 'Surat ini belum memiliki penilaian.',
            'data' => [
                'id' => $surat->id,
                'pengajuan_id' => $surat->id,
                'nik' => $surat->nik,
                'jenis_surat' => $surat->jenis_surat,
                'rating' => $ratingFields['rating'],
                'bintang' => $ratingFields['bintang'],
                'komentar' => $ratingFields['komentar'],
                'rating_at' => $ratingFields['rating_at'],
                'sudah_rating' => $ratingFields['sudah_rating'],
            ],
        ]);
    }

    public function simpanRating(Request $request, $id)
    {
        $request->validate([
            'nik' => ['nullable', 'digits:16'],
            'rating' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'bintang' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'coment' => ['nullable', 'string', 'max:1000'],
        ]);

        $identifier = trim((string) $id);

        if (preg_match('/^\d{16}$/', $identifier)) {
            $surat = SuratPengajuan::query()
                ->where('nik', $identifier)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();
        } else {
            $surat = SuratPengajuan::find($identifier);
        }

        if (!$surat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan surat tidak ditemukan untuk ID/NIK tersebut.',
            ], 404);
        }

        if ($request->filled('nik') && (string) $request->nik !== (string) $surat->nik) {
            return response()->json([
                'status' => 'error',
                'message' => 'NIK tidak sesuai dengan data pengajuan surat.',
            ], 422);
        }

        $rawRating = $request->input('rating', $request->input('bintang'));
        if ($rawRating === null || $rawRating === '' || !is_numeric($rawRating)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rating atau bintang wajib diisi dengan angka 1 sampai 5.',
            ], 422);
        }

        $rating = round((float) $rawRating, 1);
        if ($rating < 1 || $rating > 5) {
            return response()->json([
                'status' => 'error',
                'message' => 'Rating hanya boleh bernilai 1 sampai 5.',
            ], 422);
        }

        $komentar = $request->input('komentar', $request->input('comment', $request->input('coment')));
        $komentar = $komentar === null ? null : trim((string) $komentar);
        $now = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

        $variableData = $this->getExistingVariableData($surat);
        $variableData['rating'] = $rating;
        $variableData['bintang'] = $rating;
        $variableData['komentar'] = $komentar;
        $variableData['rating_at'] = $now;

        $updateData = [];

        if (Schema::hasColumn('surat_pengajuans', 'variable')) {
            $updateData['variable'] = $variableData;
        }

        if (Schema::hasColumn('surat_pengajuans', 'rating')) {
            $updateData['rating'] = $rating;
        }

        if (Schema::hasColumn('surat_pengajuans', 'bintang')) {
            $updateData['bintang'] = $rating;
        }

        foreach (['komentar', 'comment', 'coment'] as $column) {
            if (Schema::hasColumn('surat_pengajuans', $column)) {
                $updateData[$column] = $komentar;
            }
        }

        foreach (['rating_at', 'rated_at', 'tgl_rating'] as $column) {
            if (Schema::hasColumn('surat_pengajuans', $column)) {
                $updateData[$column] = $now;
            }
        }

        if (empty($updateData)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kolom penyimpanan rating belum tersedia di tabel surat_pengajuans.',
            ], 500);
        }

        try {
            DB::transaction(function () use ($surat, $updateData) {
                // Rating tidak mengubah alur/status surat, sehingga updated_at sengaja tidak disentuh.
                $surat->timestamps = false;
                $surat->forceFill($updateData)->save();
            });
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan rating: ' . $e->getMessage(),
            ], 500);
        }

        $freshSurat = $surat->fresh();
        $ratingFields = $this->ratingResponseFields($freshSurat);

        return response()->json([
            'status' => 'success',
            'message' => 'Penilaian berhasil disimpan.',
            'data' => [
                'id' => $freshSurat->id,
                'pengajuan_id' => $freshSurat->id,
                'nik' => $freshSurat->nik,
                'jenis_surat' => $freshSurat->jenis_surat,
                'rating' => $ratingFields['rating'],
                'bintang' => $ratingFields['bintang'],
                'komentar' => $ratingFields['komentar'],
                'rating_at' => $ratingFields['rating_at'],
                'sudah_rating' => $ratingFields['sudah_rating'],
            ],
        ]);
    }

    public function detail(Request $request, int $id)
    {
        $surat = SuratPengajuan::find($id);

        if (!$surat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan surat tidak ditemukan.',
            ], 404);
        }

        $jenisMap = $this->masterJenisCollection()->keyBy(function ($item) {
            return strtolower(trim((string) $item['jenis']));
        });

        $jenisKey = strtolower(trim((string) $surat->jenis_surat));
        $jenisMaster = $jenisMap->get($jenisKey);

        $data = $this->responseData($surat, $jenisKey, $jenisMaster);
        $data['tracking'] = $this->buildTrackingPayload($surat);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail surat berhasil diambil.',
            'data' => $data,
        ]);
    }

    public function history(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'digits:16'],
            'jenis_surat' => ['nullable', 'string'],
            'jenis_surat_id' => ['nullable', 'integer'],
        ]);

        $query = SuratPengajuan::query()->where('nik', $request->nik);

        $jenisFilter = $this->resolveJenisInput($request);
        if ($jenisFilter) {
            $query->where('jenis_surat', $jenisFilter);
        }

        $jenisMap = $this->masterJenisCollection()->keyBy(function ($item) {
            return strtolower(trim((string) $item['jenis']));
        });

        $data = $query->orderByDesc('id')->get()->map(function ($item) use ($jenisMap) {
            $jenisKey = strtolower(trim((string) $item->jenis_surat));
            $jenisMaster = $jenisMap->get($jenisKey);

            $payload = $this->responseData($item, $jenisKey, $jenisMaster);
            $payload['tracking'] = $this->buildTrackingPayload($item);

            return $payload;
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat surat berhasil diambil.',
            'data' => $data,
        ]);
    }



    /**
     * API realtime tracking surat untuk Super APP / Postman.
     *
     * Endpoint ini sengaja tidak mengubah alur lama. Timeline dibaca dari log_surats
     * yang sudah dibuat setiap kali status surat berubah di backend ESUKET.
     *
     * Ringkasan alur:
     * - Semua surat selain SKTM: Pengajuan -> Diproses -> Selesai
     * - SKTM: Pengajuan -> Diproses -> Lurah -> Sekcam -> Selesai
     */
    public function tracking(Request $request, $id)
    {
        $identifier = trim((string) $id);

        // Endpoint ini mendukung 2 cara panggil:
        // 1. /api/surat/{pengajuan_id}/tracking  -> ambil tracking 1 surat berdasarkan ID pengajuan.
        // 2. /api/surat/{nik}/tracking           -> ambil tracking surat terbaru milik NIK tersebut.
        if (preg_match('/^\d{16}$/', $identifier)) {
            $surat = SuratPengajuan::query()
                ->where('nik', $identifier)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->first();
        } else {
            $surat = SuratPengajuan::find($identifier);
        }

        if (!$surat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pengajuan surat tidak ditemukan untuk ID/NIK tersebut.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Tracking realtime surat berhasil diambil.',
            'data' => $this->buildTrackingPayload($surat),
        ]);
    }

    protected function buildTrackingPayload(SuratPengajuan $surat): array
    {
        $isSktm = strtolower(trim((string) $surat->jenis_surat)) === 'sktm';
        $logs = $this->trackingLogs($surat);
        $currentStatus = (int) ($surat->status ?? 0);
        $currentTracking = $this->resolveCurrentTrackingLabel($currentStatus, $isSktm);

        $alur = $isSktm
            ? ['Pengajuan', 'Diproses', 'Lurah', 'Sekcam', 'Selesai']
            : ['Pengajuan', 'Diproses', 'Selesai'];

        $timeline = $isSktm
            ? $this->buildSktmTimeline($surat, $logs)
            : $this->buildNonSktmTimeline($surat, $logs);

        $currentIndex = array_search($currentTracking, $alur, true);
        $progressPercent = $currentTracking === 'Ditolak'
            ? 100
            : ($currentIndex === false || count($alur) <= 1
                ? 0
                : (int) round(($currentIndex / (count($alur) - 1)) * 100));

        // Response dibuat ringkas untuk Postman/Super APP.
        // Detail teknis seperti raw_logs, transisi_waktu, keterangan, dan duplikasi timeline tidak dikirim.
        return [
            'pengajuan_id' => (int) $surat->id,
            'nik' => (string) $surat->nik,
            'jenis_surat' => strtolower((string) $surat->jenis_surat),
            'is_sktm' => $isSktm,
            'status_tracking' => $currentTracking,
            'progress_percent' => $progressPercent,
            'alur' => $timeline,
        ];
    }

    protected function trackingLogs(SuratPengajuan $surat)
    {
        $createdAt = $this->toJakartaDateTime($surat->created_at);

        $logs = Log_surat::query()
            ->where('tabel_surat', 'surat_pengajuans')
            ->where('id_surat', $surat->id)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get()
            ->filter(function ($log) use ($createdAt) {
                if (!$createdAt || !$log->created_at) {
                    return true;
                }

                // Pengaman agar log lama dari surat lain tidak ikut terbaca dan membuat durasi minus.
                return $this->toJakartaDateTime($log->created_at)->greaterThanOrEqualTo($createdAt->copy()->subMinute());
            })
            ->values();

        // Pengaman untuk data yang belum punya log awal.
        if ($logs->isEmpty()) {
            $fallback = new Log_surat([
                'nik' => $surat->nik,
                'tabel_surat' => 'surat_pengajuans',
                'nama_surat' => strtoupper((string) $surat->jenis_surat),
                'id_surat' => $surat->id,
                'status_surat' => (int) ($surat->status ?? 0),
            ]);
            $fallback->id = 0;
            $fallback->created_at = $surat->created_at ?: now();
            $fallback->updated_at = $fallback->created_at;

            return collect([$fallback]);
        }

        return $logs;
    }

    protected function buildNonSktmTimeline(SuratPengajuan $surat, $logs): array
    {
        $currentStatus = (int) ($surat->status ?? 0);
        $cycleLogs = $this->currentTrackingCycleLogs($surat, $logs);
        $isRejected = $currentStatus === 6;
        $currentPhase = $this->trackingPhaseForStatus($currentStatus, false);
        $activePhase = $isRejected ? $this->lastNonRejectedPhase($cycleLogs, false) : $currentPhase;

        $startPengajuan = $this->cyclePengajuanStart($surat, $cycleLogs);
        $startDitolak = $isRejected ? $this->timeFromLog($this->lastLogWithStatus($cycleLogs, [6])) : null;

        // Non-SKTM diringkas untuk Super APP/Postman:
        // Pengajuan/Admin -> Diproses/Sekkel -> Selesai/Ditolak.
        // Kuncinya: log lama setelah surat direvisi atau diturunkan tidak boleh membuat "selesai" tetap terisi.
        $startDiproses = $activePhase >= 1
            ? $this->timeFromLog($this->firstLogAfterLastStatus($cycleLogs, [2], [0, 1]))
            : null;

        if (!$startDiproses && $activePhase >= 1) {
            $startDiproses = $this->timeFromLog($this->firstLogWithStatus($cycleLogs, [2]));
        }

        $startLurah = $activePhase >= 2
            ? $this->timeFromLog($this->firstLogWithStatus($cycleLogs, [3]))
            : null;

        // Jika pernah dinaikkan ke Lurah lalu diturunkan lagi ke Sekkel/Admin,
        // status 3 lama tidak boleh lagi menjadi selesai tahap Diproses.
        $endDiprosesByLurah = $activePhase >= 2
            ? $this->timeFromLog($this->lastLogAfterLastStatus($cycleLogs, [3], [2]))
            : null;

        if (!$endDiprosesByLurah && $activePhase >= 2) {
            $endDiprosesByLurah = $startLurah;
        }

        $startSelesai = $activePhase >= 3
            ? $this->timeFromLog($this->firstLogAfterLastStatus($cycleLogs, [4, 9, 5], [3]))
            : null;

        if (!$startSelesai && $activePhase >= 3) {
            $startSelesai = $this->timeFromLog($this->firstLogWithStatus($cycleLogs, [4, 9, 5]));
        }

        $endPengajuan = null;
        if ($isRejected) {
            $endPengajuan = $startDiproses ?: $startDitolak;
        } elseif ($currentPhase >= 1) {
            $endPengajuan = $startDiproses;
        }

        $endDiproses = null;
        if ($startDiproses) {
            if ($isRejected) {
                $endDiproses = $endDiprosesByLurah ?: $startSelesai ?: $startDitolak;
            } elseif ($currentPhase > 1) {
                $endDiproses = $endDiprosesByLurah ?: $startSelesai;
            }
            // currentPhase == 1 berarti surat sedang kembali/berada di Sekkel,
            // jadi selesai Diproses wajib null walaupun pernah ada status 3 lama.
        }

        $timeline = [];
        $timeline[] = $this->timelineRow('Pengajuan', $startPengajuan, $endPengajuan);

        if ($startDiproses) {
            $timeline[] = $this->timelineRow('Diproses', $startDiproses, $endDiproses);
        }

        if ($startSelesai && !$isRejected) {
            $timeline[] = $this->timelineRow('Selesai', $startSelesai, null);
        }

        if ($startDitolak) {
            $timeline[] = $this->timelineRow('Ditolak', $startDitolak, null);
        }

        return $timeline;
    }

    protected function buildSktmTimeline(SuratPengajuan $surat, $logs): array
    {
        $currentStatus = (int) ($surat->status ?? 0);
        $cycleLogs = $this->currentTrackingCycleLogs($surat, $logs);
        $isRejected = $currentStatus === 6;
        $currentPhase = $this->trackingPhaseForStatus($currentStatus, true);
        $activePhase = $isRejected ? $this->lastNonRejectedPhase($cycleLogs, true) : $currentPhase;

        $startPengajuan = $this->cyclePengajuanStart($surat, $cycleLogs);
        $startDitolak = $isRejected ? $this->timeFromLog($this->lastLogWithStatus($cycleLogs, [6])) : null;

        $startDiproses = $activePhase >= 1
            ? $this->timeFromLog($this->firstLogAfterLastStatus($cycleLogs, [2], [0, 1]))
            : null;

        if (!$startDiproses && $activePhase >= 1) {
            $startDiproses = $this->timeFromLog($this->firstLogWithStatus($cycleLogs, [2]));
        }

        $startLurah = $activePhase >= 2
            ? $this->timeFromLog($this->firstLogAfterLastStatus($cycleLogs, [3, 4], [2]))
            : null;

        if (!$startLurah && $activePhase >= 2) {
            $startLurah = $this->timeFromLog($this->firstLogWithStatus($cycleLogs, [3, 4]));
        }

        $endDiprosesByLurah = $activePhase >= 2
            ? $this->timeFromLog($this->lastLogAfterLastStatus($cycleLogs, [3], [2]))
            : null;

        if (!$endDiprosesByLurah && $activePhase >= 2) {
            $endDiprosesByLurah = $startLurah;
        }

        // Status 11 = mulai meja Sekcam. Jika dari Sekcam/Camat diturunkan lagi,
        // status 11/8 lama tidak boleh membuat tahap sebelumnya tetap selesai.
        $startSekcam = $activePhase >= 3
            ? $this->timeFromLog($this->firstLogAfterLastStatus($cycleLogs, [11], [3, 4]))
            : null;

        if (!$startSekcam && $activePhase >= 3) {
            $startSekcam = $this->timeFromLog($this->firstLogWithStatus($cycleLogs, [11]));
        }

        $endSekcam = $activePhase >= 4
            ? $this->timeFromLog($this->lastLogAfterLastStatus($cycleLogs, [8], [11]))
            : null;

        // Status 9 = final Camat untuk SKTM. Status 5 tetap dianggap selesai jika ada data lama yang sudah dinilai.
        $startSelesai = $activePhase >= 5
            ? $this->timeFromLog($this->firstLogAfterLastStatus($cycleLogs, [9, 5], [8]))
            : null;

        if (!$startSelesai && $activePhase >= 5) {
            $startSelesai = $this->timeFromLog($this->firstLogWithStatus($cycleLogs, [9, 5]));
        }

        $endPengajuan = null;
        if ($isRejected) {
            $endPengajuan = $startDiproses ?: $startDitolak;
        } elseif ($currentPhase >= 1) {
            $endPengajuan = $startDiproses;
        }

        $endDiproses = null;
        if ($startDiproses) {
            if ($isRejected) {
                $endDiproses = $endDiprosesByLurah ?: $startDitolak;
            } elseif ($currentPhase > 1) {
                $endDiproses = $endDiprosesByLurah;
            }
        }

        $endLurah = null;
        if ($startLurah) {
            if ($isRejected) {
                $endLurah = $startSekcam ?: $startDitolak;
            } elseif ($currentPhase > 2) {
                $endLurah = $startSekcam;
            }
            // currentPhase == 2 berarti surat sedang di Lurah / kembali dari Sekcam,
            // jadi selesai Lurah wajib null.
        }

        $endSekcamRow = null;
        if ($startSekcam) {
            if ($isRejected) {
                $endSekcamRow = $endSekcam ?: $startSelesai ?: $startDitolak;
            } elseif ($currentPhase > 3) {
                $endSekcamRow = $endSekcam ?: $startSelesai;
            }
            // currentPhase == 3 berarti surat sedang di Sekcam / kembali dari Camat,
            // jadi selesai Sekcam wajib null walaupun pernah ada status 8 lama.
        }

        $timeline = [];
        $timeline[] = $this->timelineRow('Pengajuan', $startPengajuan, $endPengajuan);

        if ($startDiproses) {
            $timeline[] = $this->timelineRow('Diproses', $startDiproses, $endDiproses);
        }

        if ($startLurah) {
            $timeline[] = $this->timelineRow('Lurah', $startLurah, $endLurah);
        }

        if ($startSekcam) {
            $timeline[] = $this->timelineRow('Sekcam', $startSekcam, $endSekcamRow);
        }

        if ($startSelesai && !$isRejected) {
            $timeline[] = $this->timelineRow('Selesai', $startSelesai, null);
        }

        if ($startDitolak) {
            $timeline[] = $this->timelineRow('Ditolak', $startDitolak, null);
        }

        return $timeline;
    }

    protected function currentTrackingCycleLogs(SuratPengajuan $surat, $logs)
    {
        $logs = $logs->values();
        if ($logs->isEmpty()) {
            return $logs;
        }

        $startIndex = 0;
        $lastRejectedIndex = null;

        foreach ($logs as $index => $log) {
            $status = (int) ($log->status_surat ?? 0);

            if ($status === 6) {
                $lastRejectedIndex = $index;
                continue;
            }

            // Revisi warga setelah ditolak selalu membuat log status 0 baru.
            // Dari titik ini tracking harus restart, supaya Ditolak dan waktu selesai lama tidak ikut terbaca.
            if ($status === 0 && $lastRejectedIndex !== null && $index > $lastRejectedIndex) {
                $startIndex = $index;
            }
        }

        return $logs->slice($startIndex)->values();
    }

    protected function cyclePengajuanStart(SuratPengajuan $surat, $logs): ?Carbon
    {
        return $this->timeFromLog($this->firstLogWithStatus($logs, [0]))
            ?: $this->toJakartaDateTime($surat->created_at)
            ?: $this->timeFromLog($logs->first());
    }

    protected function trackingPhaseForStatus(int $status, bool $isSktm): int
    {
        if ($status === 6) {
            return -1;
        }

        if ($isSktm) {
            return match (true) {
                in_array($status, [0, 1], true) => 0,
                $status === 2 => 1,
                in_array($status, [3, 4], true) => 2,
                $status === 11 => 3,
                $status === 8 => 4,
                in_array($status, [5, 9], true) => 5,
                default => 0,
            };
        }

        return match (true) {
            in_array($status, [0, 1], true) => 0,
            $status === 2 => 1,
            in_array($status, [3, 8, 11], true) => 2,
            in_array($status, [4, 5, 9], true) => 3,
            default => 0,
        };
    }

    protected function lastNonRejectedPhase($logs, bool $isSktm): int
    {
        for ($i = $logs->count() - 1; $i >= 0; $i--) {
            $status = (int) ($logs[$i]->status_surat ?? 0);
            if ($status !== 6) {
                return max(0, $this->trackingPhaseForStatus($status, $isSktm));
            }
        }

        return 0;
    }

    protected function firstLogWithStatus($logs, array $statuses)
    {
        return $logs->first(function ($item) use ($statuses) {
            return in_array((int) ($item->status_surat ?? 0), $statuses, true);
        });
    }

    protected function lastLogWithStatus($logs, array $statuses)
    {
        for ($i = $logs->count() - 1; $i >= 0; $i--) {
            if (in_array((int) ($logs[$i]->status_surat ?? 0), $statuses, true)) {
                return $logs[$i];
            }
        }

        return null;
    }

    protected function firstLogAfterLastStatus($logs, array $targetStatuses, array $afterStatuses)
    {
        $afterIndex = $this->lastIndexOfStatus($logs, $afterStatuses);

        foreach ($logs as $index => $log) {
            if ($afterIndex !== null && $index <= $afterIndex) {
                continue;
            }

            if (in_array((int) ($log->status_surat ?? 0), $targetStatuses, true)) {
                return $log;
            }
        }

        return null;
    }

    protected function lastLogAfterLastStatus($logs, array $targetStatuses, array $afterStatuses)
    {
        $afterIndex = $this->lastIndexOfStatus($logs, $afterStatuses);
        $found = null;

        foreach ($logs as $index => $log) {
            if ($afterIndex !== null && $index <= $afterIndex) {
                continue;
            }

            if (in_array((int) ($log->status_surat ?? 0), $targetStatuses, true)) {
                $found = $log;
            }
        }

        return $found;
    }

    protected function lastIndexOfStatus($logs, array $statuses): ?int
    {
        for ($i = $logs->count() - 1; $i >= 0; $i--) {
            if (in_array((int) ($logs[$i]->status_surat ?? 0), $statuses, true)) {
                return $i;
            }
        }

        return null;
    }

    protected function timeFromLog($log): ?Carbon
    {
        return $log ? $this->toJakartaDateTime($log->created_at) : null;
    }

    protected function resolveCurrentTrackingLabel(int $status, bool $isSktm): string
    {
        if ($status === 6) {
            return 'Ditolak';
        }

        if ($isSktm) {
            if ($status === 9 || $status === 5) {
                return 'Selesai';
            }
            if (in_array($status, [11, 8], true)) {
                return 'Sekcam';
            }
            if (in_array($status, [3, 4], true)) {
                return 'Lurah';
            }
            if ($status === 2) {
                return 'Diproses';
            }
            return 'Pengajuan';
        }

        if (in_array($status, [4, 5, 9], true)) {
            return 'Selesai';
        }
        if (in_array($status, [2, 3, 8, 11], true)) {
            return 'Diproses';
        }
        return 'Pengajuan';
    }

    protected function firstTime($logs, array $statuses): ?Carbon
    {
        $log = $logs->first(function ($item) use ($statuses) {
            return in_array((int) $item->status_surat, $statuses, true);
        });

        return $log ? $this->toJakartaDateTime($log->created_at) : null;
    }

    protected function timelineRow(string $status, ?Carbon $mulai, ?Carbon $selesai): array
    {
        return [
            'status' => $status,
            'mulai' => $this->formatTrackingTime($mulai),
            'selesai' => $this->formatTrackingTime($selesai),
            'durasi' => ($mulai && $selesai) ? $this->formatDurationText($mulai->diffInMinutes($selesai)) : null,
        ];
    }

    protected function toJakartaDateTime($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        return Carbon::parse($value)->timezone('Asia/Jakarta');
    }

    protected function formatTrackingTime($value): ?string
    {
        $date = $this->toJakartaDateTime($value);
        return $date ? $date->format('d-m-Y H:i') : null;
    }

    protected function formatDurationText(?int $minutes): ?string
    {
        if ($minutes === null) {
            return null;
        }

        $days = intdiv($minutes, 1440);
        $hours = intdiv($minutes % 1440, 60);
        $mins = $minutes % 60;
        $parts = [];

        if ($days > 0) {
            $parts[] = $days . ' hari';
        }
        if ($hours > 0) {
            $parts[] = $hours . ' jam';
        }
        if ($mins > 0 || empty($parts)) {
            $parts[] = $mins . ' menit';
        }

        return implode(' ', $parts);
    }


    /**
     * Normalisasi path PDF dari berbagai kemungkinan format:
     * /storage/pdf/file.pdf, storage/pdf/file.pdf, public/pdf/file.pdf,
     * pdf/file.pdf, atau hanya nama_file.pdf.
     */
    protected function normalizePdfRelativePath(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        $value = str_replace('\\', '/', $value);
        $value = preg_replace('#^https?://[^/]+/#i', '/', $value);
        $value = preg_replace('#^/?storage/#i', '', $value);
        $value = preg_replace('#^/?public/#i', '', $value);
        $value = ltrim($value, '/');

        if (!str_ends_with(strtolower($value), '.pdf')) {
            return null;
        }

        if (!str_contains($value, '/')) {
            $value = 'pdf/' . $value;
        }

        return $value;
    }

    protected function existingPdfFromRelativePath(?string $relativePath): ?array
    {
        $relativePath = $this->normalizePdfRelativePath($relativePath);

        if (!$relativePath) {
            return null;
        }

        $storagePath = storage_path('app/public/' . $relativePath);
        if (is_file($storagePath)) {
            return [
                'relative_path' => $relativePath,
                'absolute_path' => $storagePath,
                'url' => asset('storage/' . $relativePath),
                'filename' => basename($storagePath),
            ];
        }

        $publicPath = public_path('storage/' . $relativePath);
        if (is_file($publicPath)) {
            return [
                'relative_path' => $relativePath,
                'absolute_path' => $publicPath,
                'url' => asset('storage/' . $relativePath),
                'filename' => basename($publicPath),
            ];
        }

        return null;
    }

    protected function resolvePdfInfo(SuratPengajuan $surat): ?array
    {
        // Di database project ini nama kolom PDF adalah `file`.
        // Contoh isi: storage/pdf/SUKET_58_signed.pdf
        // Jadi `file` wajib dicek paling awal sebelum nama kolom alternatif lain.
        $candidateColumns = [
            'file',
            'file_pdf',
            'pdf_file',
            'pdf_path',
            'path_pdf',
            'pdf',
            'dokumen_pdf',
            'surat_pdf',
            'final_pdf',
            'hasil_pdf',
            'file_surat',
            'ttd_file',
            'tte_file',
        ];

        foreach ($candidateColumns as $column) {
            if (Schema::hasColumn('surat_pengajuans', $column)) {
                $info = $this->existingPdfFromRelativePath($surat->{$column} ?? null);
                if ($info) {
                    $info['source'] = 'column:' . $column;
                    return $info;
                }
            }
        }

        $variable = $this->getExistingVariableData($surat);
        foreach ($candidateColumns as $key) {
            if (array_key_exists($key, $variable)) {
                $info = $this->existingPdfFromRelativePath($variable[$key] ?? null);
                if ($info) {
                    $info['source'] = 'variable:' . $key;
                    return $info;
                }
            }
        }

        $pdfDirs = [
            storage_path('app/public/pdf'),
            public_path('storage/pdf'),
        ];

        $patterns = array_values(array_filter(array_unique([
            (string) $surat->id,
            strtolower((string) $surat->jenis_surat) . '_' . $surat->id,
            strtolower((string) $surat->jenis_surat) . '-' . $surat->id,
            (string) ($surat->nik ?? ''),
        ])));

        foreach ($pdfDirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            $files = glob($dir . '/*.pdf') ?: [];
            usort($files, function ($a, $b) {
                return filemtime($b) <=> filemtime($a);
            });

            foreach ($files as $file) {
                $base = strtolower(basename($file));
                foreach ($patterns as $pattern) {
                    if ($pattern !== '' && str_contains($base, strtolower($pattern))) {
                        $relativePath = 'pdf/' . basename($file);

                        return [
                            'relative_path' => $relativePath,
                            'absolute_path' => $file,
                            'url' => asset('storage/' . $relativePath),
                            'filename' => basename($file),
                            'source' => 'fallback:pdf-folder',
                        ];
                    }
                }
            }
        }

        return null;
    }

    public function pdfUrl(Request $request, int $id)
    {
        $surat = SuratPengajuan::find($id);

        if (!$surat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data surat tidak ditemukan.',
            ], 404);
        }

        $pdf = $this->resolvePdfInfo($surat);

        if (!$pdf) {
            return response()->json([
                'status' => 'error',
                'message' => 'File PDF belum tersedia. Buka/generate preview PDF dari web admin terlebih dahulu, atau pastikan path PDF tersimpan di surat_pengajuans.',
                'data' => [
                    'id' => $surat->id,
                    'jenis_surat' => $surat->jenis_surat,
                    'status' => $surat->status,
                ],
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'File PDF tersedia.',
            'data' => [
                'id' => $surat->id,
                'jenis_surat' => $surat->jenis_surat,
                'status' => $surat->status,
                'pdf_url' => $pdf['url'],
                'download_url' => url('/api/surat/' . $surat->id . '/download-pdf'),
                'filename' => $pdf['filename'],
                'source' => $pdf['source'] ?? null,
            ],
        ]);
    }

    public function downloadPdf(Request $request, int $id)
    {
        $surat = SuratPengajuan::find($id);

        if (!$surat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data surat tidak ditemukan.',
            ], 404);
        }

        $pdf = $this->resolvePdfInfo($surat);

        if (!$pdf || empty($pdf['absolute_path']) || !is_file($pdf['absolute_path'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'File PDF belum tersedia atau tidak ditemukan di server.',
                'data' => [
                    'id' => $surat->id,
                    'jenis_surat' => $surat->jenis_surat,
                    'status' => $surat->status,
                ],
            ], 404);
        }

        return response()->file($pdf['absolute_path'], [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $pdf['filename'] . '"',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'Pragma' => 'public',
        ]);
    }

}
