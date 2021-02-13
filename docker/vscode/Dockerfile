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
        php8.0-zip \
    && curl -#L -o /tmp/code-server.deb https://github.com/cdr/code-server/releases/download/v3.9.0/code-server_3.9.0_amd64.deb \
    && dpkg -i /tmp/code-server.deb \
    && apt-get upgrade -y \
    && apt-get clean \
    && rm -rf /tmp/* /var/lib/apt/lists/* /var/cache/apt/archives/*

ARG UID
RUN useradd -m docker -u ${UID} -d /config -s /bin/bash

USER docker

# ENV SERVICE_URL https://marketplace.visualstudio.com/_apis/public/gallery
# ENV ITEM_URL https://marketplace.visualstudio.com/items

ADD getpsalm.psalm-vscode-plugin-1.2.2.vsix /tmp/

RUN code-server --install-extension fterrag.vscode-php-cs-fixer \
                --install-extension alphabotsec.vscode-eclipse-keybindings \
                --install-extension bmewburn.vscode-intelephense-client \
                --install-extension /tmp/getpsalm.psalm-vscode-plugin-1.2.2.vsix \
    && chmod -R a+w /config

WORKDIR /var/www

EXPOSE 8000

CMD [ \
    "code-server", \
    "--auth", "none", \
    "--disable-telemetry", \
    "--disable-update-check", \
    "--home", "http://127.0.0.1:8000/?folder=/var/www", \
    "--bind-addr", "0.0.0.0:8000" \
]
