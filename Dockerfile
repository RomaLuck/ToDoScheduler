FROM php:8.2.0-fpm-alpine

RUN set -ex \
  && apk --no-cache add \
    supervisor \
    libzip-dev \
    zip \
    libpng-dev \
    libxslt-dev \
    bash \
    icu-dev \
    openssl \
    acl \
    wget

RUN docker-php-ext-install pdo pdo_mysql zip xsl gd intl

RUN apk add --update linux-headers

RUN apk --update --no-cache add autoconf g++ make && \
     pecl install -f pcov && \
     docker-php-ext-enable pcov && \
     apk del --purge autoconf g++ make

RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN curl -sL https://getcomposer.org/installer | php -- --install-dir /usr/bin --filename composer

RUN mkdir -p /var/log/supervisor

WORKDIR /app

RUN echo 'pm.max_children = 30' >> /usr/local/etc/php-fpm.d/zz-docker.conf

RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

CMD ["php-fpm"]