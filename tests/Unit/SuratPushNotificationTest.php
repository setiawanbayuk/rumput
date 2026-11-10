<?php

namespace Tests\Unit;

use App\Jobs\SendSuratFinalPush;
use App\Models\SuratPengajuan;
use App\Services\EsuketSsoPushService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mockery;
use ReflectionMethod;
use Tests\TestCase;

class SuratPushNotificationTest extends TestCase
{
    public function test_gateway_receives_the_signed_surat_route_and_id(): void
    {
        config()->set('esuket_push.url', 'https://sso.kedirikota.go.id/api/push-notif');
        Http::fake(['sso.kedirikota.go.id/*' => Http::response(['success' => true], 200)]);

        $result = (new EsuketSsoPushService())->send('device-token', 'Surat telah ditandatangani.', [
            'route' => 'esuket-skbn-progress',
            'id' => '123',
            'state' => 'success',
        ]);

        $this->assertTrue($result['success']);
        Http::assertSent(fn ($request) => $request->url() === 'https://sso.kedirikota.go.id/api/push-notif'
            && $request['route'] === 'esuket-skbn-progress'
            && $request['param_id'] === '123'
            && $request['data']['id'] === '123'
            && $request['fcm_token'] === 'device-token');
    }

    public function test_route_is_read_from_jenis_surats(): void
    {
        $query = Mockery::mock();
        DB::shouldReceive('table')->once()->with('jenis_surats')->andReturn($query);
        $query->shouldReceive('where')->once()->with('jenis', 'skbn')->andReturnSelf();
        $query->shouldReceive('value')->once()->with('push_route')->andReturn('route-yang-diubah-admin');

        $method = new ReflectionMethod(SendSuratFinalPush::class, 'mobileRoute');
        $job = new SendSuratFinalPush(123);
        $this->assertSame('route-yang-diubah-admin', $method->invoke($job, 'SKBN'));
    }

    public function test_sktm_lurah_and_camat_signatures_are_both_eligible(): void
    {
        $method = new ReflectionMethod(SendSuratFinalPush::class, 'stillFinished');
        $surat = new SuratPengajuan(['jenis_surat' => 'sktm', 'status' => 4, 'file' => 'storage/pdf/signed.pdf']);

        $this->assertTrue($method->invoke(new SendSuratFinalPush(123, 'tte_lurah'), $surat));
        $this->assertFalse($method->invoke(new SendSuratFinalPush(123, 'tte_camat'), $surat));

        $surat->status = 9;
        $this->assertTrue($method->invoke(new SendSuratFinalPush(123, 'tte_camat'), $surat));
    }
}
