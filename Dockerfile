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
    ca-certificates \
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

# Node.js 22 + npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && node -v \
    && npm -v

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Source Laravel
COPY . /app

# Laravel dependencies
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# Frontend dependencies + Vite build
RUN npm install
RUN npm run build

EXPOSE 8001