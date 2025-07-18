#!/bin/sh
set -e

# Si vendor/ n'existe pas ou est vide, installer les deps
if [ ! -d vendor ] || [ -z "$(ls -A vendor)" ]; then
  echo "📦 Installation des dépendances Composer…"
  composer install --no-interaction --optimize-autoloader
fi

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
