
# https://frankenphp.dev/docs/production/
FROM dunglas/frankenphp

# Be sure to replace "your-domain-name.example.com" by your domain name
# ENV SERVER_NAME=your-domain-name.example.com
# If you want to disable HTTPS, use this value instead:
ENV SERVER_NAME=:80

# If your project is not using the "public" directory as the web root, you can set it here:
# ENV SERVER_ROOT=web/

# Enable PHP production settings
# Can keep, see https://frankenphp.dev/docs/config/#static-binary
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"


# Copy the PHP files of your project in the public directory
# COPY . /app/public
# If you use Symfony or Laravel, you need to copy the whole project instead:
COPY . /app
    
RUN "composer install --no-dev --optimize-autoloader"