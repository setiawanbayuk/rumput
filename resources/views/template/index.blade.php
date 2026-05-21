@extends('layouts.main')

@section('title', 'Template Surat')

@section('content')
    @push('styles')
        <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap5.css" rel="stylesheet">
        <style>
            .template-hero { border:0; border-radius:24px; background:linear-gradient(135deg,#6b5b95,#feb236); color:#fff; box-shadow:0 18px 42px rgba(107,91,149,.22); }
            .tools-card { border:0; border-radius:20px; box-shadow:0 12px 32px rgba(0,0,0,.08); }
            .step-pill { background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.25); border-radius:999px; padding:.35rem .75rem; display:inline-flex; align-items:center; gap:.4rem; }
        </style>
    @endpush

    <div class="container-fluid">
        <div class="card template-hero mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between gap-3 align-items-center">
                    <div>
                        <div class="step-pill mb-3"><i class="ri-file-word-2-line"></i> Template DOCX</div>
                        <h3 class="fw-bold mb-2">{{ $title }}</h3>
                       
                    </div>
                    <div class="display-4 opacity-75"><i class="ri-file-edit-line"></i></div>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="alert alert-info">
            <strong>Cara kerja Template Surat:</strong> download template default/custom, edit file DOCX di Word tanpa menghapus placeholder penting seperti <code>${qr}</code>, <code>[[qr_camat]]</code>, nama, alamat, dan variabel surat lain. Setelah upload ulang, sistem menyimpan sebagai <b>custom template kelurahan</b>. Preview/cetak surat berikutnya untuk jenis surat itu akan memakai template custom terbaru. Jika custom dihapus, sistem kembali memakai template default.
        </div>

        <div class="d-flex gap-2 mt-3 mb-3">
            @if (auth()->user()->role_id == 1 || auth()->user()->role_id == 8 || auth()->user()->role_id == 9)
                <a class="btn btn-primary" href="{{ route('template.add') }}">
                    <i class="ri-add-fill me-2"></i>
                    <span>Upload / Ganti Template Custom</span>
                </a>
            @endif
            <button class="btn btn-secondary" onclick="reload()"><i class="ri-refresh-line me-1"></i>Reload</button>
        </div>

        <div class="card tools-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="tableTemplate" class="table table-hover align-middle" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>State</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.js"></script>
        <script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap5.js"></script>
        <script type="text/javascript">
            $(function() {
                $('#tableTemplate').DataTable({
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    scrollX: true,
                    ajax: "{{ route('template.index') }}",
                    columns: [
                        { data: 'name', name: 'name' },
                        { data: 'jenis', name: 'jenis' },
                        {
                            data: 'state',
                            render: function(data) {
                                if (data == 'default') return `<span class="badge bg-primary">DEFAULT</span>`;
                                return `<span class="badge bg-success">CUSTOM AKTIF</span>`;
                            },
                            orderable: false,
                            searchable: false
                        },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    order: [[0, 'desc']],
                    pageLength: 10,
                });
            });

            function reload() { $('#tableTemplate').DataTable().ajax.reload(null, false); }

            function handleHapus(e) {
                let url = "{{ route('template.hapus', ':id') }}".replace(':id', e);
                Swal.fire({
                    title: 'Hapus template custom?',
                    text: 'Jika dihapus, sistem kembali memakai template default.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: 'POST',
                            url: url,
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function(response) {
                                Swal.fire('Berhasil', response.message, 'success');
                                $('#tableTemplate').DataTable().ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                const response = xhr.responseJSON || { message: 'Template gagal dihapus.' };
                                Swal.fire('Tidak bisa dihapus', response.message, 'error');
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
