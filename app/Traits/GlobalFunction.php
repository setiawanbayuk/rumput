<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

trait GlobalFunction
{
    //Esign
    function TTE_sign($data)
    {
        $flag_location = "[[" . $data['qr_loc'] . "]]";
        $x_widht = 30;
        $y_height = 25;

        if (isset($data['type'])) {
            if (preg_match('/image/i', $data['type'])) {
                $flag_location =  $data['qr_loc'];
                $x_widht = 25;
                $y_height = 20;
            }
        }

        $response = [];
        $meta = [];
        if ($data['is_visible']) {
            $meta = $this->get_esign_coordinate($data['path'], $flag_location);
            // dd($meta);
            if ($meta['status'] == true) {
                foreach ($meta['hasil'] as $hasil) {
                    $data['x'] = $hasil['data'][0] + (($hasil['data'][2] - $hasil['data'][0]) / 2) - $x_widht;
                    $data['y'] = $hasil['data'][1] + (($hasil['data'][3] - $hasil['data'][1]) / 2) - $y_height;
                    $data['page'] = $hasil['page'];
                }

                $response = $this->TTE_Visible($data);
                // dd($response);
            } else {
                $response = ['message' => 'Library PDF Miner Not Found / Prompt Not Executed / Format Flag TTE Tidak Sesuai', 'status' => false];
            }
        } else {
            $response = $this->TTE_Invisible($data);
        }

        // dd($response);
        // return $response['status'];

        if ($response['status'] == true) {
            if ((isset($meta['status']) && $meta['status'] == true && $data['is_visible'] == true) || $data['is_visible'] == false) {
                $result = array(
                    'responsStatus' => "Success",
                    'code'          => 200,
                    'responsDesc'   => "Tanda Tangan Berhasil",
                    'data'          => $response
                );
                // Log Activity
                // $log_activity = $result;
                // $log_activity['name'] = Auth::user()->name;
                // $log_activity['email'] = Auth::user()->email;
                // $log_activity['created_at'] = now();
                // activity('Response-Sign')->withProperties($log_activity)->log('Success');
            } else {
                $result = array(
                    'responsStatus' => "Warning",
                    'code'          => 201,
                    'responsDesc'   => "Tanda Tangan Berhasil, tetapi tag [[qr_here]] tidak ditemukan sehingga QR CODE gagal tergenerate!",
                    'data'          => $response
                );
                // Log Activity
                // $log_activity = $result;
                // $log_activity['name'] = Auth::user()->name;
                // $log_activity['email'] = Auth::user()->email;
                // $log_activity['created_at'] = now();
                // activity('Response-Sign')->withProperties($log_activity)->log('Error');
            }
        } else {
            $result = array(
                'responsStatus' => "Error",
                'code'          => 400,
                'responsDesc' => "Tanda Tangan Tidak Berhasil, " . $response['message']
            );
            // Log Activity
            // $log_activity = $result;
            // $log_activity['name'] = Auth::user()->name;
            // $log_activity['email'] = Auth::user()->email;
            // $log_activity['created_at'] = now();
            // activity('Response-Sign')->withProperties($log_activity)->log('Error');
        }

        return response()->json($result, 200);
    }

    // SignMiner
    public function get_esign_coordinate($input, $flag)
    {
        try {
            $command = "py " . public_path("library/signminer/coordinate.py") . " " . $input . " " . $flag . "";
            // dd($command);
            $output = shell_exec($command);
            if ($output) {
                $output = str_replace("\n", "", $output);
                $datas = array_filter(explode('!', $output));

                foreach ($datas as $data) {
                    $data = array_filter(explode(',', $data));
                    $coord = [(float) $data[0], (float) $data[1], (float) $data[2], (float) $data[3]];
                    $page = (int) $data[4] + 1;
                    $hasil[] = ['page' => $page, 'data' => $coord];
                }
                $result = ['status' => true, 'hasil' => $hasil];
                return $result;
            } else {
                $coord = [0, 0, 0, 0];
                $hasil[] = ['page' => 0, 'data' => $coord];
                $result = ['status' => false, 'hasil' => $hasil];
                return $result;
            }
        } catch (\Exception $th) {
            return ['status' => false, 'message' => $th->getMessage()];
        }
    }

    // Visible Sign
    public function TTE_Visible($request)
    {
        $data = array(
            'nik' => $request['nik'],
            'passphrase' => $request['passphrase'],
            'tampilan' => 'visible',
            'page' => $request['page'],
            'reason' => 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE',
            'location' => 'Kota Kediri',
            'image' => false,
            'linkQR' => $request['verify'],
            'xAxis' => $request['x'],
            'yAxis' => $request['y'],
            'width' => $request['x'] + 65,
            'height' => $request['y'] + 65,
        );

        if (isset($request['type'])) {
            if (preg_match('/image/i', $request['type'])) {
                $data['linkQR'] = '';
                $data['image'] = true;
                $data['width'] = $request['x'] + 40;
                $data['height'] =  $request['y'] + 40;
            }
        }
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $query = http_build_query($data);

        // dd(storage_path('app/public/pdf/' . $request['file_name']), false, stream_context_create($arrContextOptions));

        // dd(file_get_contents(public_path('assets/media/logos/' . $request['image_path'])));
        try {
            $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))
                ->asMultipart()
                ->attach('file', file_get_contents(storage_path('app/public/pdf/' . $request['file_name']), false, stream_context_create($arrContextOptions)), $request['file_name']);

            // dd($r);

            if (isset($request['type'])) {
                if (preg_match('/image/i', $request['type'])) {
                    $r = $r->attach('imageTTD', file_get_contents(public_path('assets/media/logos/' . $request['image_path']), false, stream_context_create($arrContextOptions)), $request['image_path']);
                }
            }
            $r = $r->post(env('APP_URL_TTE') . '/sign/pdf?' . $query);

            // dd($r->body(), $r->headers());

            if (!$r->json()) {
                $fp = fopen(storage_path('app/public/pdf/' . $request['file_name']), 'wb');
                fwrite($fp, $r);
                fclose($fp);
                return ['message' => 'Esign done successfully.', 'status' => true];
            } else {
                return ['message' => isset($r->json()['error']) ? $r->json()['error'] : "Kemungkinan Passphrase anda salah", 'status' => false];
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'status' => false];
        }
    }

    // InVisible Sign
    public function TTE_Invisible($request)
    {
        $data = array(
            'nik' => $request['nik'],
            'passphrase' => $request['passphrase'],
            'tampilan' => 'invisible',
            'reason' => 'Dokumen ini telah ditandatangani secara elektronik menggunakan sertifikat elektronik yang diterbitkan BSrE',
            'location' => 'Kota Kediri',
        );

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $query = http_build_query($data);

        try {
            $r = Http::withBasicAuth(env('ESIGN_USER'), env('ESIGN_PASS'))
                ->asMultipart()
                ->attach('file', file_get_contents(storage_path('app/public/pdf/' . $request['file_name']), false, stream_context_create($arrContextOptions)), $request['file_name'])
                ->post(env('APP_URL_TTE') . '/sign/pdf?' . $query);

            if (!$r->json()) {
                $fp = fopen(storage_path('app/public/pdf/' . $request['file_name']), 'wb');
                fwrite($fp, $r);
                fclose($fp);
                return ['message' => 'Esign done successfully.', 'status' => true];
            } else {
                return ['message' => isset($r->json()['error']) ? $r->json()['error'] : "Kemungkinan Passphrase anda salah", 'status' => false];
            }
        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'status' => false];
        }
    }
}
