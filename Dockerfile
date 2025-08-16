FROM serversideup/php:8.4-unit
COPY --chown=www-data:www-data . /var/www/html
RUN ls -la /var/www/html
RUN composer install --no-dev --optimize-autoloader