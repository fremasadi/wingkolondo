# Deployment dengan Docker

## Persiapan pertama

Pastikan Docker Engine dan Docker Compose tersedia di server, kemudian jalankan:

```bash
cp .env.docker.example .env.docker
docker compose --env-file .env.docker build app
docker compose --env-file .env.docker run --rm --no-deps -e RUN_MIGRATIONS=false app php artisan key:generate --show
```

Isi `APP_KEY` di `.env.docker` menggunakan hasil perintah tersebut. Ganti juga
`APP_URL`, `DB_PASSWORD`, `MYSQL_PASSWORD`, dan `MYSQL_ROOT_PASSWORD` dengan
nilai produksi. Nilai `DB_PASSWORD` dan `MYSQL_PASSWORD` harus sama.

## Menjalankan aplikasi

```bash
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker ps
docker compose --env-file .env.docker logs -f app
```

Aplikasi tersedia pada port `APP_PORT` (default `8080`). Letakkan reverse proxy
dan HTTPS server di depan port tersebut untuk deployment publik.

Service `app` otomatis menjalankan migrasi saat startup. Service `queue`
menjalankan Laravel queue worker. Database, session, cache, queue, dan upload
foto tersimpan pada volume Docker persisten.

## Update aplikasi

```bash
git pull
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker exec queue php artisan queue:restart
```

## Perintah operasional

```bash
docker compose --env-file .env.docker exec app php artisan migrate:status
docker compose --env-file .env.docker exec app php artisan about
docker compose --env-file .env.docker logs -f queue
docker compose --env-file .env.docker down
```

Jangan gunakan `docker compose --env-file .env.docker down -v` di server
produksi karena opsi `-v` akan menghapus database dan file upload.
