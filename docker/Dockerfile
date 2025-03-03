FROM php:8.3-fpm

ARG USER_ID=1000
ARG GROUP_ID=1000

ENV APP_HOME /var/www/html
ARG INSIDE_DOCKER_CONTAINER=1
ENV INSIDE_DOCKER_CONTAINER=$INSIDE_DOCKER_CONTAINER


ENV PHP_MEMORY_LIMIT=512Mo

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
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN groupadd -g $GROUP_ID devuser \
  && useradd -r -u $USER_ID -g devuser devuser

USER devuser

WORKDIR $APP_HOME