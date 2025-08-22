#!/bin/bash
set -e

echo "==== Symfony Docker setup ===="

# 1️⃣ Installer les dépendances Composer si nécessaire
if [ ! -d "vendor" ]; then
  echo "[1/4] Installation des dépendances Composer..."
  composer install --no-interaction
else
  echo "[1/4] Dépendances Composer déjà présentes."
fi

# 2️⃣ Installer les assets (Web Profiler)
echo "[2/4] Installation des assets..."
php bin/console assets:install --symlink --relative

# 3️⃣ Vider et régénérer le cache dev
echo "[3/4] Vider et régénérer le cache dev..."
rm -rf var/cache/dev
php bin/console cache:warmup

# 4️⃣ Redémarrer Apache pour prendre en compte les changements
echo "[4/4] Redémarrage d'Apache..."
apachectl restart

echo "==== Symfony setup terminé ===="
