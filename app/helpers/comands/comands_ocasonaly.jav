/home/aplicativoswebco/public_html

// once
    //?common
        php artisan migrate:fresh --seed

    //?composer

        composer global require laravel/installer
        composer require laravel/jetstream
        composer install && npm install
        php artisan serve && npm run dev


//instalado
    //?composer
        composer require laraveles/spanish
        php artisan laraveles:install-lang

        //dependencias
            //curd generator - livewire and maybe for vue
            composer require mrdebug/crudgen --dev


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
// FIN instalado


//despliege
    composer dump-autoload
    php artisan optimize:clear
    
