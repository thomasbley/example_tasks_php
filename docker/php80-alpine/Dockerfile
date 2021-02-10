FROM alpine:3.13

RUN echo "@testing http://dl-cdn.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories \
    && apk --no-cache add php8 php8-fpm php8-curl php8-pdo_mysql php8-json php8-mbstring php8-opcache \
        php8-phar php8-tokenizer php8-dom php8-simplexml php8-pecl-memcache php8-pecl-pcov@testing \
    && rm /etc/php8/conf.d/pcov.ini \
    && ln -s /usr/bin/php8 /usr/bin/php

RUN addgroup -g 82 -S www-data \
    && adduser -u 82 -h /var/www -D -S -G www-data www-data \
    && mkdir -m 0777 /var/coverage

# php global setting
COPY ./php.ini /etc/php8/php.ini

# php fpm settings
COPY ./php-fpm.conf /etc/php8/php-fpm.d/www.conf

WORKDIR /var/www

EXPOSE 9000

VOLUME /var/coverage

CMD [ "php-fpm8", "-F" ]
