# Production deployment checklist

Use this checklist when deploying **David CMS** (Laravel API + TanStack client) with CMS image uploads (`/storage/cms/*`).

## How image URLs are built

1. Admin uploads an image → `POST /api/v1/cms/media/upload`
2. Laravel stores the file at `storage/app/public/cms/<filename>`
3. API returns a **full URL**: `{APP_URL}/storage/cms/<filename>`
4. That URL is saved in `cms_section_translations.data` (JSONB)
5. The public site renders the URL as-is in `<img src="...">` / CSS `background-image`

The client does **not** build `/storage/cms/` paths. Only `APP_URL` on the backend controls the host in uploaded image URLs.

---

## Recommended production layout

| Service | Example URL | Role |
|--------|-------------|------|
| Public website | `https://mydomain.com` | TanStack client (SSR/static) |
| Laravel API | `https://api.mydomain.com` | API + uploaded media at `/storage/...` |

You can also serve API and storage on the same host (e.g. `https://mydomain.com/api/v1` + `https://mydomain.com/storage/...`).

**Rule:** `APP_URL` must be the origin that serves `/storage/*` (usually the Laravel host).

---

## 1. Backend environment variables (`.env`)

Copy `.env.example` → `.env` on the server and set:

```env
APP_NAME="David CMS"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:...                    # php artisan key:generate
APP_URL=https://api.mydomain.com      # MUST match public API/storage origin (no trailing slash)

# Database
DB_CONNECTION=pgsql
DB_HOST=...
DB_PORT=5432
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

# CORS — public frontend origin(s), comma-separated
CORS_ALLOWED_ORIGINS=https://mydomain.com,https://www.mydomain.com

# Optional but useful
FRONTEND_PUBLIC_URL=https://mydomain.com
FILESYSTEM_DISK=local
LOG_LEVEL=warning
```

### Critical for CMS images

| Variable | Production value | Why |
|----------|------------------|-----|
| `APP_URL` | `https://api.mydomain.com` | `asset('storage/...')` uses this for upload URLs |
| `APP_DEBUG` | `false` | Security |
| `APP_ENV` | `production` | Correct error handling and caching |

Do **not** leave `APP_URL=http://localhost` or `http://localhost:8000` in production.

---

## 2. Client environment variables

Set at **build time** (e.g. CI/CD or server before `npm run build`):

```env
# client/.env.production or CI secret
VITE_API_URL=https://api.mydomain.com/api/v1
```

Rebuild the client after changing this. `VITE_*` values are baked into the bundle.

---

## 3. One-time server setup (Laravel)

Run on the API server after each deploy (or automate in your deploy script):

```bash
cd backendapi

composer install --no-dev --optimize-autoloader

php artisan migrate --force

# Required for /storage/* to serve uploaded files
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Verify storage symlink

```bash
ls -la public/storage
# Should point to: .../storage/app/public
```

### Verify upload directory exists and is writable

```bash
mkdir -p storage/app/public/cms
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache
```

Replace `www-data` with your PHP-FPM user if different.

---

## 4. Nginx — Laravel API + `/storage`

Example: API at `api.mydomain.com`, document root = `backendapi/public`.

```nginx
server {
    listen 443 ssl http2;
    server_name api.mydomain.com;

    root /var/www/david-cms/backendapi/public;
    index index.php;

    # SSL certificates (certbot / your provider)
    ssl_certificate     /etc/letsencrypt/live/api.mydomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/api.mydomain.com/privkey.pem;

    client_max_body_size 12M;   # CMS upload limit is 10MB; allow headroom

    # Serve uploaded files directly (faster than PHP)
    location /storage/ {
        alias /var/www/david-cms/backendapi/storage/app/public/;
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
        try_files $uri =404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Important:** The `/storage/` `alias` must end with `/` and map to `storage/app/public/` (not `storage/app/public/cms` only).

If you skip the `/storage/` block, Laravel can still serve files when `public/storage` symlink exists, but direct Nginx serving is preferred in production.

---

## 5. Nginx — public website (client)

Example: static/SSR output from TanStack build at `client/.output/public` or your host’s equivalent.

```nginx
server {
    listen 443 ssl http2;
    server_name mydomain.com www.mydomain.com;

    root /var/www/david-cms/client/.output/public;
    index index.html;

    ssl_certificate     /etc/letsencrypt/live/mydomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/mydomain.com/privkey.pem;

    # Built-in frontend assets (/Assets/...)
    location /Assets/ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    location / {
        try_files $uri $uri/ /index.html;
    }
}
```

CMS-uploaded images are **not** on this host unless you also proxy `/storage` here. They load from `APP_URL` (API host).

---

## 6. Migrating from local to production

### A. Database

Export/import PostgreSQL as usual. Then fix image URLs if you uploaded locally:

```sql
-- Example: replace local API host with production API host
UPDATE cms_section_translations
SET data = replace(
    data::text,
    'http://localhost:8000',
    'https://api.mydomain.com'
)::jsonb
WHERE data::text LIKE '%http://localhost:8000/storage/cms/%';
```

Adjust the old/new hosts to match your environment.

### B. Files

Copy uploaded images to the production server:

```bash
# From local machine
rsync -avz backendapi/storage/app/public/cms/ \
  user@server:/var/www/david-cms/backendapi/storage/app/public/cms/
```

Or re-upload images in the CMS admin on production (simplest, no SQL/rsync).

---

## 7. Post-deploy verification

### API health

```bash
curl -s https://api.mydomain.com/up
```

### Storage file (replace with a real filename)

```bash
curl -I https://api.mydomain.com/storage/cms/your-file.png
# Expect: HTTP/2 200, Content-Type: image/png
```

### Upload flow (admin)

1. Log in to `https://mydomain.com/admin`
2. Open CMS → Hero section
3. Upload background/portrait image
4. Confirm preview shows the image
5. Save section
6. Open public homepage — image should load from `https://api.mydomain.com/storage/cms/...`

### Check saved URL in database

```sql
SELECT data->'media'->'background'->>'src'
FROM cms_section_translations
LIMIT 5;
```

URLs should use your production `APP_URL`, not `localhost`.

---

## 8. Common production issues

| Symptom | Likely cause | Fix |
|---------|--------------|-----|
| **403** on `/storage/cms/...` | Missing `public/storage` symlink | `php artisan storage:link` |
| **403** on `/storage/cms/...` | Private disk `serve` route catching requests | Ensure symlink exists; see `config/filesystems.php` `local.serve` |
| **404** on `/storage/cms/...` | File not on server | rsync `storage/app/public/cms/` or re-upload |
| Images show `localhost:8000` on live site | Old data or wrong `APP_URL` when uploaded | Fix `APP_URL`, re-upload or SQL replace |
| Upload works, public site broken | `VITE_API_URL` still points to localhost | Rebuild client with production `VITE_API_URL` |
| CORS errors on API | Frontend origin not allowed | Add origin to `CORS_ALLOWED_ORIGINS` |
| **413** on upload | Nginx `client_max_body_size` too small | Increase to ≥ 10M |

---

## 9. Deploy script template (minimal)

```bash
#!/usr/bin/env bash
set -euo pipefail

APP_DIR=/var/www/david-cms

# Backend
cd "$APP_DIR/backendapi"
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link   # safe to run repeatedly
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Frontend
cd "$APP_DIR/client"
npm ci
VITE_API_URL=https://api.mydomain.com/api/v1 npm run build

# Reload services
sudo systemctl reload php8.3-fpm
sudo systemctl reload nginx
```

---

## 10. Quick checklist (copy/paste)

- [ ] `APP_URL=https://api.mydomain.com` (no trailing slash)
- [ ] `APP_DEBUG=false`, `APP_ENV=production`
- [ ] `CORS_ALLOWED_ORIGINS` includes `https://mydomain.com`
- [ ] `VITE_API_URL=https://api.mydomain.com/api/v1` at client build time
- [ ] `php artisan storage:link` executed
- [ ] `storage/app/public/cms` writable by PHP-FPM user
- [ ] Nginx serves `/storage/` from `storage/app/public/`
- [ ] Nginx `client_max_body_size` ≥ 10M
- [ ] SSL certificates installed for API and frontend domains
- [ ] CMS images copied or re-uploaded on production
- [ ] DB URLs updated if migrating from localhost
- [ ] `curl -I https://api.mydomain.com/storage/cms/<file>.png` returns 200
- [ ] Hero images visible on public homepage after save
