FROM debian:buster-slim

RUN apt-get -y update \
    && DEBIAN_FRONTEND=noninteractive apt-get -y --no-install-recommends install apt-transport-https curl ca-certificates \
    && echo "deb https://packages.sury.org/php/ buster main" >/etc/apt/sources.list.d/ondrej-debian-php.list \
    && curl -s https://packages.sury.org/php/apt.gpg >/etc/apt/trusted.gpg.d/php.gpg \
    && apt-get -y update \
    && DEBIAN_FRONTEND=noninteractive apt-get -y --no-install-recommends install php8.0-cli \
        php8.0-mysql \
        php8.0-mbstring \
        php8.0-curl \
        php8.0-xml \
        php8.0-memcache \
    && curl -#L -o /tmp/code-server.deb https://github.com/cdr/code-server/releases/download/v3.8.0/code-server_3.8.0_amd64.deb \
    && dpkg -i /tmp/code-server.deb \
    && apt-get upgrade -y \
    && apt-get clean \
    && rm -rf /tmp/* /var/lib/apt/lists/* /var/cache/apt/archives/*

ARG UID
RUN useradd -m docker -u ${UID} -d /config -s /bin/bash

USER docker

# bmewburn.vscode-intelephense-client does not yet support phar files in include path
RUN curl -s https://phar.phpunit.de/phpunit-9.5.0.phar >/config/phpunit.phar \
    && php -r "(new Phar('/config/phpunit.phar'))->extractTo('/config/libs/phpunit/');" \
    && rm /config/phpunit.phar

RUN code-server --install-extension bmewburn.vscode-intelephense-client \
                --install-extension fterrag.vscode-php-cs-fixer \
                --install-extension alphabotsec.vscode-eclipse-keybindings \
    && chmod -R a+w /config

WORKDIR /var/www

ENV PHP_CS_FIXER_IGNORE_ENV=1

EXPOSE 8000

CMD [ \
    "code-server", \
    "--auth", "none", \
    "--disable-telemetry", \
    "--disable-update-check", \
    "--bind-addr", "0.0.0.0:8000" \
]
