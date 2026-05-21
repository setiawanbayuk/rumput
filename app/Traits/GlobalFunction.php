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
                // Koordinat dari signminer sekarang adalah bbox marker yang tepat.
                // QR dipusatkan persis pada marker Word, bukan memakai offset tetap.
                $markerCenterX = $coord[0] + (($coord[2] - $coord[0]) / 2);
                $markerCenterY = $coord[1] + (($coord[3] - $coord[1]) / 2);

                $signData = [
                    'nik'        => $params['nik'] ?? '',
                    'passphrase' => $params['passphrase'] ?? '',
                    'file_name'  => $fileName,
                    'verify'     => $params['verify'] ?? config('app.url'),
                    'page'       => (int) ($hasil['page'] ?? 1),
                    'x'          => (float) ($markerCenterX - ($qrSize / 2)),
                    'y'          => (float) ($markerCenterY - ($qrSize / 2)),
                    'qr_size'    => $qrSize,
                ];

                $response = $this->TTE_Visible_QR($signData);

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
                $fallback = [
                    'nik'        => $params['nik'] ?? '',
                    'passphrase' => $params['passphrase'] ?? '',
                    'file_name'  => $fileName,
                    'verify'     => $params['verify'] ?? config('app.url'),
                    'page'       => 1,
                    'x'          => (float) ($params['fallback_x'] ?? ($isCamat ? 255 : 390)),
                    'y'          => (float) ($params['fallback_y'] ?? ($isCamat ? 85 : 230)),
                    'qr_size'    => (int) ($params['qr_size'] ?? 95),
                ];

                Log::warning('Marker TTE tidak ditemukan, memakai fallback koordinat SKTM.', [
                    'file' => $targetPath,
                    'role' => $role,
                    'fallback' => $fallback,
                    'last_error' => $lastError,
                ]);

                $fallbackResponse = $this->TTE_Visible_QR($fallback);
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

    public function generateTte($request, $kec = null, $no_reg = null)
    {
        $manager = new ImageManager(Driver::class);
        $image = $manager->create(600, 200)->fill('white');
        $image->drawRectangle(0, 0, function (RectangleFactory $rectangle) {
            $rectangle->size(600, 200); // width & height of rectangle
            $rectangle->background('white'); // background color of rectangle
            $rectangle->border('black', 5); // border color & size of rectangle
        });
        $image->place(public_path('img/logo.png'), 'left', 10);
        if ($kec == true) {
            // dd($kec);
            $image->text('Register : ' . $no_reg, 180, 25, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Medium.ttf');
                $font->size(20);
                $font->color('black');
            });
            $image->text('Ditandatangani secara elektronik oleh:', 180, 50, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Medium.ttf');
                $font->size(20);
                $font->color('black');
            });
            $image->text('Camat Kecamatan ' . ucfirst(strtolower($request->skpd->nama)) . ',', 180, 75, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Bold.ttf');
                $font->size(24);
                $font->color('black');
            });
            $image->text('Kota Kediri', 180, 100, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Bold.ttf');
                $font->size(24);
                $font->color('black');
            });
            $image->text($request->nama, 180, 150, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Bold.ttf');
                $font->size(24);
                $font->color('black');
            });
            $image->text('NIP. ' . $request->nip, 180, 175, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Medium.ttf');
                $font->size(24);
                $font->color('black');
            });
        } else {
            $image->text('Ditandatangani secara elektronik oleh:', 180, 50, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Medium.ttf');
                $font->size(20);
                $font->color('black');
            });
            $image->text('Lurah Kelurahan ' . ucfirst(strtolower($request->skpd->nama)) . ',', 180, 75, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Bold.ttf');
                $font->size(24);
                $font->color('black');
            });
            $image->text('Kota Kediri', 180, 100, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Bold.ttf');
                $font->size(24);
                $font->color('black');
            });
            $image->text($request->nama, 180, 150, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Bold.ttf');
                $font->size(24);

                $font->color('black');
            });
            $image->text('NIP. ' . $request->nip, 180, 175, function (FontFactory $font) {
                $font->filename('./fonts/KumbhSans-Medium.ttf');
                $font->size(24);
                $font->color('black');
            });
        }

        $imgName = 'tte_' . hash('sha256', now()) . '.png';
        $outputTte = 'img/' . $imgName;
        $image->toPng()->save($outputTte);
        $result = [
            'filename' => $imgName,
            'path' => $outputTte,
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
