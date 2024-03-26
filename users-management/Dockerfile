FROM bitnami/laravel
WORKDIR /app
COPY . /app
RUN cat php.ini >> /opt/bitnami/php/etc/php.ini
RUN composer install

