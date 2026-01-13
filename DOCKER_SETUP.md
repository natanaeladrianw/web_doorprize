# 🚀 Panduan Setup: Laravel + GitHub Actions + Docker

## 📁 File yang Telah Dibuat

| File | Fungsi |
|------|--------|
| `Dockerfile` | Instruksi untuk build Docker image Laravel |
| `.dockerignore` | Daftar file yang tidak perlu di-copy ke Docker |
| `.github/workflows/docker-build.yml` | Workflow GitHub Actions otomatis |
| `docker-compose.yml` | Setup untuk development lokal |

---

## 🔧 STEP-BY-STEP SETUP

### **STEP 1: Buat Akun Docker Hub (Jika Belum Punya)**

1. Kunjungi https://hub.docker.com/
2. Klik **Sign Up** dan buat akun
3. Catat **username** Anda (akan digunakan nanti)

---

### **STEP 2: Buat Access Token di Docker Hub**

1. Login ke Docker Hub
2. Klik profil Anda (pojok kanan atas) → **Account Settings**
3. Pilih **Security** → **Access Tokens**
4. Klik **New Access Token**
5. Beri nama token (contoh: "github-actions")
6. Pilih **Read, Write, Delete**
7. Klik **Generate**
8. **PENTING**: Copy token ini! Token hanya ditampilkan sekali

---

### **STEP 3: Tambahkan Secrets di GitHub Repository**

1. Buka repository Anda di GitHub
2. Pergi ke **Settings** → **Secrets and variables** → **Actions**
3. Klik **New repository secret**
4. Tambahkan 2 secrets berikut:

| Name | Value |
|------|-------|
| `DOCKER_USERNAME` | Username Docker Hub Anda |
| `DOCKER_PASSWORD` | Access Token dari Step 2 |

---

### **STEP 4: Push Kode ke GitHub**

```bash
# Tambahkan semua file baru
git add .

# Commit perubahan
git commit -m "Add Docker and GitHub Actions setup"

# Push ke GitHub
git push origin main
```

---

### **STEP 5: Cek GitHub Actions**

1. Buka repository Anda di GitHub
2. Klik tab **Actions**
3. Anda akan melihat workflow **"Build and Push Docker Image"** berjalan
4. Tunggu sampai selesai (biasanya 3-5 menit)

---

## 🎯 Cara Kerja Workflow

```
┌─────────────────┐
│   Push ke       │
│   GitHub        │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  GitHub Actions │
│  Triggered      │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Job 1: Test    │
│  - Setup PHP    │
│  - Composer     │
│  - NPM Build    │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Job 2: Docker  │
│  - Build Image  │
│  - Push to Hub  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  Docker Image   │
│  Ready! 🎉      │
└─────────────────┘
```

---

## 💻 Cara Menjalankan di Server (Production)

Setelah image tersedia di Docker Hub, Anda bisa menjalankannya di server:

```bash
# Pull image dari Docker Hub
docker pull YOUR_USERNAME/web-doorprize:latest

# Jalankan container
docker run -d \
  --name doorprize-app \
  -p 80:80 \
  -e APP_KEY=base64:YOUR_APP_KEY \
  -e DB_CONNECTION=mysql \
  -e DB_HOST=your-db-host \
  -e DB_DATABASE=your-db-name \
  -e DB_USERNAME=your-db-user \
  -e DB_PASSWORD=your-db-pass \
  YOUR_USERNAME/web-doorprize:latest
```

---

## 🖥️ Cara Menjalankan Lokal dengan Docker Compose

```bash
# Build dan jalankan semua services
docker-compose up -d --build

# Akses aplikasi di browser
# Aplikasi: http://localhost:8080
# phpMyAdmin: http://localhost:8081

# Jalankan migration (pertama kali)
docker-compose exec app php artisan migrate

# Jalankan seeder (jika diperlukan)
docker-compose exec app php artisan db:seed

# Stop semua services
docker-compose down

# Stop dan hapus volumes (database)
docker-compose down -v
```

---

## ⚠️ Troubleshooting

### Error: "Composer install failed"
- Pastikan `composer.lock` di-commit ke repository
- Jalankan `composer update` lokal terlebih dahulu

### Error: "npm ci failed"
- Pastikan `package-lock.json` di-commit ke repository
- Jalankan `npm install` lokal terlebih dahulu

### Error: "Docker login failed"
- Cek kembali DOCKER_USERNAME dan DOCKER_PASSWORD di GitHub Secrets
- Pastikan Access Token Docker Hub masih valid

### Error: "Permission denied"
- Jalankan: `docker-compose exec app chmod -R 755 storage bootstrap/cache`

---

## 📝 Tips Tambahan

1. **Ganti nama image**: Edit `DOCKER_IMAGE_NAME` di file `.github/workflows/docker-build.yml`

2. **Aktifkan tests**: Uncomment bagian PHPUnit di workflow file

3. **Auto-deploy ke server**: Tambahkan step SSH ke workflow untuk auto-deploy

4. **Multi-stage build**: Untuk image yang lebih kecil, gunakan multi-stage Dockerfile

---

## 🔗 Referensi

- [Docker Documentation](https://docs.docker.com/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [Laravel Deployment](https://laravel.com/docs/deployment)
