<?php

namespace Tests\Unit;

use App\Traits\GlobalFunction;
use App\Traits\GeneratePDF;
use App\Http\Controllers\Requests\EsignController;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;
use Tests\TestCase;

class TteImagePlacementTest extends TestCase
{
    public function test_generated_pdf_validation_checks_each_requested_tte_marker(): void
    {
        $controller = new class extends EsignController
        {
            public array $requestedMarkers = [];

            public function get_esign_coordinate($input, $flag)
            {
                $this->requestedMarkers[] = $flag;

                return ['status' => true, 'hasil' => [['page' => 1, 'data' => [1, 1, 2, 2]]]];
            }
        };

        (new \ReflectionMethod($controller, 'assertTteCoordinateMarkersInPdf'))
            ->invoke($controller, 'generated.pdf', ['tte_lurah', 'tte_camat']);

        $this->assertSame([
            '__TTE_LURAH_POSITION__',
            '__TTE_CAMAT_POSITION__',
        ], $controller->requestedMarkers);
    }

    public function test_generated_pdf_validation_fails_if_a_tte_marker_is_missing(): void
    {
        $controller = new class extends EsignController
        {
            public function get_esign_coordinate($input, $flag)
            {
                return ['status' => false, 'message' => 'marker tidak ditemukan'];
            }
        };

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('__TTE_LURAH_POSITION__');

        (new \ReflectionMethod($controller, 'assertTteCoordinateMarkersInPdf'))
            ->invoke($controller, 'generated.pdf', ['tte_lurah']);
    }

    public function test_tte_placeholders_are_kept_as_hidden_coordinate_markers(): void
    {
        $templatePath = sys_get_temp_dir().'\\tte-template-'.uniqid('', true).'.docx';
        $outputPath = sys_get_temp_dir().'\\tte-markers-'.uniqid('', true).'.docx';
        $word = new PhpWord;
        $section = $word->addSection();
        $section->addText('${tte_lurah}');
        $section->addText('${tte_camat}');
        \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save($templatePath);

        $signer = new class
        {
            use GeneratePDF;
        };
        $data = [
            'tte_lurah' => '',
            'tte_camat' => '',
            '__tte_coordinate_markers' => ['tte_lurah', 'tte_camat'],
        ];

        try {
            $processor = new TemplateProcessor($templatePath);
            (new \ReflectionMethod($signer, 'fillTemplateValuesSafely'))->invoke($signer, $processor, $data);
            (new \ReflectionMethod($signer, 'applyTteImagePlaceholders'))->invoke($signer, $processor, $data);
            $this->assertSame([], $processor->getVariables());
            $processor->saveAs($outputPath);

            (new \ReflectionMethod($signer, 'hideTteCoordinateMarkers'))->invoke(
                $signer,
                $outputPath,
                $data['__tte_coordinate_markers']
            );

            $zip = new ZipArchive;
            $this->assertTrue($zip->open($outputPath));
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            $this->assertIsString($xml);
            foreach (['__TTE_LURAH_POSITION__', '__TTE_CAMAT_POSITION__'] as $marker) {
                $this->assertStringContainsString($marker, $xml);
            }

            $document = new \DOMDocument;
            $this->assertTrue($document->loadXML($xml));
            $xpath = new \DOMXPath($document);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $runs = $xpath->query('//w:r');
            $hiddenMarkers = [];

            foreach ($runs as $run) {
                $text = '';
                foreach ($run->getElementsByTagNameNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 't') as $textNode) {
                    $text .= $textNode->textContent;
                }

                if (in_array($text, ['__TTE_LURAH_POSITION__', '__TTE_CAMAT_POSITION__'], true)) {
                    $color = $xpath->query('./w:rPr/w:color[@w:val="FFFFFF"]', $run)->item(0);
                    $this->assertNotNull($color);
                    $fontSize = $xpath->query('./w:rPr/w:sz[@w:val="2"]', $run)->item(0);
                    $this->assertNotNull($fontSize);
                    $hiddenMarkers[] = $text;
                }
            }

            $this->assertCount(2, $hiddenMarkers);
        } finally {
            @unlink($templatePath);
            @unlink($outputPath);
        }
    }

    public function test_missing_required_coordinate_marker_fails_pdf_generation(): void
    {
        $templatePath = sys_get_temp_dir().'\\tte-template-'.uniqid('', true).'.docx';
        $outputPath = sys_get_temp_dir().'\\tte-markers-'.uniqid('', true).'.docx';
        $word = new PhpWord;
        $word->addSection()->addText('${tte_lurah}');
        \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007')->save($templatePath);

        $signer = new class
        {
            use GeneratePDF;
        };
        $data = [
            'tte_lurah' => '',
            'tte_camat' => '',
            '__tte_coordinate_markers' => ['tte_lurah', 'tte_camat'],
        ];

        try {
            $processor = new TemplateProcessor($templatePath);
            (new \ReflectionMethod($signer, 'applyTteImagePlaceholders'))->invoke($signer, $processor, $data);
            $processor->saveAs($outputPath);

            $exception = null;
            try {
                (new \ReflectionMethod($signer, 'hideTteCoordinateMarkers'))->invoke(
                    $signer,
                    $outputPath,
                    $data['__tte_coordinate_markers']
                );
            } catch (\ReflectionException $e) {
                throw $e;
            } catch (\Throwable $e) {
                $exception = $e->getPrevious() ?? $e;
            }

            $this->assertInstanceOf(\RuntimeException::class, $exception);
            $this->assertStringContainsString('__TTE_CAMAT_POSITION__', $exception->getMessage());
        } finally {
            @unlink($templatePath);
            @unlink($outputPath);
        }
    }

    public function test_combined_tte_image_places_its_qr_at_the_marker_center(): void
    {
        $sourcePath = tempnam(sys_get_temp_dir(), 'tte-source-');
        file_put_contents($sourcePath, '%PDF-test');
        $fileName = 'tte-position-'.uniqid('', true).'.pdf';
        $targetPath = storage_path('app/public/pdf/'.$fileName);

        $signer = new class
        {
            use GlobalFunction;

            public array $imageRequest = [];
            public ?string $coordinateMarker = null;

            public function get_esign_coordinate($input, $flag)
            {
                $this->coordinateMarker = $flag;

                return [
                    'status' => true,
                    'hasil' => [
                        ['page' => 1, 'data' => [150, 300, 250, 400]],
                    ],
                ];
            }

            public function TTE_Visible_Image(array $request)
            {
                $this->imageRequest = $request;

                return ['status' => true];
            }
        };

        try {
            $result = $signer->TTE_sign([
                'path' => $sourcePath,
                'file_name' => $fileName,
                'use_custom_image' => true,
                'image_path' => $sourcePath,
                'image_width' => 308,
                'image_height' => 75,
                'qr_loc' => '__TTE_LURAH_POSITION__',
                'allow_coordinate_fallback' => false,
            ]);

            $this->assertSame('success', $result['status']);
            $this->assertSame('__TTE_LURAH_POSITION__', $signer->coordinateMarker);
            $this->assertSame(162.5, $signer->imageRequest['x']);
            $this->assertSame(312.5, $signer->imageRequest['y']);
        } finally {
            if (file_exists($sourcePath)) {
                unlink($sourcePath);
            }
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
        }
    }
}
