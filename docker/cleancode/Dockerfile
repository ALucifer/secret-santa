FROM jakzal/phpqa:php8.3

RUN echo php -v

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
    bash-completion \
    fish \
    procps \
    nano \
    git \
    unzip \
    libicu-dev \
    zlib1g-dev \
    libxml2 \
    libxml2-dev \
    libreadline-dev \
    supervisor \
    cron \
    sudo \
    libzip-dev \
    wget \
    librabbitmq-dev \
    libpq-dev \
    graphviz \
    && pecl install amqp \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
        sockets \
        intl \
        opcache \
        zip \
        pdo_pgsql \
        pgsql \
    && docker-php-ext-enable amqp \
    && rm -rf /tmp/* /var/list/apt/* /var/lib/apt/lists/* \
    && apt-get clean