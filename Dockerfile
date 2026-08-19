FROM dunglas/frankenphp:php8.4

WORKDIR /app

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    wget \
    libpq-dev \
    libzip-dev \
    libicu-dev \
    autoconf \
    g++ \
    make \
    inotify-tools \
    postgresql-client \
    && docker-php-ext-install \
        pdo_pgsql \
        pgsql \
        intl \
        zip \
        sockets \
        pcntl \
        opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Source Laravel
COPY . /app

# Install dependency Laravel
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# Custom Caddy
COPY Caddyfile /etc/frankenphp/Caddyfile

EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile"]