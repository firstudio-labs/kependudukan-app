# Step-by-Step Setup Queue Worker di Server Production

## Prerequisites
- Server path: `/www/wwwroot/pelayanan.desaverse.id/kependudukan-app`
- User: `firstudio`
- Redis sudah terinstall dan berjalan
- Queue connection sudah dikonfigurasi ke Redis di `.env`

## Step 1: Verifikasi Konfigurasi Queue

```bash
cd /www/wwwroot/pelayanan.desaverse.id/kependudukan-app

# Cek .env sudah benar
grep QUEUE_CONNECTION .env
# Harus menampilkan: QUEUE_CONNECTION=redis

# Cek Redis connection
redis-cli -a "Jamalharusbisa" ping
# Harus menampilkan: PONG

# Clear config cache
php artisan config:clear
php artisan cache:clear
```

## Step 2: Test Queue Connection

```bash
# Test queue connection
php artisan tinker
>>> dispatch(new App\Jobs\PreloadCacheJob('berita_desa', 1));
>>> exit

# Cek apakah job masuk ke queue
php artisan queue:monitor redis:default
# Tekan Ctrl+C untuk keluar
```

## Step 3: Install Supervisor (jika belum ada)

```bash
# Cek apakah supervisor sudah terinstall
which supervisorctl

# Jika belum ada, install
sudo apt-get update
sudo apt-get install supervisor -y

# Cek status
sudo systemctl status supervisor
```

## Step 4: Buat Konfigurasi Supervisor

```bash
# Buat file konfigurasi
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

**Isi file dengan konfigurasi berikut:**

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=firstudio
numprocs=2
redirect_stderr=true
stdout_logfile=/www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs/worker.log
stopwaitsecs=3600
```

**Penjelasan:**
- `numprocs=2`: Menjalankan 2 worker secara bersamaan
- `--sleep=3`: Tunggu 3 detik jika tidak ada job
- `--tries=3`: Retry maksimal 3 kali jika gagal
- `--max-time=3600`: Restart worker setiap 1 jam untuk menghindari memory leak
- `user=firstudio`: Jalankan sebagai user firstudio
- `stdout_logfile`: Lokasi log file

**Simpan file:** Tekan `Ctrl+O`, lalu `Enter`, lalu `Ctrl+X`

## Step 5: Buat Log Directory (jika belum ada)

```bash
# Pastikan directory log ada dan writable
mkdir -p /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs
chmod -R 775 /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs
chown -R firstudio:firstudio /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs
```

## Step 6: Aktifkan Supervisor Configuration

```bash
# Baca konfigurasi baru
sudo supervisorctl reread

# Update supervisor dengan konfigurasi baru
sudo supervisorctl update

# Start worker
sudo supervisorctl start laravel-worker:*

# Cek status
sudo supervisorctl status laravel-worker:*
```

**Output yang diharapkan:**
```
laravel-worker:laravel-worker_00   RUNNING   pid 12345, uptime 0:00:05
laravel-worker:laravel-worker_01   RUNNING   pid 12346, uptime 0:00:05
```

## Step 7: Verifikasi Queue Worker Berjalan

```bash
# Cek proses worker
ps aux | grep "queue:work"

# Cek log worker
tail -f /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs/worker.log

# Test dengan dispatch job
php artisan tinker
>>> dispatch(new App\Jobs\PreloadCacheJob('berita_desa', 1));
>>> exit

# Monitor log untuk melihat job diproses
tail -f /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs/worker.log
```

## Step 8: Setup Auto-restart Supervisor (Opsional)

```bash
# Enable supervisor untuk auto-start saat boot
sudo systemctl enable supervisor

# Cek status
sudo systemctl status supervisor
```

## Step 9: Monitoring dan Maintenance

### Cek Status Worker
```bash
sudo supervisorctl status laravel-worker:*
```

### Restart Worker (setelah update code)
```bash
# Restart semua worker
sudo supervisorctl restart laravel-worker:*

# Atau restart supervisor
sudo systemctl restart supervisor
```

### Cek Log Worker
```bash
# Real-time log
tail -f /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs/worker.log

# Last 100 lines
tail -n 100 /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs/worker.log
```

### Cek Failed Jobs
```bash
cd /www/wwwroot/pelayanan.desaverse.id/kependudukan-app
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Flush failed jobs (hati-hati!)
php artisan queue:flush
```

### Monitor Queue
```bash
php artisan queue:monitor redis:default
```

## Step 10: Testing Queue di Production

### Test 1: Dispatch Job Manual
```bash
cd /www/wwwroot/pelayanan.desaverse.id/kependudukan-app
php artisan tinker
>>> dispatch(new App\Jobs\PreloadCacheJob('berita_desa', 1));
>>> exit
```

### Test 2: Test via API
```bash
# Hit API yang akan trigger queue job (setelah create/update berita)
# Lalu cek log
tail -f storage/logs/worker.log
```

### Test 3: Cek Redis Queue
```bash
redis-cli -a "Jamalharusbisa"
> LLEN queues:default
> LRANGE queues:default 0 -1
> exit
```

## Troubleshooting

### Worker tidak jalan
```bash
# Cek error log
sudo tail -f /var/log/supervisor/supervisord.log

# Cek status
sudo supervisorctl status laravel-worker:*

# Restart
sudo supervisorctl restart laravel-worker:*
```

### Permission denied
```bash
# Pastikan user firstudio punya akses
sudo chown -R firstudio:firstudio /www/wwwroot/pelayanan.desaverse.id/kependudukan-app
sudo chmod -R 775 /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage
```

### Job stuck/tidak diproses
```bash
# Restart worker
sudo supervisorctl restart laravel-worker:*

# Clear queue (hati-hati!)
php artisan queue:flush

# Cek failed jobs
php artisan queue:failed
```

### Memory issues
```bash
# Kurangi numprocs di supervisor config
# Atau tambahkan --max-time yang lebih pendek
# Edit: sudo nano /etc/supervisor/conf.d/laravel-worker.conf
# Ubah: numprocs=1 (dari 2)
# Lalu: sudo supervisorctl reread && sudo supervisorctl update
```

## Command Cheat Sheet

```bash
# Status worker
sudo supervisorctl status laravel-worker:*

# Start worker
sudo supervisorctl start laravel-worker:*

# Stop worker
sudo supervisorctl stop laravel-worker:*

# Restart worker
sudo supervisorctl restart laravel-worker:*

# Reload config
sudo supervisorctl reread
sudo supervisorctl update

# Cek log
tail -f /www/wwwroot/pelayanan.desaverse.id/kependudukan-app/storage/logs/worker.log

# Cek failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Monitor queue
php artisan queue:monitor redis:default
```

## Setelah Setup Selesai

1. ✅ Worker berjalan: `sudo supervisorctl status laravel-worker:*`
2. ✅ Test dispatch job: `php artisan tinker` → dispatch job
3. ✅ Monitor log: `tail -f storage/logs/worker.log`
4. ✅ Test API: Hit endpoint yang trigger queue job

Queue worker sekarang siap digunakan! 🚀

