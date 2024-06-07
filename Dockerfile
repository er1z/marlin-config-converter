FROM php:8.3-cli-bookworm

ENV DEBIAN_FRONTEND=noninteractive
RUN apt update && apt install -y cpp zlib1g-dev libzip-dev zip && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip

ENV APP_ENV=prod

USER 1000
COPY --chown=1000:1000 converter.phar /app/converter.phar
CMD ["php", "/app/converter.phar"]
