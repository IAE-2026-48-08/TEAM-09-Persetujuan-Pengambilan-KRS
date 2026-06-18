# Panduan Migrasi Service Nilai & Kurikulum ke Monorepo Kelompok (MVP Blueprint)

Panduan ini disusun untuk membantu **Muhammad Manhal Syarifudin** (NIM: **102022400285**) memindahkan proyek **Service Nilai & Kurikulum** ke dalam repository monorepo kelompok **`TUBES-IAE_TEAM-09`** sesuai dengan blueprint arsitektur MVP terpadu.

---

## 1. Persiapan dan Kloning Monorepo

Langkah pertama adalah mengkloning repository monorepo kelompok ke laptop Anda.

1. Buka terminal (Git Bash, Command Prompt, atau PowerShell).
2. Arahkan ke folder tempat Anda menyimpan proyek-proyek kuliah (misal: `C:\Projects`).
3. Jalankan perintah clone:
   ```bash
   git clone <URL_REPOSITORY_MONOREPO_TEAM_09> TUBES-IAE_TEAM-09
   ```
4. Masuk ke direktori monorepo yang baru dikloning:
   ```bash
   cd TUBES-IAE_TEAM-09
   ```
5. Buat folder baru khusus untuk service Anda menggunakan NIM Manhal:
   ```bash
   mkdir 102022400285_grades-service
   ```

---

## 2. Salin Berkas Proyek (Copying Files)

Untuk menghindari file sampah atau konfigurasi Git yang tumpang tindih, Anda harus menyalin berkas secara selektif.

### A. Berkas & Folder yang WAJIB Disalin:
Salin berkas-berkas berikut dari folder proyek mandiri Anda (`102022400285_nilai-dan-kurikulum`) ke dalam folder monorepo baru (`TUBES-IAE_TEAM-09/102022400285_grades-service/`):

*   **`app/`** (Seluruh folder Controller, Model, Middleware, dan Service)
*   **`bootstrap/`** (Kecuali folder `bootstrap/cache/`)
*   **`config/`** (Seluruh folder konfigurasi, termasuk `l5-swagger.php`)
*   **`database/`** (Semua berkas migration, seeders, dan factory. Jika menggunakan SQLite, pastikan file `database.sqlite` disalin atau dibuat ulang)
*   **`public/`** (Seluruh folder aset public)
*   **`resources/`** (Tampilan/Views dan aset frontend jika ada)
*   **`routes/`** (`api.php`, `web.php`, `console.php`)
*   **`tests/`** (Unit & Feature tests)
*   **`artisan`** (Script CLI Laravel)
*   **`composer.json`** & **`composer.lock`** (Definisi package PHP dependency)
*   **`package.json`** & **`vite.config.js`** (Konfigurasi front-end/Vite)

### B. Berkas & Folder yang TIDAK BOLEH Disalin (Abaikan/Skip):
> [!WARNING]
> Jangan menyalin file atau folder di bawah ini untuk menghindari konflik di monorepo:

*   ❌ **`.git/`** (Ini adalah folder Git repositori lama Anda. Menyalinnya akan merusak Git monorepo!)
*   ❌ **`vendor/`** (Folder package PHP, biarkan diunduh ulang melalui composer install)
*   ❌ **`node_modules/`** (Folder package Javascript, biarkan diunduh ulang via npm install)
*   ❌ **`.env`** (File rahasia konfigurasi lokal. Anda cukup menyalin `.env.example` lalu mengeditnya secara manual)
*   ❌ **`storage/`** (Cukup buat folder kosong `storage/framework/views`, `storage/framework/sessions`, `storage/framework/cache`, dan `storage/logs`)
*   ❌ **`bootstrap/cache/*`** (Hapus semua file di dalam folder cache bootstrap)

---

## 3. Penyesuaian Konfigurasi Port & Environment

Di dalam monorepo kelompok, beberapa service akan dijalankan secara bersamaan. Oleh karena itu, konflik port harus dihindari.

### A. Pengaturan Port di `.env` & `docker-compose.yml`
Sesuai dengan blueprint MVP, service Anda akan berjalan di **Port Laptop 8003** dengan nama service Docker **`nilai-service`**.

Di file **`.env`** lokal Anda (`TUBES-IAE_TEAM-09/102022400285_grades-service/.env`):
```env
APP_URL=http://localhost:8003
L5_SWAGGER_USE_ABSOLUTE_PATH=false
L5_SWAGGER_UI_PERSIST_AUTHORIZATION=true
```

Di file **`app/Http/Controllers/Controller.php`** Anda:
```php
#[OA\Server(url: "http://localhost:8003", description: "Local API Server")]
```

### B. Pengaturan Komunikasi Antar-Service di Docker
Jika service Anda nantinya perlu melakukan request HTTP GET/POST ke service lain, gunakan URL nama service Docker mereka di dalam file `.env`:
```env
MAHASISWA_SERVICE_URL=http://mahasiswa-service/api
KRS_SERVICE_URL=http://krs-service/api
NILAI_SERVICE_URL=http://nilai-service/api
```

---

## 4. Struktur Integrasi Docker-Compose Monorepo

Tambahkan konfigurasi container Anda ke dalam file **`docker-compose.yml` utama di root monorepo** (`TUBES-IAE_TEAM-09/docker-compose.yml`).

Berikut contoh integrasi service Anda (dengan nama service `nilai-service` di port `8003`):

```yaml
services:
  # --- SERVICE DATA MAHASISWA (HANS) ---
  mahasiswa-service:
    # ... konfigurasi service mahasiswa

  # --- SERVICE MATA KULIAH & KRS (GALIH) ---
  krs-service:
    # ... konfigurasi service krs

  # --- SERVICE NILAI & KURIKULUM (MANHAL) ---
  nilai-service:
    build:
      context: ./102022400285_grades-service
      target: runtime
    container_name: nilai_service_app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./102022400285_grades-service:/var/www/html
    networks:
      - tubes_iae_network

  nilai-web:
    image: nginx:alpine
    container_name: nilai_service_web
    restart: unless-stopped
    ports:
      - "8003:80" # Diakses oleh Host di port 8003
    volumes:
      - ./102022400285_grades-service:/var/www/html
      - ./102022400285_grades-service/.docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf
    depends_on:
      - nilai-service
    networks:
      - tubes_iae_network

networks:
  tubes_iae_network:
    driver: bridge
```

---

## 5. Integrasi Endpoint & Payload Request (MVP Blueprint)

Service Anda menyediakan 3 endpoint yang wajib berjalan di port `8003` dengan validasi header keamanan `X-IAE-KEY: KEY-MHS-310`:

### A. GET `/api/v1/curriculums`
*   *Deskripsi:* Menampilkan daftar aturan prasyarat kurikulum program studi.

### B. GET `/api/v1/grades/{student_id}`
*   *Deskripsi:* Menampilkan riwayat transkrip nilai mahasiswa berdasarkan NIM (`student_id`).

### C. POST `/api/v1/grades/initialize` (Krusial)
*   *Deskripsi:* Dipanggil oleh Service KRS (Galih) ketika mahasiswa melakukan submit KRS untuk menginisialisasi baris nilai kosong baru di database Anda.
*   *Penyesuaian Parameter:* Sesuai blueprint, endpoint ini mendukung input `course_id` (sebagai alias `course_code`).
*   *Contoh Payload Request:*
    ```json
    {
        "student_id": "102022400400",
        "course_id": "SI4000"
    }
    ```
*   *Response Sukses:* `201 Created`

---

## 6. Template README untuk Folder Service Anda

Buat berkas **`README.md`** di dalam folder `102022400285_grades-service/README.md` dengan isi singkat seperti berikut:

```markdown
# Grades & Curriculum Service (Service Nilai & Kurikulum)

Layanan ini bertanggung jawab untuk mengelola data transkrip nilai mahasiswa dan aturan prasyarat kurikulum mata kuliah.

## Detail Anggota Kelompok
*   **Nama:** Muhammad Manhal Syarifudin
*   **NIM:** 102022400285
*   **Kelompok:** TEAM-09

## Cara Menjalankan Service Lokal (Standalone)
1. Salin `.env.example` ke `.env`
2. Jalankan perintah instalasi dependency:
   ```bash
   composer install
   npm install
   ```
3. Generate application key & buat file DB SQLite:
   ```bash
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate
   ```
4. Regenerasi berkas Swagger API:
   ```bash
   php artisan l5-swagger:generate
   ```
5. Jalankan server lokal:
   ```bash
   php artisan serve --port=8003
   ```
   Akses Swagger API UI di: **http://localhost:8003/api/documentation**

## Integrasi Endpoint
Layanan ini menyediakan endpoint inisialisasi nilai kritikal `/api/v1/grades/initialize` yang menerima panggilan POST dari Service KRS saat mahasiswa melakukan submit KRS.
```

---

## 7. Melakukan Commit & Push ke Monorepo

Setelah semua file disalin dan disesuaikan, saatnya mengirim perubahan ke monorepo kelompok:

1. Pastikan Anda berada di direktori utama monorepo (`TUBES-IAE_TEAM-09`):
   ```bash
   cd /path/to/TUBES-IAE_TEAM-09
   ```
2. Periksa status file untuk memastikan folder service Anda terdeteksi:
   ```bash
   git status
   ```
3. Tambahkan semua file baru ke staging area:
   ```bash
   git add 102022400285_grades-service/
   ```
4. Lakukan commit dengan pesan commit yang jelas dan deskriptif:
   ```bash
   git commit -m "feat(grades): add grades and curriculum service by Muhammad Manhal (102022400285)"
   ```
5. Kirim perubahan Anda ke server repositori monorepo:
   ```bash
   git push origin main
   ```
