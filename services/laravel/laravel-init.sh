#!/bin/sh
set -e

printenv > vars.txt

cat <<LARAVEL_ENV > .env
APP_NAME=Laravel
APP_ENV=$APP_ENV
APP_KEY=base64:frWGOukHeIZdw+3E/sGkoflAsP/+624CCMRcVZdgRbs=
APP_DEBUG=$APP_IS_DEBUG
APP_URL=$APP_URL

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=$APP_DB_DRIVER
DB_HOST=$APP_DB_HOST
DB_PORT=$APP_DB_PORT
DB_DATABASE=$APP_DB_NAME
DB_USERNAME=$APP_DB_USER
DB_PASSWORD=$APP_DB_PASSWORD
LARAVEL_ENV

php artisan migrate
php artisan db:seed

php artisan serve --host=0.0.0.0 --port=8000