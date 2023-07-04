
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
php artisan make:crud posicionUser "nombre:string, importancia:integer"
php artisan make:crud Parametro "prompEjercicios:string, NumeroTicketDefecto:integer"
php artisan make:crud RespuestaEjercicio "core:string, precisa:integer"

//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
//its donest need a Model -> materia_user


// no yet
php artisan make:crud clasificacionUser "nombre:string, descripcion:string"
php artisan make:crud clasificacionMateria "nombre:string, descripcion:string"
php artisan make:crud clasificacionEjercicio "nombre:string, descripcion:string"


// php artisan make:crud semestre "nombre:string, descripcion:string"
//laravel excel 
php artisan make:import PersonalImport --model=User

//fin laravel excel




// node
// laravel
composer require maatwebsite/excel
