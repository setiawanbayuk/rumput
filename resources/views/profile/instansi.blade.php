@extends('layouts.main')

@section('title', 'Profil Instansi')

@section('content')
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h3 class="fw-bold mb-1">{{ $title }}</h3>
                <div class="text-muted small">Data ini dipakai pada header dan informasi surat.</div>
            </div>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="ri-arrow-left-line me-1"></i> Kembali
            </a>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Data belum bisa disimpan.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.instansi.update') }}">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Instansi <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $skpd->nama) }}" required>
                            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kode Instansi</label>
                            <input type="text" name="instansi_kode" class="form-control @error('instansi_kode') is-invalid @enderror" value="{{ old('instansi_kode', $skpd->instansi_kode) }}">
                            @error('instansi_kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="instansi_telp" class="form-control @error('instansi_telp') is-invalid @enderror" value="{{ old('instansi_telp', $skpd->instansi_telp) }}">
                            @error('instansi_telp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fax</label>
                            <input type="text" name="instansi_fax" class="form-control @error('instansi_fax') is-invalid @enderror" value="{{ old('instansi_fax', $skpd->instansi_fax) }}">
                            @error('instansi_fax') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Instansi</label>
                            <input type="email" name="instansi_email" class="form-control @error('instansi_email') is-invalid @enderror" value="{{ old('instansi_email', $skpd->instansi_email) }}">
                            @error('instansi_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        @if ($hasKodePos)
                            <div class="col-md-6">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="instansi_kode_pos" class="form-control @error('instansi_kode_pos') is-invalid @enderror" value="{{ old('instansi_kode_pos', $skpd->instansi_kode_pos) }}">
                                @error('instansi_kode_pos') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="instansi_alamat" rows="3" class="form-control @error('instansi_alamat') is-invalid @enderror">{{ old('instansi_alamat', $skpd->instansi_alamat) }}</textarea>
                            @error('instansi_alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ri-save-3-line me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
@endsection
