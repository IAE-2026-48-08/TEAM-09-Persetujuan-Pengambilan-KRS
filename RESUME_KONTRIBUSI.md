Resume Kontribusi TEAM-09 - Persetujuan-Pengambilan-KRS

Dokumen ini mencatat detail kontribusi dari masing-masing anggota kelompok TEAM-09 dalam pengerjaan Tugas Besar Integrasi Aplikasi Enterprise (IAE) 2026.

1 Muhammad Manhal Syarifudin (manhalsyarif - NIM: 102022400285)

Berfokus pada pengembangan **Nilai dan Kurikulum Service (grades-service)**.

Pengembangan Core Service: Mengembangkan Service Nilai & Kurikulum (102022400285_grades-service) menggunakan framework Laravel.
Implementasi Endpoint:
  -GET /api/v1/curriculums untuk menyajikan aturan prasyarat kurikulum program studi.
  - GET /api/v1/grades/{student_id} untuk menyajikan transkrip nilai mahasiswa berdasarkan NIM.
  - POST /api/v1/grades/initialize (Transaksi Kritis) untuk inisialisasi baris data nilai baru mahasiswa.

Mekanisme Autentikasi: Mengembangkan middleware `CheckIaeKey` (`iae.auth`) untuk memvalidasi API Key kelompok (`KEY-MHS-310`) dan pemetaan hak akses berbasis SSO.

Integrasi SOAP Audit: Mengembangkan `SoapAuditService` untuk mengirimkan log audit transaksi kritis dalam format XML ke SOAP server pusat, menangkap `<iae:ReceiptNumber>` menggunakan Regex, dan meng-update data ke database lokal.

Dokumentasi & Analisis: Menyusun dokumen `analisis_tugas_3.md`, merancang sequence diagram sistem, dan mendokumentasikan sesi konsultasi AI pada berkas `LOG_PROMPTING.md`.

Ringkasan kontribusi: Bertanggung jawab atas pengembangan Nilai & Kurikulum Service, integrasi SOAP Audit Service, serta dokumentasi/analisis sistem.


2 Hans Dhika Slamet (hansdhika11-dpk - NIM: 102022400280)

Berfokus pada pengembangan Data Mahasiswa Service (mahasiswa-service).

(Isi sesuaoi Pengerjaan)

- 
- 
- 

Ringkasan kontribusi: (Buat Ringkasan Kontreibusi)


3 Galih Hirpana (binjal1410 - NIM: 102022400068)

Berfokus pada pengembangan Mata Kuliah & KRS Service (krs-service), pengaturan orkestrasi Docker Compose monorepo, serta konfigurasi API Gateway.

Infrastruktur & API Gateway: Menyusun `docker-compose.yml` utama di root folder, serta mengonfigurasi API Gateway menggunakan Nginx reverse proxy (`api-gateway`) untuk menyatukan routing port seluruh service di bawah port `8000`.

Integrasi RabbitMQ: Mengonfigurasi dan mengintegrasikan broker pesan RabbitMQ untuk keperluan komunikasi asynchronous / event-driven antar-service dalam monorepo.
(Silakan lengkapi detail kontribusi KRS Service Anda di sini)

Ringkasan kontribusi: Bertanggung jawab atas pengaturan orkestrasi Docker Compose, konfigurasi API Gateway Nginx, integrasi RabbitMQ, serta pengembangan KRS Service.


Ringkasan Keseluruhan

| Kontributor | Jumlah Commit | Area Kontribusi |
| manhalsyarif | 4 | Grades Service, SOAP Audit, Dokumentasi |
| hansdhika11-dpk | (Diisi sendiri) | (Diisi sendiri) |
| binjal1410 | (Diisi sendiri) | KRS Service, Nginx API Gateway, Docker Compose, RabbitMQ Integration, *(Lengkapi lainnya)* |

Total Commit Kelompok: (Diisi sendiri)
