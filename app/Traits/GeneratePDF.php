<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use ZipArchive;

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

    /**
     * Isi placeholder teks biasa, tetapi JANGAN menyentuh marker TTE.
     *
     * Penting:
     * - ${qr} dipakai sebagai patokan posisi TTE Lurah.
     * - [[qr_camat]] / ~camat~ dipakai sebagai patokan posisi TTE Camat.
     *
     * Jika key 'qr' ikut di-setValue kosong, marker ${qr} akan hilang dari DOCX/PDF.
     * Akibatnya proses TTE berikutnya tidak menemukan posisi barcode dan hasilnya kosong.
     */
    protected function fillTemplateValuesSafely(TemplateProcessor $templateProcessor, array $data): void
    {
        $reservedTteMarkers = [
            'qr',
            'qr_camat',
            // Placeholder khusus tabel BORO. Jangan di-setValue kosong,
            // karena akan diproses dengan setComplexBlock/cloneBlock di generatePdfTable().
            'header',
            'block',
            'detail_pengikut',
        ];

        foreach ($data as $key => $value) {
            if (in_array((string) $key, $reservedTteMarkers, true)) {
                continue;
            }

            if (is_array($value)) {
                continue;
            }

            $templateProcessor->setValue($key, (string) ($value ?? ''));
        }
    }



    /**
     * Gambar TTE yang dibuat sendiri ditempel di template Word,
     * sedangkan QR/barcode resmi tetap ditempel oleh BSrE pada marker ${qr}~ / ~camat~.
     * Placeholder ini sengaja dipisah agar hasilnya seperti format lama: QR bersebelahan dengan kartu TTE.
     */
    protected function applyTteImagePlaceholders(TemplateProcessor $templateProcessor, array $data): void
    {
        foreach (['tte_lurah', 'tte_camat'] as $placeholder) {
            $imageSpec = $data[$placeholder] ?? null;
            $path = null;
            $width = 225;
            $height = 75;
            $ratio = true;

            if (is_array($imageSpec)) {
                $path = $imageSpec['path'] ?? null;
                $width = (int) ($imageSpec['width'] ?? $width);
                $height = (int) ($imageSpec['height'] ?? $height);
                $ratio = (bool) ($imageSpec['ratio'] ?? true);
            } elseif (is_string($imageSpec)) {
                $path = $imageSpec;
            }

            if ($path && file_exists($path) && filesize($path) > 0 && @getimagesize($path) !== false) {
                try {
                    $templateProcessor->setImageValue($placeholder, [
                        'path' => $path,
                        'width' => $width,
                        'height' => $height,
                        'ratio' => $ratio,
                    ]);
                    continue;
                } catch (\Throwable $e) {
                    // Jika placeholder tidak ada di template tertentu, jangan gagalkan proses surat.
                }
            }

            try {
                $templateProcessor->setValue($placeholder, '');
            } catch (\Throwable $e) {
                // Abaikan template yang memang tidak mempunyai placeholder ini.
            }
        }
    }

    protected function cleanupGeneratedTteImages(array $data): void
    {
        $cleanupImages = $data['__cleanup_images'] ?? [];
        if (!is_array($cleanupImages)) {
            return;
        }

        foreach ($cleanupImages as $imagePath) {
            if (is_string($imagePath) && $imagePath !== '' && File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }
    }

    /**
     * Fallback otomatis untuk template yang belum punya placeholder gambar TTE.
     * Banyak template lama hanya punya marker ${qr}~ / ~camat~ untuk posisi barcode BSrE.
     * Method ini membuat copy DOCX sementara lalu menyisipkan ${tte_lurah}/${tte_camat}
     * di sebelah marker tersebut, sehingga Non-SKTM tetap menampilkan gambar TTE tanpa
     * harus mengganti semua template custom satu per satu.
     */
    protected function prepareTemplateForTteImages(string $templateFile, array $data): array
    {
        $needsLurah = $this->hasValidTteImageSpec($data['tte_lurah'] ?? null);
        $needsCamat = $this->hasValidTteImageSpec($data['tte_camat'] ?? null);

        if (!$needsLurah && !$needsCamat) {
            return [$templateFile, null];
        }

        if (!file_exists($templateFile)) {
            return [$templateFile, null];
        }

        $this->ensurePdfFolders();

        $tempTemplate = storage_path('app/public/doc/template_tte_' . uniqid('', true) . '.docx');
        if (!@copy($templateFile, $tempTemplate)) {
            return [$templateFile, null];
        }

        $zip = new ZipArchive();
        if ($zip->open($tempTemplate) !== true) {
            if (File::exists($tempTemplate)) {
                File::delete($tempTemplate);
            }
            return [$templateFile, null];
        }

        $xmlNames = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (preg_match('#^word/(document|header\d+|footer\d+)\.xml$#', $name)) {
                $xmlNames[] = $name;
            }
        }

        foreach ($xmlNames as $xmlName) {
            $xml = $zip->getFromName($xmlName);
            if (!is_string($xml) || $xml === '') {
                continue;
            }

            $originalXml = $xml;

            if ($needsLurah && !str_contains($xml, '${tte_lurah}')) {
                $xml = $this->injectTtePlaceholderAfterMarker($xml, '${qr}~', 'tte_lurah');
                $xml = $this->injectTtePlaceholderAfterMarker($xml, '${qr}', 'tte_lurah');
            }

            if ($needsCamat && !str_contains($xml, '${tte_camat}')) {
                $xml = $this->injectTtePlaceholderAfterMarker($xml, '~camat~', 'tte_camat');
                $xml = $this->injectTtePlaceholderAfterMarker($xml, '[[qr_camat]]', 'tte_camat');
                $xml = $this->injectTtePlaceholderAfterMarker($xml, '${qr_camat}', 'tte_camat');
            }

            if ($xml !== $originalXml) {
                $zip->addFromString($xmlName, $xml);
            }
        }

        $zip->close();

        return [$tempTemplate, $tempTemplate];
    }

    protected function hasValidTteImageSpec($imageSpec): bool
    {
        $path = null;

        if (is_array($imageSpec)) {
            $path = $imageSpec['path'] ?? null;
        } elseif (is_string($imageSpec)) {
            $path = $imageSpec;
        }

        return is_string($path)
            && $path !== ''
            && file_exists($path)
            && filesize($path) > 0
            && @getimagesize($path) !== false;
    }

    protected function injectTtePlaceholderAfterMarker(string $xml, string $marker, string $placeholder): string
    {
        if (str_contains($xml, '${' . $placeholder . '}')) {
            return $xml;
        }

        $needle = '<w:t>' . htmlspecialchars($marker, ENT_XML1) . '</w:t>';
        $plainNeedle = '<w:t>' . $marker . '</w:t>';
        $insert = '<w:t>' . htmlspecialchars($marker, ENT_XML1) . '     ${' . $placeholder . '}</w:t>';
        $plainInsert = '<w:t>' . $marker . '     ${' . $placeholder . '}</w:t>';

        if (str_contains($xml, $needle)) {
            return preg_replace('/' . preg_quote($needle, '/') . '/', $insert, $xml, 1) ?? $xml;
        }

        if (str_contains($xml, $plainNeedle)) {
            return preg_replace('/' . preg_quote($plainNeedle, '/') . '/', $plainInsert, $xml, 1) ?? $xml;
        }

        // Fallback untuk marker yang terpecah menjadi beberapa run oleh Word/WPS.
        // Tidak agresif agar tidak merusak XML template.
        return $xml;
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
        [$effectiveTemplateFile, $temporaryTemplateFile] = $this->prepareTemplateForTteImages($templateFile, $data);
        $templateProcessor = new TemplateProcessor($effectiveTemplateFile);

        $this->fillTemplateValuesSafely($templateProcessor, $data);
        $this->applyTteImagePlaceholders($templateProcessor, $data);

        $this->ensurePdfFolders();

        $qrPath = null;
        if (!empty($data['show_qr'])) {
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
            if (!empty($qrPath) && File::exists($qrPath)) {
                File::delete($qrPath);
            }
            if (!empty($temporaryTemplateFile) && File::exists($temporaryTemplateFile)) {
                File::delete($temporaryTemplateFile);
            }
            $this->cleanupGeneratedTteImages($data);
        }
    }

    protected function prepareBoroTemplateForTable(string $templateFile, array $data): array
    {
        $detailPengikut = $data['detail_pengikut'] ?? [];
        if (!is_array($detailPengikut) || count($detailPengikut) < 1 || !file_exists($templateFile)) {
            return [$templateFile, null];
        }

        $zipCheck = new ZipArchive();
        if ($zipCheck->open($templateFile) !== true) {
            return [$templateFile, null];
        }

        $existingXml = (string) $zipCheck->getFromName('word/document.xml');
        $zipCheck->close();

        // Kalau template sudah mempunyai marker tabel BORO, jangan ubah template.
        if (str_contains($existingXml, 'detail_pengikut') && str_contains($existingXml, 'header') && str_contains($existingXml, 'block')) {
            return [$templateFile, null];
        }

        $this->ensurePdfFolders();
        $tempTemplate = storage_path('app/public/doc/template_boro_table_' . uniqid('', true) . '.docx');
        if (!@copy($templateFile, $tempTemplate)) {
            return [$templateFile, null];
        }

        $zip = new ZipArchive();
        if ($zip->open($tempTemplate) !== true) {
            if (File::exists($tempTemplate)) {
                File::delete($tempTemplate);
            }
            return [$templateFile, null];
        }

        $xml = (string) $zip->getFromName('word/document.xml');
        if ($xml === '') {
            $zip->close();
            if (File::exists($tempTemplate)) {
                File::delete($tempTemplate);
            }
            return [$templateFile, null];
        }

        $insert = $this->boroTablePlaceholderXml();
        $position = strpos($xml, 'surat_jml_pengikut');

        if ($position !== false) {
            $beforePlaceholder = substr($xml, 0, $position);
            $tableStartBefore = strrpos($beforePlaceholder, '<w:tbl');
            $tableEndBefore = strrpos($beforePlaceholder, '</w:tbl>');
            $placeholderInsideTable = $tableStartBefore !== false && ($tableEndBefore === false || $tableEndBefore < $tableStartBefore);

            if ($placeholderInsideTable) {
                $tableEnd = strpos($xml, '</w:tbl>', $position);
                if ($tableEnd !== false) {
                    $tableEnd += strlen('</w:tbl>');
                    $xml = substr($xml, 0, $tableEnd) . $insert . substr($xml, $tableEnd);
                } else {
                    $xml = str_replace('</w:body>', $insert . '</w:body>', $xml);
                }
            } else {
                $paragraphEnd = strpos($xml, '</w:p>', $position);
                if ($paragraphEnd !== false) {
                    $paragraphEnd += strlen('</w:p>');
                    $xml = substr($xml, 0, $paragraphEnd) . $insert . substr($xml, $paragraphEnd);
                } else {
                    $xml = str_replace('</w:body>', $insert . '</w:body>', $xml);
                }
            }
        } else {
            $xml = str_replace('</w:body>', $insert . '</w:body>', $xml);
        }

        $zip->addFromString('word/document.xml', $xml);
        $zip->close();

        return [$tempTemplate, $tempTemplate];
    }

    protected function boroTablePlaceholderXml(): string
    {
        // Desain tabel pengikut BORO.
        // Jarak tabel dari baris "m. Pengikut" diatur oleh spacerBeforeTable.
        // Jika ingin tabel lebih turun lagi, naikkan w:after="360" menjadi 420/480.
        $spacerBeforeTable = '<w:p><w:pPr><w:spacing w:before="0" w:after="360"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:t xml:space="preserve"> </w:t></w:r></w:p>';
        $spacerAfterTable = '<w:p><w:pPr><w:spacing w:before="120" w:after="240"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr><w:r><w:t xml:space="preserve"> </w:t></w:r></w:p>';
        $paragraphProperties = '<w:pPr><w:spacing w:before="0" w:after="0"/><w:jc w:val="center"/><w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr></w:pPr>';
        $runProperties = '<w:rPr><w:rFonts w:ascii="Arial" w:hAnsi="Arial" w:cs="Arial"/><w:sz w:val="20"/><w:szCs w:val="20"/></w:rPr>';

        return $spacerBeforeTable
            . '<w:p>' . $paragraphProperties . '<w:r>' . $runProperties . '<w:t>${header}</w:t></w:r></w:p>'
            . '<w:p>' . $paragraphProperties . '<w:r>' . $runProperties . '<w:t>${block}</w:t></w:r></w:p>'
            . '<w:p>' . $paragraphProperties . '<w:r>' . $runProperties . '<w:t>${detail_pengikut}</w:t></w:r></w:p>'
            . '<w:p>' . $paragraphProperties . '<w:r>' . $runProperties . '<w:t>${/block}</w:t></w:r></w:p>'
            . $spacerAfterTable;
    }

    public function generatePdfTable($data, $templateFile, $outputPdf)
    {
        [$boroTemplateFile, $temporaryBoroTemplateFile] = $this->prepareBoroTemplateForTable($templateFile, $data);
        [$effectiveTemplateFile, $temporaryTemplateFile] = $this->prepareTemplateForTteImages($boroTemplateFile, $data);
        $templateProcessor = new TemplateProcessor($effectiveTemplateFile);

        $this->fillTemplateValuesSafely($templateProcessor, $data);
        $this->applyTteImagePlaceholders($templateProcessor, $data);

        $tableWidth = 10600;
        $cellWidths = [650, 3000, 2050, 850, 2150, 1900];
        $headerCellStyle = ['valign' => 'center'];
        $bodyCellStyle = ['valign' => 'center'];
        $headerTextStyle = ['name' => 'Arial', 'color' => '000000', 'size' => 9, 'bold' => true];
        $bodyTextStyle = ['name' => 'Arial', 'color' => '000000', 'size' => 9];
        $centerAlignment = ['alignment' => 'center'];

        $header = new Table([
            'borderSize' => 8,
            'borderColor' => '000000',
            'width' => $tableWidth,
            'unit' => TblWidth::TWIP,
        ]);

        $header->addRow(null);
        $header->addCell($cellWidths[0], $headerCellStyle)->addText('NO', $headerTextStyle, $centerAlignment);
        $header->addCell($cellWidths[1], $headerCellStyle)->addText('NAMA', $headerTextStyle, $centerAlignment);
        $header->addCell($cellWidths[2], $headerCellStyle)->addText('NIK', $headerTextStyle, $centerAlignment);
        $header->addCell($cellWidths[3], $headerCellStyle)->addText('USIA', $headerTextStyle, $centerAlignment);
        $header->addCell($cellWidths[4], $headerCellStyle)->addText('STATUS PERKAWINAN', $headerTextStyle, $centerAlignment);
        $header->addCell($cellWidths[5], $headerCellStyle)->addText('HUBUNGAN KELUARGA', $headerTextStyle, $centerAlignment);

        try {
            $templateProcessor->setComplexBlock('header', $header);
        } catch (\Throwable $e) {
            // Template custom lama mungkin tidak mempunyai marker tabel; jangan gagalkan generate surat.
        }

        $detailPengikut = $data['detail_pengikut'] ?? [];
        $detailPengikut = is_array($detailPengikut) ? array_values($detailPengikut) : [];

        if (count($detailPengikut) > 0) {
            try {
                $templateProcessor->cloneBlock('block', count($detailPengikut), true, true);
            } catch (\Throwable $e) {
                // Jika marker block tidak ada, proses PDF tetap lanjut.
            }

            foreach ($detailPengikut as $index => $pengikut) {
                $table = new Table([
                    'borderSize' => 8,
                    'borderColor' => '000000',
                    'width' => $tableWidth,
                    'unit' => TblWidth::TWIP,
                ]);

                $rowNum = $index + 1;
                $usia = $pengikut['umur'] ?? ($pengikut['usia'] ?? '');
                $status = $pengikut['status_kwn_nm'] ?? ($pengikut['status_kwn'] ?? '');
                $hubungan = strtoupper(trim((string) ($pengikut['hubungan'] ?? '')));
                if ($hubungan === 'ANAK KANDUNG') {
                    $hubungan = 'ANAK';
                } elseif ($hubungan === 'KELUARGA') {
                    $hubungan = 'KELUARGA LAIN';
                }

                $table->addRow(null, ['valign' => 'center']);
                $table->addCell($cellWidths[0], $bodyCellStyle)->addText((string) $rowNum, $bodyTextStyle, $centerAlignment);
                $table->addCell($cellWidths[1], $bodyCellStyle)->addText((string) ($pengikut['nama'] ?? ''), $bodyTextStyle, $centerAlignment);
                $table->addCell($cellWidths[2], $bodyCellStyle)->addText((string) ($pengikut['nik'] ?? ''), $bodyTextStyle, $centerAlignment);
                $table->addCell($cellWidths[3], $bodyCellStyle)->addText((string) $usia, $bodyTextStyle, $centerAlignment);
                $table->addCell($cellWidths[4], $bodyCellStyle)->addText((string) $status, $bodyTextStyle, $centerAlignment);
                $table->addCell($cellWidths[5], $bodyCellStyle)->addText($hubungan, $bodyTextStyle, $centerAlignment);

                try {
                    $templateProcessor->setComplexBlock('detail_pengikut#' . ($index + 1), $table);
                } catch (\Throwable $e) {
                    // Abaikan jika marker tidak tersedia pada template custom lama.
                }
            }
        } else {
            try {
                $templateProcessor->setValue('header', '');
                $templateProcessor->cloneBlock('block', 0, true, true);
            } catch (\Throwable $e) {
                try {
                    $templateProcessor->setValue('detail_pengikut', '');
                } catch (\Throwable $inner) {
                    // Abaikan.
                }
            }
        }

        $this->ensurePdfFolders();

        $qrPath = null;
        if (!empty($data['show_qr'])) {
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
            if (!empty($qrPath) && File::exists($qrPath)) {
                File::delete($qrPath);
            }
            foreach (array_unique(array_filter([$temporaryTemplateFile, $temporaryBoroTemplateFile])) as $temporaryFile) {
                if (File::exists($temporaryFile)) {
                    File::delete($temporaryFile);
                }
            }
            $this->cleanupGeneratedTteImages($data);
        }
    }
}
