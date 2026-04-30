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
			'pengantar' => $surat->pengantar,
			'variable' => $this->getExistingVariableData($surat),
	
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

        return response()->json([
            'status' => 'success',
            'message' => 'Detail surat berhasil diambil.',
            'data' => $this->responseData($surat, $jenisKey, $jenisMaster),
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

            return $this->responseData($item, $jenisKey, $jenisMaster);
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Riwayat surat berhasil diambil.',
            'data' => $data,
        ]);
    }

}