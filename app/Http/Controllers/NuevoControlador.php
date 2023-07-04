
            <?php
 

            namespace App\Http\Controllers;
 

            use App\Http\Controllers\Controller;

            use App\Models\User;

            

            class NuevoControlador extends Controller

            {

                public function index()

                {

                    $personas = User::all();

                }

            }
        