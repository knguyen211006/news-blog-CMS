FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libonig-dev \
    && docker-php-ext-install -j"$(nproc)" mbstring pdo_mysql \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-cms.conf /etc/apache2/conf-enabled/cms.conf
COPY docker/config.php /opt/cms/config.php
COPY index.php .htaccess /var/www/html/
COPY config/routes.php /var/www/html/config/routes.php
COPY app/ /var/www/html/app/
COPY assets/ /var/www/html/assets/
COPY vendor/ /var/www/html/vendor/
COPY uploads/.htaccess /var/www/html/uploads/.htaccess
COPY scripts/check.php /var/www/html/scripts/check.php

RUN mkdir -p /var/www/html/storage /var/www/html/uploads \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/uploads

ENV CMS_CONFIG=/opt/cms/config.php
