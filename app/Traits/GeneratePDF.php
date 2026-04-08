<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait GeneratePDF
{
    protected function buildQrImage(string $link): ?string
    {
        $code = time() . '_' . uniqid();
        $qrImageName = $code . '.png';
        $qrDir = storage_path('app/public/qr/');

        if (!File::exists($qrDir)) {
            File::makeDirectory($qrDir, 0777, true);
        }

        $qrPath = $qrDir . $qrImageName;

        try {
            $qrBinary = QrCode::format('png')->size(300)->generate($link ?: url('/'));
            file_put_contents($qrPath, $qrBinary);
            clearstatcache(true, $qrPath);

            if (
                file_exists($qrPath) &&
                filesize($qrPath) > 0 &&
                @getimagesize($qrPath) !== false
            ) {
                return $qrPath;
            }
        } catch (\Throwable $e) {
            // fallback ke PNG kecil valid
        }

        $validPng = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9WnWc6QAAAAASUVORK5CYII='
        );

        file_put_contents($qrPath, $validPng);
        clearstatcache(true, $qrPath);

        if (
            file_exists($qrPath) &&
            filesize($qrPath) > 0 &&
            @getimagesize($qrPath) !== false
        ) {
            return $qrPath;
        }

        return null;
    }

    protected function getSofficePath(): string
    {
        $candidates = [
            'C:\\Program Files\\LibreOffice\\program\\soffice.com',
            'C:\\Program Files\\LibreOffice\\program\\soffice.exe',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.com',
            'C:\\Program Files (x86)\\LibreOffice\\program\\soffice.exe',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        throw new \Exception('LibreOffice / soffice tidak ditemukan.');
    }

    protected function ensurePdfFolders(): void
    {
        $dirs = [
            storage_path('app/public/doc/'),
            storage_path('app/public/pdf/'),
            storage_path('app/public/qr/'),
            storage_path('app/libreoffice-profile/'),
        ];

        foreach ($dirs as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0777, true);
            }
        }
    }

			protected function convertDocxToPdf(string $tempDocxPath, string $outputPdfPath, string $outputPdf): string
		{
			$sourcePdfPath = rtrim($outputPdfPath, '\\/') . DIRECTORY_SEPARATOR . pathinfo($tempDocxPath, PATHINFO_FILENAME) . '.pdf';
			$finalPdfPath  = rtrim($outputPdfPath, '\\/') . DIRECTORY_SEPARATOR . $outputPdf . '.pdf';
		
			if (File::exists($sourcePdfPath)) {
				File::delete($sourcePdfPath);
			}
		
			if (File::exists($finalPdfPath)) {
				File::delete($finalPdfPath);
			}
		
			$sofficePath = $this->getSofficePath();
			$profileDir = storage_path('app/libreoffice-profile');
		
			if (!File::exists($profileDir)) {
				File::makeDirectory($profileDir, 0777, true);
			}
		
			$quotedSoffice = '"' . $sofficePath . '"';
			$quotedOutDir  = '"' . rtrim($outputPdfPath, '\\/') . '"';
			$quotedDocx    = '"' . $tempDocxPath . '"';
			$userInstall   = 'file:///' . str_replace('\\', '/', $profileDir);
		
			$command =
				$quotedSoffice .
				' --headless --norestore --nofirststartwizard --nolockcheck' .
				' -env:UserInstallation="' . $userInstall . '"' .
				' --convert-to pdf:writer_pdf_Export --outdir ' . $quotedOutDir . ' ' . $quotedDocx . ' 2>&1';
		
			$output = [];
			$resultCode = 0;
			exec($command, $output, $resultCode);
		
			for ($i = 0; $i < 30; $i++) {
				clearstatcache(true, $sourcePdfPath);
		
				if (File::exists($sourcePdfPath) && filesize($sourcePdfPath) > 0) {
					break;
				}
		
				usleep(300000);
			}
		
			clearstatcache(true, $sourcePdfPath);
		
			if (!File::exists($sourcePdfPath) || filesize($sourcePdfPath) <= 0) {
				throw new \Exception(
					"Konversi PDF gagal. Result code: {$resultCode}\nCommand: {$command}\nOutput: " . implode("\n", $output)
				);
			}
		
			File::move($sourcePdfPath, $finalPdfPath);
		
			clearstatcache(true, $finalPdfPath);
		
			if (!File::exists($finalPdfPath) || filesize($finalPdfPath) <= 0) {
				throw new \Exception("PDF berhasil dibuat oleh LibreOffice, tetapi gagal dipindahkan ke nama akhir: {$finalPdfPath}");
			}
		
			return $finalPdfPath;
		}

    public function generatePdf($data, $templateFile, $outputPdf)
    {
        $templateProcessor = new TemplateProcessor($templateFile);

        foreach ($data as $key => $value) {
            $templateProcessor->setValue($key, is_array($value) ? '' : (string) ($value ?? ''));
        }

        $this->ensurePdfFolders();

        $qrPath = $this->buildQrImage($data['link'] ?? url('/'));
        if (
            $qrPath &&
            file_exists($qrPath) &&
            filesize($qrPath) > 0 &&
            @getimagesize($qrPath) !== false
        ) {
            $templateProcessor->setImageValue('qr', [
                'path' => $qrPath,
                'width' => 100,
                'height' => 100,
                'ratio' => true,
            ]);
        }

        $tempDocxPath = storage_path('app/public/doc/' . uniqid('', true) . '.docx');
        $outputPdfPath = storage_path('app/public/pdf/');
        $templateProcessor->saveAs($tempDocxPath);

        try {
            return $this->convertDocxToPdf($tempDocxPath, $outputPdfPath, $outputPdf);
        } finally {
            if (File::exists($tempDocxPath)) {
                File::delete($tempDocxPath);
            }
        }
    }

    public function generatePdfTable($data, $templateFile, $outputPdf)
    {
        $templateProcessor = new TemplateProcessor($templateFile);

        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $templateProcessor->setValue($key, (string) ($value ?? ''));
            }
        }

        $headerCellStyle = ['valign' => 'center'];
        $headerTextStyle = ['name' => 'Arial', 'color' => '000000', 'size' => 12, 'bold' => true];
        $centerAlignment = ['alignment' => 'center'];

        $header = new Table([
            'borderSize' => 8,
            'width' => 10600,
            'unit' => TblWidth::TWIP,
        ]);

        $header->addRow(null);
        $header->addCell(25, $headerCellStyle)->addText('No', $headerTextStyle, $centerAlignment);
        $header->addCell(120, $headerCellStyle)->addText('NAMA', $headerTextStyle, $centerAlignment);
        $header->addCell(100, $headerCellStyle)->addText('NIK', $headerTextStyle, $centerAlignment);
        $header->addCell(50, $headerCellStyle)->addText('USIA', $headerTextStyle, $centerAlignment);
        $header->addCell(100, $headerCellStyle)->addText('STATUS PERKAWINAN', $headerTextStyle, $centerAlignment);
        $header->addCell(75, $headerCellStyle)->addText('HUBUNGAN KELUARGA', $headerTextStyle, $centerAlignment);

        $templateProcessor->setComplexBlock('header', $header);

        $detailPengikut = $data['detail_pengikut'] ?? [];
        if (count($detailPengikut) > 0) {
            $templateProcessor->cloneBlock('block', count($detailPengikut), true, true);

            foreach ($detailPengikut as $index => $pengikut) {
                $table = new Table([
                    'borderSize' => 8,
                    'width' => 10600,
                    'unit' => TblWidth::TWIP,
                ]);

                $rowNum = $index + 1;
                $table->addRow(null, ['valign' => 'center']);
                $table->addCell(25, ['valign' => 'center'])->addText((string) $rowNum, null, $centerAlignment);
                $table->addCell(120, ['valign' => 'center'])->addText((string) ($pengikut['nama'] ?? ''), null, $centerAlignment);
                $table->addCell(100, ['valign' => 'center'])->addText((string) ($pengikut['nik'] ?? ''), null, $centerAlignment);
                $table->addCell(50, ['valign' => 'center'])->addText((string) ($pengikut['umur'] ?? ''), null, $centerAlignment);
                $table->addCell(100, ['valign' => 'center'])->addText((string) ($pengikut['status_kwn_nm'] ?? ''), null, $centerAlignment);
                $table->addCell(75, ['valign' => 'center'])->addText((string) ($pengikut['hubungan'] ?? ''), null, $centerAlignment);

                $templateProcessor->setComplexBlock('detail_pengikut#' . ($index + 1), $table);
            }
        }

        $this->ensurePdfFolders();

        $qrPath = $this->buildQrImage($data['link'] ?? url('/'));
        if (
            $qrPath &&
            file_exists($qrPath) &&
            filesize($qrPath) > 0 &&
            @getimagesize($qrPath) !== false
        ) {
            $templateProcessor->setImageValue('qr', [
                'path' => $qrPath,
                'width' => 100,
                'height' => 100,
                'ratio' => true,
            ]);
        }

        $tempDocxPath = storage_path('app/public/doc/' . uniqid('', true) . '.docx');
        $outputPdfPath = storage_path('app/public/pdf/');
        $templateProcessor->saveAs($tempDocxPath);

        try {
            return $this->convertDocxToPdf($tempDocxPath, $outputPdfPath, $outputPdf);
        } finally {
            if (File::exists($tempDocxPath)) {
                File::delete($tempDocxPath);
            }
        }
    }
}