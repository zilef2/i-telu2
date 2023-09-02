//misterdebug - crud-generator-laravel
php artisan make:crud foo "nombre:string"
//para borrar:  
php artisan rm:crud foo --force

//models
    Php artisan make:model foo --all

//middleware
    php artisan make:middleware IsAdmin


# EXPORT AND IMPORTS AND CORREO
    //CORREO
    php artisan make:mail ExampleMail

    //#-- excel
    // php artisan make:export ExampleExport --model=Empresa
    // php artisan make:import ExampleImport --model=User



# DESPLIEGUE

composer dump-autoload
php artisan key:generate

rm -r /public_html/modulonom/moduloNomina
rm -r /home/aplicativoswebco/public_html/modulonom/moduloNomina
rm -r /home/aplicativoswebco/public_html/modulonom/storage/logs/laravel.log

// <!-- borrar -->
    rm -r "direccion"
// <!-- permisos recursivamente -->
    chmod a+rwx folder_name -R
    chmod -R 555 /home/aplicativoswebco/public_html/modulonom/config
    chmod -R 555 /home/aplicativoswebco/public_html/modulonom/app
    chmod -R 775 /home/aplicativoswebco/public_html/modulonom/storage
    chmod -R 775 /home/aplicativoswebco/public_html/modulonom/bootstrap
    sudo chmod -R ugo+rw /home/aplicativoswebco/public_html/modulonom/storage
    sudo chmod -R ugo+rw /home/aplicativoswebco/public_html/modulonom/bootstrap
    chmod -R 555 /home/aplicativoswebco/public_html/modulonom/*
*/

mv /home/aplicativoswebco/public_html/modulonom/bootstrap/cache /home/aplicativoswebco/public_html/modulonom/bootstrap/cache_2
mkdir /home/aplicativoswebco/public_html/modulonom/storage/framework/cache/data


# DEPENDENCIAS

// node
//composer
    composer require maatwebsite/excel
    composer require mrdebug/crudgen --dev
    composer require smalot/pdfparser

