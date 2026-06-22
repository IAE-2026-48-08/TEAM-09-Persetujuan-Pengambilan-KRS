# Resume Kontribusi TEAM-09
## Persetujuan Pengambilan KRS

Dokumen ini mencatat detail kontribusi dari masing-masing anggota kelompok TEAM-09 dalam pengerjaan Tugas Besar Integrasi Aplikasi Enterprise (IAE) 2026.

---

## 1. Muhammad Manhal Syarifudin
**GitHub:** manhalsyarif | **NIM:** 102022400285

Berfokus pada pengembangan **Nilai dan Kurikulum Service** (`grades-service`).

### Pengembangan Core Service
Mengembangkan Service Nilai & Kurikulum (`102022400285_grades-service`) menggunakan framework Laravel.

### Implementasi Endpoint
*   `GET /api/v1/curriculums` — menyajikan aturan prasyarat kurikulum program studi.
*   `GET /api/v1/grades/{student_id}` — menyajikan transkrip nilai mahasiswa berdasarkan NIM.
*   `POST /api/v1/grades/initialize` *(Transaksi Kritis)* — inisialisasi baris data nilai baru mahasiswa.

### Mekanisme Autentikasi
Mengembangkan middleware `CheckIaeKey` (`iae.auth`) untuk memvalidasi API Key kelompok (`KEY-MHS-310`) dan pemetaan hak akses berbasis SSO.

### Integrasi SOAP Audit
Mengembangkan `SoapAuditService` untuk mengirimkan log audit transaksi kritis dalam format XML ke SOAP server pusat, menangkap `<iae:ReceiptNumber>` menggunakan Regex, dan meng-update data ke database lokal.

### Dokumentasi & Analisis
Menyusun dokumen `analisis_tugas_3.md`, merancang sequence diagram sistem, dan mendokumentasikan sesi konsultasi AI pada berkas `LOG_PROMPTING.md`.

> **Ringkasan kontribusi:** Bertanggung jawab atas pengembangan Nilai & Kurikulum Service, integrasi SOAP Audit Service, serta dokumentasi/analisis sistem.

---

## 2. D Hans Dhika Slamet
**GitHub:** hansdhika11-dpk | **NIM:** 102022400280

Berfokus pada pengembangan **Data Mahasiswa Service** (`mahasiswa-service`).

### Pengembangan Core Service
Mengembangkan backend penyimpanan master data mahasiswa (`102022400280_Data_Mahasiswa_Service`) menggunakan framework Laravel untuk melayani kebutuhan informasi entitas mahasiswa bagi service lainnya.

### Implementasi Endpoint
*   `GET /api/v1/students` — menampilkan daftar keseluruhan data mahasiswa aktif.
*   `GET /api/v1/students/{student_id}` — menyajikan detail profil spesifik dari seorang mahasiswa berdasarkan NIM.

### Integrasi Keamanan
Mengimplementasikan validasi header dan token untuk membatasi akses ke data sensitif mahasiswa hanya untuk request yang memiliki otorisasi valid dari ekosistem kampus.

### Konektivitas Data
Memastikan ketersediaan resource mahasiswa agar dapat dikonsumsi (*lookup*) dengan aman oleh KRS Service dan Grades Service melalui API Gateway.

> **Ringkasan kontribusi:** Bertanggung jawab atas pengelolaan data master mahasiswa, penyediaan endpoint profil untuk service lain, dan keamanan akses resource mahasiswa.

---

## 3. Galih Hirpana
GitHub: binjal1410 | NIM: 102022400068

Berfokus pada pembuatan Mata Kuliah & KRS Service (krs-service), setup Docker Compose untuk kelompok, dan konfigurasi Nginx API Gateway.

Setup Docker & API Gateway
Membuat file docker-compose.yml untuk menggabungkan service milik semua anggota kelompok.

Mengonfigurasi Nginx sebagai API Gateway agar semua service (Mahasiswa, KRS, Grades) bisa diakses lewat satu pintu di port 8000.

Fitur Utama KRS Service
Membangun krs-service menggunakan framework Laravel dengan database lokal SQLite.

Membuat endpoint utama untuk proses pendaftaran: GET /v1/courses, GET /v1/krs/{student_id}, dan POST /v1/krs/submit.

Keamanan Transaksi Database
Mencegah error kuota (bentrok data) saat mahasiswa mendaftar bersamaan dengan memakai DB::beginTransaction().

Menggunakan fitur lockForUpdate() untuk mengunci baris data mata kuliah sementara waktu sampai transaksi selesai.

Integrasi Sistem Pusat (SSO, SOAP, RabbitMQ)
Menambahkan parameter nim saat request Token JWT M2M ke SSO Dosen agar otorisasi berhasil.

Membungkus data JSON ke dalam format XML Envelope untuk dikirim sebagai log ke server Legacy SOAP.

Mengirimkan notifikasi event pendaftaran secara asinkron ke RabbitMQ pusat menggunakan HTTP Request biasa.

Ringkasan kontribusi: Bertanggung jawab atas setup Docker dan Nginx Gateway kelompok, membuat fitur pendaftaran KRS yang aman dari bentrok kuota, serta menghubungkan KRS lokal dengan ketiga sistem pusat dosen (SSO, SOAP, dan RabbitMQ).

---

## Ringkasan Keseluruhan

| Kontributor | Jumlah Commit | Area Kontribusi |
|---|---|---|
| manhalsyarif | 9 | Grades Service, SOAP Audit, Dokumentasi |
| hansdhika11-dpk | 1 | Data Mahasiswa Service, Endpoint Master Mahasiswa, API Security |
| binjal1410 | 12 | KRS Service (DB Transaction/Locking), Nginx/Laravel API Gateway, Docker Compose, SSO JWT, SOAP XML Audit, RabbitMQ Integration |