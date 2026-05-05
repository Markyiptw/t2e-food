FROM node:20 AS build-assets
WORKDIR /app
# Copy the whole project for Tailwind scanning
COPY . /app
RUN npm install
RUN npm run build

FROM serversideup/php:8.5-frankenphp-debian-v4.3.5
# Switch to root so we can do root things
USER root
# Install the intl and bcmath extensions with root permissions
RUN install-php-extensions intl bcmath exif
USER www-data

COPY --chown=www-data:www-data . /var/www/html
RUN composer install --no-dev --optimize-autoloader
COPY --from=build-assets --chown=www-data:www-data /app/public/build /var/www/html/public/build