#!/bin/bash
set -e

# Installer les dépendances si nécessaire
if [ ! -d "vendor" ]; then
  echo "Installing composer dependencies..."
  composer install --no-interaction
fi

# Vider et régénérer le cache dev
php bin/console cache:clear
php bin/console cache:warmup

# Lancer Apache
exec apache2-foreground
