
//?helps
    //memory limit
        C:\laragon\bin\php\php-7.4.30-Win32-vc15-x64\php.ini

    //permissions deined
        mv /home/aplicativoswebco/public_html/modulonom/bootstrap/cache /home/aplicativoswebco/public_html/modulonom/bootstrap/cache_2
        mkdir /home/aplicativoswebco/public_html/modulonom/storage/framework/cache/data
// FIN helps

//models
    Php artisan make:model CentroTrabajo --all
    Php artisan make:model Producto --all

//middleware
    php artisan make:middleware IsAdmin


//#--  correo - EXPORT AND IMPORTS
    //CORREO
    php artisan make:mail ExampleMail

    //#-- excel
    // php artisan make:export ExampleExport --model=Empresa
    // php artisan make:import ExampleImport --model=User

//despliegue

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
        