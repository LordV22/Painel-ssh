FROM dunglas/frankenphp:1-bookworm

RUN install-php-extensions mysqli pdo pdo_mysql mbstring curl gd

COPY . /app
WORKDIR /app

EXPOSE 8080
