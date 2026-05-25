<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Typography\FontFactory;

trait GlobalFunction
{
    public function TTE_sign(array $params)
    {
        /**
         * FIX FINAL SKTM TTE:
         * Sistem sebelumnya memakai endpoint /v2/sign/pdf + imageBase64/tag.
         * Hasilnya di PDF muncul stempel tulisan "Ditandatangani secara elektronik...",
         * bukan QR/barcode seperti alur lama, dan Camat sering gagal 500.
         *
         * Method ini mengembalikan pola lama yang sudah cocok untuk QR/barcode BSrE:
         * 1. cari koordinat marker di PDF dengan signminer (contoh: ~ atau [[qr_camat]])
         * 2. panggil endpoint /sign/pdf dengan image=false dan linkQR
         * 3. hasil PDF binary ditulis ke storage/app/public/pdf/{file_name}
         *
         * Jadi TTE Lurah tampil barcode/QR, dan TTE Camat menimpa file hasil TTE Lurah
         * tanpa generate ulang template.
         */
        try {
            if (empty($params['path']) || !file_exists($params['path'])) {
                return ['status' => 'error', 'message' => 'File PDF tidak ditemukan: ' . ($params['path'] ?? '-')];
            }

            $fileName = $params['file_name'] ?? ('signed_' . time() . '.pdf');
            $targetPath = storage_path('app/public/pdf/' . $fileName);

            if (!is_dir(dirname($targetPath))) {
                @mkdir(dirname($targetPath), 0777, true);
            }

            // Untuk Camat, input adalah PDF hasil TTE Lurah. Copy dulu ke nama output Camat,
            // agar proses TTE Camat tidak menghilangkan TTE Lurah.
            if (realpath($params['path']) !== realpath($targetPath)) {
                @copy($params['path'], $targetPath);
            }

            clearstatcache(true, $targetPath);
            if (!file_exists($targetPath) || filesize($targetPath) <= 0) {
                return ['status' => 'error', 'message' => 'Gagal menyiapkan file PDF untuk proses TTE.'];
            }

            $tagInput = $params['qr_loc'] ?? '~';
            $tagCandidates = is_array($tagInput) ? $tagInput : [$tagInput];
            $tagCandidates = array_values(array_unique(array_filter(array_map(function ($tag) {
                return trim((string) $tag);
            }, $tagCandidates), fn ($tag) => $tag !== '')));

            if (empty($tagCandidates)) {
                $tagCandidates = ['~'];
            }

            $lastError = null;
            foreach ($tagCandidates as $tag) {
                $meta = $this->get_esign_coordinate($targetPath, $tag);

                if (($meta['status'] ?? false) !== true || empty($meta['hasil'])) {
                    $lastError = 'Marker TTE tidak ditemukan di PDF: ' . $tag;
                    Log::warning($lastError, ['file' => $targetPath]);
                    continue;
                }

                // Ambil koordinat marker terakhir yang ditemukan.
                $hasil = end($meta['hasil']);
                $coord = $hasil['data'] ?? [0, 0, 0, 0];

                $qrSize = (int) ($params['qr_size'] ?? 95);
                // Koordinat marker Word dipakai sebagai titik tengah patokan.
                // Jika memakai gambar gabungan QR + kartu TTE, seluruh blok gambar dipusatkan pada marker.
                $markerCenterX = $coord[0] + (($coord[2] - $coord[0]) / 2);
                $markerCenterY = $coord[1] + (($coord[3] - $coord[1]) / 2);

                $useCustomImage = !empty($params['use_custom_image']) && !empty($params['image_path']);
                $imageWidth = (int) ($params['image_width'] ?? 330);
                $imageHeight = (int) ($params['image_height'] ?? 90);
                $offsetX = (float) ($params['offset_x'] ?? 0);
                $offsetY = (float) ($params['offset_y'] ?? 0);

                $signData = [
                    'nik'        => $params['nik'] ?? '',
                    'passphrase' => $params['passphrase'] ?? '',
                    'file_name'  => $fileName,
                    'verify'     => $params['verify'] ?? config('app.url'),
                    'page'       => (int) ($hasil['page'] ?? 1),
                    'x'          => (float) ($useCustomImage ? ($markerCenterX - ($imageWidth / 2) + $offsetX) : ($markerCenterX - ($qrSize / 2) + $offsetX)),
                    'y'          => (float) ($useCustomImage ? ($markerCenterY - ($imageHeight / 2) + $offsetY) : ($markerCenterY - ($qrSize / 2) + $offsetY)),
                    'qr_size'    => $qrSize,
                    'image_path' => $params['image_path'] ?? null,
                    'image_width' => $imageWidth,
                    'image_height' => $imageHeight,
                ];

                $response = $useCustomImage
                    ? $this->TTE_Visible_Image($signData)
                    : $this->TTE_Visible_QR($signData);

                if (($response['status'] ?? false) === true) {
                    return ['status' => 'success', 'message' => 'Esign Berhasil', 'tag' => $tag];
                }

                $lastError = 'Gagal Esign marker ' . $tag . ': ' . ($response['message'] ?? 'Unknown Error');
                Log::error($lastError, ['file' => $targetPath]);
            }

            // Fallback terakhir agar proses tidak berhenti total ketika marker dari PDF tidak terbaca oleh pdfminer.
            // Ini hanya dipakai jika semua marker gagal ditemukan/dipakai. Koordinat dibuat khusus untuk template SKTM 1 halaman.
            // Lurah: kanan bawah area tanda tangan Lurah. Camat: bawah tengah area tanda tangan Camat.
            if (($params['allow_coordinate_fallback'] ?? true) !== false) {
                $role = (int) ($params['role'] ?? 0);
                $isCamat = $role === 5;
                $useCustomImage = !empty($params['use_custom_image']) && !empty($params['image_path']);
                $fallbackImageWidth = (int) ($params['image_width'] ?? 330);
                $fallbackImageHeight = (int) ($params['image_height'] ?? 90);
                $fallback = [
                    'nik'        => $params['nik'] ?? '',
                    'passphrase' => $params['passphrase'] ?? '',
                    'file_name'  => $fileName,
                    'verify'     => $params['verify'] ?? config('app.url'),
                    'page'       => 1,
                    'x'          => (float) ($params['fallback_x'] ?? ($isCamat ? 255 : 390)),
                    'y'          => (float) ($params['fallback_y'] ?? ($isCamat ? 85 : 230)),
                    'qr_size'    => (int) ($params['qr_size'] ?? 95),
                    'image_path' => $params['image_path'] ?? null,
                    'image_width' => $fallbackImageWidth,
                    'image_height' => $fallbackImageHeight,
                ];

                Log::warning('Marker TTE tidak ditemukan, memakai fallback koordinat SKTM.', [
                    'file' => $targetPath,
                    'role' => $role,
                    'fallback' => $fallback,
                    'last_error' => $lastError,
                ]);

                $fallbackResponse = $useCustomImage
                    ? $this->TTE_Visible_Image($fallback)
                    : $this->TTE_Visible_QR($fallback);
                if (($fallbackResponse['status'] ?? false) === true) {
                    return ['status' => 'success', 'message' => 'Esign Berhasil dengan fallback koordinat', 'tag' => 'fallback-coordinate'];
                }

                $lastError = 'Fallback koordinat gagal: ' . ($fallbackResponse['message'] ?? $lastError ?? 'Unknown Error');
            }

            return ['status' => 'error', 'message' => $lastError ?: 'TTE gagal. Marker tidak ditemukan atau response BSrE tidak valid.'];
        } catch (\Throwable $e) {
            Log::error('TTE_sign Exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Sistem Error: ' . $e->getMessage()];
        }
    }

    protected function getPythonCommand(): string
    {
        // Server production user Windows; py biasanya tersedia. Fallback untuk Linux hosting/dev.
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 'py';
        }
        return trim((string) shell_exec('command -v python3 2>/dev/null')) ?: 'python3';
    }

    public function get_esign_coordinate($input, $flag)
    {
        try {
            $script = public_path('library/signminer/coordinate.py');
            if (!file_exists($script)) {
                return ['status' => false, 'message' => 'File signminer coordinate.py tidak ditemukan.'];
            }

            $command = $this->getPythonCommand() . ' ' . escapeshellarg($script) . ' ' . escapeshellarg($input) . ' ' . escapeshellarg($flag) . ' 2>&1';
            $output = shell_exec($command);

            if (!$output) {
                return ['status' => false, 'message' => 'Marker tidak ditemukan / signminer tidak mengembalikan koordinat. Output: ' . trim((string) $output)];
            }

            $output = str_replace("\n", '', trim($output));
            $datas = array_filter(explode('!', $output));
            $hasil = [];

            foreach ($datas as $data) {
                $parts = array_values(array_filter(explode(',', $data), fn ($v) => $v !== ''));
                if (count($parts) < 5 || !is_numeric($parts[0]) || !is_numeric($parts[1]) || !is_numeric($parts[2]) || !is_numeric($parts[3])) {
                    continue;
                }
                $coord = [(float) $parts[0], (float) $parts[1], (float) $parts[2], (float) $parts[3]];
                $page = ((int) $parts[4]) + 1;
                $hasil[] = ['page' => $page, 'data' => $coord];
            }

            return !empty($hasil)
                ? ['status' => true, 'hasil' => $hasil]
                : ['status' => false, 'message' => 'Marker tidak ditemukan di PDF: ' . $flag];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    public function TTE_Visible_QR(array $request)
    {
        $qrSize = (int) ($request['qr_size'] ?? 95);

        $data = [
            'nik'        => $request['nik'],
            'passphrase' => $request['passphrase'],
            'tampilan'   => 'visible',
            'page'       => $request['page'],
            'reason'     => 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE',
            'location'   => 'Kota Kediri',
            'image'      => false,
            'linkQR'     => $request['verify'] ?? config('app.url'),
            'xAxis'      => $request['x'],
            'yAxis'      => $request['y'],
            // API lama BSrE memakai width/height sebagai koordinat kanan-bawah, bukan ukuran murni.
            'width'      => $request['x'] + $qrSize,
            'height'     => $request['y'] + $qrSize,
        ];

        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $filePath = storage_path('app/public/pdf/' . $request['file_name']);
        if (!file_exists($filePath)) {
            return ['message' => 'File PDF untuk dikirim ke BSrE tidak ditemukan.', 'status' => false];
        }

        try {
            $query = http_build_query($data);
            $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))
                ->asMultipart()
                ->attach('file', file_get_contents($filePath, false, stream_context_create($arrContextOptions)), $request['file_name'])
                ->post(env('APP_URL_TTE') . '/sign/pdf?' . $query);

            $json = null;
            try {
                $json = $r->json();
            } catch (\Throwable $e) {
                $json = null;
            }

            // Endpoint lama mengembalikan PDF binary saat sukses.
            $body = $r->body();
            $looksLikePdf = is_string($body) && substr($body, 0, 4) === '%PDF';
            if (($looksLikePdf || !$json) && $r->successful()) {
                file_put_contents($filePath, $body);
                clearstatcache(true, $filePath);

                if (file_exists($filePath) && filesize($filePath) > 0) {
                    return ['message' => 'Esign done successfully.', 'status' => true];
                }

                return ['message' => 'BSrE sukses tetapi file hasil kosong.', 'status' => false];
            }

            $message = is_array($json)
                ? ($json['error'] ?? $json['message'] ?? json_encode($json))
                : ($r->body() ?: 'Kemungkinan passphrase salah atau marker TTE tidak valid.');

            return ['message' => $message, 'status' => false];
        } catch (\Throwable $e) {
            return ['message' => $e->getMessage(), 'status' => false];
        }
    }

    public function TTE_Visible_Image(array $request)
    {
        $imagePath = $request['image_path'] ?? null;
        $imageWidth = (int) ($request['image_width'] ?? 330);
        $imageHeight = (int) ($request['image_height'] ?? 90);

        if (!$imagePath || !file_exists($imagePath) || filesize($imagePath) <= 0) {
            return ['message' => 'File gambar TTE tidak ditemukan atau kosong.', 'status' => false];
        }

        $data = [
            'nik'        => $request['nik'],
            'passphrase' => $request['passphrase'],
            'tampilan'   => 'visible',
            'page'       => $request['page'],
            'reason'     => 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE',
            'location'   => 'Kota Kediri',
            'image'      => true,
            'linkQR'     => '',
            'xAxis'      => $request['x'],
            'yAxis'      => max(0, (float) $request['y']),
            'width'      => $request['x'] + $imageWidth,
            'height'     => max(0, (float) $request['y']) + $imageHeight,
        ];

        $arrContextOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $filePath = storage_path('app/public/pdf/' . $request['file_name']);
        if (!file_exists($filePath)) {
            return ['message' => 'File PDF untuk dikirim ke BSrE tidak ditemukan.', 'status' => false];
        }

        try {
            $query = http_build_query($data);
            $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))
                ->asMultipart()
                ->attach('file', file_get_contents($filePath, false, stream_context_create($arrContextOptions)), $request['file_name'])
                ->attach('imageTTD', file_get_contents($imagePath, false, stream_context_create($arrContextOptions)), basename($imagePath))
                ->post(env('APP_URL_TTE') . '/sign/pdf?' . $query);

            $json = null;
            try {
                $json = $r->json();
            } catch (\Throwable $e) {
                $json = null;
            }

            $body = $r->body();
            $looksLikePdf = is_string($body) && substr($body, 0, 4) === '%PDF';
            if (($looksLikePdf || !$json) && $r->successful()) {
                file_put_contents($filePath, $body);
                clearstatcache(true, $filePath);

                if (file_exists($filePath) && filesize($filePath) > 0) {
                    return ['message' => 'Esign image done successfully.', 'status' => true];
                }

                return ['message' => 'BSrE sukses tetapi file hasil kosong.', 'status' => false];
            }

            $message = is_array($json)
                ? ($json['error'] ?? $json['message'] ?? json_encode($json))
                : ($r->body() ?: 'Kemungkinan passphrase salah atau gambar TTE tidak valid.');

            return ['message' => $message, 'status' => false];
        } catch (\Throwable $e) {
            return ['message' => $e->getMessage(), 'status' => false];
        }
    }

    protected function saveSingleTteSampleImage(?string $sourcePath): void
    {
        try {
            if (!$sourcePath || !file_exists($sourcePath) || filesize($sourcePath) <= 0) {
                return;
            }

            $samplePath = public_path('img/tte_contoh.png');
            if (!is_dir(dirname($samplePath))) {
                @mkdir(dirname($samplePath), 0777, true);
            }

            @copy($sourcePath, $samplePath);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan 1 contoh gambar TTE.', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    protected function cleanupTemporaryGeneratedTteImages(): void
    {
        try {
            foreach ((array) glob(public_path('img/tte_*.png')) as $file) {
                if (is_string($file) && basename($file) !== 'tte_contoh.png' && file_exists($file)) {
                    @unlink($file);
                }
            }
            foreach ((array) glob(public_path('img/qr_tte_*.png')) as $file) {
                if (is_string($file) && file_exists($file)) {
                    @unlink($file);
                }
            }
            foreach ((array) glob(public_path('img/tte_qr_*.png')) as $file) {
                if (is_string($file) && basename($file) !== 'tte_contoh.png' && file_exists($file)) {
                    @unlink($file);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Gagal membersihkan file gambar TTE lama.', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function generateTteWithQr($request, string $verifyUrl, $kec = null, $no_reg = null): ?array
    {
        try {
            $this->cleanupTemporaryGeneratedTteImages();
            $manager = new ImageManager(Driver::class);
            $qrName = 'qr_tte_' . hash('sha256', now()->format('YmdHisv') . '|' . uniqid('', true)) . '.png';
            $qrPath = public_path('img/' . $qrName);

            if (!is_dir(dirname($qrPath))) {
                @mkdir(dirname($qrPath), 0777, true);
            }

            // QR dibuat lebih proporsional agar hasil Lurah/Camat serasi dan tidak terlalu besar.
            \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(200)->margin(1)->generate($verifyUrl ?: config('app.url'), $qrPath);

            $card = $this->generateTte($request, $kec, $no_reg);
            $cardPath = $card['full_path'] ?? (isset($card['path']) ? public_path($card['path']) : null);

            if (!$cardPath || !file_exists($cardPath) || filesize($cardPath) <= 0) {
                return null;
            }

            $qrImage = $manager->read($qrPath);
            $qrImage->resize(200, 200);
            $cardImage = $manager->read($cardPath);
            $cardImage->resize(610, 190);

            $composite = $manager->create(820, 200)->fill('white');
            $composite->place($qrImage, 'left', 0, 0);
            $composite->place($cardImage, 'left', 210, 5);

            $imgName = 'tte_qr_' . hash('sha256', implode('|', [
                now()->format('YmdHisv'),
                $request->id ?? '',
                $request->nip ?? '',
                uniqid('', true),
            ])) . '.png';

            $outputTte = 'img/' . $imgName;
            $fullOutputTte = public_path($outputTte);
            $composite->toPng()->save($fullOutputTte);
            $this->saveSingleTteSampleImage($fullOutputTte);

            return [
                'filename' => $imgName,
                'path' => $outputTte,
                'full_path' => $fullOutputTte,
                // Bersihkan semua file hasil generate sementara setelah proses TTE selesai,
                // cukup sisakan 1 contoh saja di public/img/tte_contoh.png
                'cleanup_paths' => array_values(array_filter([$qrPath, $cardPath, $fullOutputTte])),
            ];
        } catch (\Throwable $e) {
            Log::warning('Gagal membuat gambar TTE gabungan QR.', [
                'message' => $e->getMessage(),
                'pejabat_id' => $request->id ?? null,
            ]);
            return null;
        }
    }

    public function generateTte($request, $kec = null, $no_reg = null)
    {
        $manager = new ImageManager(Driver::class);
        $registrationNumber = trim((string) ($no_reg ?? ''));
        $showKecamatanRegister = ($kec == true && $registrationNumber !== '');
        $image = $manager->create(610, 190)->fill('white');
        $image->drawRectangle(0, 0, function (RectangleFactory $rectangle) {
            $rectangle->size(610, 190);
            $rectangle->background('white');
            $rectangle->border('black', 3);
        });

        $logoPath = public_path('img/logo.png');
        if (file_exists($logoPath)) {
            $logo = $manager->read($logoPath);
            $logo->resize($showKecamatanRegister ? 108 : 135, $showKecamatanRegister ? 108 : 135);
            $image->place($logo, 'left', 10, $showKecamatanRegister ? 28 : 22);
        }

        $namaSkpd = ucfirst(strtolower((string) optional($request->skpd)->nama));
        $namaPejabat = trim((string) ($request->nama ?? ''));
        $nipPejabat = trim((string) ($request->nip ?? ''));
        $pangkatPejabat = trim((string) optional($request->pangkat)->nama);
        $jabatanPejabat = trim((string) optional($request->jabatan)->nama);

        if ($kec == true) {
            $jabatanBaris = ($jabatanPejabat !== '' ? $jabatanPejabat : 'Camat') . ' Kecamatan ' . $namaSkpd . ',';
        } else {
            $jabatanBaris = ($jabatanPejabat !== '' ? $jabatanPejabat : 'Lurah') . ' Kelurahan ' . $namaSkpd . ',';
        }

        $regularFontCandidates = [
            public_path('fonts/arial.ttf'),
            public_path('fonts/Arial.ttf'),
            'C:/Windows/Fonts/arial.ttf',
            '/usr/share/fonts/truetype/msttcorefonts/Arial.ttf',
            '/usr/share/fonts/truetype/msttcorefonts/arial.ttf',
            public_path('fonts/KumbhSans-Medium.ttf'),
        ];
        $boldFontCandidates = [
            public_path('fonts/arialbd.ttf'),
            public_path('fonts/Arialbd.ttf'),
            'C:/Windows/Fonts/arialbd.ttf',
            '/usr/share/fonts/truetype/msttcorefonts/Arial_Bold.ttf',
            '/usr/share/fonts/truetype/msttcorefonts/arialbd.ttf',
            public_path('fonts/KumbhSans-Bold.ttf'),
        ];
        $regularFont = collect($regularFontCandidates)->first(fn ($path) => is_string($path) && file_exists($path)) ?: public_path('fonts/KumbhSans-Medium.ttf');
        $boldFont = collect($boldFontCandidates)->first(fn ($path) => is_string($path) && file_exists($path)) ?: public_path('fonts/KumbhSans-Bold.ttf');

        if ($showKecamatanRegister) {
            // Registrasi Kecamatan dibuat kecil, hitam, dan serasi dengan isi TTE.
            // Hanya desain tampilan, tidak mengubah alur/proses TTE.
            $image->text('Registrasi Kecamatan', 160, 18, function (FontFactory $font) use ($boldFont) {
                $font->filename($boldFont);
                $font->size(12);
                $font->color('black');
            });

            $image->text($registrationNumber, 160, 34, function (FontFactory $font) use ($boldFont) {
                $font->filename($boldFont);
                $font->size(12);
                $font->color('black');
            });
        }

        $ySigned = $showKecamatanRegister ? 57 : 32;
        $yJabatan = $showKecamatanRegister ? 79 : 56;
        $yKota = $showKecamatanRegister ? 100 : 80;
        $yNama = $showKecamatanRegister ? 132 : 126;
        $yPangkat = $showKecamatanRegister ? 156 : 149;
        $yNip = $showKecamatanRegister ? 175 : 171;

        $image->text('Ditandatangani secara elektronik oleh:', 160, $ySigned, function (FontFactory $font) use ($regularFont, $showKecamatanRegister) {
            $font->filename($regularFont);
            $font->size($showKecamatanRegister ? 15 : 17);
            $font->color('black');
        });

        $image->text($jabatanBaris, 160, $yJabatan, function (FontFactory $font) use ($boldFont, $showKecamatanRegister) {
            $font->filename($boldFont);
            $font->size($showKecamatanRegister ? 17 : 19);
            $font->color('black');
        });

        $image->text('Kota Kediri', 160, $yKota, function (FontFactory $font) use ($boldFont, $showKecamatanRegister) {
            $font->filename($boldFont);
            $font->size($showKecamatanRegister ? 17 : 19);
            $font->color('black');
        });

        $image->text($namaPejabat, 160, $yNama, function (FontFactory $font) use ($boldFont, $showKecamatanRegister) {
            $font->filename($boldFont);
            $font->size($showKecamatanRegister ? 17 : 19);
            $font->color('black');
        });

        if ($pangkatPejabat !== '') {
            $image->text($pangkatPejabat, 160, $yPangkat, function (FontFactory $font) use ($regularFont, $showKecamatanRegister) {
                $font->filename($regularFont);
                $font->size($showKecamatanRegister ? 14 : 16);
                $font->color('black');
            });
        }

        $image->text('NIP. ' . $nipPejabat, 160, $yNip, function (FontFactory $font) use ($regularFont, $showKecamatanRegister) {
            $font->filename($regularFont);
            $font->size($showKecamatanRegister ? 14 : 16);
            $font->color('black');
        });

        $imgName = 'tte_' . hash('sha256', implode('|', [
            now()->format('YmdHisv'),
            $request->id ?? '',
            $request->nip ?? '',
            uniqid('', true),
        ])) . '.png';
        $outputTte = 'img/' . $imgName;
        $fullOutputTte = public_path($outputTte);

        if (!is_dir(dirname($fullOutputTte))) {
            @mkdir(dirname($fullOutputTte), 0777, true);
        }

        $image->toPng()->save($fullOutputTte);
        $result = [
            'filename' => $imgName,
            'path' => $outputTte,
            'full_path' => $fullOutputTte,
        ];
        return $result;
    }

    //Esign
    // function TTE_sign($data)
    // {
    //     $flag_location = "[[" . $data['qr_loc'] . "]]";
    //     $x_widht = 30;
    //     $y_height = 25;

    //     if (isset($data['type'])) {
    //         if (preg_match('/image/i', $data['type'])) {
    //             // $flag_location =  $data['qr_loc'];
    //             // $x_widht = 85;
    //             $y_height = 50;
    //         }
    //     }

    //     $response = [];
    //     $meta = [];
    //     if ($data['is_visible']) {
    //         $meta = $this->get_esign_coordinate($data['path'], $flag_location);
    //         // dd($flag_location);
    //         if ($meta['status'] == true) {
    //             foreach ($meta['hasil'] as $hasil) {
    //                 $data['x'] = $hasil['data'][0] + (($hasil['data'][2] - $hasil['data'][0]) / 2) - $x_widht;
    //                 $data['y'] = $hasil['data'][1] + (($hasil['data'][3] - $hasil['data'][1]) / 2) - $y_height;
    //                 $data['page'] = $hasil['page'];
    //                 if (isset($data['jenis'])) {
    //                     // if (($data['jenis'] == 'skkelahiran') || ($data['jenis'] == 'skkematian')) {
    //                     $data['x'] = $data['x'] - 75;
    //                     // }
    //                 }
    //                 // dd($data);
    //             }

    //             $response = $this->TTE_Visible($data);
    //             // dd($response);
    //         } else {
    //             $response = ['message' => 'Library PDF Miner Not Found / Prompt Not Executed / Format Flag TTE Tidak Sesuai', 'status' => false];
    //         }
    //     } else {
    //         $response = $this->TTE_Invisible($data);
    //     }

    //     // dd($response);
    //     // return $response['status'];

    //     if ($response['status'] == true) {
    //         if ((isset($meta['status']) && $meta['status'] == true && $data['is_visible'] == true) || $data['is_visible'] == false) {
    //             $result = array(
    //                 'responsStatus' => "Success",
    //                 'code'          => 200,
    //                 'responsDesc'   => "Tanda Tangan Berhasil",
    //                 'data'          => $response
    //             );
    //             // Log Activity
    //             // $log_activity = $result;
    //             // $log_activity['name'] = Auth::user()->name;
    //             // $log_activity['email'] = Auth::user()->email;
    //             // $log_activity['created_at'] = now();
    //             // activity('Response-Sign')->withProperties($log_activity)->log('Success');
    //         } else {
    //             $result = array(
    //                 'responsStatus' => "Warning",
    //                 'code'          => 201,
    //                 'responsDesc'   => "Tanda Tangan Berhasil, tetapi tag [[qr_here]] tidak ditemukan sehingga QR CODE gagal tergenerate!",
    //                 'data'          => $response
    //             );
    //             // Log Activity
    //             // $log_activity = $result;
    //             // $log_activity['name'] = Auth::user()->name;
    //             // $log_activity['email'] = Auth::user()->email;
    //             // $log_activity['created_at'] = now();
    //             // activity('Response-Sign')->withProperties($log_activity)->log('Error');
    //         }
    //     } else {
    //         $result = array(
    //             'responsStatus' => "Error",
    //             'code'          => 400,
    //             'responsDesc' => "Tanda Tangan Tidak Berhasil, " . $response['message']
    //         );
    //         // Log Activity
    //         // $log_activity = $result;
    //         // $log_activity['name'] = Auth::user()->name;
    //         // $log_activity['email'] = Auth::user()->email;
    //         // $log_activity['created_at'] = now();
    //         // activity('Response-Sign')->withProperties($log_activity)->log('Error');
    //     }

    //     return response()->json($result, 200);
    // }

    // // SignMiner
    // public function get_esign_coordinate($input, $flag)
    // {
    //     try {
    //         $command = "py " . public_path("library/signminer/coordinate.py") . " " . $input . " " . $flag . "";
    //         // dd($command);
    //         $output = shell_exec($command);
    //         if ($output) {
    //             $output = str_replace("\n", "", $output);
    //             $datas = array_filter(explode('!', $output));

    //             foreach ($datas as $data) {
    //                 $data = array_filter(explode(',', $data));
    //                 $coord = [(float) $data[0], (float) $data[1], (float) $data[2], (float) $data[3]];
    //                 $page = (int) $data[4] + 1;
    //                 $hasil[] = ['page' => $page, 'data' => $coord];
    //             }
    //             $result = ['status' => true, 'hasil' => $hasil];
    //             return $result;
    //         } else {
    //             $coord = [0, 0, 0, 0];
    //             $hasil[] = ['page' => 0, 'data' => $coord];
    //             $result = ['status' => false, 'hasil' => $hasil];
    //             return $result;
    //         }
    //     } catch (\Exception $th) {
    //         return ['status' => false, 'message' => $th->getMessage()];
    //     }
    // }

    // // Visible Sign
    // public function TTE_Visible($request)
    // {
    //     $data = array(
    //         'nik' => $request['nik'],
    //         'passphrase' => $request['passphrase'],
    //         'tampilan' => 'visible',
    //         'page' => $request['page'],
    //         'reason' => 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE',
    //         'location' => 'Kota Kediri',
    //         'image' => false,
    //         'linkQR' => $request['verify'],
    //         'xAxis' => $request['x'],
    //         'yAxis' => $request['y'],
    //         'width' => $request['x'] + 65,
    //         'height' => $request['y'] + 65,
    //     );

    //     if (isset($request['type'])) {
    //         if (preg_match('/image/i', $request['type'])) {
    //             $data['linkQR'] = '';
    //             $data['image'] = true;
    //             $data['width'] = $request['x'] + 210;
    //             $data['height'] =  $request['y'] + 90;
    //         }
    //     }
    //     $arrContextOptions = array(
    //         "ssl" => array(
    //             "verify_peer" => false,
    //             "verify_peer_name" => false,
    //         ),
    //     );

    //     $query = http_build_query($data);

    //     // dd(storage_path('app/public/pdf/' . $request['file_name']), false, stream_context_create($arrContextOptions));

    //     // dd(file_get_contents(public_path('assets/media/logos/' . $request['image_path'])));
    //     try {
    //         $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))
    //             ->asMultipart()
    //             ->attach('file', file_get_contents(storage_path('app/public/pdf/' . $request['file_name']), false, stream_context_create($arrContextOptions)), $request['file_name']);

    //         // dd($r);

    //         if (isset($request['type'])) {
    //             if (preg_match('/image/i', $request['type'])) {
    //                 $r = $r->attach('imageTTD', file_get_contents(public_path('img/' . $request['image_path']), false, stream_context_create($arrContextOptions)), $request['image_path']);
    //             }
    //         }
    //         $r = $r->post(env('APP_URL_TTE') . '/sign/pdf?' . $query);

    //         // dd($r->body(), $r->headers());

    //         if (!$r->json()) {
    //             $fp = fopen(storage_path('app/public/pdf/' . $request['file_name']), 'wb');
    //             fwrite($fp, $r);
    //             fclose($fp);
    //             return ['message' => 'Esign done successfully.', 'status' => true];
    //         } else {
    //             return ['message' => isset($r->json()['error']) ? $r->json()['error'] : "Kemungkinan Passphrase anda salah", 'status' => false];
    //         }
    //     } catch (\Exception $e) {
    //         return ['message' => $e->getMessage(), 'status' => false];
    //     }
    // }

    // // InVisible Sign
    // public function TTE_Invisible($request)
    // {
    //     $data = array(
    //         'nik' => $request['nik'],
    //         'passphrase' => $request['passphrase'],
    //         'tampilan' => 'invisible',
    //         'reason' => 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE',
    //         'location' => 'Kota Kediri',
    //     );

    //     $arrContextOptions = array(
    //         "ssl" => array(
    //             "verify_peer" => false,
    //             "verify_peer_name" => false,
    //         ),
    //     );

    //     $query = http_build_query($data);

    //     try {
    //         $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))
    //             ->asMultipart()
    //             ->attach('file', file_get_contents(storage_path('app/public/pdf/' . $request['file_name']), false, stream_context_create($arrContextOptions)), $request['file_name'])
    //             ->post(env('APP_URL_TTE') . '/sign/pdf?' . $query);

    //         if (!$r->json()) {
    //             $fp = fopen(storage_path('app/public/pdf/' . $request['file_name']), 'wb');
    //             fwrite($fp, $r);
    //             fclose($fp);
    //             return ['message' => 'Esign done successfully.', 'status' => true];
    //         } else {
    //             return ['message' => isset($r->json()['error']) ? $r->json()['error'] : "Kemungkinan Passphrase anda salah", 'status' => false];
    //         }
    //     } catch (\Exception $e) {
    //         return ['message' => $e->getMessage(), 'status' => false];
    //     }
    // }

}
