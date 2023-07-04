<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PruebaGenerarControlador extends Command
{
    protected $signature = 'command:nuncaUsare';
    // protected $signature = 'config:register {activation=on}';

    protected $description = 'Command description';


    // public function handle()
    // {
    //     $config = Config::allowRegistrations($this->argument('activation') == 'on');

    //     $this->info($config->allow_registrations
    //         ? 'El registro de nuevos usuarios está activado'
    //         : 'El registro de nuevos usuarios está desactivado'
    //     );
    // }

    public function handle()
    {
        $codigo = '$personas = User::all();';

        // Generar el controlador con la línea de código
        // Puedes ajustar la ruta y el nombre del controlador según tus necesidades
        $this->info('Actualmente se tienen '. User::all()->count() . ' Usuarios en before_BD');

        file_put_contents(app_path('Http/Controllers/NuevoControlador.php'), "
            <?php\n \n
            namespace App\Http\Controllers;\n \n
            use App\Http\Controllers\Controller;\n
            use App\Models\User;\n
            \n
            class NuevoControlador extends Controller\n
            {\n
                public function index()\n
                {\n
                    $codigo\n
                }\n
            }
        ");

        $this->info('El controlador ha sido generado exitosamente.');
        return Command::SUCCESS;
    }
}
