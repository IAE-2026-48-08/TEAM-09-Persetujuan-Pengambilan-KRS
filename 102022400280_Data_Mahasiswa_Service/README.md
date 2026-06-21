# 102022400280_service_data_mahasiswa

## Deskripsi

Service ini adalah implementasi **mahasiswa-service** untuk MVP sistem KRS terpadu TEAM-09.
Service ditujukan untuk menyediakan data mahasiswa dan validasi kuota SKS bagi service KRS.

---

## Teknologi yang Digunakan

* Laravel 12
* PHP 8.2
* SQLite
* L5-Swagger
* REST API

---

## Service Name dan Port

- Docker service name: `mahasiswa-service`
- Port host lokal: `8001`
- Container port: `8000`

---

## REST API Endpoints

### 1. Mengambil seluruh data mahasiswa

```
GET /api/v1/students
```

### 2. Mengambil detail mahasiswa berdasarkan ID

```
GET /api/v1/students/{id}
```

### 3. Validasi kuota SKS

```
POST /api/v1/students/validate-quota
```

Body Request:

```json
{
  "student_id": 1,
  "requested_sks": 4
}
```

### 4. Header Autentikasi

Semua endpoint menggunakan API Key header:

```
X-API-KEY: 1020224xxxxx-HANS
```

---

## Docker Compose

Gunakan `docker-compose up --build mahasiswa-service` untuk menjalankan service ini.

Service ini siap diintegrasikan ke monorepo TEAM-09 dengan nama service `mahasiswa-service`.

---

## Menjalankan Secara Lokal

1. Install dependency

```
composer install
```

2. Copy environment

```
cp .env.example .env
```

3. Buat file database SQLite

```
touch database/database.sqlite
```

4. Generate aplikasi key

```
php artisan key:generate
```

5. Jalankan migrasi dan seeder

```
php artisan migrate --seed
```

6. Jalankan service

```
php artisan serve --host=0.0.0.0 --port=8000
```

Service akan tersedia di `http://localhost:8001` jika dijalankan melalui Docker Compose.

---

## Catatan Integrasi

- Dalam monorepo, service ini harus diakses oleh KRS service menggunakan URL internal Docker:

```
http://mahasiswa-service:8000/api
```

- Jangan gunakan `localhost` saat service-to-service request dilakukan di dalam Docker network.

---

## Swagger Documentation

Jika Swagger diaktifkan, dokumentasi dapat diakses melalui:

```
http://localhost:8001/api/documentation
```


```
php artisan migrate
```

Menjalankan server

```
php artisan serve
```

---

## Author

Hans Dhika Slamet

NIM : 102022400280
