FROM nginx:latest as production

COPY ./infra/docker/nginx/conf/php.conf /etc/nginx/conf.d/default.conf

FROM production
