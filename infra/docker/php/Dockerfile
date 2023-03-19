FROM webdevops/php-nginx:8.2-alpine as base
# doc : https://dockerfile.readthedocs.io/en/latest/content/DockerImages/dockerfiles/php-nginx.html

ENV WEB_DOCUMENT_ROOT=/app/backendApp/public

COPY ./infra/docker/php/nginx /opt/docker/etc/nginx/

FROM base as production

RUN rm /opt/docker/etc/nginx/vhost.common.d/10-location-mercure.conf

COPY --chown=application:application ./src /app

FROM base
