#!/bin/bash
set -e

cd /var/www/html

# Maximal 30 Sekunden auf DB warten
MAX_WAIT=30
COUNTER=0
echo "Warte auf MariaDB..."
until php -r "new PDO('mysql:host=db;dbname=kassenbuch','kassenbuch','n');" 2>/dev/null
do
    COUNTER=$((COUNTER+1))
    if [ $COUNTER -ge $MAX_WAIT ]; then
        echo "MariaDB nicht erreichbar nach $MAX_WAIT Sekunden"
        exit 1
    fi
    sleep 1
done

echo "MariaDB erreichbar, starte Migration..."
php migrate.php

echo "Migration abgeschlossen, starte Apache..."
exec apache2-foreground
