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
                <form id="tambahForm" method="POST" enctype="multipart/form-data" action="{{ route('skusaha.save') }}">
                    @csrf

                    <input type="hidden" name="_method">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <input type="hidden" id="nik" name="nik" value="{{ $nik }}">
                            <div class="row mb-3 align-items-md-center">
                                <label for="nip" class="col-md-3 col-form-label text-md-end">Jenis Surat
                                    Usaha</label>
                                <div class="col-md-8">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="register_as"
                                            id="flexRadioDefault1" value="kelurahan"
                                            onchange="handleChangeRegisterAs('kelurahan')" @checked(old('register_as', 'kelurahan') == 'kelurahan')>
                                        <label class="form-check-label" for="flexRadioDefault1">Kelurahan</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="register_as"
                                            id="flexRadioDefault2" value="luar"
                                            onchange="handleChangeRegisterAs('luar')" @checked(old('register_as') == 'luar')>
                                        <label class="form-check-label" for="flexRadioDefault2">Luar Kelurahan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="nama_usaha"
                                    class="col-md-3 col-form-label text-md-end">{{ __('Nama Usaha') }}</label>

                                <div class="col-md-8">
                                    <input id="nama_usaha" type="text"
                                        class="form-control @error('nama_usaha') is-invalid @enderror" name="nama_usaha"
                                        value="{{ old('nama_usaha') }}" autocomplete="nama_usaha">

                                    @error('nama_usaha')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="alamat_usaha"
                                    class="col-md-3 col-form-label text-md-end">{{ __('Alamat Usaha') }}</label>

                                <div class="col-md-8">
                                    <textarea class="form-control @error('alamat_usaha') is-invalid @enderror" id="alamat_usaha" name="alamat_usaha"
                                        autocomplete="alamat_usaha" autofocus>{{ old('alamat_usaha') }}</textarea>
                                    @error('alamat_usaha')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>


                            <x-kepada><x-slot:kepada></x-slot:kepada></x-kepada>
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
