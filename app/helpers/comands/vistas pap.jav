//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
php artisan make:crud Archivo "nombre:string, peso:integer, nick:string"
//its donest need a Model -> materia_user


// no yet
php artisan make:crud clasificacionUser "nombre:string, descripcion:string"
php artisan make:crud clasificacionMateria "nombre:string, descripcion:string"
php artisan make:crud clasificacionEjercicio "nombre:string, descripcion:string"


// php artisan make:crud semestre "nombre:string, descripcion:string"
//laravel excel 
php artisan make:import PersonalImport --model=User
php artisan make:import PersonalUniversidadImport --model=User
//fin laravel excel



//smalot pdfparser
php artisan make:controller TemporalPdfReader
