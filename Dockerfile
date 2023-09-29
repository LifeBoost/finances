ARG PHP_VERSION="8.2"

#### \
FROM dunglas/frankenphp:latest-php${PHP_VERSION}-alpine AS php_upstream
FROM composer/composer:2-bin AS composer

FROM php_upstream AS app-base

WORKDIR /app

RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
	;

RUN set -eux; \
    install-php-extensions \
		apcu \
		intl \
		opcache \
		zip \
    	bcmath \
        sockets \
        pdo_mysql \
        pdo_pgsql \
        amqp \
    ;

COPY --link .docker/php/php.ini $PHP_INI_DIR/conf.d/
COPY --link --chmod=755 .docker/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link .docker/caddy/Caddyfile /etc/caddy/Caddyfile

ENTRYPOINT ["docker-entrypoint"]

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer --link /composer /usr/bin/composer

HEALTHCHECK CMD wget --no-verbose --tries=1 --spider http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile" ]

# Dev image
FROM app-base AS app-dev

ENV APP_ENV=dev XDEBUG_MODE=off

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
	install-php-extensions \
    	xdebug \
    ;

COPY --link .docker/php/php.dev.ini $PHP_INI_DIR/conf.d/

CMD [ "frankenphp", "run", "--config", "/etc/caddy/Caddyfile", "--watch" ]

# Prod image
FROM app-base AS app

ENV APP_ENV=prod
ENV FRANKENPHP_CONFIG="import worker.Caddyfile"

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY --link .docker/php/php.prod.ini $PHP_INI_DIR/conf.d/
COPY --link .docker/caddy/worker.Caddyfile /etc/caddy/worker.Caddyfile

# prevent the reinstallation of vendors at every changes in the source code
COPY --link composer.* symfony.* ./
RUN set -eux; \
	composer install --no-cache --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress

# copy sources
COPY --link . ./
RUN rm -Rf frankenphp/

RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
	chmod +x bin/console; sync;
