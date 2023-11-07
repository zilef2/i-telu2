//24/08/2023
//sugerir objetivos
    php artisan make:job SugerirObjetivos
    //el modelo y tabla de
    php artisan make:crud clasificacionUser "nombre:string, descripcion:string"

//correr jobs
php artisan queue:work
php artisan queue:listen

php artisan queue:work database --queue=secondary

