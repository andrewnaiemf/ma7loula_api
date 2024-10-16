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

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pcntl

COPY ./docker/production/php/supervisor /etc/supervisor

RUN groupadd -g ${GID} ma7lola && useradd -u ${UID} -g ${GID} ma7lola --password password

RUN mkdir /home/ma7lola

RUN chown -R ma7lola:ma7lola /var/log /run /home/ma7lola

COPY --chown=ma7lola:ma7lola . /var/www/html

RUN chown -R ma7lola:ma7lola /var/log /run

USER ma7lola

EXPOSE 9000