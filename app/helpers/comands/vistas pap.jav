//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
php artisan make:crud Archivo "nombre:string, peso:integer, nick:string"
php artisan make:crud Articulo  "nick:string, Portada:string, Resumen:string, Palabras_Clave:string, Introduccion:string, Revisión_de_la_Literatura:string, Metodologia:string, Resultados:string, Discusion:string, Conclusiones:string, Agradecimientos:string, Referencias:string, AnexosoApéndices:string"
php artisan make:crud Ensayo    "nombre:string, "
php artisan make:crud Resumen   "nombre:string, "
php artisan make:crud Plan "nombre:string, tipo:string, valor:integer, caducidad:datetime, tokens:integer"
php artisan make:crud Calificacion "TipoPrueba:string, prompUsado:string, valor:float, tokens:integer"
//its donest need a Model -> materia_user


//6nov2023
php artisan make:crud UsuarioPendientesPago "fecha_peticion:timestamp, fecha_aprovacion:timestamp, valorTotal:float, tokensComprados:integer"
php artisan make:crud Cuotas "numeroDeLaCuota:integer, numeroDecuotas:integer, valor:float"



//smalot pdfparser
php artisan make:controller TemporalPdfReader


//model for save pdf average
php artisan make:crud RespuestaPDf " guardar_pdf:string, resumen:string, nivel:string, precisa:string, idExistente:string"


----***** laravel excel *****----
php artisan make:Oimport PersonalImport --model=User
php artisan make:Oimport PersonalUniversidadImport --model=User
//fin laravel excel
