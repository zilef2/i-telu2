/home/aplicativoswebco/public_html

// once
    //?common
        php artisan migrate:fresh --seed

    //?composer
        php artisan migrate:fresh --seed
        composer dump-autoload

        composer global require laravel/installer
        composer require laravel/jetstream
        composer install && npm install

//instalado
    //?composer
        composer require laraveles/spanish
        php artisan laraveles:install-lang
        aun no se instalan{
            // composer require psr/simple-cache:^1.0 maatwebsite/excel
            // npm install @tailwindcss/forms
        }
    in brive{
        Vue && Inertia && Tailwind
        Hero Icons && HeadlessUI
        Spatie (permisos)
        Floating Vue 
        VueUse
    }
    //? git
        git clone https://github.com/erikwibowo/Laravel-Brive.git
        cd Laravel-Brive && composer update && npm install && cp .env.example .env && php artisan key:generate
    // FIN REQUIRED