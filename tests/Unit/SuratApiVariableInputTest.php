<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\SuratApiController;
use App\Models\Resident;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use ReflectionMethod;
use Tests\TestCase;

class SuratApiVariableInputTest extends TestCase
{
    private function expand(Request $request): void
    {
        (new ReflectionMethod(SuratApiController::class, 'mergeVariableInput'))
            ->invoke(new SuratApiController(), $request);
    }

    public function test_json_variable_is_expanded_without_overwriting_explicit_fields(): void
    {
        $request = Request::create('/api/surat/store', 'POST', [
            'jenis_surat' => 'suket',
            'keterangan' => 'Nilai dari field terpisah',
            'variable' => json_encode([
                'keterangan' => 'Nilai dari JSON',
                'kepada' => 'Kelurahan',
                'register_as' => 'perorangan',
            ]),
        ]);

        $this->expand($request);

        $this->assertSame('Nilai dari field terpisah', $request->input('keterangan'));
        $this->assertSame('Kelurahan', $request->input('kepada'));
        $this->assertSame('perorangan', $request->input('register_as'));
        $this->assertFalse($request->exists('variable'));

        $controller = new SuratApiController();
        $allInput = (new ReflectionMethod($controller, 'buildAllInput'))
            ->invoke($controller, $request, new Resident(), 'suket');
        $storedVariable = (new ReflectionMethod($controller, 'cleanInternalPayload'))
            ->invoke($controller, $allInput);

        $this->assertSame('Nilai dari field terpisah', $storedVariable['keterangan']);
        $this->assertArrayNotHasKey('kepada', $storedVariable);
        $this->assertArrayNotHasKey('variable', $storedVariable);
    }

    public function test_invalid_json_is_rejected(): void
    {
        $request = Request::create('/api/surat/store', 'POST', ['variable' => '{invalid']);

        $this->expectException(ValidationException::class);
        $this->expand($request);
    }

    public function test_main_fields_inside_variable_are_rejected(): void
    {
        $request = Request::create('/api/surat/store', 'POST', [
            'nik' => '3571010101900001',
            'variable' => json_encode(['nik' => '0000000000000000']),
        ]);

        $this->expectException(ValidationException::class);
        $this->expand($request);
    }

    public function test_existing_flat_fields_remain_supported(): void
    {
        $request = Request::create('/api/surat/store', 'POST', [
            'keterangan' => 'Format lama',
        ]);

        $this->expand($request);

        $this->assertSame('Format lama', $request->input('keterangan'));
    }
}
