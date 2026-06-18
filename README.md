# Monorepo TEAM-09: Sistem KRS Terpadu MVP

Repositori ini menyatukan 3 microservices yang berkolaborasi dalam proses bisnis persetujuan dan pengambilan rencana studi mahasiswa (KRS).

## Struktur Project Monorepo

```text
TUBES-IAE_TEAM-09/
├── 102022400068_krs-service/          ← Service Mata Kuliah & KRS (Galih Hirpana)
├── 1020224xxxxx_student-service/      ← Service Data Mahasiswa (Hans) [Placeholder]
├── 1020224xxxxx_grades-service/       ← Service Nilai & Kurikulum (Manhal) [Placeholder]
├── docker-compose.yml                 ← Konfigurasi orchestration Docker Compose
└── README.md                          ← Dokumen panduan utama ini
```

---

## Aturan Jaringan & Port Mapping

| Service Name Docker | Laptop Port | Developer | Fungsi Utama |
| :--- | :--- | :--- | :--- |
| `api-gateway` | `8000` | Team-09 | Titik masuk utama Frontend / API Documentation |
| `mahasiswa-service` | `8001` | D Hans Dhika Slamet | Mengelola data & status keaktifan mahasiswa |
| `krs-service` | `8002` | Galih Hirpana | Mengelola mata kuliah & submit draft KRS |
| `nilai-service` | `8003` | Muhammad Manhal Syarifudin | Mengelola prasyarat nilai & kurikulum |

---

## Alur Integrasi MVP (Synchronous API)

Proses submit KRS berjalan dengan alur synchronous melalui HTTP Client:
1. Mahasiswa mengirim request submit KRS ke `krs-service`.
2. `krs-service` mengirimkan request **HTTP GET** ke `mahasiswa-service` di `http://mahasiswa-service/api/v1/students/{id}` untuk memverifikasi keaktifan mahasiswa.
3. `krs-service` mengirimkan request **HTTP POST** ke `nilai-service` di `http://nilai-service/api/v1/grades/initialize` untuk menginisialisasi baris nilai kosong mahasiswa.
4. Jika kedua validasi di atas berhasil, `krs-service` mengunci transaksi KRS di database SQLite lokalnya dan mengembalikan respon sukses.

---

## Cara Menjalankan dengan Docker Compose

Untuk menjalankan semua layanan dalam satu jaringan Docker secara simultan:

1. Buka folder monorepo:
   ```bash
   cd TUBES-IAE_TEAM-09
   ```
2. Pastikan masing-masing sub-folder service sudah terisi kodenya.
3. Jalankan Docker Compose:
   ```bash
   docker compose up --build -d
   ```
4. Verifikasi container yang berjalan:
   ```bash
   docker compose ps
   ```
