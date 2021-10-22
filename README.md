
## Tentang Project

MINI HRIS

## Server Requirements
 1. PHP
- PHP >= 7.3
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Exntension
- PDO PHP Extension
- MySQL PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- GD PHP Extension

2. MySQL
- MySQL 5.7


## Langkah-langka deploy
- Pull project ke directory Server
- Setup owner dan permission file directory
- Copy file .env.example menjadi .env dan sesuaikan kredensial database, redis, pusher dsb
- jalankan perintah composer update
- Jalankan perintah php artisan migrate
- Jalankan perintah php artisan storage:link
- jalankan perintah php artisan server (Hanya untuk tahap development) 



