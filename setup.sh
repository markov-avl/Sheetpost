sudo chmod -R ugo+w ./*
composer install
cp .env.example .env
vim .env
vendor/bin/doctrine orm:schema-tool:create
vendor/bin/doctrine orm:generate-proxies