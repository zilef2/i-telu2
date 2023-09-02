<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use ZipArchive;

class SendZipFile extends Command
{
    protected $signature = 'send:zip';
    protected $description = 'Enviar archivo ZIP por correo diariamente';

    public function handle()
    {
        try {
            $zip = new ZipArchive;
            $zipFileName = public_path('archivo.zip');
            
            if ($zip->open($zipFileName, ZipArchive::CREATE) === true) {
                $zip->setCompressionIndex(0, ZipArchive::CM_DEFLATE, 9);
                
                // Lógica para agregar archivos al ZIP
                $pato = storage_path('app\IntelU\2023-08-17-20-28-56.zip');
                $zip->addFile(($pato), 'backup IntelU');
                $zip->close();
            

                // Envío del correo electrónico
                Mail::send([], [], function ($message) use ($zipFileName) {
                    $message->to('ajelof2@gmail.com')
                            ->subject('Respaldo ZIP Diario')
                            ->attach($zipFileName);
                });
                $this->info('Archivo ZIP enviado por correo.');

            }else{
                $this->warn('Error al comprimir el archivo');
            }
        } catch (\Throwable $th) {
            $this->warn('Ocurrio un error| '.$th->getMessage() . ' L: ' .$th->getLine());
        }
    }
}
