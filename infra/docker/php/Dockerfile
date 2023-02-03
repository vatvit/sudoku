FROM webdevops/php-nginx:8.2-alpine as base
# doc : https://dockerfile.readthedocs.io/en/latest/content/DockerImages/dockerfiles/php-nginx.html

ENV WEB_DOCUMENT_ROOT=/app/public

COPY ./infra/docker/php/nginx /opt/docker/etc/nginx/

FROM base as production

COPY ./src /app

FROM base
