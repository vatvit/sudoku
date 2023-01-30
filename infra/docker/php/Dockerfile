FROM webdevops/php-nginx:8.2-alpine as production
# doc : https://dockerfile.readthedocs.io/en/latest/content/DockerImages/dockerfiles/php-nginx.html

COPY ./src /app

ENV WEB_DOCUMENT_ROOT=/app/public

FROM production
