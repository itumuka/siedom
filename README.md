# Siedom

Siedom adalah aplikasi web berbasis Laravel yang dirancang untuk manajemen sistem informasi akademik, khususnya untuk mengelola data mahasiswa, registrasi, dan administrasi. Aplikasi ini menyediakan antarmuka yang mudah digunakan untuk mengelola pengguna, mahasiswa, dan data terkait lainnya.

## Fitur Utama

- **Manajemen Mahasiswa**: Tambah, edit, dan hapus data mahasiswa.
- **Manajemen Registrasi**: Kelola proses registrasi mahasiswa baru.
- **Sistem Admin**: Panel admin untuk mengelola pengguna dan data sistem.
- **Autentikasi**: Sistem login dan registrasi dengan JWT.
- **API RESTful**: Endpoint API untuk integrasi dengan aplikasi lain.
- **Database Migrations**: Migrasi database untuk setup awal.

## Persyaratan Sistem

- PHP >= 8.1
- Composer
- Node.js & NPM (untuk frontend assets)
- MySQL atau database lain yang didukung Laravel
- Laravel Framework

## Instalasi

1. **Clone Repository**:
   ```bash
   git clone https://github.com/itumuka/siedom.git
   cd siedom
   ```

2. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   - Salin file `.env.example` ke `.env`:
     ```bash
     cp .env.example .env
     ```
   - Edit file `.env` dan atur konfigurasi database, JWT secret, dll.

4. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

5. **Jalankan Migrasi Database**:
   ```bash
   php artisan migrate
   ```

6. **Seed Database (Opsional)**:
   ```bash
   php artisan db:seed
   ```

7. **Build Assets**:
   ```bash
   npm run build
   ```

8. **Jalankan Server**:
   ```bash
   php artisan serve
   ```

Aplikasi akan berjalan di `http://localhost:8000`.

## Penggunaan

- Akses panel admin melalui `/admin`.
- Gunakan API endpoint di `/api` untuk integrasi.
- Lihat dokumentasi API di [Postman Collection] atau file terkait.

## Testing

Jalankan test dengan:
```bash
php artisan test
```

## Kontribusi

1. Fork repository ini.
2. Buat branch fitur baru (`git checkout -b feature/AmazingFeature`).
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`).
4. Push ke branch (`git push origin feature/AmazingFeature`).
5. Buat Pull Request.

## Lisensi

Proyek ini menggunakan lisensi MIT. Lihat file `LICENSE` untuk detail lebih lanjut.

## Kontak

Untuk pertanyaan atau dukungan, hubungi [developer] atau buat issue di repository ini.
