
//pendiente
(example-project: tiempop)
//misterdebug - crud-generator-laravel
        //para borrar:  php artisan rm:crud post --force
        php artisan rm:crud producto --force
        php artisan rm:crud centroTrabajo --force
    php artisan make:crud centroCosto "nombre:string"
    php artisan make:crud Reporte "fecha_ini:datetime, fecha_fin:datetime, horas_trabajadas:integer, valido:boolean,observaciones:text"

    
php artisan make:crud Universidad "nombre:string"
php artisan make:crud Carrera "nombre:string, descripcion:string"
php artisan make:crud Materia "nombre:string, descripcion:string"
php artisan make:crud Tema "nombre:string, descripcion:string"
php artisan make:crud Subtopico "nombre:string, descripcion:string"
php artisan make:crud ejercicio "nombre:string, descripcion:string"

php artisan make:crud objetivo "nombre:string, descripcion:string"
//its donest need a Model -> materia_user


// no yet
php artisan make:crud clasificacionUser "nombre:string, descripcion:string"
php artisan make:crud clasificacionMateria "nombre:string, descripcion:string"
php artisan make:crud clasificacionEjercicio "nombre:string, descripcion:string"


// php artisan make:crud semestre "nombre:string, descripcion:string"
//vistas 

        
    // tablas
    //fin tablas

    //vistas iniciales
        //# superadmin
        
        // (admin )

        // (operator)

    //vistas crud
    //
//fin vistas

