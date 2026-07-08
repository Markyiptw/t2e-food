FROM ubuntu:24.04

LABEL maintainer="Taylor Otwell"

ARG WWWGROUP
ARG NODE_VERSION=24
ARG MYSQL_CLIENT="mysql-client"
ARG POSTGRES_VERSION=18

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
ENV SUPERVISOR_PHP_COMMAND="/usr/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80"
ENV SUPERVISOR_PHP_USER="sail"
ENV PLAYWRIGHT_BROWSERS_PATH=0

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN echo "Acquire::http::Pipeline-Depth 0;" > /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::http::No-Cache true;" >> /etc/apt/apt.conf.d/99custom && \
    echo "Acquire::BrokenProxy    true;" >> /etc/apt/apt.conf.d/99custom

# Phase 1: install bootstrap tools needed to fetch GPG keys
RUN apt-get update && apt-get upgrade -y \
    && mkdir -p /etc/apt/keyrings \
    && apt-get install -y gnupg curl ca-certificates \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Phase 2: register all third-party APT repos
RUN curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0xb8dc7e53946656efbce4c1dd71daeaab4ad4cab6' | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu noble main" > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_VERSION.x nodistro main" > /etc/apt/sources.list.d/nodesource.list \
    && curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | gpg --dearmor | tee /etc/apt/keyrings/yarn.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/yarn.gpg] https://dl.yarnpkg.com/debian/ stable main" > /etc/apt/sources.list.d/yarn.list \
    && curl -sS https://www.postgresql.org/media/keys/ACCC4CF8.asc | gpg --dearmor | tee /etc/apt/keyrings/pgdg.gpg >/dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/pgdg.gpg] http://apt.postgresql.org/pub/repos/apt noble-pgdg main" > /etc/apt/sources.list.d/pgdg.list

# Phase 3: single update → install all packages → post-install → cleanup
RUN apt-get update \
    && apt-get install -y \
    gosu \
    zip unzip git supervisor sqlite3 libcap2-bin libpng-dev python3 dnsutils librsvg2-bin fswatch ffmpeg nano \
    libgd3 \
    php8.5-cli \
    php8.5-dev \
    php8.5-pgsql \
    php8.5-sqlite3 \
    php8.5-gd \
    php8.5-curl \
    php8.5-mongodb \
    php8.5-imap \
    php8.5-mysql \
    php8.5-mbstring \
    php8.5-xml \
    php8.5-zip \
    php8.5-bcmath \
    php8.5-soap \
    php8.5-intl \
    php8.5-readline \
    php8.5-ldap \
    php8.5-msgpack \
    php8.5-igbinary \
    php8.5-redis \
    #php8.5-swoole \
    php8.5-memcached \
    php8.5-pcov \
    php8.5-imagick \
    php8.5-xdebug \
    nodejs \
    yarn \
    $MYSQL_CLIENT \
    postgresql-client-$POSTGRES_VERSION \
    ruby-full \
    sudo \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && npm install -g npm \
    && npm install -g pnpm \
    && npm install -g bun \
    && npx playwright install-deps \
    && npm i -g agent-browser \
    && npm i -g opencode-ai \
    && gem install kamal \
    && apt-get -y autoremove \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN setcap "cap_net_bind_service=+ep" /usr/bin/php8.5

RUN userdel -r ubuntu
RUN groupadd --force -g $WWWGROUP sail
RUN useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail
RUN git config --global --add safe.directory /var/www/html

RUN adduser sail sudo
RUN echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

RUN echo 'alias kamal="dotenv -f .env.production kamal"' >> /home/sail/.bashrc

COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY php.ini /etc/php/8.5/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

RUN mkdir -p /home/sail/.local/share/opencode
RUN chown -R sail:sail /home/sail/.local/share/opencode

RUN agent-browser install --with-deps

EXPOSE 80/tcp

ENTRYPOINT ["start-container"]
