#!/bin/bash
set -e
export PGPASSWORD=$APP_DB_PASSWORD;
psql -v ON_ERROR_STOP=1 --username "$APP_DB_USER" --dbname "$APP_DB_NAME" <<-EOSQL
  CREATE EXTENSION "uuid-ossp"; 
EOSQL