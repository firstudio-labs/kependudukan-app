# Setup Queue untuk Optimasi Performa

## Konfigurasi Queue

Queue sudah dikonfigurasi untuk menggunakan Redis sesuai dengan `.env`:
```
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null  # atau password Redis Anda
REDIS_PORT=6379
```

## Jobs yang Tersedia

### 1. PreloadWilayahInfoJob
Job untuk pre-load wilayah info ke cache secara background, menghindari API call berulang.

### 2. PreloadCacheJob
Job untuk pre-warm cache untuk berbagai kombinasi pagination dan search.

## Menjalankan Queue Worker

### Development (Local)
```bash
php artisan queue:work redis --queue=default
```

### Production (Server)
Gunakan supervisor atau systemd untuk menjalankan queue worker secara persistent:

#### Option 1: Supervisor (Recommended)
1. Install supervisor:
```bash
sudo apt-get install supervisor
```

2. Buat file konfigurasi `/etc/supervisor/conf.d/laravel-worker.conf`:
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

3. Reload supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

#### Option 2: Systemd
Buat file `/etc/systemd/system/laravel-worker.service`:
```ini
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=firstudio
WorkingDirectory=/www/wwwroot/pelayanan.desaverse.id/kependudukan-app
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Kemudian:
```bash
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
sudo systemctl status laravel-worker
```

## Monitoring Queue

### Cek Status Queue
```bash
php artisan queue:monitor redis:default
```

### Cek Failed Jobs
```bash
php artisan queue:failed
```

### Retry Failed Jobs
```bash
php artisan queue:retry all
```

### Flush Failed Jobs
```bash
php artisan queue:flush
```

## Optimasi yang Diterapkan

1. **Wilayah Info Optional**: Tambahkan `?include_wilayah=0` untuk skip wilayah info dan mempercepat response
2. **Background Pre-loading**: Wilayah info di-preload di background menggunakan queue
3. **Cache Pre-warming**: Cache di-pre-warm setelah data berubah untuk halaman berikutnya

## Testing

### Test Queue Connection
```bash
php artisan tinker
>>> dispatch(new App\Jobs\PreloadCacheJob('berita_desa', 1));
```

### Test Queue Worker
```bash
php artisan queue:work redis --once
```

## Troubleshooting

### Queue tidak jalan
1. Pastikan Redis running: `redis-cli ping`
2. Pastikan `.env` sudah benar: `QUEUE_CONNECTION=redis`
3. Clear config: `php artisan config:clear`

### Jobs stuck
1. Restart worker: `php artisan queue:restart`
2. Cek failed jobs: `php artisan queue:failed`
3. Cek log: `tail -f storage/logs/worker.log`

### Memory issues
Tambahkan `--max-time=3600` untuk restart worker setiap jam.

