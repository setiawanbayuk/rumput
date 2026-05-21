@extends('layouts.main')

@section('title', 'Upload Template Surat')

@section('content')
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent py-3 fw-bold">Upload / Ganti Template Custom</div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <strong>Penting:</strong> upload DOCX di sini akan menjadi template aktif untuk kelurahan ini dan jenis surat yang dipilih. Jangan hapus placeholder penting seperti <code>${qr}</code> untuk TTE Lurah dan <code>[[qr_camat]]</code> untuk TTE Camat SKTM.
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" enctype="multipart/form-data" action="{{ route('template.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Nama Template</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                            @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="jenis" class="form-label fw-semibold">Jenis Surat</label>
                            <select class="form-select @error('jenis') is-invalid @enderror" id="jenis" name="jenis" required>
                                <option value="">- Pilih Jenis Surat -</option>
                                <option value="skbn">Surat Keterangan Belum Menikah</option>
                                <option value="skboro">Surat Keterangan Boro</option>
                                <option value="skdom">Surat Keterangan Domisili</option>
                                <option value="skhsl">Surat Keterangan Penghasilan</option>
                                <option value="sktm_perorangan">Surat Keterangan Tidak Mampu Perorangan</option>
                                <option value="sktm_sekolah">Surat Keterangan Tidak Mampu Sekolah</option>
                                <option value="skusaha">Surat Keterangan Usaha</option>
                                <option value="suket">Surat Keterangan</option>
                            </select>
                            @error('jenis')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-12">
                            <label for="file" class="form-label fw-semibold">File Template Surat (.docx)</label>
                            <input class="form-control @error('file') is-invalid @enderror" type="file" id="file" name="file" accept=".docx" required>
                            @error('file')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('template.index') }}" class="btn btn-light">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ri-save-3-fill me-1"></i> Simpan Template
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
