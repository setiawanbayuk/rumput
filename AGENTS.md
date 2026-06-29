# E-SUKET — Panduan untuk Cursor AI

Proyek Laravel 11 (E-SUKET) dengan desain **Glassmorphism + Teal Tosca**.

## Instruksi Persisten

Aturan detail ada di `.cursor/rules/`:

| Rule | Scope |
|------|-------|
| `rumput-project.mdc` | Selalu aktif — konteks domain, stack, layout |
| `glassmorphism-ui.mdc` | Blade, SCSS, CSS — panduan visual |

## Desain Singkat

- **Gaya:** Glassmorphism (blur, border putih tipis, shadow lembut)
- **Base:** Putih semi-transparan `rgba(255,255,255,0.6)`
- **Aksen:** Teal `#14b8a6` — tombol, link aktif, hover
- **Background:** Gradien lembut putih/abu + tosca (`rumput-glass-body`)
- **Tech:** Laravel Blade + Bootstrap 5.3 (SCSS/Vite)

## Perintah Dev

```bash
composer install
npm install
npm run dev          # Vite HMR
php artisan serve
```

## Saat Menambah/Mengubah UI

1. Gunakan class `glass-card`, `glass-navbar`, `rumput-glass-body`
2. Pakai CSS variables `--rumput-*` dari `resources/sass/_theme.scss`
3. Jangan hardcode warna gold/blue lama
4. Ikuti layout yang sudah ada (`main`, `warga`, `page`, `create`)
5. Sertakan Remix Icon (`ri-*`) untuk ikon
