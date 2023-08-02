FROM php:8.1-fpm-alpine

ARG PUID=1000

RUN set -xe \
    && apk add --no-cache --virtual .build-deps bash curl g++ make autoconf;

RUN set eux; \
    apk add --no-cache --virtual build-base \
      rabbitmq-c-dev \
      libzip-dev \
    && pecl install \
      amqp \
    && docker-php-ext-install -j$(nproc) \
      pdo pdo_mysql \
      bcmath \
      sockets \
      zip \
    && docker-php-ext-enable \
      pdo_mysql \
      amqp \
      opcache \
    && apk del --no-network .build-deps \
    && rm -rf /tmp/pear ~/.pearrc

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer && rm -rf /root/.composer/cache

RUN apk --no-cache add bash shadow acl && usermod -u ${PUID} www-data; chown -R www-data:www-data /var/www