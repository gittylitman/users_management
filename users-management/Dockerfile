FROM bitnami/laravel
WORKDIR /app
COPY . .
RUN cat php.ini >> /opt/bitnami/php/etc/php.ini
RUN composer install