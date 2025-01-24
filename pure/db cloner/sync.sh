#!/bin/bash
echo "Executing script with table: $1"
# MySQL credentials
MYSQL_USER="mysql_user"
MYSQL_PASSWORD="mysql_user_password"
SOURCE_DB="source_db"
DESTINATION_DB="destination_db"
MYSQL_HOST="localhost"

TABLE_NAME="$1"

mysqldump --default-character-set=utf8mb4 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -h"$MYSQL_HOST" "$SOURCE_DB" "$TABLE_NAME" > "$TABLE_NAME.sql"
mysql --default-character-set=utf8mb4 -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -h"$MYSQL_HOST" "$DESTINATION_DB" < "$TABLE_NAME.sql"

rm "$TABLE_NAME.sql"

RESULTS=$(mysql -h "$MYSQL_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$DESTINATION_DB" -se "SELECT product_id FROM oc_product WHERE manufacturer_id != '8'")
echo "$RESULTS" | while read -r PRODUCT_ID; do
    mysql -h "$MYSQL_HOST" -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$DESTINATION_DB" -e "DELETE FROM $TABLE_NAME WHERE product_id = '$PRODUCT_ID'"
done
