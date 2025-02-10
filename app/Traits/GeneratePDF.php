<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use NcJoes\OfficeConverter\OfficeConverter;
use PhpOffice\PhpWord\TemplateProcessor;

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
}
