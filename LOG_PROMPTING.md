# Log Prompting Integrasi Monorepo (TEAM-09)

Dokumen ini mencatat riwayat konsultasi dan perapihan yang dilakukan bersama Asisten AI dalam proses integrasi monorepo Tugas Besar IAE 2026.

---

## Sesi 1: Analisis Kendala Transaksi KRS (Integrity Constraint)

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

## Sesi 2: Pengecekan Daftar NIM Mahasiswa Uji Coba

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

## Sesi 3: Analisis Error Verifikasi Mahasiswa Gagal

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

## Sesi 4: Perapihan Repositori (Masalah Folder Merah di VS Code)

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
