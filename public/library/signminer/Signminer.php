<?php

class Signminer
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    // public function sign($filename = null, $coord = null, $path = null)
    // {
    //     if ($path == null) {
    //         $path = realpath('files');
    //     }


    //     $input  = $path . "/" . $filename;
    //     $output = $path . "/" . "signed_" . $filename;

    //     // $coord = $this->get_esign_coordinate($input);
    //     // $coord = ['status' => true, 'page' => 0, 'data' => [(float) 1, (float) 1]];

    //     if ($coord->status) {
    //         $command = "python " .
    //             APPPATH . "libraries/signminer/ttd.py " .
    //             $input . " " .
    //             $output . " " .
    //             ($coord->page) . " " . $coord->data[0] . " " . $coord->data[1];

    //         shell_exec($command);
    //     }

    //     while (!file_exists(realpath($input))) {
    //     }

    //     return $input;
    // }

    public function get_esign_coordinate($input, $flag)
    {
        try{
            $command = "python3 " . APPPATH . "libraries/signminer/coordinate.py" . " " . $input . " " . $flag."";
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
        } catch (\Throwable $th) {            
            return ['status' => false, 'message' => $th->getMessage()];
        }
    }
    // private function _pagecount_pdf($file)
    // {
    //     $pageCount = 0;
    //     if (file_exists($file)) {
    //         $pdf = new \setasign\Fpdi\Fpdi();
    //         $pageCount = $pdf->setSourceFile($file);
    //         return $pageCount;
    //     }
    //     return $pageCount;
    // }

    // private function _get_esign_coordinate()
    // {
    //     $command = "python " . APPPATH . "libraries/signminer/coordinate.py" . " " . realpath("") . "/data_tulis_surat/" . $this->session->userdata('id_user') . "/surat.pdf";
    //     $output = shell_exec($command);
    //     $data = explode(',', $output);
    //     $coord = [(float) $data[0], (float) $data[1]];

    //     return ['status' => true, 'data' => $coord];
    // }

    // public function sign($input, $output, $page, $coord)
    // {
    //     $command = "python " .
    //         APPPATH . "libraries/signminer/ttd.py " .
    //         realpath("") . "/data_tulis_surat/" . $this->session->userdata('id_user') . "/combined.pdf " .
    //         realpath("") . "/data_tulis_surat/" . $this->session->userdata('id_user') . "/signed.pdf " .
    //         ($page - 1) . " " . $coord[0] . " " . $coord[1];

    //     // echo $command; die();
    //     // $command = "python c:/xampp/htdocs/esurat/data_tulis_surat/_python/ttd.py C:/xampp/htdocs/esurat/data_tulis_surat/1440091/combined.pdf C:/xampp/htdocs/esurat/data_tulis_surat/1440091/output.pdf 382.2 256.408";
    //     $output = shell_exec($command);
    // }
}
