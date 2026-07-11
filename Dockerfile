FROM dunglas/frankenphp:1-bookworm

RUN install-php-extensions mysqli pdo pdo_mysql mbstring curl gd

RUN printf ':8080 {\n\troot * /app\n\tphp_server\n}\n' > /etc/caddy/Caddyfile

COPY . /app
WORKDIR /app

EXPOSE 8080
