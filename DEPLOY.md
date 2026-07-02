# Panduan Deploy Monev P3KM ke Production

Deploy aplikasi Laravel menggunakan Podman di VPS Ubuntu/Debian, dengan Nginx sebagai reverse proxy dan SSL otomatis via Let's Encrypt.

**Stack:** Laravel 13 · Podman · Nginx · Certbot  
**OS Target:** Ubuntu 22.04 LTS / 24.04 LTS  
**Database:** SQLite

---

## Prasyarat

- VPS dengan minimal 1 vCPU, 1 GB RAM
- OS Ubuntu 22.04 atau 24.04 LTS
- Domain/subdomain yang sudah diarahkan ke IP VPS
- Akses SSH sebagai root atau user dengan sudo
- Port 80 dan 443 terbuka di firewall VPS

> **Penting:** DNS domain harus sudah propagasi dan mengarah ke IP VPS sebelum menjalankan Certbot. Verifikasi dengan `nslookup monev.domainanda.id` — hasilnya harus IP VPS Anda.

---

## 1. Setup Awal VPS

```bash
# Login sebagai root
ssh root@<IP_VPS>

# Update sistem
apt update && apt upgrade -y

# Install package dasar
apt install -y curl git wget unzip ufw

# Buat user deploy
adduser deploy
usermod -aG sudo deploy

# Aktifkan SSH key untuk user deploy (opsional tapi dianjurkan)
su - deploy
mkdir -p ~/.ssh && chmod 700 ~/.ssh
# Salin public key Anda ke ~/.ssh/authorized_keys
```

```bash
# Konfigurasi UFW Firewall
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw enable

ufw status
```

---

## 2. Install Podman di VPS

```bash
# Install Podman
apt install -y podman

# Verifikasi
podman --version

# Aktifkan lingkungan rootless untuk user deploy
su - deploy
podman system migrate
```

```bash
# Aktifkan lingering agar container tetap berjalan saat logout (jalankan sebagai root)
loginctl enable-linger deploy
```

> Podman berjalan **rootless** — container dijalankan sebagai user biasa tanpa akses root, lebih aman dibanding Docker daemon.

---

## 3. Deploy Aplikasi dari GitHub

```bash
# Pindah ke user deploy
su - deploy

# Clone repository
git clone https://github.com/feryfadly27/monevp3km.git ~/monevp3km
cd ~/monevp3km

# Buat direktori data yang akan di-mount (persist saat container diupdate)
mkdir -p ~/monev-data/database
mkdir -p ~/monev-data/storage/app
mkdir -p ~/monev-data/storage/framework/{sessions,views,cache}
mkdir -p ~/monev-data/storage/logs
chmod -R 775 ~/monev-data
```

```bash
# Build image dari Containerfile
podman build -t monev-p3km:latest -f Containerfile .

# Verifikasi image
podman images | grep monev-p3km
```

```bash
# Jalankan container (bind ke localhost saja, Nginx yang expose ke luar)
podman run -d \
  --name monev-p3km \
  --restart=always \
  -p 127.0.0.1:8000:8000 \
  -v ~/monev-data/database:/var/www/html/database:Z \
  -v ~/monev-data/storage:/var/www/html/storage:Z \
  --env-file ~/monevp3km/monev-app/.env \
  monev-p3km:latest

# Pastikan container berjalan
podman ps
podman logs monev-p3km
```

> Container di-bind ke `127.0.0.1:8000` bukan `0.0.0.0:8000` — hanya bisa diakses dari localhost VPS. Nginx yang meneruskan request dari luar.

---

## 4. Konfigurasi Nginx sebagai Reverse Proxy

```bash
# Install Nginx
apt install -y nginx
systemctl enable nginx
systemctl start nginx
```

Buat file konfigurasi virtual host:

```bash
nano /etc/nginx/sites-available/monev-p3km
```

Isi dengan konfigurasi berikut (ganti `monev.domainanda.id` dengan domain Anda):

```nginx
server {
    listen 80;
    server_name monev.domainanda.id;

    # Ukuran upload maksimal
    client_max_body_size 20M;

    access_log /var/log/nginx/monev-access.log;
    error_log  /var/log/nginx/monev-error.log;

    location / {
        proxy_pass         http://127.0.0.1:8000;
        proxy_http_version 1.1;
        proxy_set_header   Host              $host;
        proxy_set_header   X-Real-IP         $remote_addr;
        proxy_set_header   X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header   X-Forwarded-Proto $scheme;
        proxy_set_header   Upgrade           $http_upgrade;
        proxy_set_header   Connection        "upgrade";
        proxy_read_timeout 120s;
        proxy_buffering    off;
    }
}
```

```bash
# Aktifkan site
ln -s /etc/nginx/sites-available/monev-p3km /etc/nginx/sites-enabled/

# Test konfigurasi
nginx -t

# Reload Nginx
systemctl reload nginx
```

---

## 5. SSL dengan Let's Encrypt (Certbot)

```bash
# Install Certbot via snap
snap install --classic certbot
ln -s /snap/bin/certbot /usr/bin/certbot

# Generate sertifikat SSL (Certbot otomatis edit konfigurasi Nginx)
certbot --nginx -d monev.domainanda.id
```

Ikuti instruksi: masukkan email, setujui TOS. Certbot akan menambahkan konfigurasi HTTPS dan redirect HTTP→HTTPS otomatis.

```bash
# Test auto-renewal
certbot renew --dry-run

# Cek timer renewal aktif
systemctl status snap.certbot.renew.timer
```

> Sertifikat diperbarui otomatis sebelum expired (90 hari). Tidak perlu intervensi manual.

---

## 6. Konfigurasi Environment Production

Edit file `.env` di server:

```bash
nano ~/monevp3km/monev-app/.env
```

Nilai yang wajib diubah untuk production:

```env
APP_NAME="Monev P3KM"
APP_ENV=production          # Wajib: ubah dari local ke production
APP_KEY=base64:...          # Jangan ubah — key enkripsi session
APP_DEBUG=false             # Wajib: matikan debug di production
APP_URL=https://monev.domainanda.id

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/database.sqlite

SESSION_DRIVER=file
CACHE_STORE=file

# Akun admin awal — ubah sebelum deploy pertama kali!
SEED_ADMIN_NAME="Admin P3KM"
SEED_ADMIN_EMAIL="admin@domainanda.id"
SEED_ADMIN_PASSWORD="password_kuat_min12karakter"
```

> **Peringatan:** `APP_DEBUG=true` di production akan menampilkan stack trace dan isi `.env` ke pengguna. Pastikan selalu `false`.

```bash
# Restart container setelah mengubah .env
podman restart monev-p3km

# Amankan file .env (hanya bisa dibaca pemilik)
chmod 600 ~/monevp3km/monev-app/.env
```

---

## 7. Auto-start dengan Systemd

Agar container otomatis berjalan saat VPS reboot:

```bash
# Sebagai user deploy
su - deploy

# Generate systemd unit file
mkdir -p ~/.config/systemd/user
podman generate systemd --name monev-p3km --restart-policy=always \
  > ~/.config/systemd/user/monev-p3km.service

# Reload systemd dan aktifkan service
systemctl --user daemon-reload
systemctl --user enable monev-p3km.service
systemctl --user start monev-p3km.service

# Cek status
systemctl --user status monev-p3km.service
```

> Pastikan `loginctl enable-linger deploy` sudah dijalankan (langkah 2) agar service tetap berjalan meski tidak ada sesi SSH aktif.

---

## 8. Update Aplikasi

Setiap ada perubahan kode yang di-push ke GitHub:

```bash
# Sebagai user deploy
cd ~/monevp3km

# 1. Backup database sebelum update major
cp ~/monev-data/database/database.sqlite \
   ~/monev-data/database/database.sqlite.bak.$(date +%Y%m%d)

# 2. Pull perubahan terbaru
git pull origin main

# 3. Rebuild image
podman build -t monev-p3km:latest -f Containerfile .

# 4. Stop dan hapus container lama
podman stop monev-p3km
podman rm monev-p3km

# 5. Jalankan container baru
podman run -d \
  --name monev-p3km \
  --restart=always \
  -p 127.0.0.1:8000:8000 \
  -v ~/monev-data/database:/var/www/html/database:Z \
  -v ~/monev-data/storage:/var/www/html/storage:Z \
  --env-file ~/monevp3km/monev-app/.env \
  monev-p3km:latest

# 6. Verifikasi
podman logs monev-p3km --tail 20
```

> Data tetap aman karena database dan storage di-mount dari direktori host (`~/monev-data/`), bukan disimpan di dalam container.

---

## Ringkasan Port & Path Penting

| Komponen | Port / Path | Keterangan |
|---|---|---|
| Container Laravel | `127.0.0.1:8000` | Hanya bisa diakses dari localhost |
| Nginx (HTTP) | `0.0.0.0:80` | Redirect ke HTTPS |
| Nginx (HTTPS) | `0.0.0.0:443` | Proxy ke container port 8000 |
| Database SQLite | `~/monev-data/database/` | Di-mount ke container, persist saat update |
| Storage Laravel | `~/monev-data/storage/` | Session, cache, upload berkas |
| Sertifikat SSL | `/etc/letsencrypt/live/` | Dikelola Certbot, auto-renew |
| Nginx config | `/etc/nginx/sites-available/monev-p3km` | Konfigurasi virtual host |
| Nginx log | `/var/log/nginx/monev-*.log` | Access & error log |

---

## Checklist Sebelum Go-Live

- [ ] `APP_ENV=production` dan `APP_DEBUG=false` di file `.env`
- [ ] `APP_URL` sudah menggunakan `https://` dan domain yang benar
- [ ] `SEED_ADMIN_PASSWORD` sudah diganti dengan password kuat (min. 12 karakter)
- [ ] Sertifikat SSL aktif — akses `https://monev.domainanda.id` tanpa browser warning
- [ ] HTTP redirect ke HTTPS berjalan (coba akses via `http://`)
- [ ] Login dengan akun admin berhasil
- [ ] Fitur upload berkas kegiatan berfungsi
- [ ] Registrasi dosen dan aktivasi oleh admin berfungsi
- [ ] Firewall hanya membuka port 22, 80, 443
- [ ] Backup database rutin dijadwalkan
- [ ] `certbot renew --dry-run` berhasil tanpa error

---

*Monev P3KM — Deploy Guide v1.0*
