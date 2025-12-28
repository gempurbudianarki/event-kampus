
# 🏛️ EventKampus Pro - Sistem Manajemen Ekosistem Event Digital

![Banner](https://raw.githubusercontent.com/andreasbm/readme/master/assets/lines/rainbow.png)

[![Laravel 11](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament V3](https://img.shields.io/badge/filament-v3-yellow?style=for-the-badge&logo=laravel&logoColor=white)](https://filamentphp.com)
[![TailwindCSS](https://img.shields.io/badge/tailwindcss-%2338B2AC.svg?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![MySQL](https://img.shields.io/badge/mysql-%2300f.svg?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)

**EventKampus Pro** adalah platform *enterprise-grade* untuk manajemen kegiatan mahasiswa. Sistem ini tidak hanya menangani pendaftaran, tapi juga mengelola siklus hidup event secara utuh—mulai dari publikasi, manajemen kuota, validasi admin, hingga penerbitan E-Ticket otomatis berbasis PDF.

---

## 🚀 Fitur Utama & Keunggulan Teknis

### 🛠️ Administrator Intelligence (Backend)
- **Smart Event Engine:** CRUD event lengkap dengan sistem status (*Draft, Published, Closed*).
- **Dynamic Pricing Logic:** Sistem otomatis mendeteksi tiket **GRATIS** atau **BERBAYAR** dengan format mata uang Rupiah otomatis.
- **One-Click Validation:** Admin dapat menyetujui atau menolak pendaftaran langsung dari tabel utama dengan konfirmasi instan.
- **Data Analytics:** Dashboard dengan widget statistik pendaftar dan ketersediaan kuota secara real-time.

### 🎓 Mahasiswa Center (User Dashboard)
- **Identity Security:** Registrasi ketat yang mewajibkan data akademik (NIM, Jurusan, Prodi).
- **E-Ticket Wallet:** Daftar seluruh tiket yang diikuti lengkap dengan status konfirmasi admin.
- **Automated PDF Generator:** Mahasiswa dapat mengunduh tiket PDF resmi setelah pendaftaran dikonfirmasi (ACC) oleh admin.
- **Profile Mastery:** Fitur kustomisasi profil termasuk upload avatar dengan *image editor* bawaan.

### 🌐 Frontend & UX
- **Responsive Showcase:** Tampilan kartu event modern dengan animasi **AOS (Animate On Scroll)**.
- **Traffic Controller:** Sistem *Smart Redirect* yang memisahkan jalur login Admin (`/admin`) dan Mahasiswa (`/mahasiswa`) secara cerdas.

---

## 🛠️ Tech Stack & Library
- **Core Framework:** Laravel 11 (PHP 8.2+)
- **Admin Panel:** Filament V3 (TALL Stack)
- **PDF Engine:** Barryvdh DomPDF
- **Security:** Laravel Sanctum & Filament Middleware Protection
- **Icons:** Heroicons

---

## ⚙️ Arsitektur Sistem (Workflow)
1. **Event Creation:** Admin membuat event, mengatur lokasi, kuota, dan harga melalui `/admin`.
2. **Discovery:** Mahasiswa menjelajahi event aktif di Landing Page.
3. **Registration:** Mahasiswa login dan mendaftar event (Data masuk ke tabel pendaftaran dengan status `Pending`).
4. **Verification:** Admin meninjau data mahasiswa, lalu melakukan **Approve** (Konfirmasi).
5. **Finalization:** Status tiket mahasiswa berubah menjadi `Confirmed`, memicu munculnya tombol **Cetak Tiket PDF**.

---

## 📦 Panduan Instalasi (Development)

### 1. Persiapan
```bash
git clone [https://github.com/username/event-kampus.git](https://github.com/username/event-kampus.git)
cd event-kampus
composer install
npm install && npm run build

```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate

```

*Sesuaikan database di `.env` (DB_DATABASE, DB_USERNAME, DB_PASSWORD).*

### 3. Migrasi & Storage Link

```bash
php artisan migrate --seed
php artisan storage:link

```

### 4. Menjalankan Aplikasi

```bash
php artisan serve

```

---

## 🔑 Akses Default (Testing)

| Role | Email | Password |
| --- | --- | --- |
| **Admin** | `admin@gmail.com` | `password` |
| **Mahasiswa** | *Lakukan registrasi di web* | - |

---

## 🗺️ Roadmap Masa Depan

* [ ] Integrasi **Payment Gateway (Midtrans)** untuk tiket berbayar.
* [ ] Sistem **Absensi QR Code** saat hari-H acara.
* [ ] Export laporan pendaftar ke format Excel.
* [ ] Notifikasi otomatis via WhatsApp API.

---

## 🤝 Kontribusi & Hak Cipta

Dikembangkan dengan penuh dedikasi oleh:

* **[Gempur Budi]** - *Lead System Architect & Backend*
