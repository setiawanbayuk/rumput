@extends('layouts.main')

@section('title', '{{ $title }}')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-transparent py-3 text-center fw-bold">{{ $title }}</div>

                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('suket.save') }}">
                            @csrf

                            <div class="row justify-content-center">
                                <div class="col-md-12">
                                    <input type="hidden" id="nik" name="nik" value="{{ $nik }}">
                                    <x-keterangan><x-slot:keterangan></x-slot:keterangan></x-keterangan>
                                    <x-kepada><x-slot:kepada></x-slot:kepada></x-kepada>
                                    <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                                    <x-pengantar></x-pengantar>
                                    <div class="row mb-0">
                                        <div class="col-md-8 offset-md-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-save-3-fill"></i>
                                                <span>Simpan</span>
                                            </button>
                                        </div>
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
