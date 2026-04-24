# Gunakan FrankenPHP PHP 8.4
FROM dunglas/frankenphp:php8.4

WORKDIR /app

# Install dependency sistem
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

# Copy composer dari image resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy source code
COPY . .

# Jangan jalankan composer install di build, jalankan di runtime supaya volume host bisa dipakai
EXPOSE 8001