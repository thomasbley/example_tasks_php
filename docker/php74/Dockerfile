FROM php:7.4-fpm-alpine as fpm

RUN mkdir -p /usr/src/php/ext/pcov /usr/src/php/ext/memcache \
  && apk --no-cache add zlib-dev \
  && curl -sS 'https://pecl.php.net/get/pcov-1.0.6.tgz' | tar xz -C /usr/src/php/ext/pcov --strip-components=1 \
  && curl -sS 'https://pecl.php.net/get/memcache-4.0.5.2.tgz' | tar xz -C /usr/src/php/ext/memcache --strip-components=1 \
  && docker-php-ext-install -j$(nproc) pdo_mysql pcov memcache opcache \
  && docker-php-source delete \
  && apk --no-cache del zlib-dev \
  && rm /usr/local/etc/php/conf.d/docker-php-ext-pcov.ini

# php global setting
COPY ./php.ini /usr/local/etc/php/php.ini

# php fpm settings
COPY ./php-fpm.conf /usr/local/etc/php-fpm.d/www.conf


# use multi-stage to reduce container size from 82M to 45M
FROM alpine:3.11

RUN mkdir -m 0777 /var/coverage

COPY --from=fpm /etc/passwd /etc/group /etc/
COPY --from=fpm /usr/local/bin/php /usr/local/bin/
COPY --from=fpm /usr/local/sbin/php-fpm /usr/local/sbin/
COPY --from=fpm /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=fpm /usr/lib/ /usr/lib/
COPY --from=fpm /usr/local/etc/ /usr/local/etc/

WORKDIR /var/www

EXPOSE 9000

VOLUME /var/coverage

CMD [ "php-fpm", "-F" ]