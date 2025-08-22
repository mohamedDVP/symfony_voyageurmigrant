#!/bin/bash
# start-server.sh
# Script pour démarrer Symfony CLI dans Docker sur port 8080

# S'assurer que Symfony CLI est installé
if ! command -v symfony &> /dev/null
then
    echo "Symfony CLI n'est pas installé. Veuillez l'installer dans le conteneur."
    exit 1
fi

# Définir le port
PORT=8080

# Lancer le serveur Symfony en HTTP sur toutes les interfaces
echo "Démarrage du serveur Symfony sur http://0.0.0.0:${PORT} ..."
symfony serve --no-tls --allow-http --port=${PORT} --dir=/var/www/html
