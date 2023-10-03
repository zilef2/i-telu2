//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
php artisan make:crud Archivo "nombre:string, peso:integer, nick:string"
php artisan make:crud Articulo  "nick:string, Portada:string, Resumen:string, Palabras_Clave:string, Introduccion:string, Revisión_de_la_Literatura:string, Metodologia:string, Resultados:string, Discusion:string, Conclusiones:string, Agradecimientos:string, Referencias:string, AnexosoApéndices:string"
php artisan make:crud Ensayo    "nombre:string, "
php artisan make:crud Resumen   "nombre:string, "
//its donest need a Model -> materia_user



// php artisan make:crud semestre "nombre:string, descripcion:string"
//laravel excel 
php artisan make:import PersonalImport --model=User
php artisan make:import PersonalUniversidadImport --model=User
//fin laravel excel



//smalot pdfparser
php artisan make:controller TemporalPdfReader


//model for save pdf average
php artisan make:crud RespuestaPDf " guardar_pdf:string, resumen:string, nivel:string, precisa:string, idExistente:string"