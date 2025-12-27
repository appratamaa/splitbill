#!/usr/bin/env bash
# exit on error
set -o errexit

composer install --no-dev --optimize-autoloader

# Menghapus cache lama dan membuat cache baru
php artisan config:cache
php artisan route:cache
php artisan view:cache