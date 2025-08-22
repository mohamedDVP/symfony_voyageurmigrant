#!/bin/bash
set -e

# Vérifie si vendor/ existe, sinon lance composer install
if [ ! -d "vendor" ]; then
  echo "Installing composer dependencies..."
  composer install --no-interaction
fi

exec "$@"
