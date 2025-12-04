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
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class SuratCollection
{
    protected array $config = [
        'skbn'        => [ 'model' => SuratSkbn::class,       'route' => 'skbn.edit',        'title' => 'Surat Keterangan Belum Menikah' ],
        'sktm'        => [ 'model' => SuratSktm::class,       'route' => 'sktm.edit',        'title' => 'Surat Keterangan Miskin' ],
        'skdom'       => [ 'model' => SuratDomisili::class,   'route' => 'skdom.edit',       'title' => 'Surat Keterangan Domisili' ],
        'skboro'      => [ 'model' => SuratBoro::class,       'route' => 'skboro.edit',      'title' => 'Surat Keterangan Boro' ],
        'skhsl'       => [ 'model' => SuratPenghasilan::class,'route' => 'skhsl.edit',       'title' => 'Surat Keterangan Penghasilan' ],
        'skusaha'     => [ 'model' => SuratUsaha::class,      'route' => 'skusaha.edit',     'title' => 'Surat Keterangan Usaha' ],
        'suket'       => [ 'model' => SuratKeterangan::class, 'route' => 'suket.edit',       'title' => 'Surat Keterangan' ],
        'skkelahiran' => [ 'model' => SuratKelahiran::class,  'route' => 'skkelahiran.edit', 'title' => 'Surat Keterangan Kelahiran' ],
        'skkematian'  => [ 'model' => SuratKematian::class,   'route' => 'skkematian.edit',  'title' => 'Surat Keterangan Kematian' ],
    ];

    public function getAllForUser(User $user): Collection
    {
        $items = collect();

        foreach ($this->config as $jenis => $cfg) {
            if (in_array($user->role_id, [5, 6]) && $jenis !== 'sktm') {
                continue;
            }   

            $query = $cfg['model']::query();

            if ($user->role_id == 2) {
               // KHUSUS SK KELAHIRAN & SK KEMATIAN → pakai nik_pelapor
                if (in_array($jenis, ['skkelahiran', 'skkematian'])) {
                    $query->where('nik_pelapor', $user->nik);
                } else {
                    $query->where('nik', $user->nik);
                }
            } elseif ($user->role_id == 8) {
                $query->where('id_kel', $user->id_instansi)
                    ->where('id_rw', $user->id_rw)
                    ->where('id_rt', $user->id_rt);
            } elseif (in_array($user->role_id, [3, 4, 5, 6])) {
                $query->where('id_kel', $user->id_instansi);
            }

            $rows = $query->get()->map(function ($row) use ($jenis, $cfg) {
                return (object) [
                    'id'          => $row->id,
                    'jenis'       => $jenis,
                    'jenis_label' => $cfg['title'],
                    'route_edit'  => $cfg['route'],
                    'nik'         => $row->nik ?? $row->nik_pelapor ?? null,
                    'peruntukan'  => $row->peruntukan,
                    'status'      => $row->status,
                    'tgl_surat'   => $row->tgl_surat,
                    'created_at'  => optional($row->created_at)->timezone('Asia/Jakarta'),
                    'raw'         => $row,
                ];
            });

            $items = $items->merge($rows);
        }
        return $items->sortByDesc('tgl_surat')->values();
    }

    public function getConfig()
    {
        return $this->config;
    }
}
