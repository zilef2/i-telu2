//instalations ()
composer require spatie/laravel-backup
//configuras lo que quieres respaldar
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"

//# luego programar un comando para enviar el archivo que generamos arriba
php artisan make:command SendZipFile
//after config the command. go to Console/kernel -> 'protected function schedule'
$schedule->command('send:zip')->daily();

//comand
php artisan schedule:work
//cron job
//NOT :  15 14 * * * /home/elalejo/intelu2 && php artisan backup:run >> /home/elalejo/intelu2/storage/logs/commands.log 2>&1
//!Make backup in storage
0 20 * * * php /home/elalejo/intelu2/artisan backup:run >> /home/elalejo/intelu2/storage/logs/commands.log 2>&1
0 11 * * * php /home/elalejo/intelu2/artisan backup:run >> /home/elalejo/intelu2/storage/logs/commands.log 2>&1
//!send email to me
00 7 * * * php /home/elalejo/intelu2/artisan send:zip >> /home/elalejo/intelu2/storage/logs/commands.log 2>&1
* 15 * * * php /home/elalejo/intelu2/artisan send:zip >> /home/elalejo/intelu2/storage/logs/commands.log 2>&1


//if all of this works in local, you should go to terminal on deploy server and verify the permissions to do the backup
//permissions in server
GRANT SELECT, INSERT, UPDATE, DELETE ON intelu TO 'elalejo'@'162.240.106.160';



// # DEMCO PROJECT
//firstable - backup:run
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider" 
 php artisan backup:run
//!Make backup in git 
0 20 * * * php /home/elalejo/intelu2/artisan backup:run >> /home/elalejo/intelu2/storage/logs/commands.log 2>&1



// # DEMCO modnom
composer require spatie/laravel-backup
