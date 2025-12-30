<div align="center">

<!-- Animated Header -->
<img src="https://readme-typing-svg.demolab.com?font=JetBrains+Mono&size=28&duration=3000&pause=800&color=38B2AC&center=true&vCenter=true&width=900&lines=EventKampus+Pro;Digital+Campus+Event+Ecosystem;Built+with+Laravel+11+%7C+Filament+v3;Modern+%7C+Secure+%7C+Scalable" />

<br/>

<img src="https://raw.githubusercontent.com/andreasbm/readme/master/assets/lines/rainbow.png" width="100%"/>

<br/>

<!-- Badges -->
<a href="https://laravel.com">
  <img src="https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
</a>
<a href="https://filamentphp.com">
  <img src="https://img.shields.io/badge/Filament-v3-FACC15?style=for-the-badge&logo=laravel&logoColor=white"/>
</a>
<a href="https://tailwindcss.com">
  <img src="https://img.shields.io/badge/TailwindCSS-3-38B2AC?style=for-the-badge&logo=tailwindcss&logoColor=white"/>
</a>
<a href="https://www.mysql.com">
  <img src="https://img.shields.io/badge/MySQL-Database-00758F?style=for-the-badge&logo=mysql&logoColor=white"/>
</a>

<br/><br/>

<strong>🏛️ EventKampus Pro</strong>  
<p><i>Enterprise-Grade Digital Event Management System for Modern Campus</i></p>

</div>

---

## 🌐 Tentang Proyek

**EventKampus Pro** adalah sistem manajemen event kampus berbasis web yang dirancang untuk mengelola **seluruh siklus hidup kegiatan mahasiswa** secara digital dari publikasi event, pendaftaran peserta, validasi admin, hingga penerbitan **E-Ticket PDF otomatis**.

Project ini dibangun dengan pendekatan **clean architecture**, performa tinggi, dan siap dikembangkan ke skala institusi.

---

## 🚀 Fitur Unggulan

### 🛡️ Admin Panel (Filament v3)
- 🎛️ **Smart Event Engine**  
  Manajemen event lengkap dengan status: `Draft • Published • Closed`
- 💸 **Dynamic Pricing System**  
  Otomatis membedakan tiket **Gratis / Berbayar** (Rupiah format)
- ✅ **One-Click Validation**  
  Approve / Reject peserta langsung dari tabel
- 📊 **Realtime Analytics**  
  Statistik pendaftar & kuota tersisa

---

### 🎓 Mahasiswa Dashboard
- 🆔 **Academic Identity Security**  
  Registrasi berbasis NIM, Prodi, dan Jurusan
- 🎟️ **E-Ticket Wallet**  
  Riwayat event & status pendaftaran
- 📄 **Auto PDF Ticket Generator**  
  Tombol cetak tiket aktif setelah admin ACC
- 🖼️ **Profile Customization**  
  Upload avatar + image editor bawaan

---

### 🎨 Frontend & UX
- ⚡ **Responsive Event Showcase**
- ✨ **AOS Animation (Animate On Scroll)**
- 🔀 **Smart Role Redirect**
  - `/admin` → Admin Panel
  - `/mahasiswa` → User Dashboard

---

## 🧠 Arsitektur Sistem (Workflow)

```mermaid
flowchart LR
A[Admin Buat Event] --> B[Event Published]
B --> C[Mahasiswa Daftar]
C --> D[Status Pending]
D -->|Approve| E[Tiket Confirmed]
E --> F[Cetak Tiket PDF]
````

---

## 🧰 Tech Stack

| Layer       | Teknologi                |
| ----------- | ------------------------ |
| Backend     | Laravel 11 (PHP 8.2+)    |
| Admin Panel | Filament v3 (TALL Stack) |
| Frontend    | Blade + TailwindCSS      |
| Database    | MySQL                    |
| PDF Engine  | DomPDF                   |
| Security    | Sanctum + Middleware     |
| Icons       | Heroicons                |

---

## 📦 Instalasi (Development)

### 1️⃣ Clone Repository

```bash
git clone https://github.com/username/event-kampus.git
cd event-kampus
```

### 2️⃣ Install Dependency

```bash
composer install
npm install && npm run build
```

### 3️⃣ Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

> Atur database di file `.env`

### 4️⃣ Migrasi & Storage

```bash
php artisan migrate --seed
php artisan storage:link
```

### 5️⃣ Jalankan Server

```bash
php artisan serve
```

---

## 🔑 Akun Testing

| Role      | Email                                     | Password |
| --------- | ----------------------------------------- | -------- |
| Admin     | [admin@gmail.com](mailto:admin@gmail.com) | password |
| Mahasiswa | Registrasi via Web                        | -        |

🔗 Admin: [http://event-kampus.test/admin](http://event-kampus.test/admin)
🔗 Mahasiswa: [http://127.0.0.1:8000/mahasiswa](http://127.0.0.1:8000/mahasiswa)

---

## 🗺️ Roadmap

* [ ] Payment Gateway (Midtrans)
* [ ] QR Code Attendance
* [ ] Export Excel & PDF Report
* [ ] WhatsApp Notification API
* [ ] Role Panitia & Scanner

---

## 👨‍💻 Author

**Gempur Budi Anarki**
🧠 Lead System Architect & Backend Engineer

> “Build systems that scale, not projects that die.”

---

<div align="center">

<img src="https://raw.githubusercontent.com/andreasbm/readme/master/assets/lines/rainbow.png" width="100%"/>

⭐ Jangan lupa kasih star kalau project ini kepake
🚀 Built for campus. Ready for the future.

</div>