#!/bin/bash
# ====================================================
# InfyHMS — Render.com Startup Script
# ====================================================
# Render automatically sets:
#   PORT        → web traffic port
#   RENDER=true → identifies Render environment
# ====================================================

set -e

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║   🏥  InfyHMS v14.0 — Render Startup    ║"
echo "╚══════════════════════════════════════════╝"
echo ""

# ── Apache Port: Render sets $PORT dynamically ──────
LISTEN_PORT="${PORT:-10000}"
echo "▶ Configuring Apache on port $LISTEN_PORT..."

# Update Apache to listen on Render's assigned port
sed -i "s/Listen 80/Listen $LISTEN_PORT/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$LISTEN_PORT>/" /etc/apache2/sites-available/000-default.conf

# ── Validate .env exists ─────────────────────────────
if [ ! -f "/var/www/html/.env" ]; then
    echo "📄 No .env found — creating from environment variables..."
    cat > /var/www/html/.env << EOF
APP_NAME="InfyHMS"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}
FILESYSTEM_DISK=public
MEDIA_DISK=public

LOG_CHANNEL=errorlog
LOG_LEVEL=error

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

# PlanetScale requires SSL
MYSQL_ATTR_SSL_CA=${MYSQL_ATTR_SSL_CA:-/etc/ssl/certs/ca-certificates.crt}

BROADCAST_DRIVER=log
CACHE_DRIVER=${CACHE_DRIVER:-file}
SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=120
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}

REDIS_HOST=${REDIS_HOST:-127.0.0.1}
REDIS_PASSWORD=${REDIS_PASSWORD:-null}
REDIS_PORT=${REDIS_PORT:-6379}

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST:-smtp.mailtrap.io}
MAIL_PORT=${MAIL_PORT:-2525}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-null}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS}
MAIL_FROM_NAME="InfyHMS"

STRIPE_KEY=${STRIPE_KEY}
STRIPE_SECRET_KEY=${STRIPE_SECRET_KEY}

TWILIO_SID=${TWILIO_SID}
TWILIO_TOKEN=${TWILIO_TOKEN}
TWILIO_FROM_NUMBER=${TWILIO_FROM_NUMBER}

ZOOM_API_URL=${ZOOM_API_URL}
ZOOM_API_KEY=${ZOOM_API_KEY}
ZOOM_API_SECRET=${ZOOM_API_SECRET}

NOCAPTCHA_SECRET=${NOCAPTCHA_SECRET}
NOCAPTCHA_SITEKEY=${NOCAPTCHA_SITEKEY}

VERSION_NUMBER=true
UPGRADE_MODE=false
COOKIE_CONSENT_ENABLED=true
DEBUGBAR_ENABLED=false
QUERY_DETECTOR_ENABLED=false
EOF
fi

# ── Generate APP_KEY if missing ──────────────────────
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── Set Permissions ──────────────────────────────────
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ── Storage Symlink ──────────────────────────────────
echo "🔗 Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# ── Wait for Database ────────────────────────────────
echo "⏳ Checking database connection..."
max_tries=30
counter=0
until php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . (getenv('DB_PORT') ?: 3306) . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::MYSQL_ATTR_SSL_CA => getenv('MYSQL_ATTR_SSL_CA') ?: '/etc/ssl/certs/ca-certificates.crt',
             PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false]
        );
        echo 'ok';
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    counter=$((counter+1))
    if [ $counter -ge $max_tries ]; then
        echo "⚠️  DB connection failed after ${max_tries} tries — proceeding anyway..."
        break
    fi
    echo "   DB not ready yet (${counter}/${max_tries})..."
    sleep 3
done
echo "✅ Database connected!"

# ── Run Migrations ───────────────────────────────────
echo "🗄️  Running database migrations..."
php artisan migrate --force --no-interaction
# ── Production Cache ─────────────────────────────────
echo "⚡ Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:clear

# ── Start Supervisor (Queue Worker) ──────────────────
echo "📋 Starting supervisor (queue worker)..."
service supervisor start 2>/dev/null || true
supervisorctl reread 2>/dev/null || true
supervisorctl update 2>/dev/null || true

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║   ✅  InfyHMS is LIVE on port $LISTEN_PORT    ║"
echo "╚══════════════════════════════════════════╝"
echo ""

# ── Start Apache (foreground) ────────────────────────
exec apache2-foreground
