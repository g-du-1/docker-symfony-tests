FROM php:7.4-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

RUN apk update
RUN apk upgrade
RUN apk add bash
RUN apk add curl
RUN apk add wget
RUN apk add git
RUN apk add yarn
RUN apk add --update nodejs npm
RUN git config --global user.email "dudaszeg1@gmail.com"
RUN git config --global user.name "g-du-1"

# INSTALL COMPOSER
RUN curl -s https://getcomposer.org/installer | php
RUN alias composer='php composer.phar'
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN wget https://get.symfony.com/cli/installer -O - | bash

RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug-3.0.0 \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps

# Configure Xdebug
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log=/var/www/html/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini

# Warning generated from top line of this file. TODO remove this