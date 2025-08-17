FROM node:20 AS build-assets
WORKDIR /app
# Copy the whole project for Tailwind scanning
COPY . /app
RUN npm install
RUN npm run build

FROM serversideup/php:8.4-unit
COPY --chown=www-data:www-data . /var/www/html
RUN composer install --no-dev --optimize-autoloader
COPY --from=build-assets --chown=www-data:www-data /app/public/build /var/www/html/public/build