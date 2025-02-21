FROM php:8.2-fpm-alpine as base

# install packages for deps
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf bash

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN pecl install redis \
    && docker-php-ext-enable redis

# Clean packages for deps
RUN apk del -f .build-deps

FROM base

ENV XDEBUG_CLIENT_HOST="host.docker.internal"

###> symfony/mercure-bundle ###
# See https://symfony.com/doc/current/mercure.html#configuration
# The URL of the Mercure hub, used by the app to publish updates (can be a local URL)
ENV MERCURE_URL=http://sudoku_mercure/.well-known/mercure
# The public URL of the Mercure hub, used by the browser to connect
ENV MERCURE_PUBLIC_URL=http://localhost/.well-known/mercure
# The secret used to sign the JWTs
ENV MERCURE_JWT_SECRET="UkXp2s5v8y/B?E(H+MbPeShVmYq3t6w9"
###< symfony/mercure-bundle ###

ENV CACHE_HOST=sudoku_cache
ENV CACHE_PORT=6379

EXPOSE 3000

RUN apk add --no-cache bash

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS autoconf linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps

# Create Xdebug configuration
ENV XDEBUG_CLIENT_HOST="host.docker.internal"
ENV XDEBUG_MODE=debug
#ENV XDEBUG_START_WITH_REQUEST=yes
ENV XDEBUG_CLIENT_PORT=9003
RUN echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/xdebug.ini \
#    && echo "xdebug.start_with_request=${XDEBUG_START_WITH_REQUEST}" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=${XDEBUG_CLIENT_PORT}" >> /usr/local/etc/php/conf.d/xdebug.ini

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli

RUN apk -UvX https://dl-4.alpinelinux.org/alpine/edge/main add -u npm

RUN mkdir /.npm && chown www-data:www-data /.npm

USER www-data:www-data

COPY ./infra/docker/php/.bashrc /home/www-data/

WORKDIR /app
