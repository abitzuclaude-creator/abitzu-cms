#!/usr/bin/env sh
set -e

# Generate an app key if one wasn't provided via env
if [ -z "$APP_KEY" ]; then
  echo "WARNING: APP_KEY not set — generating an ephemeral one."
  export APP_KEY=$(php artisan key:generate --show)
fi

# Cache config/routes/views for production speed
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations, then seed once (DatabaseSeeder self-guards against re-seeding)
php artisan migrate --force
php artisan db:seed --force || true

# Serve on the platform-provided port (Railway/Render set $PORT)
PORT="${PORT:-8080}"
echo "Starting Abitzu CMS on :$PORT"
exec php artisan serve --host=0.0.0.0 --port="$PORT"
