@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container mt-2">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header border-0 pt-3 pb-2" style="background: #AEA07A; border-radius: 1rem 1rem 0 0">
                        <h5 class="my-3 fw-bold text-white text-center" style="letter-spacing: .5px">{{ $title }}</h5>
                    </div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('suket.store') }}">
                            @csrf

                            <div class="row" style="min-height: 500px;">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-1 shadow-sm rounded-4" style="background: #fff; border-color: #AEA07A">
                                        <div class="card-body">
                                            <div>
                                                <x-nosrt>
                                                    <x-slot:kd_jenis_surat></x-slot:kd_jenis_surat>
                                                    <x-slot:no_urut_surat>{{ $no_urut_surat }}</x-slot:no_urut_surat>
                                                    <x-slot:instansi_kode>{{ $currentUser->skpd->instansi_kode }}</x-slot:instansi_kode>
                                                    <x-slot:tgl_surat></x-slot:tgl_surat>
                                                </x-nosrt>
                                                <x-pribadi></x-pribadi>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-45 border-1 shadow-sm rounded-4" style="background: #fff; border-color: #AEA07A">
                                        <div class="card-body">
                                            <x-keterangan><x-slot:keterangan></x-slot:keterangan></x-keterangan>
                                            <x-kepada><x-slot:kepada></x-slot:kepada></x-kepada>
                                            @isset($var)
                                                @foreach ($var as $item)
                                                    <div class="row mb-3">
                                                        <label for="{{ $item }}"
                                                            class="col-md-3 col-form-label text-md-start ms-2">{{ $item }}</label>

                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                class="form-control @error('{{ $item }}') is-invalid @enderror"
                                                                name="<?= $item ?>" id="<?= $item ?>" placeholder="" />

                                                            @error('{{ $item }}')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endisset
                                            <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                                            <x-pengantar></x-pengantar>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-center mt-4">
                                        <a href="{{ url()->previous() ?? route('suket.index') }}" class="btn btn-danger py-2 px-4 me-2" style="border-radius: 8px">
                                            <i class="ri-close-line me-1"></i>
                                            <span>Batal</span>
                                        </a>
                                        <button type="submit" class="btn text-white py-2 px-4"
                                            style="background: #7896B2; border-radius: 8px;">
                                            <i class="ri-save-3-fill me-1"></i>
                                            <span>Update</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script type="text/javascript" src="{{ asset('assets/js/personal.js') }}"></script>
    @endpush
@endsection
