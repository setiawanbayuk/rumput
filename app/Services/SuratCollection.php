<?php

namespace App\Services;

use App\Models\SuratSkbn;
use App\Models\SuratSktm;
use App\Models\SuratDomisili;
use App\Models\SuratBoro;
use App\Models\SuratPenghasilan;
use App\Models\SuratUsaha;
use App\Models\SuratKeterangan;
use App\Models\SuratKelahiran;
use App\Models\SuratKematian;
use App\Models\SuratPengajuan;
use App\Models\User;
use App\Models\Skpd;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SuratCollection
{
    protected array $config = [
        'skbn'        => ['model' => SuratSkbn::class,        'route' => 'skbn.edit',        'title' => 'Surat Keterangan Belum Menikah'],
        'sktm'        => ['model' => SuratSktm::class,        'route' => 'sktm.edit',        'title' => 'Surat Keterangan Miskin'],
        'skdom'       => ['model' => SuratDomisili::class,    'route' => 'skdom.edit',       'title' => 'Surat Keterangan Domisili'],
        'skboro'      => ['model' => SuratBoro::class,        'route' => 'skboro.edit',      'title' => 'Surat Keterangan Boro'],
        'skhsl'       => ['model' => SuratPenghasilan::class, 'route' => 'skhsl.edit',       'title' => 'Surat Keterangan Penghasilan'],
        'skusaha'     => ['model' => SuratUsaha::class,       'route' => 'skusaha.edit',     'title' => 'Surat Keterangan Usaha'],
        'suket'       => ['model' => SuratKeterangan::class,  'route' => 'suket.edit',       'title' => 'Surat Keterangan'],
        'skkelahiran' => ['model' => SuratKelahiran::class,   'route' => 'skkelahiran.edit', 'title' => 'Surat Keterangan Kelahiran'],
        'skkematian'  => ['model' => SuratKematian::class,    'route' => 'skkematian.edit',  'title' => 'Surat Keterangan Kematian'],
    ];

    protected function isSuperAdminUser(User $user): bool
    {
        return (int) $user->role_id === 7;
    }

    protected function accessibleKelurahanIds(User $user): ?array
    {
        if ($this->isSuperAdminUser($user)) {
            return null;
        }

        $roleId = (int) $user->role_id;
        $idInstansi = (int) ($user->id_instansi ?? 0);

        if ($idInstansi <= 0) {
            return [];
        }

        if (in_array($roleId, [5, 6], true)) {
            $skpdKecamatan = Skpd::find($idInstansi);
            $idKec = trim((string) optional($skpdKecamatan)->id_kec);

            if ($idKec === '') {
                return [];
            }

            return Skpd::query()
                ->where('id_kec', $idKec)
                ->get(['id', 'id_region'])
                ->filter(fn ($skpd) => substr_count((string) $skpd->id_region, '.') >= 3)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all();
        }

        return [$idInstansi];
    }

    protected function applyWilayahScope($query, User $user)
    {
        $kelurahanIds = $this->accessibleKelurahanIds($user);

        if ($kelurahanIds === null) {
            return $query;
        }

        if (empty($kelurahanIds)) {
            return $query->whereRaw('1 = 0');
        }

        $query->whereIn('id_kel', $kelurahanIds);

        if ((int) $user->role_id === 8) {
            $query->where('id_rw', $user->id_rw)
                ->where('id_rt', $user->id_rt);
        }

        return $query;
    }

    public function getAllForUser(User $user, ?array $universalStatusFilter = null): Collection
    {
        $items = collect();

        foreach ($this->config as $jenis => $cfg) {
            if (in_array($user->role_id, [5, 6]) && $jenis !== 'sktm') {
                continue;
            }

            $query = $cfg['model']::query();

            if ($user->role_id == 2) {
                if (in_array($jenis, ['skkelahiran', 'skkematian'])) {
                    $query->where('nik_pelapor', $user->nik);
                } else {
                    $query->where('nik', $user->nik);
                }
            } else {
                $this->applyWilayahScope($query, $user);
            }

            $rows = $query->get()->map(function ($row) use ($jenis, $cfg) {
                return (object) [
                    'id'          => $row->id,
                    'jenis'       => $jenis,
                    'jenis_label' => $cfg['title'],
                    'route_edit'  => $cfg['route'],
                    'nik'         => $row->nik ?? $row->nik_pelapor ?? null,
                    'peruntukan'  => $row->peruntukan,
                    'kepada'      => $row->kepada,
                    'nama_anak'   => $row->nama_anak,
                    'nama'        => $row->nama,
                    'status'      => $row->status,
                    'tgl_surat'   => $row->tgl_surat,
                    'created_at'  => optional($row->created_at)->timezone('Asia/Jakarta'),
                    'raw'         => $row,
                ];
            });

            $items = $items->merge($rows);
        }

        if ($user->role_id != 2) {
            // Data universal dari tabel surat_pengajuans harus ikut dihitung SEMUA statusnya.
            // Jangan default-filter status [4, 9], karena grafik Beranda harus menampilkan
            // surat yang masih Diajukan, Diproses, Dinaikkan ke Sekkel/Lurah/Camat, Ditolak, dll.
            $universal = SuratPengajuan::query();

            if (is_array($universalStatusFilter)) {
                $universal->whereIn('status', $universalStatusFilter);
            }

            // Role Camat (5) dan Sekretaris Camat/Sekcam (6) hanya boleh melihat SKTM.
            // Ini juga berlaku untuk data universal dari tabel surat_pengajuans, termasuk Beranda.
            if (in_array((int) $user->role_id, [5, 6], true)) {
                $universal->where('jenis_surat', 'sktm');
            }

            $this->applyWilayahScope($universal, $user);

            $universalRows = $universal->get()->map(function ($row) {
                return (object) [
                    'id'          => $row->id,
                    'jenis'       => Str::lower((string) $row->jenis_surat),
                    'jenis_label' => 'Surat ' . strtoupper((string) $row->jenis_surat),
                    'route_edit'  => 'admin.surat.edit',
                    'nik'         => $row->nik,
                    'peruntukan'  => $row->peruntukan,
                    'kepada'      => $row->kepada,
                    'nama_anak'   => data_get($row->variable, 'nama_anak'),
                    'nama'        => data_get($row->variable, 'name'),
                    'status'      => $row->status,
                    'tgl_surat'   => $row->tgl_surat,
                    'created_at'  => optional($row->created_at)->timezone('Asia/Jakarta'),
                    'raw'         => $row,
                ];
            });

            $items = $items->merge($universalRows);
        }

        return $items->sortByDesc('tgl_surat')->values();
    }

    public function getConfig()
    {
        return $this->config;
    }
}
