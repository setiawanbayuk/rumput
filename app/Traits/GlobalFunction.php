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
        try {
            if (!file_exists($params['path'])) {
                return ['status' => 'error', 'message' => 'File PDF tidak ditemukan.'];
            }

            $pdfBase64 = base64_encode(file_get_contents($params['path']));

            $imageBase64 = "";
            if (isset($params['image_path']) && file_exists(public_path($params['image_path']))) {
                $imageBase64 = base64_encode(file_get_contents(public_path($params['image_path'])));
            }
            // dd($imageBase64, $pdfBase64);
            $payload = [
                "nik" => $params['nik'],
                "passphrase" => $params['passphrase'],
                "signatureProperties" => [
                    [
                        "imageBase64" => $imageBase64,
                        "tampilan" => "VISIBLE",
                        "tag" => $params['qr_loc'] ?? "~",
                        "width" => 100,
                        "height" => 100,
                        "location" => "Kediri",
                        "reason" => "Dokumen Kelurahan Resmi",
                        "contactInfo" => "Pemerintah Kota Kediri"
                    ]
                ],
                "file" => [$pdfBase64]
            ];
            $client = new Client([
                'verify' => false,
                'timeout' => 60,
            ]);

            $response = $client->post(env("APP_URL_TTE") . '/v2/sign/pdf', [
                'auth' => [
                    env('ESIGN_USER'),
                    env('ESIGN_PASS'),
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            if (isset($result['file'][0])) {
                $decodedPdf = base64_decode($result['file'][0]);
                $savePath = 'public/pdf/' . $params['file_name'];
                Storage::put($savePath, $decodedPdf);

                return ['status' => 'success', 'message' => 'Esign Berhasil'];
            }

            return ['status' => 'error', 'message' => 'Respon BSrE tidak valid.'];
        } catch (RequestException $e) {
            // Tangkap error spesifik dari Guzzle (4xx atau 5xx)
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            Log::error('BSrE Guzzle Error: ' . $errorBody);

            return [
                'status' => 'error',
                'message' => 'Gagal Esign: ' . ($errorBody ?: 'Unknown Error')
            ];
        } catch (\Exception $e) {
            Log::error('TTE_sign Exception: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Sistem Error: ' . $e->getMessage()];
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
