//control
php artisan make:crud MedidaControl "tokens_usados:string, user_id:integer"
php artisan make:crud Archivo "nombre:string, peso:integer, nick:string"
php artisan make:crud Articulo  "nick:string, Portada:string, Resumen:string, Palabras_Clave:string, Introduccion:string, Revisión_de_la_Literatura:string, Metodologia:string, Resultados:string, Discusion:string, Conclusiones:string, Agradecimientos:string, Referencias:string, AnexosoApéndices:string"
php artisan make:crud Ensayo    "nombre:string, "
php artisan make:crud Resumen   "nombre:string, "
php artisan make:crud Plan "nombre:string, tipo:string, valor:integer, caducidad:datetime, tokens:integer"
php artisan make:crud Calificacion "TipoPrueba:string, prompUsado:string, valor:float, tokens:integer"
php artisan make:crud Grupo "nombre:string,codigo:string"


//6nov2023
php artisan make:crud UsuarioPendientesPago "fecha_peticion:timestamp, fecha_aprovacion:timestamp, valorTotal:float, tokensComprados:integer"
php artisan make:crud Cuotas "numeroDeLaCuota:integer, numeroDecuotas:integer, valor:float"
//7nov
php artisan make:crud TiemposArticulo "startTime:datetime, endTime:datetime, tiempoEscritura:float"

//aun no se corren
php artisan make:crud Quiz "TipoPrueba:string, prompUsado:string, calificacionMaxima:float, tokens:integer"
php artisan make:crud QuizPreguntas "Pregunta:string, Respuesta:string"








--------------------------------* INSTALACIONES *--------------------------------
----***** PDF *****----
//smalot pdfparser
php artisan make:controller TemporalPdfReader
//model for save pdf average
php artisan make:crud RespuestaPDf " guardar_pdf:string, resumen:string, nivel:string, precisa:string, idExistente:string"

----***** laravel excel *****----
php artisan make:Oimport PersonalImport --model=User
php artisan make:Oimport PersonalUniversidadImport --model=User
//fin laravel excel




<div class="bg-white py-6 sm:py-8 lg:py-12">

</div>
