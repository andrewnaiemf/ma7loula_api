FROM php:8.1-fpm

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

COPY ./pickle .
RUN chmod +x pickle \
    && ./pickle install --defaults redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./docker/production/php/supervisor /etc/supervisor

RUN groupadd -g 1000 ma7lola && useradd -u 1000 -g 1000 ma7lola --password password

COPY --chown=ma7lola:ma7lola . /var/www/html
COPY --chown=ma7lola:ma7lola ./docker/production/run.sh /var/www/html/run.sh
RUN chmod +x /var/www/html/run.sh

RUN chown -R ma7lola:ma7lola /var/log /run

USER ma7lola

RUN composer install --optimize-autoloader --no-dev

EXPOSE 9000