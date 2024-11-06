composer install

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan key:generate

pnpm install

php artisan migrate:fresh --seed

php artisan storage:link
