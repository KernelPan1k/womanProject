#!/usr/bin/env bash

echo "Dropping database"
php bin/console doctrine:database:drop --force 2>/dev/null

echo "Create database"
php bin/console doctrine:database:create 2>/dev/null

echo "Create schema"
php bin/console doctrine:schema:update --force 2>/dev/null

echo "Load Fixture"
php bin/console doctrine:fixture:load -n --append

echo "Load GeoPostalCode"
php bin/console project_admin:geopostalcode:update

echo "create admin"
php bin/console project:user:create-admin