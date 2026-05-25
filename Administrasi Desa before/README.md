# NamaWeb — Sistem Layanan Administrasi Desa

Website layanan administrasi desa berbasis HTML, CSS, dan JavaScript murni (tanpa framework).

---

## 📁 Struktur File

```
namaweb/
├── index.html            → Landing page / Beranda utama
├── tentang.html          → Halaman Tentang Kami
├── kontak.html           → Halaman Kontak
├── login.html            → Halaman Login
├── register.html         → Halaman Daftar Akun
├── dashboard.html        → Dashboard Warga
├── pengajuan.html        → Form Pengajuan Surat
├── riwayat.html          → Riwayat Pengajuan
├── detail-surat.html     → Detail Satu Pengajuan
├── profil.html           → Profil Pengguna
├── dashboard-kades.html  → Dashboard Kepala Desa
├── css/
│   └── style.css         → Stylesheet utama (dengan komentar lengkap)
└── js/
    └── main.js           → JavaScript utama (dengan komentar lengkap)
```

---

## 🚀 Cara Menjalankan

1. **Buka langsung** file `index.html` di browser, ATAU
2. Gunakan **Live Server** di VS Code untuk pengalaman terbaik
3. Tidak memerlukan server khusus — semua berjalan di browser

---

## 📄 Halaman-Halaman

### Halaman Publik
| Halaman | File | Keterangan |
|---------|------|------------|
| Beranda | `index.html` | Landing page dengan hero, layanan, alur, FAQ, kontak |
| Tentang | `tentang.html` | Info tim, visi, misi |
| Kontak | `kontak.html` | Form pesan dan info kontak |

### Autentikasi
| Halaman | File | Keterangan |
|---------|------|------------|
| Login | `login.html` | Form masuk akun |
| Register | `register.html` | Form daftar akun baru |

### Dashboard (Warga)
| Halaman | File | Keterangan |
|---------|------|------------|
| Dashboard | `dashboard.html` | Ringkasan statistik & notifikasi |
| Pengajuan | `pengajuan.html` | Form pengajuan surat |
| Riwayat | `riwayat.html` | Tabel semua pengajuan |
| Detail | `detail-surat.html` | Detail satu pengajuan |
| Profil | `profil.html` | Data profil warga |

### Dashboard (Kepala Desa)
| Halaman | File | Keterangan |
|---------|------|------------|
| Dashboard Kades | `dashboard-kades.html` | Grafik & statistik semua pengajuan |

---

## 🎨 Desain

- **Warna utama**: #1a1a2e (biru tua gelap)
- **Warna aksen**: #e8c547 (kuning emas)  
- **Background**: #faf8f3 (krem hangat)
- **Font judul**: Playfair Display (Google Fonts)
- **Font teks**: DM Sans (Google Fonts)

---

## ⚙️ Kustomisasi

### Mengubah Nama Website
Cari `NamaWeb` di semua file HTML dan ganti dengan nama desa/website Anda.

### Mengubah Nomor Kontak
Di `index.html`, cari `08xx-xxxx-xxxx` dan ganti dengan nomor WhatsApp aktif.

### Menambah Jenis Surat
Di `pengajuan.html`, salin blok `<div class="kartu-form">` dan sesuaikan field-nya.

---

## 📝 Catatan

- Semua data bersifat **dummy/statis** — belum terhubung ke database
- Login disimulasikan: username + password apa saja dengan minimal 6 karakter akan berhasil
- Untuk integrasi backend, hubungkan fungsi di `js/main.js` ke API server Anda

---

© 2026 NamaWeb — Kelompok 5
