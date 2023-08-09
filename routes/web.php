<?php
use App\Http\Controllers\CarrerasController;
use App\Http\Controllers\EjerciciosController;
use App\Http\Controllers\LosPrompsController;
use App\Http\Controllers\MateriasController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubtopicosController;
use App\Http\Controllers\UnidadsController;
use App\Http\Controllers\UniversidadsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ParametrosController;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;


// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
Route::get('/', function () { return redirect('/login'); });


Route::get('/dashboard', function () {
    $permissions = auth()->user()->roles->pluck('name')[0];
    
    if($permissions == "estudiante"){
        return redirect('/materia');
    }

    return Inertia::render('Dashboard', [
        'users'         => (int) User::count(),
        'roles'         => (int) Role::count(),
        'permissions'   => (int) Permission::count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return back();
})->name('setlang');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //# user
    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    // Route::get('/subirexceles', [UserController::class, 'subirexceles'])->name('subirexceles');


    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');
    
    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');

    Route::resource('/parametro', ParametrosController::class);


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //# user
    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    Route::get('/subirexceles', [UserController::class, 'subirexceles'])->name('subirexceles');
    Route::post('/uploadEstudiantes', [UserController::class, 'uploadEstudiantes'])->name('user.uploadEstudiantes');
    Route::post('/uploadUniversidad', [UserController::class, 'uploadUniversidad'])->name('user.uploadUniversidad');


    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');

    Route::resource('/universidad', UniversidadsController::class);
    Route::resource('/carrera', CarrerasController::class);

    // #materia
    Route::resource('/materia', MateriasController::class);
    Route::get('/AsignarMateria/{materiaid}', [MateriasController::class, 'AsignarUsers'])->name('materia.AsignarUsers');
    Route::post('/AsignarMateria', [MateriasController::class, 'SubmitAsignarUsers'])->name('materia.SubmitAsignarUsers');
    Route::get('/VistaTema/{materiaID}/{ejercicioID?}/{nivel?}/{temaid?}/{selectedprompid?}', [MateriasController::class, 'VistaTema'])->name('materia.VistaTema');
    // Route::get('/masPreguntas/{id}/{nuevaPregunta}', [MateriasController::class,'masPreguntas'])->name('materia.masPreguntas');
    Route::get('/masPreguntas', [MateriasController::class, 'masPreguntas'])->name('materia.masPreguntas');
    // Route::match(['post', 'get'],'/Estudiando', [MateriasController::class, 'actionEQH'])->name('materia.actionEQH');
    Route::post('/Estudiando', [MateriasController::class, 'actionEQH'])->name('materia.actionEQH');
    Route::get('/Estudiando', function(){
        return redirect('/materia');
    })->name('materia.actionEQH');

    // #universidad
    Route::get('/AsignaruserUni/{universidadid}', [UniversidadsController::class, 'AsignarUsers'])->name('universidad.AsignarUsers');
    Route::post('/AsignaruserUni', [UniversidadsController::class, 'SubmitAsignarUsers'])->name('universidad.SubmitAsignarUsers');
    Route::post('/AsignaruserUni2', [UniversidadsController::class, 'toEraseId'])->name('universidad.toEraseId');

    // #carrera
    Route::get('/AsignaruserCarrera/{carreraid}', [CarrerasController::class, 'AsignarUsers'])->name('carrera.AsignarUsers');
    Route::post('/AsignaruserCar', [CarrerasController::class, 'SubmitAsignarUsers'])->name('carrera.SubmitAsignarUsers');

    // #unidads
    Route::resource('/Unidad', UnidadsController::class);
    Route::post('/gpt/temasCreate', [UnidadsController::class, 'temasCreate']); //->name('unidads.temasCreate');
    //otros
    Route::resource('/subtopico', SubtopicosController::class);
    Route::resource('/ejercicio', EjerciciosController::class);
    Route::resource('/parametro', ParametrosController::class);

    // #promps
    Route::resource('/LosPromp', LosPrompsController::class)->except('create', 'show', 'edit');

});



require __DIR__ . '/auth.php';

// <editor-fold desc="Artisan">
Route::get('/exception', function () {
    throw new Exception('Probando excepciones y enrutamiento. La prueba ha concluido exitosamente.');
});

Route::get('/foo', function () {
    if (file_exists(public_path('storage'))) {
        return 'Ya existe';
    }
    App('files')->link(
        storage_path('App/public'),
        public_path('storage')
    );
    return 'Listo';
});

Route::get('/clear-c', function () {
    Artisan::call('optimize');
    Artisan::call('optimize:clear');
    return "Optimizacion finalizada";
    // throw new Exception('Optimizacion finalizada!');
});

Route::get('/tmantenimiento', function () {
    echo Artisan::call('down --secret="token-it"');
    return "Aplicación abajo: token-it";
});
Route::get('/Arriba', function () {
    echo Artisan::call('up');
    return "Aplicación funcionando";
});

?>