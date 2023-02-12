FROM dunglas/mercure:latest as base

FROM base as production

FROM base

ENV MERCURE_PUBLISHER_JWT_KEY='publisher_key'
ENV MERCURE_SUBSCRIBER_JWT_KEY='subscriber_key'

ENV SERVER_NAME='localhost:2019'

COPY ./infra/docker/mercure/caddy/Caddyfile.dev /etc/caddy/Caddyfile.dev

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile.dev"]
