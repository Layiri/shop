FROM php:8.0.7-fpm-alpine


# Install packages for Linux Apline
RUN apk update
RUN apk add --no-cache --virtual .phpize-deps $PHPIZE_DEPS
RUN apk add --no-cache \
        nano \
        freetype \
        libpng \
        libltdl \
        libjpeg-turbo \
        libintl \
        icu

RUN apk add --no-cache freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        icu-dev \
        libmcrypt-dev \
        libxml2-dev \
        libzip-dev

RUN apk add --update npm

# Install packages for PHP
RUN pecl install redis
RUN docker-php-ext-enable redis
RUN docker-php-source delete

RUN pecl install "xdebug-3.0.4"
RUN docker-php-ext-enable xdebug
RUN docker-php-source delete

RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg nproc=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1)
RUN docker-php-ext-install -j$(nproc) gd
RUN docker-php-ext-install opcache
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install bcmath

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove dev packages from Linux Aplina
RUN apk del --no-cache freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        icu-dev \
        libmcrypt-dev \
        libxml2-dev \
        libzip-dev

# Create system user to run Composer and Artisan Commands
ARG user="shop"
ARG uid

RUN echo "$user:x:$uid:$uid::/home/$user:" >> /etc/passwd
RUN echo "$user:!:$(($(date +%s) / 60 / 60 / 24)):0:99999:7:::" >> /etc/shadow
RUN echo "$user:x:$uid:" >> /etc/group
RUN mkdir /home/$user && chown $user:$user /home/$user

# Set working directory
WORKDIR /var/www/html

USER $user

#CMD ["php-fpm"]
