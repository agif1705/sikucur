FROM dunglas/frankenphp

# Install PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    redis \
    opcache \
    pcntl

# Copy project files
COPY . /app
WORKDIR /app