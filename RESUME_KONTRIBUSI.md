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
**GitHub:** binjal1410 | **NIM:** 102022400068

Berfokus pada pengembangan **Mata Kuliah & KRS Service** (`krs-service`), pengaturan orkestrasi Docker Compose monorepo, serta konfigurasi API Gateway.

### Infrastruktur & API Gateway
Menyusun `docker-compose.yml` utama di root folder, serta menyediakan konfigurasi routing hub melalui API Gateway (baik Nginx maupun gateway berbasis Laravel) untuk menyatukan routing port seluruh service terisolasi di bawah port 8000.

### Integrasi Event-Driven (RabbitMQ)
Mengonfigurasi dan mengintegrasikan broker pesan untuk komunikasi asynchronous. Mengimplementasikan pola REST-to-AMQP Gateway (`Http::post` ke REST Proxy) untuk menyiarkan event transaksi secara aman ke exchange RabbitMQ pusat.

### Pengembangan Core KRS Service
Mengembangkan `102022400068_krs-service` menggunakan Laravel dan SQLite. Membuat endpoint `GET /v1/courses`, `GET /v1/krs/{student_id}`, dan `POST /v1/krs/submit`.

### Integritas Database (Pessimistic Locking)
Mengamankan state pendaftaran dengan membungkus proses insert KRS di dalam `DB::beginTransaction()` dan mengunci kuota kursi mata kuliah secara real-time menggunakan mekanisme `lockForUpdate()` untuk mencegah race condition.

### Integrasi SSO M2M & Legacy SOAP
Memperbarui kontrak API dengan menyuntikkan payload `nim` pada request JWT ke IAE SSO, serta mendesain XML CDATA Envelope untuk mencatatkan resi validasi pendaftaran KRS ke Legacy SOAP Audit pusat secara otomatis.

> **Ringkasan kontribusi:** Bertanggung jawab atas orkestrasi Docker monorepo, routing API Gateway, pengembangan core logic KRS Service dengan Pessimistic Locking, serta integrasi penuh Tritunggal pusat (SSO JWT, SOAP Audit, dan REST Proxy RabbitMQ).

---

## Ringkasan Keseluruhan

| Kontributor | Jumlah Commit | Area Kontribusi |
|---|---|---|
| manhalsyarif | 9 | Grades Service, SOAP Audit, Dokumentasi |
| hansdhika11-dpk | 1 | Data Mahasiswa Service, Endpoint Master Mahasiswa, API Security |
| binjal1410 | 12 | KRS Service (DB Transaction/Locking), Nginx/Laravel API Gateway, Docker Compose, SSO JWT, SOAP XML Audit, RabbitMQ Integration |