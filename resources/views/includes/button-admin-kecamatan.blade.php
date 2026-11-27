{{-- SKTM: Meja administrasi kecamatan (role 10), mengikuti komponen Action existing. --}}
<div class="d-flex gap-1 flex-wrap align-items-center justify-content-center">
    {!! $catatanHtml ?? '' !!}
    @if (strtolower((string) $jenis) === 'sktm' && (int) $status === 12)
        <button class="js-surat-action btn btn-danger btn-sm" data-url="{{ route('admin.surat.turunkan', $id) }}" data-action="turunkan" data-method="POST" data-confirm="Turunkan SKTM ke Lurah?" data-success="SKTM telah dikembalikan ke Lurah." title="Turunkan ke Lurah"><i class="ri-arrow-down-double-line"></i></button>
        <button type="button" class="btn btn-primary btn-sm btn-naik-surat" data-url="{{ route('admin.surat.naik', $id) }}" data-title="Naikkan SKTM ke Sekcam?" data-register-kecamatan="1"
                data-reg-kode="{{ ($defaultRegistrasiKecamatan['kode'] ?? '') ?: ($kodeJenisSurat ?? 'SKTM') }}"
                data-reg-nomor="{{ $defaultRegistrasiKecamatan['nomor'] ?? 1 }}"
                data-reg-existing="{{ $variableRow['no_register_kecamatan'] ?? ($variableRow['register_kecamatan'] ?? '') }}"
                data-reg-instansi="{{ $defaultRegistrasiKecamatan['instansi'] ?? '' }}"
                data-reg-tahun="{{ $defaultRegistrasiKecamatan['tahun'] ?? now('Asia/Jakarta')->format('Y') }}"
                title="Registrasi otomatis dan naikkan ke Sekcam"><i class="ri-arrow-up-double-line"></i></button>
    @endif
    <x-btnpreview :id="$id" :route="$route" />
</div>
