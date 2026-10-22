# Swagger Surat API

Buka `/dokumentasi-api/surat` tanpa login untuk melihat dokumentasi interaktif di aplikasi. Tautan juga tersedia di menu **Tools → Dokumentasi API Surat** bagi pengguna yang sudah login. Halaman ini memuat spesifikasi publik dari `/openapi/surat-api.json`. Aset Swagger UI ada di `public/vendor/swagger-ui`, sehingga halaman tidak memerlukan entry Vite tambahan.

Untuk memakai berkas di luar aplikasi, buka `public/openapi/surat-api.json` di [Swagger Editor](https://editor.swagger.io/) dengan **File → Import file**.

Semua operasi memakai base path `/api` dan token Sanctum. Isi **Authorize** dengan bearer token dari endpoint login aplikasi. Untuk `POST /surat/store` dan endpoint revisi, gunakan `multipart/form-data`; `pengantar` adalah gambar JPG/PNG maksimal 2 MiB.

Pada `POST /surat/store` untuk pengajuan baru, kirim `jenis_surat`, `nik`, `peruntukan`, dan berkas gambar `pengantar` sebagai multipart. Field lain mengikuti respons `/surat/form-config?jenis_surat={kode}` dan dapat dikirim dalam satu field teks `variable` berisi object JSON, misalnya `{"keterangan":"Untuk administrasi","kepada":"Kelurahan"}`. Controller mengambil `kepada` untuk kolom utama dan menyimpan field tambahan lain ke JSON `variable`. Format lama yang mengirim field tambahan satu per satu tetap didukung. Endpoint revisi menerima `pengantar` baru secara opsional.

Sumber kebenaran spesifikasi ini adalah `routes/api.php` dan `app/Http/Controllers/Api/SuratApiController.php`. Bila perilaku controller berubah, perbarui berkas OpenAPI ini bersama perubahan tersebut.
