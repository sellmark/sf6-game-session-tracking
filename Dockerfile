FROM php:8.2-fpm

RUN groupadd -g 1000 appuser && \
    useradd -r -u 1000 -g appuser appuser

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    autoconf \
    gcc \
    make \
    g++ \
    libc-dev \
    pkg-config \
    libssl-dev

# Install PHP Redis extension
RUN pecl install redis && docker-php-ext-enable redis

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

COPY . /var/www


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN mkdir /home/appuser
RUN chown -R appuser:appuser /home/appuser
RUN chown -R appuser:appuser /var/www
USER appuser

RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
CMD ["php-fpm"]