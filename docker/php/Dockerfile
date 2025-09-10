# Base PHP avec Apache
FROM php:8.4-apache

# Installer dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

#installer Composer pour les dépendances de symfony
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Changer l'utilisateur courant pour www-data
COPY --chown=www-data:www-data . /var/www/html

# Configurer Apache pour pointer vers le dossier public de Symfony
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/public>|g' /etc/apache2/apache2.conf \
    && a2enmod rewrite


# Exposer le port Apache
EXPOSE 80

# Commande par défaut
CMD ["apache2-foreground"]
