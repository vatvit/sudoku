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

RUN apk add --no-cache bash

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash
RUN apk add symfony-cli

RUN apk -UvX https://dl-4.alpinelinux.org/alpine/edge/main add -u npm

RUN mkdir /.npm && chown www-data:www-data /.npm

USER www-data:www-data

EXPOSE 3000

WORKDIR /app
