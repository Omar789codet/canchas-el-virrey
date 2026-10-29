#!/bin/sh
set -e

# Paso unificado de inicialización: espera a la base de datos,
# corre migraciones y seeders, y levanta el servidor, en un solo comando.
until php artisan db:show > /dev/null 2>&1; do
  echo "Esperando a la base de datos..."
  sleep 2
done

php artisan migrate --force --seed
php artisan config:cache
php artisan route:cache

exec php artisan serve --host=0.0.0.0 --port=8000
