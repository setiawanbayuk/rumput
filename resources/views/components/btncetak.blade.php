@php
    /**
     * Tombol Cetak/Download final.
     * Sengaja dibuat link langsung, bukan AJAX handleCetak(), karena endpoint
     * admin.surat.cetak mengembalikan response()->download()/file PDF,
     * bukan JSON. Jika dipanggil AJAX dengan dataType json, browser tidak akan
     * memunculkan download.
     */
    $cetakUrl = \Illuminate\Support\Facades\Route::has('admin.surat.cetak')
        ? route('admin.surat.cetak', $id)
        : url('/admin/surat/cetak/' . $id);
@endphp

<a href="{{ $cetakUrl }}"
   class="print btn btn-success btn-sm"
   data-bs-toggle="tooltip"
   data-bs-title="Cetak"
   title="Cetak / Download"
   target="_blank"
   rel="noopener">
    <i class="ri-printer-line"></i>
</a>
