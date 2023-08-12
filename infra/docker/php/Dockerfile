FROM webdevops/php-nginx:8.2-alpine as base
# doc : https://dockerfile.readthedocs.io/en/latest/content/DockerImages/dockerfiles/php-nginx.html

ENV WEB_DOCUMENT_ROOT=/app/backendApp/public

COPY ./infra/docker/php/nginx /opt/docker/etc/nginx/

COPY --chown=application:application ./src /app

FROM base as production

RUN rm /opt/docker/etc/nginx/vhost.common.d/10-location-mercure.conf

###> symfony/mercure-bundle ###
ENV MERCURE_JWT_SECRET="UkXp2s5v8y/B?E(H+MbPeShVmYq3t6w9"
###< symfony/mercure-bundle ###

FROM base

###> symfony/mercure-bundle ###
# See https://symfony.com/doc/current/mercure.html#configuration
# The URL of the Mercure hub, used by the app to publish updates (can be a local URL)
ENV MERCURE_URL=http://sudoku_mercure/.well-known/mercure
# The public URL of the Mercure hub, used by the browser to connect
ENV MERCURE_PUBLIC_URL=http://localhost/.well-known/mercure
# The secret used to sign the JWTs
ENV MERCURE_JWT_SECRET="UkXp2s5v8y/B?E(H+MbPeShVmYq3t6w9"
###< symfony/mercure-bundle ###
