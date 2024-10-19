#!/bin/bash

# Check if vendor directory exists, if not, run composer install
if [ ! -d "vendor" ]; then
  composer install --optimize-autoloader --no-cache
fi

# Start PHP-FPM
php-fpm