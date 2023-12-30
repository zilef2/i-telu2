//24/08/2023
//sugerir objetivos
    php artisan make:job SugerirObjetivos
    //el modelo y tabla de
    php artisan make:crud clasificacionUser "nombre:string, descripcion:string"

//correr jobs
php artisan queue:work //solo ejecuta los jobs('default')
php artisan queue:listen //creo q ejecuta todo

php artisan queue:work database --queue=secondary


//tendra su propio log
