#!/bin/bash
set -e

ENV_FILE=".env.local"
WEB_CONTAINER="symfony_web"

echo "🔹 Build et démarrage des conteneurs Docker..."
docker compose --env-file $ENV_FILE up -d --build

echo "🔹 Installation des dépendances PHP/Symfony..."
docker exec -it $WEB_CONTAINER composer install --no-interaction

echo "🔹 Création de la base de données..."
docker exec -it $WEB_CONTAINER php bin/console doctrine:database:create || true

echo "🔹 Exécution des migrations..."
docker exec -it $WEB_CONTAINER php bin/console doctrine:migrations:migrate --no-interaction || true

echo "🔹 Vider et régénérer le cache dev..."
docker exec -it $WEB_CONTAINER php bin/console cache:clear

echo "✅ Tout est prêt !"
echo "Accédez à Symfony : http://localhost:8080/"
echo "Accédez à PhpMyAdmin : http://localhost:8086/"
