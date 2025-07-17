#!/bin/sh
set -e

# Lancer les migrations automatiquement
echo "🔍 Vérification des migrations…"
if php bin/console doctrine:migrations:status --no-interaction 2>&1 | grep -q 'No migrations'; then
  echo "Aucune migration détectée, j’ignore cette étape."
else
  echo "Exécution des migrations…"
  php bin/console doctrine:migrations:migrate \
      --no-interaction \
      --allow-no-migration
fi

# Lancer la commande par défaut du container (php-fpm / php -S /app/public)
exec "$@"
