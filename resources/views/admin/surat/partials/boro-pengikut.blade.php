@php
    $initialBoroPengikut = $initialBoroPengikut ?? [];

    if (!is_array($initialBoroPengikut)) {
        $initialBoroPengikut = [];
    }

    $oldNamaPengikut = old('pengikut_nama');
    if (is_array($oldNamaPengikut)) {
        $oldNikPengikut = old('pengikut_nik', []);
        $oldTglPengikut = old('pengikut_tgl_lahir', []);
        $oldUsiaPengikut = old('pengikut_usia', []);
        $oldStatusPengikut = old('pengikut_status_kwn', []);
        $oldHubunganPengikut = old('pengikut_hubungan', []);
        $initialBoroPengikut = [];

        foreach ($oldNamaPengikut as $idx => $namaPengikut) {
            $initialBoroPengikut[] = [
                'nama' => $namaPengikut,
                'nik' => $oldNikPengikut[$idx] ?? '',
                'tgl_lahir' => $oldTglPengikut[$idx] ?? '',
                'umur' => $oldUsiaPengikut[$idx] ?? '',
                'usia' => $oldUsiaPengikut[$idx] ?? '',
                'status_kwn' => $oldStatusPengikut[$idx] ?? '',
                'status_kwn_nm' => $oldStatusPengikut[$idx] ?? '',
                'hubungan' => $oldHubunganPengikut[$idx] ?? '',
            ];
        }
    }

    $initialBoroPengikut = array_values($initialBoroPengikut);
    $boroStatusOptions = ['KAWIN', 'BELUM KAWIN', 'CERAI HIDUP', 'CERAI MATI'];
    $boroHubunganOptions = ['ISTRI', 'SUAMI', 'ANAK', 'ORANG TUA', 'SAUDARA', 'KELUARGA LAIN', 'LAINNYA'];
@endphp

<div class="row mb-4" id="boro-pengikut-wrapper">
    <div class="col-12">
        <div class="card border-0 shadow-sm boro-pengikut-card">
            <div class="card-header bg-white d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                <div>
                    <h6 class="mb-1 fw-bold text-dark">Data Diri Pengikut BORO</h6>
                    <div class="text-muted small">
                        Jumlah baris mengikuti isian <strong>Jumlah Pengikut</strong>. Kolom <strong>Usia</strong> diisi tanggal lahir, lalu umur dihitung otomatis.
                    </div>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                    Wajib sesuai jumlah pengikut
                </span>
            </div>

            <div class="card-body">
                @error('jumlah_pengikut')
                    <div class="alert alert-danger py-2 mb-3"><strong>{{ $message }}</strong></div>
                @enderror

                <div class="alert alert-info py-2 mb-3 small">
                    Form tabel dibuat lebar agar kolom <strong>Nama</strong> dan <strong>NIK</strong> tidak terpotong. Geser ke kanan/kiri jika layar tidak cukup.
                </div>

                <div class="table-responsive boro-table-scroll pb-2">
                    <table class="table table-bordered table-sm align-middle mb-1 boro-pengikut-table">
                        <thead class="table-light text-center">
                            <tr>
                                <th class="boro-col-no">NO</th>
                                <th class="boro-col-nama">NAMA</th>
                                <th class="boro-col-nik">NIK</th>
                                <th class="boro-col-usia">USIA / TANGGAL LAHIR</th>
                                <th class="boro-col-status">STATUS PERKAWINAN</th>
                                <th class="boro-col-hubungan">HUBUNGAN KELUARGA</th>
                            </tr>
                        </thead>
                        <tbody id="boro-pengikut-body"></tbody>
                    </table>
                </div>

                <small class="text-muted d-block mt-2">
                    Contoh: jika Jumlah Pengikut diisi 3, maka 3 baris data pengikut wajib diisi lengkap.
                </small>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .boro-pengikut-card {
        border-radius: 14px;
        overflow: hidden;
    }

    .boro-table-scroll {
        width: 100%;
        overflow-x: auto;
    }

    .boro-pengikut-table {
        min-width: 1280px;
        table-layout: fixed;
    }

    .boro-pengikut-table th,
    .boro-pengikut-table td {
        vertical-align: middle !important;
        white-space: normal;
    }

    .boro-col-no { width: 70px; }
    .boro-col-nama { width: 330px; }
    .boro-col-nik { width: 230px; }
    .boro-col-usia { width: 220px; }
    .boro-col-status { width: 220px; }
    .boro-col-hubungan { width: 210px; }

    .boro-pengikut-table .form-control,
    .boro-pengikut-table .form-select {
        min-height: 38px;
        font-size: 14px;
    }

    .boro-age-text {
        display: block;
        margin-top: 4px;
        font-size: 12px;
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const countInput = document.getElementById('jumlah_pengikut');
    const tableBody = document.getElementById('boro-pengikut-body');
    if (!countInput || !tableBody) return;

    const initialRows = @json($initialBoroPengikut);
    const statusOptions = @json($boroStatusOptions);
    const hubunganOptions = @json($boroHubunganOptions);

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function normalize(value) {
        return String(value ?? '').toUpperCase().trim();
    }

    function normalizeHubungan(value) {
        const normalized = normalize(value);
        if (normalized === 'ANAK KANDUNG') return 'ANAK';
        if (normalized === 'KELUARGA') return 'KELUARGA LAIN';
        return normalized;
    }

    function calculateAge(dateValue) {
        if (!dateValue) return '';
        const birth = new Date(dateValue + 'T00:00:00');
        if (Number.isNaN(birth.getTime())) return '';

        const today = new Date();
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }

        return age >= 0 ? String(age) : '';
    }

    function optionHtml(options, selectedValue) {
        const selected = normalizeHubungan(selectedValue);
        let html = '<option value="">Pilih</option>';
        options.forEach(function (item) {
            const value = normalize(item);
            html += `<option value="${escapeHtml(value)}" ${selected === value ? 'selected' : ''}>${escapeHtml(value)}</option>`;
        });
        return html;
    }

    function collectRows() {
        const rows = [];
        tableBody.querySelectorAll('tr[data-boro-row]').forEach(function (row) {
            rows.push({
                nama: row.querySelector('[name="pengikut_nama[]"]')?.value || '',
                nik: row.querySelector('[name="pengikut_nik[]"]')?.value || '',
                tgl_lahir: row.querySelector('[name="pengikut_tgl_lahir[]"]')?.value || '',
                umur: row.querySelector('[name="pengikut_usia[]"]')?.value || '',
                usia: row.querySelector('[name="pengikut_usia[]"]')?.value || '',
                status_kwn_nm: row.querySelector('[name="pengikut_status_kwn[]"]')?.value || '',
                hubungan: row.querySelector('[name="pengikut_hubungan[]"]')?.value || '',
            });
        });
        return rows;
    }

    function buildRow(index, rowData) {
        const data = rowData || {};
        const birthDate = data.tgl_lahir || '';
        const age = calculateAge(birthDate) || data.usia || data.umur || '';
        const ageText = birthDate
            ? (age !== '' ? `Usia saat ini: ${escapeHtml(age)} tahun` : 'Tanggal lahir belum valid')
            : (age !== '' ? `Usia lama: ${escapeHtml(age)} tahun` : 'Pilih tanggal lahir');

        return `
            <tr data-boro-row="1">
                <td class="text-center fw-semibold">${index + 1}</td>
                <td>
                    <input type="text" class="form-control form-control-sm" name="pengikut_nama[]" value="${escapeHtml(data.nama || '')}" placeholder="Nama lengkap pengikut" required>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm boro-nik" name="pengikut_nik[]" value="${escapeHtml(data.nik || '')}" maxlength="16" inputmode="numeric" pattern="[0-9]{16}" placeholder="16 digit NIK" required>
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm boro-birth-date" name="pengikut_tgl_lahir[]" value="${escapeHtml(birthDate)}" required>
                    <input type="hidden" name="pengikut_usia[]" value="${escapeHtml(age)}">
                    <small class="text-muted boro-age-text">${ageText}</small>
                </td>
                <td>
                    <select class="form-select form-select-sm" name="pengikut_status_kwn[]" required>${optionHtml(statusOptions, data.status_kwn_nm || data.status_kwn || '')}</select>
                </td>
                <td>
                    <select class="form-select form-select-sm" name="pengikut_hubungan[]" required>${optionHtml(hubunganOptions, normalizeHubungan(data.hubungan || ''))}</select>
                </td>
            </tr>
        `;
    }

    function syncCountHidden() {
        const hidden = document.getElementById('surat_jml_pengikut');
        if (hidden) hidden.value = String(Math.max(0, parseInt(countInput.value || '0', 10) || 0));
    }

    function renderRows() {
        let count = parseInt(countInput.value || '0', 10);
        if (Number.isNaN(count) || count < 0) count = 0;
        countInput.value = count;

        const currentRows = collectRows();
        const sourceRows = currentRows.length ? currentRows : initialRows;
        let html = '';
        for (let i = 0; i < count; i++) {
            html += buildRow(i, sourceRows[i] || {});
        }

        tableBody.innerHTML = html || '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada pengikut. Isi Jumlah Pengikut untuk menampilkan baris data.</td></tr>';
        syncCountHidden();
    }

    countInput.addEventListener('input', renderRows);
    countInput.addEventListener('change', renderRows);

    tableBody.addEventListener('input', function (event) {
        if (event.target.classList.contains('boro-nik')) {
            event.target.value = event.target.value.replace(/[^0-9]/g, '').slice(0, 16);
        }

        if (event.target.classList.contains('boro-birth-date')) {
            const row = event.target.closest('tr');
            const age = calculateAge(event.target.value);
            const hidden = row.querySelector('[name="pengikut_usia[]"]');
            const text = row.querySelector('.boro-age-text');
            if (hidden) hidden.value = age;
            if (text) text.textContent = age !== '' ? `Usia saat ini: ${age} tahun` : 'Tanggal lahir belum valid';
        }
    });

    document.addEventListener('DOMContentLoaded', renderRows);
    renderRows();
})();
</script>
@endpush
