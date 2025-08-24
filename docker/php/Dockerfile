# Base PHP avec Apache
FROM php:8.4-apache

# Installer dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    zip \
    ssl-cert \
    && docker-php-ext-install \
    intl \
    pdo_mysql \
    opcache \
    zip \
    mbstring \
    && a2enmod rewrite \
    && a2enmod ssl \
    && a2ensite default-ssl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Installer Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier la configuration Apache pour Symfony (HTTP)
COPY ./docker/php/vhost.conf /etc/apache2/sites-available/000-default.conf

# Copier la configuration Apache SSL personnalisée (si besoin)
COPY ./docker/php/vhost-ssl.conf /etc/apache2/sites-available/default-ssl.conf
# COPY ./docker/php/vhost-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Copier les scripts d’initialisation (si tu en as)
COPY ./docker/php/setup-symfony.sh /usr/local/bin/setup-symfony.sh
RUN chmod +x /usr/local/bin/setup-symfony.sh

COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

COPY ./docker/php/start-server.sh /usr/local/bin/start-server.sh
RUN chmod +x /usr/local/bin/start-server.sh

# Exposer HTTP (80 / 8000) et HTTPS (443)
EXPOSE 8000 443

# Entrypoint et commande par défaut
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
