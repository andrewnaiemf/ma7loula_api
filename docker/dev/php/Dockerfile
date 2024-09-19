FROM php:8.2-fpm

ARG UID
ARG GID

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    curl \
    g++ \
    git \
    libbz2-dev \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmcrypt-dev \
    libpng-dev \
    libreadline-dev \
    sudo \
    unzip \
    zip \
    libzip-dev \
    libonig-dev \
    supervisor \
    nano \
 && rm -rf /var/lib/apt/lists/*


RUN docker-php-ext-install \
    bcmath \
    bz2 \
    calendar \
    iconv \
    intl \
    mbstring \
    opcache \
    pdo_mysql \
    zip

RUN pecl install redis xdebug\
    && docker-php-ext-enable redis xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./docker/dev/php/supervisor /etc/supervisor
COPY ./docker/dev/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY ./docker/dev/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# COPY . .

RUN groupadd -g ${GID} ma7lola && useradd -u ${UID} -g ${GID} ma7lola --password password

RUN mkdir /home/ma7lola

RUN chown -R ma7lola:ma7lola /var/log /run /home/ma7lola

USER ma7lola

# RUN composer install

EXPOSE 9000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/php-fpm.conf"]