FROM alpine:3.11

RUN echo "@testing http://dl-cdn.alpinelinux.org/alpine/edge/testing" >> /etc/apk/repositories \
    && echo "@community http://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories \
    && apk --no-cache add php7 php7-fpm php7-curl php7-pdo_mysql php7-json php7-mbstring php7-opcache \
        php7-phar php7-tokenizer php7-dom php7-simplexml php7-pecl-memcache@community php7-pecl-pcov@testing \
    && rm /etc/php7/conf.d/pcov.ini

RUN addgroup -g 82 -S www-data \
    && adduser -u 82 -h /var/www -D -S -G www-data www-data \
    && mkdir -m 0777 /var/coverage

# php global setting
COPY ./php.ini /etc/php7/php.ini

# php fpm settings
COPY ./php-fpm.conf /etc/php7/php-fpm.d/www.conf

WORKDIR /var/www

EXPOSE 9000

VOLUME /var/coverage

CMD [ "php-fpm7", "-F" ]
