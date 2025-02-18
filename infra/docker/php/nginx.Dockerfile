FROM nginx:latest as base

COPY ./infra/docker/php/nginx /etc/nginx/conf.d/

CMD ["nginx", "-g", "daemon off;"]

FROM base

COPY ./infra/docker/php/nginx.dev /etc/nginx/conf.d/
