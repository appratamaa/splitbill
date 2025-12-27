<div align="center">

# 🧾 Split Bill Optimizer
### Studi Komparasi Algoritma 0/1 Knapsack: Iteratif vs Rekursif

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Algorithm](https://img.shields.io/badge/Algorithm-0%2F1%20Knapsack-orange?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Final%20Project-success?style=for-the-badge)

<p align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=4F46E5&height=200&section=header&text=eksten.&fontSize=80&fontAlign=50&fontAlignY=35&desc=Optimasi%20Diskon%20Split%20Bill&descAlign=50&descAlignY=60&animation=fadeIn" alt="Header Image" />
</p>

Aplikasi web modern untuk menghitung pembagian tagihan (split bill) sekaligus mengoptimalkan penggunaan voucher diskon menggunakan pendekatan **Algoritma 0/1 Knapsack**. Proyek ini bertujuan membandingkan efisiensi performa (*running time*) antara pendekatan **Iteratif (Dynamic Programming)** dan **Rekursif (Memoization)**.

[Lihat Demo Aplikasi](https://eksten.koyeb.app/) · [Lapor Bug](https://github.com/appratamaa/splitbill/issues)

</div>

---

## 🏫 Identitas Akademik

Proyek ini disusun untuk memenuhi **Tugas Besar Mata Kuliah Analisis Kompleksitas Algoritma (AKA)**.

| Entitas | Detail |
| :--- | :--- |
| **Kelas** | IF-48-11 |
| **Kelompok** | **"eksten"** |
| **Dosen Pengampu** | **Dr. Z K ABDURAHMAN BAIZAL, S.Si., M.Kom.** |

### 👥 Anggota Tim

<div align="center">

| NIM | Nama Mahasiswa | Peran |
| :---: | :--- | :--- |
| **103012580051** | **ANDRE PUTRA PRATAMA, A.Md.Kom.** | *MAHASISWA* |
| **103012500135** | **ARDIYA MALIK JAELANI, A.Md.Kom.** | *MAHASISWA* |
| **103012500133** | **RAFIE NOVIANTO SUDRAJAT, A.Md.Kom.** | *MAHASISWA* |

</div>

---

## ✨ Fitur Utama

Aplikasi ini tidak hanya sekadar kalkulator, tetapi juga alat analisis algoritma:

* 📦 **Manajemen Produk:** Input dinamis untuk nama produk, harga, dan kuantitas.
* 👥 **Manajemen Anggota:** Tambah anggota dan tetapkan item yang mereka pesan secara spesifik.
* 🎫 **Optimasi Voucher (Knapsack):** Menentukan kombinasi voucher terbaik untuk mendapatkan diskon maksimal berdasarkan total belanja (Kapasitas W).
* 📊 **Analisis Real-time:** Grafik interaktif (Chart.js) yang membandingkan kecepatan eksekusi algoritma Iteratif vs Rekursif.
* 🧾 **Cetak Struk:** Generate struk tagihan resmi yang bisa dicetak atau diunduh sebagai gambar.
* 📱 **Responsif UI:** Tampilan yang optimal baik di Desktop, Tablet, maupun Mobile.

---

## 🧠 Analisis Algoritma

Kami menerapkan **0/1 Knapsack Problem** di mana:
* **Weight (W):** Syarat minimum belanja voucher.
* **Value (V):** Nominal potongan harga.
* **Capacity (C):** Total subtotal belanja pengguna.

### Perbandingan Pendekatan

| Metrik | Iteratif (Tabulasi) | Rekursif (Memoization) |
| :--- | :--- | :--- |
| **Kompleksitas Waktu** | $O(N \cdot W)$ | $O(N \cdot W)$ |
| **Kompleksitas Ruang** | $O(N \cdot W)$ (Tabel DP) | $O(N \cdot W)$ + Stack Overhead |
| **Karakteristik** | Stabil, menghindari *stack overflow*. | Kode lebih ringkas, namun berisiko pada input besar. |

---

## 🛠️ Teknologi yang Digunakan

* **Backend:** [Laravel 11](https://laravel.com) (PHP 8.2)
* **Frontend:** Blade Template, [Tailwind CSS](https://tailwindcss.com)
* **Interaktivitas:** JavaScript (Fetch API), [Chart.js](https://www.chartjs.org/), [AutoAnimate](https://auto-animate.formkit.com/)
* **Efek Visual:** Canvas Confetti, Glassmorphism UI
* **Deployment:** Koyeb (Docker Container)

---

## 🚀 Instalasi Lokal

Ingin menjalankan proyek ini di komputer Anda? Ikuti langkah berikut:

1.  **Clone Repositori**
    ```bash
    git clone [https://github.com/appratamaa/splitbill.git](https://github.com/appratamaa/splitbill.git)
    cd splitbill
    ```

2.  **Install Dependensi**
    ```bash
    composer install
    npm install
    ```

3.  **Setup Environment**
    Salin file `.env.example` menjadi `.env` dan atur database (SQLite direkomendasikan untuk simpel).
    ```bash
    cp .env.example .env
    php artisan key:generate
    touch database/database.sqlite
    ```
    *Pastikan `DB_CONNECTION=sqlite` di file .env*

4.  **Migrasi Database**
    ```bash
    php artisan migrate
    ```

5.  **Jalankan Server**
    ```bash
    npm run build
    php artisan serve
    ```
    Buka browser dan akses `http://localhost:8000`

---

<div align="center">

### Dibuat dengan ❤️ oleh Kelompok Eksten
*Telkom University - 2025*

</div>