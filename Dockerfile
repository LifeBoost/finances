ARG PHP_VERSION="8.2"
ARG PHP_ALPINE_VERSION="3.17"
ARG XDEBUG_VERSION="3.2.0"
ARG COMPOSER_VERSION="2"
ARG COMPOSER_AUTH
ARG APP_BASE_DIR="."
ARG NGINX_VERSION="1.21"

# ======================================================================================================================
#                                                 --- PHP/FPM ---
# ======================================================================================================================
FROM composer:${COMPOSER_VERSION} AS composer
FROM php:${PHP_VERSION}-fpm-alpine${PHP_ALPINE_VERSION} AS base

ARG XDEBUG_VERSION

SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

RUN RUNTIME_DEPS="tini fcgi"; \
    SECURITY_UPGRADES="curl"; \
    apk add --no-cache --upgrade ${RUNTIME_DEPS} ${SECURITY_UPGRADES}

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libzip-dev \
        icu-dev \
        linux-headers \
    && docker-php-ext-install -j$(nproc) \
        bcmath \
        sockets \
        intl \
        opcache \
        pdo_mysql \
        pdo_pgsql \
        zip \
    && pecl install apcu && docker-php-ext-enable apcu \
    && rm -r /tmp/pear; \
    out="$(php -r 'exit(0);')"; \
        [ -z "$out" ]; err="$(php -r 'exit(0);' 3>&1 1>&2 2>&3)"; \
        [ -z "$err" ]; extDir="$(php -r 'echo ini_get("extension_dir");')"; \
        [ -d "$extDir" ]; \
        runDeps="$( \
            scanelf --needed --nobanner --format '%n#p' --recursive "$extDir" \
                | tr ',' '\n' | sort -u | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
        )"; \
        apk add --no-network --virtual .php-extensions-rundeps $runDeps; \
        apk del --no-network .build-deps; \
        err="$(php --version 3>&1 1>&2 2>&3)"; 	[ -z "$err" ]

## Install AMQP
RUN set -eux; \
  apk add rabbitmq-c-dev; \
  pecl install amqp-1.11.0; \
  docker-php-ext-enable amqp; \
  php -m | grep -oiE '^amqp$'

RUN apk add --no-cache --virtual .build-xdebug linux-headers && pecl install xdebug-${XDEBUG_VERSION}

RUN deluser --remove-home www-data && adduser -u1000 -D www-data && rm -rf /var/www /usr/local/etc/php-fpm.d/* && \
    mkdir -p /var/www/.composer /app && chown -R www-data:www-data /app /var/www/.composer; \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# PHP config
COPY .docker/php/base-* $PHP_INI_DIR/conf.d

# FPM config
COPY .docker/fpm/*.conf /usr/local/etc/php-fpm.d/

# Scripts
COPY .docker/entrypoint/*-base \
     .docker/fpm/healthcheck-fpm \
     .docker/scripts/command-loop* \
     /usr/local/bin/

RUN chmod +x /usr/local/bin/*-base /usr/local/bin/healthcheck-fpm /usr/local/bin/command-loop*

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# MISC
WORKDIR /app
USER www-data

ENV APP_ENV prod
ENV APP_DEBUG 0

RUN php-fpm -t

HEALTHCHECK CMD ["healthcheck-fpm"]

ENTRYPOINT ["entrypoint-base"]
CMD ["php-fpm"]

FROM composer AS vendor

ARG PHP_VERSION
ARG COMPOSER_AUTH
ARG APP_BASE_DIR

ENV COMPOSER_AUTH $COMPOSER_AUTH

WORKDIR /app

COPY $APP_BASE_DIR/composer.json composer.json
COPY $APP_BASE_DIR/composer.lock composer.lock

RUN composer config platform.php ${PHP_VERSION}; \
    composer install -n --no-progress --ignore-platform-reqs --no-dev --prefer-dist --no-scripts --no-autoloader

# =========== #
#     PROD    #
# =========== #
FROM base AS app

ARG APP_BASE_DIR
USER root

COPY .docker/php/prod-* $PHP_INI_DIR/conf.d/
COPY .docker/entrypoint/*-prod /usr/local/bin/
RUN chmod +x /usr/local/bin/*-prod && pecl uninstall xdebug

USER www-data

COPY --chown=www-data:www-data --from=vendor /app/vendor/ /app/vendor/
COPY --chown=www-data:www-data $APP_BASE_DIR/ .

RUN composer install --optimize-autoloader --apcu-autoloader --no-dev -n --no-progress && \
    composer check-platform-reqs

ENTRYPOINT ["entrypoint-prod"]
CMD ["php-fpm"]

# =========== #
#     DEV     #
# =========== #
FROM base AS app-dev

ENV APP_ENV dev
ENV APP_DEBUG 1

USER root

RUN apk --no-cache add git openssh bash; \
    docker-php-ext-enable xdebug

ENV XDEBUG_CLIENT_HOST="host.docker.internal"

COPY .docker/php/dev-* $PHP_INI_DIR/conf.d/
COPY .docker/entrypoint/*-dev /usr/local/bin/
RUN chmod +x /usr/local/bin/*-dev; \
    mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

USER www-data

ENTRYPOINT ["entrypoint-dev"]
CMD ["php-fpm"]

# ======================================================================================================================
#                                                 --- NGINX ---
# ======================================================================================================================
FROM nginx:${NGINX_VERSION}-alpine AS nginx

RUN rm -rf /var/www/* /etc/nginx/conf.d/* && adduser -u 1000 -D -S -G www-data www-data
COPY .docker/nginx/nginx-* /usr/local/bin/
COPY .docker/nginx/ /etc/nginx
RUN chown -R www-data /etc/nginx/ && chmod +x /usr/local/bin/nginx-*

ENV PHP_FPM_HOST "localhost"
ENV PHP_FPM_PORT "9000"
ENV NGINX_LOG_FORMAT "json"

EXPOSE 8080

USER www-data

HEALTHCHECK CMD ["nginx-healthcheck"]
ENTRYPOINT ["nginx-entrypoint"]

# =========== #
#     PROD    #
# =========== #
FROM nginx AS web

USER root

RUN SECURITY_UPGRADES="curl"; \
    apk add --no-cache --upgrade ${SECURITY_UPGRADES}

USER www-data

COPY --chown=www-data:www-data --from=app /app/public /app/public

# =========== #
#     DEV     #
# =========== #
FROM nginx AS web-dev

ENV NGINX_LOG_FORMAT "combined"

COPY --chown=www-data:www-data .docker/nginx/dev/*.conf /etc/nginx/conf.d/
COPY --chown=www-data:www-data .docker/nginx/dev/certs/ /etc/nginx/certs/
