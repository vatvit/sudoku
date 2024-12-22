FROM webdevops/php-nginx-dev:8.2-alpine as base
# doc : https://dockerfile.readthedocs.io/en/latest/content/DockerImages/dockerfiles/php-nginx-dev.html

ENV WEB_DOCUMENT_ROOT=/app/backendApp/public

COPY ./infra/docker/php/nginx /opt/docker/etc/nginx/
COPY ./infra/docker/php/nginx.dev /opt/docker/etc/nginx/

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

RUN apk add nodejs npm