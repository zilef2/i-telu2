//Aun no se deberian instalar en los proyectos
    //composer
            composer require mrdebug/crudgen --dev

    //para livewire
        composer require laravelcollective/html
            npm install --save-dev @defstudio/vite-livewire-plugin
            php artisan vendor:publish --provider="Mrdebug\Crudgen\CrudgenServiceProvider"

            //por silas
            "devDependencies": {
                // "@defstudio/vite-livewire-plugin": "^1.0.7",
                
//# Vue dependencies
    // Vue test
        npm install -g @vue/cli
        vue add @vue/unit-jest
        npm install vue-select@beta
    // datetime
        npm install --save luxon vue-datetime weekstart
        npm install --save luxon vue-datetime weekstart --force