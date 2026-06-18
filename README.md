# TUBES-IAE_TEAM-09 Monorepo

Repository monorepo untuk Tugas Besar Integrated Application Environment (IAE) - Kelompok TEAM-09.

## Anggota Kelompok
*   **Galih** (NIM: 102022400068) - Service Mata Kuliah & KRS (`krs-service`)
*   **Hans** (NIM: 1020224xxxxx) - Service Data Mahasiswa (`mahasiswa-service`)
*   **Manhal** (NIM: 102022400285) - Service Nilai & Kurikulum (`nilai-service`)

## Struktur Repository
```text
TUBES-IAE_TEAM-09/
├── 102022400068_krs-service/       ← Service Galih (Port: 8002)
├── 1020224xxxxx_student-service/   ← Service Hans (Port: 8001)
├── 102022400285_grades-service/    ← Service Manhal (Port: 8003)
├── api-gateway/                    ← API Gateway (Port: 8000)
├── docker-compose.yml              ← Konfigurasi Orchestrasi Docker Compose
└── README.md
```

## Cara Menjalankan Semua Service
Pastikan Docker Desktop aktif di laptop Anda, kemudian jalankan perintah berikut di root folder monorepo ini:
```bash
docker compose up -d --build
```
Semua service akan menyala dan siap diakses melalui API Gateway di:
👉 **http://localhost:8000/api/v1/...**
