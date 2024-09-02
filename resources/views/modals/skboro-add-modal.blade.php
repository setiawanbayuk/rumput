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
                <form id="tambahForm" method="POST" enctype="multipart/form-data" action="{{ route('skboro.save') }}">
                    @csrf

                    <input type="hidden" name="_method">
                    <div class="row justify-content-center">
                        <div class="col-md-12">
                            <input type="hidden" id="nik" name="nik" value="{{ $nik }}">
                            <div class="card-header bg-transparent mb-3 text-center fw-bold">Data Pengikut</div>
                            <div id="data_pengikut" name="data_pengikut">
                                <div class="row mb-3">
                                    <label for="pengikut_nik"
                                        class="col-md-3 col-form-label text-md-end">{{ __('NIK') }}</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <input type="number"
                                                class="form-control @error('pengikut_nik') is-invalid @enderror"
                                                id="pengikut_nik" name="pengikut_nik"
                                                placeholder="Masukkan 16 digit NIK" aria-label="NIK"
                                                aria-describedby="basic-addon2">
                                            {{-- <button type="button" class="input-group-text btn btn-subtle-primary"
                                                        onclick="checkNIK()">CARI</button> --}}

                                            @error('pengikut_nik')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="pengikut"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Nama') }}</label>

                                    <div class="col-md-8">
                                        <input id="pengikut" type="text"
                                            class="form-control @error('pengikut') is-invalid @enderror" name="pengikut"
                                            value="{{ old('pengikut') }}" autocomplete="pengikut">

                                        @error('pengikut')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="pengikut_gender"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Jenis Kelamin') }}</label>

                                    <div class="col-md-8">
                                        <select class="form-control @error('pengikut_gender') is-invalid @enderror"
                                            id="pengikut_gender" name="pengikut_gender"
                                            data-placeholder="Jenis Kelamin">
                                        </select>
                                        @error('pengikut_gender')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="pengikut_umur"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Umur') }}</label>

                                    <div class="col-md-8">
                                        <input id="pengikut_umur" type="number"
                                            class="form-control @error('pengikut_umur') is-invalid @enderror"
                                            name="pengikut_umur" value="{{ old('pengikut_umur') }}"
                                            autocomplete="pengikut_umur">

                                        @error('pengikut')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="pengikut_status_kwn"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Status Perkawinan') }}</label>

                                    <div class="col-md-8">
                                        <select class="form-control @error('pengikut_status_kwn') is-invalid @enderror"
                                            id="pengikut_status_kwn" name="pengikut_status_kwn"
                                            data-placeholder="Status Perkawinan">
                                        </select>
                                        @error('pengikut_status_kwn')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="pengikut_hubungan"
                                        class="col-md-3 col-form-label text-md-end">{{ __('Hubungan Keluarga') }}</label>

                                    <div class="col-md-8">
                                        <select
                                            class="form-control select2-hubungan @error('pengikut_hubungan') is-invalid @enderror"
                                            id="pengikut_hubungan" name="pengikut_hubungan" data-placeholder="Hubungan">
                                            <option value=""></option>
                                            <option value="KEPALA KELUARGA">KEPALA KELUARGA</option>
                                            <option value="SUAMI">SUAMI</option>
                                            <option value="ISTERI">ISTERI</option>
                                            <option value="ANAK">ANAK</option>
                                            <option value="MENANTU">MENANTU</option>
                                            <option value="CUCU">CUCU</option>
                                            <option value="ORANG TUA">ORANG TUA</option>
                                            <option value="MERTUA">MERTUA</option>
                                            <option value="FAMILI LAIN">FAMILI LAIN</option>
                                            <option value="PEMBANTU">PEMBANTU</option>
                                            <option value="LAINNYA">LAINNYA</option>
                                        </select>
                                        @error('pengikut_hubungan')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-8 offset-md-3">
                                    <button type="button" class="btn btn-success" name="tambah_pengikut"
                                        id ="tambah_pengikut"><i class="ri-user-add-fill"></i>
                                        <span>Tambah</span>
                                    </button>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>NIK</th>
                                                <th>Nama</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Umur</th>
                                                <th>Status</th>
                                                <th>Hubungan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody id="tabelbody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="card-header bg-transparent mb-3 text-center fw-bold">Bepergian / Boro Ke
                            </div>
                            <x-boro>
                                <x-slot:alamat_boro></x-slot:alamat_boro>
                                <x-slot:tgl_awal></x-slot:tgl_awal>
                                <x-slot:tgl_akhir></x-slot:tgl_akhir>
                            </x-boro>
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
