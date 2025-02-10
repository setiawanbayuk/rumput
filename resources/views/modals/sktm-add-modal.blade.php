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
                <form id="tambahForm" method="POST" enctype="multipart/form-data" action="{{ route('sktm.save') }}">
                    @csrf

                    <input type="hidden" name="_method">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <input type="hidden" id="nik" name="nik" value="{{ $nik }}">
                            <div class="row mb-3 align-items-md-center">
                                <label for="nip" class="col-md-3 col-form-label text-md-end">Jenis SKTM</label>
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
                                            id="flexRadioDefault2" value="sekolah"
                                            onchange="handleChangeRegisterAs('sekolah')"
                                            @checked(old('register_as') == 'sekolah')>
                                        <label class="form-check-label" for="flexRadioDefault2">Sekolah</label>
                                    </div>
                                </div>
                            </div>


                            <div id="input-sekolah" @class([
                                'd-none' => old('register_as', 'perorangan') == 'perorangan',
                            ])>

                                <div class="row mb-3">
                                    <label for="kepada"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Nama Anak') }}</label>

                                    <div class="col-md-8">
                                        <input id="kepada" type="text"
                                            class="form-control @error('kepada') is-invalid @enderror"
                                            name="kepada" value="{{ old('kepada') }}"
                                            autocomplete="kepada">

                                        @error('kepada')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="kepada_ttl"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Tempat/Tgl. Lahir') }}</label>

                                    <div class="col-md-5">
                                        <input id="kepada_tempat_lhr" type="text"
                                            class="form-control @error('kepada_tempat_lhr') is-invalid @enderror"
                                            name="kepada_tempat_lhr" value="{{ old('kepada_tempat_lhr') }}"
                                            autocomplete="kepada_tempat_lhr" autofocus>

                                        @error('kepada_tempat_lhr')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <input id="kepada_tgl_lhr" type="date"
                                            class="form-control @error('kepada_tgl_lhr') is-invalid @enderror"
                                            name="kepada_tgl_lhr" value="{{ old('kepada_tgl_lhr') }}" autocomplete="kepada_tgl_lhr"
                                            autofocus>

                                        @error('kepada_tgl_lhr')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="kepada_gender"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin Anak') }}</label>

                                    <div class="col-md-8">
                                        <select class="form-control @error('kepada_gender') is-invalid @enderror"
                                            id="kepada_gender" name="kepada_gender" data-placeholder="Jenis Kelamin">
                                        </select>
                                        @error('kepada_gender')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="kepada_hubungan"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Hubungan Dengan Wali') }}</label>

                                    <div class="col-md-8">
                                        <select class="form-control @error('kepada_hubungan') is-invalid @enderror"
                                            id="kepada_hubungan" name="kepada_hubungan" data-placeholder="Hubungan">
                                        <option value=""></option>
                                            <option value="Putranya">Putranya</option>
                                            <option value="Putrinya">Putrinya</option>
                                            <option value="Cucunya">Cucunya</option>
                                            <option value="Keluarga">Keluarga</option>
                                        </select>
                                        @error('kepada_hubungan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="kepada_sekolah"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Nama Sekolah') }}</label>

                                    <div class="col-md-8">
                                        <input id="kepada_sekolah" type="text"
                                            class="form-control @error('kepada_sekolah') is-invalid @enderror"
                                            name="kepada_sekolah" value="{{ old('kepada_sekolah') }}"
                                            autocomplete="kepada_sekolah">

                                        @error('kepada_sekolah')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="kepada_kelas"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Kelas/Semester') }}</label>

                                    <div class="col-md-8">
                                        <input id="kepada_kelas" type="text"
                                            class="form-control @error('kepada_kelas') is-invalid @enderror"
                                            name="kepada_kelas" value="{{ old('kepada_kelas') }}"
                                            autocomplete="kepada_kelas">

                                        @error('kepada_kelas')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="kepada_alamat_sekolah"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Alamat Sekolah') }}</label>

                                    <div class="col-md-8">
                                        <textarea class="form-control @error('kepada_alamat_sekolah') is-invalid @enderror" id="kepada_alamat_sekolah" name="kepada_alamat_sekolah"
                                            autocomplete="kepada_alamat_sekolah" autofocus>{{ old('kepada_alamat_sekolah') }}</textarea>
                                        @error('kepada_alamat_sekolah')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                            <x-peruntukan><x-slot:peruntukan></x-slot:peruntukan></x-peruntukan>

                            <div class="row mb-3">
                                <label for="kategori"
                                    class="col-md-3 col-form-label text-md-end">{{ __('Kategori') }}</label>

                                <div class="col-md-8">
                                    <select class="form-control @error('kategori') is-invalid @enderror"
                                        id="kategori" name="kategori" data-placeholder="Kategori">
                                        <option value=""></option>
                                        <option value="DTKS">DTKS</option>
                                        <option value="Non DTKS">Non DTKS</option>
                                    </select>
                                    @error('kategori')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
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
