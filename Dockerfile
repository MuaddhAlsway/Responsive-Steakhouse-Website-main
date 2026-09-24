# ============================================================
# Steakhouse Restaurant - PHP 8 + Apache container
# Builds on Render (or locally with `docker build`).
# ============================================================

FROM php:8.2-apache

# PHP extensions required by the application (PDO for MySQL).
RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

# Custom virtual host - listens on Render's $PORT.
# (The numeric port value is baked in by docker/entrypoint.sh.)
COPY docker/apache.conf /etc/apache2/conf-available/steakhouse.conf
RUN a2enconf steakhouse

# Only our virtual host should be active.
RUN a2dissite 000-default.conf || true

# Entrypoint that makes $PORT available to Apache.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html

# Copy the application code.
# .dockerignore keeps .env, Req/, preview and git data out of the image.
COPY . .

# Render always overrides this via the PORT environment variable.
EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]