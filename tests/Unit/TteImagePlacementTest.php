<?php

namespace Tests\Unit;

use App\Traits\GlobalFunction;
use Tests\TestCase;

class TteImagePlacementTest extends TestCase
{
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

            public function get_esign_coordinate($input, $flag)
            {
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
            ]);

            $this->assertSame('success', $result['status']);
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
