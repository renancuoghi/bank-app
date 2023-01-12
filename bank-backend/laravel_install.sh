composer install

php artisan key:generate

# RUN migrations and seeders
php artisan migrate --database=mysql
php artisan migrate --database=mysql_test
php artisan db:seed --database=mysql
php artisan db:seed --database=mysql_test

php artisan storage:link