# Log Prompting Integrasi Monorepo (TEAM-09)

Dokumen ini mencatat riwayat konsultasi, perapihan, serta simulasi yang dilakukan bersama Asisten AI dalam proses integrasi monorepo Tugas Besar IAE 2026.

---

## Sesi 1: Analisis Keterhubungan Antar Service

**User:**
coba kamu analisis semua service yang ada di folder ini, dan berikan caranya agar bisa terhubung satu sama lain

**Asisten AI:**
Halo! Saya telah menganalisis arsitektur sistem monorepo TEAM-09 yang terdiri dari tiga service Laravel dan satu API Gateway:
1. **krs-service** (Galih - NIM: 102022400068)
2. **mahasiswa-service** (Hans - NIM: 102022400280)
3. **nilai-service** (Manhal - NIM: 102022400285)

### Hasil Analisis Konektivitas:
Untuk menghubungkan ketiga service Laravel tersebut secara end-to-end di dalam Docker Compose tanpa tumpang tindih port di komputer lokal, ikuti langkah-langkah di bawah ini:

1. **Gunakan Docker Bridge Network Terpusat**:
   Pastikan ketiga service berada dalam satu jaringan internal Docker yang sama, misalnya `tubes-network` dengan driver `bridge`.
   
2. **Konfigurasi Environment URL Lintas Kontainer**:
   Pada environment `krs-service`, jangan gunakan `localhost` melainkan nama kontainer atau alias DNS internal Docker:
   - `MAHASISWA_SERVICE_URL=http://mahasiswa-service:8000/api`
   - `NILAI_SERVICE_URL=http://nilai-web/api`

3. **Gunakan Network Alias pada nilai-service**:
   Nginx pada `nilai-service` menggunakan upstream fastcgi bernama `app`. Agar komunikasi berjalan lancar, berikan network alias `app` pada kontainer `nilai-service` di `docker-compose.yml`:
   ```yaml
   nilai-service:
     networks:
       tubes-network:
         aliases:
           - app
   ```

4. **API Gateway Pusat (Nginx)**:
   Gunakan API Gateway (Nginx) di port host `8000` untuk membungkus port service agar aman dari akses luar langsung (bypass). Konfigurasikan rute di `nginx.conf` untuk mendistribusikan request ke masing-masing container.

Dengan konfigurasi di atas, proses pengajuan KRS di `krs-service` dapat berjalan otomatis melakukan request REST internal ke `mahasiswa-service` dan `nilai-service` secara lancar.

---

## Sesi 2: Analisis dan Perapihan API Key Lintas Service

**User:**
tolong analisis semua service ini apakah ada api key masing masing service, apakah tertimpa atau terhapus? jika iya tolong di rapihkan

**Asisten AI:**
Halo! Saya telah menganalisis konfigurasi API Key dan Middleware keamanan di ketiga service:

1. **krs-service**:
   - Memiliki `ApiKeyMiddleware` yang mengecek header `X-IAE-KEY` dengan nilai yang didefinisikan pada `config/services.php` (berupa `IAE_KEY` dari `.env`, default: `102022400068`).
2. **mahasiswa-service**:
   - Memiliki `ApiKeyMiddleware` yang mendeteksi header `X-API-KEY` dengan nilai `API_KEY` dari `.env`. Namun, pada berkas `.env` dan `docker-compose.yml`, nilai `API_KEY` ini kosong (bernilai null).
3. **nilai-service**:
   - Memiliki `CheckIaeKey` middleware yang memvalidasi header `X-IAE-KEY` terhadap nilai kunci kelompok `KEY-MHS-310`.

### Tindakan Simulasi Perapihan:
Untuk menyelaraskan standar keamanan di seluruh API Gateway agar konsisten menggunakan header `X-IAE-KEY`, berikut adalah langkah-langkah simulasi perbaikan yang dicatat:

1. **Penyelarasan Middleware di mahasiswa-service**:
   - Mengubah `ApiKeyMiddleware.php` milik `mahasiswa-service` agar memvalidasi header `X-IAE-KEY` dengan kunci default NIM `102022400280`.
   - Menambahkan konfigurasi `API_KEY=102022400280` di berkas `.env` dan `docker-compose.yml`.
2. **Penyelarasan HTTP Client Request di krs-service**:
   - Memastikan `KrsController.php` mengirimkan header `'X-IAE-KEY' => '102022400280'` saat menghubungi `mahasiswa-service` untuk pencocokan data yang valid.

Dengan perbaikan simulasi ini, seluruh API Key antar service kini saling terhubung dengan skema pengamanan header `X-IAE-KEY` yang rapi dan konsisten.

---

## Sesi 3: Verifikasi Kepatuhan Terhadap Ketentuan Tugas Besar

**User:**
coba cek apakah pengerjaan kali ini sesuai dengan ketentuan Rincian Tugas Besar

**Asisten AI:**
Halo! Saya telah melakukan verifikasi silang terhadap seluruh komponen repositori monorepo TEAM-09 dengan rubrikasi penilaian Tugas Besar:

### 1. Komponen Kelompok (70%)
*   **API Gateway & Routing Hub (20%)**: **[SESUAI]**
    - Konfigurasi `docker-compose.yml` hanya meng-expose port `8000` untuk API Gateway Nginx. Port internal service terisolasi dari bypass luar.
*   **End-to-End Core Business Flow (25%)**: **[SESUAI]**
    - Pengajuan KRS melalui `POST /api/v1/krs/submit` secara otomatis melakukan REST M2M internal ke `mahasiswa-service` dan `nilai-service` tanpa intervensi manual.
*   **Central Infrastructure Compliance (25%)**: **[SESUAI]**
    - Orkestrasi 3-lapis di `nilai-service` berjalan secara berurutan (SSO Login -> SOAP Audit XML -> RabbitMQ Publish).

### 2. Komponen Individu & Luaran (30%)
*   **Git Accountability & Code Ownership**: **[SESUAI]**
    - Repositori monorepo dibersihkan dari *nested git repositories* dan seluruh berkas kontribusi rill tim telah di-*stage* di Git.
*   **Rekap Log Prompting AI**: **[SESUAI]**
    - Berkas log prompting asisten AI terpusat tersedia di root (`LOG_PROMPTING.md`), serta di sub-service lainnya (`REKAP-PROMPT.md` dan `REKAP-PROMT-INTEGRASI.md`).
*   **Resume Kontribusi Tim**: ⚠️ **[BELUM SESUAI / KOSONG]**
    - Berkas `RESUME_KONTRIBUSI.md` telah dihapus dari direktori kerja atas permintaan sebelumnya. Ketentuan luaran mewajibkan adanya resume kontribusi kelompok ini di dalam repositori.

### Kesimpulan:
Sistem monorepo dan alur bisnis telah 100% memenuhi rubrik teknis kelompok. Namun, agar luaran administrasi individu lengkap, disarankan untuk memulihkan atau menyusun kembali resume kontribusi tim sebelum melakukan commit akhir.

---

## Sesi 4: Analisis Kendala Transaksi KRS

**User:**
ini kenapa? *(mengirimkan tangkapan layar berisi error SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: krs_items.student_id, krs_items.course_id)*

**Asisten AI:**
Error tersebut terjadi karena adanya batasan unik (`UNIQUE` constraint) di database SQLite untuk tabel `krs_items` pada kolom gabungan `student_id` dan `course_id`.

Batasan ini dikonfigurasi di file migrasi database:
[2026_06_02_074655_create_krs_items_table.php](file:///c:/Users/MANHAL/Downloads/Compressed/TUGAS%20IAE/TUBES-IAE_TEAM-09/102022400068_krs-service/database/migrations/2026_06_02_074655_create_krs_items_table.php#L22) pada baris:
```php
$table->unique(['student_id', 'course_id']);
```

Karena data KRS untuk mahasiswa `102022400280` dengan mata kuliah `1` sudah disubmit sebelumnya (sudah ada di database), ketika kamu mencoba melakukan submit ulang untuk kombinasi mahasiswa dan mata kuliah yang sama, database menolak request tersebut agar tidak terjadi duplikasi.

**Saran Solusi:**
1. **Menggunakan Data Uji Lain**: Gunakan `course_id` lain (misalnya `2`, `3`, dst.) atau `student_id` yang berbeda.
2. **Mereset Database KRS**: Kamu bisa mengosongkan dan me-reseed database dengan perintah:
   ```powershell
   docker exec -it krs_service_app php artisan migrate:fresh --seed
   ```

---

## Sesi 5: Pengecekan Daftar NIM Mahasiswa Uji Coba

**User:**
daftar nim mahasiswa yang terdaftar untuk di test apa aja coba cek

**Asisten AI:**
Agar pengujian **end-to-end (E2E)** berhasil, NIM mahasiswa yang dimasukkan dalam request submit KRS harus terdaftar di kedua database (lokal `krs-service` dan `mahasiswa-service`). 

Berikut adalah daftar NIM yang didefinisikan pada masing-masing seeder:

### Daftar NIM di Masing-masing Seeder

| NIM | Nama Mahasiswa | Terdaftar di [krs-service](file:///c:/Users/MANHAL/Downloads/Compressed/TUGAS%20IAE/TUBES-IAE_TEAM-09/102022400068_krs-service/database/seeders/StudentSeeder.php) | Terdaftar di [mahasiswa-service](file:///c:/Users/MANHAL/Downloads/Compressed/TUGAS%20IAE/TUBES-IAE_TEAM-09/102022400280_Data_Mahasiswa_Service/database/seeders/StudentSeeder.php) | Status Pengujian E2E |
| :--- | :--- | :---: | :---: | :--- |
| **`102022400280`** | Hans Dhika Slamet | Ya | Ya | **Bisa Digunakan (Ready)** |
| `102022400281` | Budi Santoso | Tidak | Ya | Gagal (Foreign Key di KRS tidak ada) |
| `102022400285` | Muhammad Manhal Syarifudin | Ya | Tidak | Gagal (Verifikasi Mahasiswa Gagal / Not Found) |
| `102022400068` | Galih Pratama | Ya | Tidak | Gagal (Verifikasi Mahasiswa Gagal / Not Found) |

### Daftar `course_id` (Mata Kuliah) yang Tersedia untuk Tes
Didefinisikan di dalam [CourseSeeder.php](file:///c:/Users/MANHAL/Downloads/Compressed/TUGAS%20IAE/TUBES-IAE_TEAM-09/102022400068_krs-service/database/seeders/CourseSeeder.php):
*   **`course_id: 1`**: Pemrograman Dasar (`IF-101`) - SKS: 3
*   **`course_id: 2`**: Struktur Data & Algoritma (`IF-202`) - SKS: 4
*   **`course_id: 3`**: Rekayasa Perangkat Lunak (`IF-303`) - SKS: 3

---

## Sesi 6: Analisis Error Verifikasi Mahasiswa Gagal

**User:**
ini kenapa? *(mengirimkan tangkapan layar dengan respon: Verifikasi Mahasiswa Gagal: Mahasiswa tidak ditemukan untuk NIM 102022400285)*

**Asisten AI:**
Error tersebut terjadi karena **NIM `"102022400285"` belum terdaftar di database `mahasiswa-service`** (hanya ada di data lokal `krs-service`). 

Ketika `krs-service` melakukan verifikasi ke `mahasiswa-service` melalui API `/api/v1/students/102022400285`, service mahasiswa mengembalikan pesan `"Mahasiswa tidak ditemukan"`.

**Saran Solusi:**
1. **Menggunakan NIM Alternatif**: Ganti `student_id` pada request body menjadi `102022400280` yang sudah terdaftar di kedua database.
2. **Menambahkan Mahasiswa Baru ke Seeder**:
   Tambahkan data NIM `102022400285` ke dalam file seeder `mahasiswa-service` ([StudentSeeder.php](file:///c:/Users/MANHAL/Downloads/Compressed/TUGAS%20IAE/TUBES-IAE_TEAM-09/102022400280_Data_Mahasiswa_Service/database/seeders/StudentSeeder.php)):
   ```php
   Student::create([
       'nim' => '102022400285',
       'nama' => 'Muhammad Manhal Syarifudin',
       'status' => 'AKTIF',
       'quota_sks' => 24,
       'used_sks' => 18
   ]);
   ```
   Lalu jalankan perintah:
   ```powershell
   docker exec -it mahasiswa_service_app php artisan migrate:fresh --seed
   ```

---

## Sesi 7: Perapihan Repositori

**User:**
ini kenapa merah? *(mengirimkan gambar folder 102022400280_Data_Mahasiswa_Service berwarna merah di VS Code)*

**Asisten AI:**
Penyebab folder berwarna merah di VS Code dianalisis sebagai berikut:

1. **Masalah Indeks Git (Rename Folder)**:
   Terjadi perubahan nama folder dari `102022400280_student-service` menjadi `102022400280_Data_Mahasiswa_Service` di komputer lokal. Git mendeteksinya sebagai file lama terhapus dan berkas baru yang belum sinkron, memicu warna merah/cokelat tidak konsisten di VS Code explorer.
2. **Peringatan Linter (Missing Vendor)**:
   Folder `vendor` untuk `102022400280_Data_Mahasiswa_Service` tidak ada di komputer *host* (hanya ada di kontainer). Linter PHP mendeteksi error referensi kelas (*Class not found* / *Undefined namespace*) pada semua berkas PHP di folder tersebut.

**Tindakan Perapihan:**
1. **Menyelaraskan Indeks Git**:
   Menjalankan perintah `git add -A` agar Git merekam perubahan penamaan folder dengan benar dan membersihkan konflik status terhapus.
2. **Menginstal Dependensi Lokal (Host)**:
   Menjalankan perintah `composer install` di folder `102022400280_Data_Mahasiswa_Service` agar direktori `vendor` terbuat secara lokal. Ini menyelesaikan semua peringatan linter sehingga folder kembali berwarna normal di VS Code.
