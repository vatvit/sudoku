FROM dunglas/mercure:latest as base
# doc : https://mercure.rocks/docs

FROM base as production

ENV MERCURE_PUBLISHER_JWT_KEY='UkXp2s5v8y/B?E(H+MbPeShVmYq3t6w9'
ENV MERCURE_SUBSCRIBER_JWT_KEY='UkXp2s5v8y/B?E(H+MbPeShVmYq3t6w9'

COPY ./infra/docker/mercure/caddy/Caddyfile /etc/caddy/Caddyfile

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile"]

FROM base

ENV MERCURE_PUBLISHER_JWT_KEY='UkXp2s5v8y/B?E(H+MbPeShVmYq3t6w9'
ENV MERCURE_SUBSCRIBER_JWT_KEY='UkXp2s5v8y/B?E(H+MbPeShVmYq3t6w9'

ENV SERVER_NAME='sudoku_mercure:80'

COPY ./infra/docker/mercure/caddy/Caddyfile.dev /etc/caddy/Caddyfile.dev

CMD ["caddy", "run", "--config", "/etc/caddy/Caddyfile.dev"]
