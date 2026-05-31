# ---- Abitzu CMS — production image for Railway/Render ----
FROM php:8.3-cli-bookworm

# System deps + PHP extensions (pdo_mysql for Railway MySQL, plus common Laravel ones)
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libonig-dev libxml2-dev \
        ca-certificates curl gnupg \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath \
    && rm -rf /var/lib/apt/lists/*

# Node 20 (for building Vite assets)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP deps first (better layer caching)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# Install JS deps
COPY package.json package-lock.json ./
RUN npm ci

# App source
COPY . .

# Finish composer autoload + build front-end assets
RUN composer dump-autoload --optimize --no-dev --no-scripts \
    && npm run build \
    && rm -rf node_modules

# Permissions for Laravel runtime dirs
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 8080
CMD ["sh", "docker/start.sh"]
