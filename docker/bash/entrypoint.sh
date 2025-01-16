#!/bin/bash
if [ ! -f ".env" ]
then
    echo "Error: .env not found."
    exit 1
fi
# su -c "chmod 777 -R storage/logs" -s /bin/sh dev
# su -c "chmod 777 -R storage/framework" -s /bin/sh dev
su -c "composer install" -s /bin/sh dev
su -c "php artisan horizon:install" -s /bin/sh dev
/usr/bin/supervisord -n -c /etc/supervisor/supervisord.conf &
cron
php-fpm

exit 0
