#!/bin/bash
# ====================================================
# InfyHMS - Docker Entrypoint Script
# ====================================================

set -e

echo "🏥 ============================================"
echo "🏥  InfyHMS Hospital Management System"
echo "🏥  Starting up..."
echo "🏥 ============================================"

# ---- Wait for MySQL to be ready ----
echo "⏳ Waiting for MySQL database..."
max_tries=60
counter=0
until php -r "
    \$conn = @mysqli_connect(
        getenv('DB_HOST') ?: 'mysql',
        getenv('DB_USERNAME') ?: 'hms_user',
        getenv('DB_PASSWORD') ?: 'hms_secret',
        getenv('DB_DATABASE') ?: 'hms',
        getenv('DB_PORT') ?: 3306
    );
    if (\$conn) { echo 'ok'; exit(0); } else { exit(1); }
" 2>/dev/null; do
    counter=$((counter+1))
    if [ $counter -ge $max_tries ]; then
        echo "❌ Could not connect to MySQL after ${max_tries} attempts. Exiting."
        exit 1
    fi
    echo "   → MySQL not ready yet... (attempt ${counter}/${max_tries})"
    sleep 2
done
echo "✅ MySQL is ready!"

# ---- Generate APP_KEY if not set ----
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force
fi

# ---- Create .env if missing (copy from .env.example) ----
if [ ! -f ".env" ]; then
    echo "📄 Creating .env from .env.example..."
    cp .env.example .env
fi

# ---- Set correct permissions ----
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ---- Create storage symlink ----
echo "🔗 Creating storage symlink..."
php artisan storage:link --force 2>/dev/null || true

# ---- Run Database Migrations ----
echo "🗄️  Running database migrations..."
php artisan migrate --force

# ---- Cache Configuration (Production) ----
if [ "$APP_ENV" = "production" ]; then
    echo "⚡ Caching configuration for production..."
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
else
    echo "🔧 Development mode - skipping cache..."
    php artisan config:clear
    php artisan cache:clear
fi

# ---- Start Cron (for Laravel Scheduler) ----
echo "⏰ Starting cron service..."
service cron start

# ---- Start Supervisor (Queue Workers) ----
echo "📋 Starting supervisor..."
supervisord -c /etc/supervisor/conf.d/supervisord.conf &

echo ""
echo "✅ ============================================"
echo "✅  InfyHMS is READY!"
echo "✅  App URL: http://localhost:${APP_PORT:-8080}"
echo "✅  phpMyAdmin: http://localhost:${PMA_PORT:-8081}"
echo "✅ ============================================"
echo ""

# ---- Start Apache in foreground ----
exec apache2-foreground
