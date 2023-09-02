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



//if all of this works in local, you should go to terminal on deploy server and verify the permissions to do the backup
//permissions in server
GRANT SELECT, INSERT, UPDATE, DELETE ON intelu TO 'elalejo'@'162.240.106.160';
