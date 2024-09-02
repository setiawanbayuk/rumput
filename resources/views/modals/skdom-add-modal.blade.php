<!-- Button trigger modal -->
{{-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
    Launch demo modal
</button> --}}

<!-- Modal -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modalTambahLabel">Tambah Surat Keterangan Usaha</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tambahForm" method="POST" enctype="multipart/form-data" action="{{ route('skdom.save') }}">
                    @csrf

                    <input type="hidden" name="_method">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <input type="hidden" id="nik" name="nik" value="{{ $nik }}">
                            <div class="row mb-3 align-items-md-center">
                                <label for="nip" class="col-md-3 col-form-label text-md-end">Jenis Surat
                                    Domisili</label>
                                <div class="col-md-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="register_as"
                                            id="flexRadioDefault1" value="perorangan"
                                            onchange="handleChangeRegisterAs('perorangan')"
                                            @checked(old('register_as', 'perorangan') == 'perorangan')>
                                        <label class="form-check-label" for="flexRadioDefault1">Perorangan</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="register_as"
                                            id="flexRadioDefault2" value="perusahaan"
                                            onchange="handleChangeRegisterAs('perusahaan')"
                                            @checked(old('register_as') == 'perusahaan')>
                                        <label class="form-check-label" for="flexRadioDefault2">Perusahaan</label>
                                    </div>
                                </div>
                            </div>


                            <div id="input-perusahaan" @class([
                                'd-none' => old('register_as', 'perorangan') == 'perorangan',
                            ])>

                                <div class="row mb-3">
                                    <label for="nama_perusahaan"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Nama Perusahaan') }}</label>

                                    <div class="col-md-8">
                                        <input id="nama_perusahaan" type="text"
                                            class="form-control @error('nama_perusahaan') is-invalid @enderror"
                                            name="nama_perusahaan" value="{{ old('nama_perusahaan') }}"
                                            autocomplete="nama_perusahaan">

                                        @error('nama_perusahaan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="status_bangunan"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Status Bangunan') }}</label>

                                    <div class="col-md-8">
                                        <input id="status_bangunan" type="text"
                                            class="form-control @error('status_bangunan') is-invalid @enderror"
                                            name="status_bangunan" value="{{ old('status_bangunan') }}"
                                            autocomplete="status_bangunan">

                                        @error('status_bangunan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="jumlah_karyawan"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Jumlah Karyawan') }}</label>

                                    <div class="col-md-8">
                                        <input id="jumlah_karyawan" type="number"
                                            class="form-control @error('jumlah_karyawan') is-invalid @enderror"
                                            name="jumlah_karyawan" value="{{ old('jumlah_karyawan') }}"
                                            autocomplete="jumlah_karyawan">

                                        @error('jumlah_karyawan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="alamat_domisili"
                                    class="col-md-3 col-form-label text-md-end">{{ __('Alamat Domisili') }}</label>

                                <div class="col-md-8">
                                    <textarea class="form-control @error('alamat_domisili') is-invalid @enderror" id="alamat_domisili"
                                        name="alamat_domisili" autocomplete="alamat_domisili" autofocus>{{ old('alamat_domisili') }}</textarea>
                                    @error('alamat_domisili')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="kepada" id='labelKepada'
                                    class="col-md-3 col-form-label text-md-end">{{ __('Diberikan Kepada') }}</label>

                                <div class="col-md-8">
                                    <input id="kepada" type="text"
                                        class="form-control @error('kepada') is-invalid @enderror" name="kepada"
                                        value="{{ old('kepada') }}" autocomplete="kepada">

                                    @error('kepada')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>
                            <x-pengantar></x-pengantar>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
