<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\TemplateProcessor;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait GeneratePDF
{
    // Fungsi untuk membuat dokumen Word
    public function generatePdf($data, $templateFile, $outputPdf)
    {
        // Load the .docx template
        $templateProcessor = new TemplateProcessor($templateFile);

        // Replace placeholders in the template with actual data
        foreach ($data as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }
        $code = time();
        $qr = QrCode::format('png')->generate($data['link']);
        $qrImageName = $code . '.png';

        // simpan ke local storage
        Storage::put('public/qr/' . $qrImageName, $qr);

        $templateProcessor->setImageValue('qr', array('path' => storage_path('app/public/qr/' . $qrImageName), 'width' => 100, 'height' => 100, 'ratio' => true));

        // Define temporary paths for .docx and .pdf
        if (!File::exists(storage_path('app/public/doc/'))) {
            File::makeDirectory(storage_path('app/public/doc/'));
        }
        if (!File::exists(storage_path('app/public/pdf/'))) {
            File::makeDirectory(storage_path('app/public/pdf/'));
        }
        // Check if file pdf hasil exists
        if (File::exists(storage_path('app/public/pdf/' . $outputPdf . '.pdf'))) {
            File::delete(storage_path('app/public/pdf/' . $outputPdf . '.pdf'));
        }

        // Define temporary paths for .docx and .pdf
        $tempDocxPath = storage_path('app/public/doc/' . uniqid() . '.docx');
        $outputPdfPath = storage_path('app/public/pdf/');

        // Save the .docx file temporarily
        $templateProcessor->saveAs($tempDocxPath);

        $convert = new OfficeConverter($tempDocxPath, $outputPdfPath, 'soffice', false);
        $convert->convertTo($outputPdf . '.pdf');

        // Move the generated PDF to public storage for preview/download
        $publicPdfPath = storage_path('app/public/pdf/' . $outputPdf . '.pdf');
        // File::move(storage_path('app/' . $outputPdf . '.pdf'), $publicPdfPath);

        // Clean up temporary .docx file
        File::delete($tempDocxPath);

        // Return the PDF path for preview
        return $publicPdfPath;
    }

    // COnvert Word to PDF table
    public function generatePdfTable($data, $templateFile, $outputPdf)
    {
        // Load the .docx template
        $templateProcessor = new TemplateProcessor($templateFile);
        // Replace simple placeholders in the template with actual data
        foreach ($data as $key => $value) {
            // Cek apakah $value bukan array untuk menghindari data child
            if (!is_array($value)) {
                $templateProcessor->setValue($key, $value);
            }
        }

        $headerCellStyle = array('valign' => 'center');
        // Pengaturan font style untuk teks header
        $headerTextStyle = array('name' => 'Arial', 'color' => '000000', 'size' => 12, 'bold' => true);
        // Pengaturan paragraph style untuk rata tengah
        $centerAlignment = array('alignment' => 'center');

        $header = new Table(array('borderSize' => 8, 'width' => 10600, 'unit' => TblWidth::TWIP));
        $header->addRow(null);
        $header->addCell(25, $headerCellStyle)->addText('No', $headerTextStyle, $centerAlignment);
        $header->addCell(120, $headerCellStyle)->addText('NAMA', $headerTextStyle, $centerAlignment);
        $header->addCell(100, $headerCellStyle)->addText('NIK', $headerTextStyle, $centerAlignment);
        $header->addCell(50, $headerCellStyle)->addText('USIA', $headerTextStyle, $centerAlignment);
        $header->addCell(100, $headerCellStyle)->addText('STATUS PERKAWINAN', $headerTextStyle, $centerAlignment);
        $header->addCell(75, $headerCellStyle)->addText('HUBUNGAN KELUARGA', $headerTextStyle, $centerAlignment);

        $templateProcessor->setComplexBlock('header', $header);

        foreach ($data['detail_pengikut'] as $index => $pengikut) {
            // set count pemeriksaan
            $blockCount = count($data['detail_pengikut']);
            // Clone block berdasarkan kategori
            $templateProcessor->cloneBlock('block', $blockCount, true, true);

            // Membuat tabel dengan pengaturan border dan lebar
            $table = new Table(array('borderSize' => 8, 'width' => 10600, 'unit' => TblWidth::TWIP));

            // Menambahkan data detail pemeriksaan dengan perataan tengah
            $rowNum = $index + 1;
            $table->addRow(null, array('valign' => 'center'));
            $table->addCell(25, array('valign' => 'center'))->addText($rowNum, null, $centerAlignment);
            $table->addCell(120, array('valign' => 'center'))->addText($pengikut['nama'], null, $centerAlignment);
            $table->addCell(100, array('valign' => 'center'))->addText($pengikut['nik'], null, $centerAlignment);
            $table->addCell(50, array('valign' => 'center'))->addText($pengikut['umur'], null, $centerAlignment);
            $table->addCell(100, array('valign' => 'center'))->addText($pengikut['status_kwn_nm'], null, $centerAlignment);
            $table->addCell(75, array('valign' => 'center'))->addText($pengikut['hubungan'], null, $centerAlignment);

            // Mengatur complex block dalam template
            $templateProcessor->setComplexBlock('detail_pengikut#' . ($index + 1), $table);
        }

        $code = time();
        $qr = QrCode::format('png')->generate($data['link']);
        $qrImageName = $code . '.png';

        // simpan ke local storage
        Storage::put('public/qr/' . $qrImageName, $qr);

        $templateProcessor->setImageValue('qr', array('path' => storage_path('app/public/qr/' . $qrImageName), 'width' => 100, 'height' => 100, 'ratio' => true));

        // Define temporary paths for .docx and .pdf
        if (!File::exists(storage_path('app/public/doc/'))) {
            File::makeDirectory(storage_path('app/public/doc/'));
        }
        if (!File::exists(storage_path('app/public/pdf/'))) {
            File::makeDirectory(storage_path('app/public/pdf/'));
        }
        // Check if file pdf hasil exists
        if (File::exists(storage_path('app/public/pdf/' . $outputPdf . '.pdf'))) {
            File::delete(storage_path('app/public/pdf/' . $outputPdf . '.pdf'));
        }
        $tempDocxPath = storage_path('app/public/doc/' . uniqid() . '.docx');
        $outputPdfPath = storage_path('app/public/pdf/');

        // Save the .docx file temporarily
        $templateProcessor->saveAs($tempDocxPath);

        // Convert the .docx file to PDF using OfficeConverter (LibreOffice or MS Office)
        $convert = new OfficeConverter($tempDocxPath, $outputPdfPath, 'soffice', false);
        $convert->convertTo($outputPdf . '.pdf');

        // Define the public path to the generated PDF
        $publicPdfPath = storage_path('app/public/pdf/' . $outputPdf . '.pdf');

        // Clean up the temporary .docx file
        File::delete($tempDocxPath);

        // Return the PDF path for preview/download
        return $publicPdfPath;
    }
}
