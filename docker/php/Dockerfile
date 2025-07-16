# Use the official PHP image as the base image
FROM php:8.4-fpm

# Copy composer.lock and composer.json
COPY composer.lock composer.json /var/www/

# Set working directory
WORKDIR /var/www

# Install dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev \
    libpq-dev \
    libgd-dev \
    ghostscript \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick
    # supervisor \
# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#Mine

# Install extensions
RUN docker-php-ext-enable imagick
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring zip exif pcntl sodium
RUN docker-php-ext-configure gd --with-external-gd
RUN docker-php-ext-install gd

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# RUN mkdir -p "/etc/supervisor/logs"

#Copy supervisor configuration file

# COPY ./supervisor/supervisord.conf /etc/supervisor/supervisord.conf

# Change current user to www
USER www

# Expose port 9000 and start php-fpm server
EXPOSE 9000

#Start Supervisor to magage processes
# CMD ["/usr/bin/supervisord", "-n", "-c",  "/etc/supervisor/supervisord.conf"]

CMD ["php-fpm"]


