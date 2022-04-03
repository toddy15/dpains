#!/usr/bin/env sh

set -e

# Import latest data and export without CREATE statements
mariadb -ppass --database dpains < ~/backup/sql/dpains.sql
mysqldump -ppass --no-create-info dpains \
analyzed_months \
comments \
due_shifts \
employees \
episodes \
rawplans \
staffgroups \
users \
> tmp.sql

# Insert NULL for user table
sed -i -e "/^INSERT INTO \`users\` VALUES/ s/@asklepios.com',/@asklepios.com',NULL,/g" tmp.sql

php artisan migrate:fresh

mariadb -ppass --database dpains < tmp.sql
rm tmp.sql
