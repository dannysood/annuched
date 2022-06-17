FROM php:8.1-fpm-alpine

RUN apk add --no-cache nginx wget

RUN apk add autoconf gcc g++ make && pecl install redis && docker-php-ext-enable redis
RUN apk add -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql


RUN mkdir -p /run/nginx

COPY docker/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /app
COPY . /app
# COPY ./src /app

RUN sh -c "wget http://getcomposer.org/composer.phar && chmod a+x composer.phar && mv composer.phar /usr/local/bin/composer"
RUN cd /app && \
    /usr/local/bin/composer install --no-dev

RUN chown -R www-data: /app

CMD sh /app/docker/startup.sh
