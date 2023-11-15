<?php

use App\helpers\Myhelp;
use App\Http\Controllers\ArticulosController;
use App\Http\Controllers\CarrerasController;
use App\Http\Controllers\EjerciciosController;
use App\Http\Controllers\ExtraUser;
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
use App\Http\Controllers\PlansController;
use App\Http\Controllers\UsuarioPendientesPagosController;
use App\Http\Controllers\TemporalPdfReader;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
require __DIR__ . '/auth.php';

Route::get('/', function () { return redirect('/login'); });
Route::get('/dashboard', [UserController::class,'VistaPrincipal'])->middleware(['auth', 'verified'])->name('dashboard');
//Route::get('/dashboard', function () { })


Route::get('/setLang/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return back();
})->name('setlang');

Route::middleware('auth', 'verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit');
    Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit');
    Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //# parametro
    Route::resource('/parametro', ParametrosController::class);

    //# user
    Route::resource('/user', UserController::class)->except('create', 'show', 'edit');
    Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    Route::get('/subirexceles', [UserController::class, 'subirexceles'])->name('subirexceles');
    Route::post('/uploadEstudiantes', [UserController::class, 'uploadEstudiantes'])->name('user.uploadEstudiantes');
    Route::post('/uploadUniversidad', [UserController::class, 'uploadUniversidad'])->name('user.uploadUniversidad');
    Route::post('/uploadCarreras', [UserController::class, 'uploadCarreras'])->name('user.uploadCarreras');

    $rutasUsers = ['VerTiemposEstudiantes'];
    foreach ($rutasUsers as $key => $value) {
        Route::get('/'.$value, [ExtraUser::class, $value])->name($value);
    }
    $rutasUsersConID = ['verEstudiante'];
    foreach ($rutasUsersConID as $value) {
        Route::get('/'.$value.'/{id}', [ExtraUser::class, $value])->name($value);
    }


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

    Route::post('/materiaGenerar', [MateriasController::class, 'materiaGenerar'])->name('materia.Generar');
    Route::post('/materiaguardarGenerado', [MateriasController::class, 'materiaguardarGenerado'])->name('materia.guardarGenerado');

    Route::get('/Estudiando', function(){ return redirect('/materia'); });

    Route::get('/Archivos/{materiaid}', [MateriasController::class, 'Archivosindex'])->name('materia.Archivos');
    Route::post('/storeArchivos', [MateriasController::class, 'storeArchivos'])->name('materia.storeArchivos');
    Route::post('/DeleteArchivos', [MateriasController::class, 'DeleteArchivos'])->name('materia.DeleteArchivos');



    // #universidad
    Route::get('/AsignaruserUni/{universidadid}', [UniversidadsController::class, 'AsignarUsers'])->name('universidad.AsignarUsers');
    Route::post('/AsignaruserUni', [UniversidadsController::class, 'SubmitAsignarUsers'])->name('universidad.SubmitAsignarUsers');
    Route::post('/AsignaruserUni2', [UniversidadsController::class, 'toEraseId'])->name('universidad.toEraseId');

    // #carrera
    Route::get('/AsignaruserCarrera/{carreraid}', [CarrerasController::class, 'AsignarUsers'])->name('carrera.AsignarUsers');
    Route::get('/carreraMapa/{carreraid}', [CarrerasController::class, 'carreraMapa'])->name('carrera.Mapa');
    Route::post('/AsignaruserCar', [CarrerasController::class, 'SubmitAsignarUsers'])->name('carrera.SubmitAsignarUsers');

    // #unidads
    Route::resource('/Unidad', UnidadsController::class);
    Route::post('/Unidad/destroy-bulk', [UnidadsController::class, 'destroyBulk'])->name('Unidad.destroy-bulk');

    Route::post('/gpt/temasCreate', [UnidadsController::class, 'temasCreate']); //->name('unidads.temasCreate');

    //# otros
    Route::resource('/subtopico', SubtopicosController::class);
    Route::post('/subtopico/destroy-bulk', [SubtopicosController::class, 'destroyBulk'])->name('subtopico.destroy-bulk');

    Route::resource('/ejercicio', EjerciciosController::class);
    Route::post('/ejercicio/destroy-bulk', [EjerciciosController::class, 'destroyBulk'])->name('ejercicio.destroy-bulk');

    Route::resource('/parametro', ParametrosController::class);



    // #promps
    Route::resource('/LosPromp', LosPrompsController::class)->except('create', 'show', 'edit');

    // #leyendopdf
    Route::get('/leyendopdf', [TemporalPdfReader::class , 'Index'])->name('leyendopdf');
    Route::post('/leyendopdf', [TemporalPdfReader::class , 'Read'])->name('leyendopdf.read');
    Route::get('/verPdf/{archivoid}', [TemporalPdfReader::class , 'verPdf'])->name('verPdf');
    Route::get('/vistaPDF/{archivoid}', [TemporalPdfReader::class , 'vistaPDF'])->name('vistaPDF');


    Route::get('/generarResumen/{archivoid}/{opcion?}', [TemporalPdfReader::class , 'generarResumen'])->name('generarResumen');
    //# openAI PDF
    Route::get('/subirPDFOpenAI/{archivoid}', [TemporalPdfReader::class , 'subirPDFOpenAI'])->name('subirPDFOpenAI');


    // #Articulo
    Route::resource('/Articulo', ArticulosController::class);
    Route::get('/Articulo/revisar/{id}', [ArticulosController::class,'RevisarArticulo'])->name('Articulo.revisar');
    Route::get('/Articul/index2', [ArticulosController::class,'index2'])->name('Articulo.index2');
    Route::post('/Articulo/destroy-bulk', [ArticulosController::class, 'destroyBulk'])->name('Articulo.destroy-bulk');
    Route::post('/guardarTiempoUser', [ArticulosController::class, 'guardarTiempoUser']);

    Route::get('/Resumen', [ArticulosController::class,'ArticuloResumen'])->name('Resumen');


    //#plan
    Route::resource('/Plan', PlansController::class);
    // #pendientes
    Route::resource('/pendiente', UsuarioPendientesPagosController::class);
    Route::post('/pendiente/destroy-bulk', [UsuarioPendientesPagosController::class, 'destroyBulk'])->name('pendiente.destroy-bulk');
    Route::get('/AceptarUsersPendiente/{pendienteid}', [UsuarioPendientesPagosController::class, 'AceptarUsers'])->name('pendiente.AceptarUsers');


    // #SeleccioneAsignatura (parte inicial para un usuario que compro la subscripcion
    Route::get('/SeleccioneAsignatura', [UserController::class, 'SeleccioneAsignatura'])->name('SeleccioneAsignatura');
    Route::post('/ComprarAsignatura', [UserController::class, 'ComprarAsignatura'])->name('ComprarAsignatura');
});




//<editor-fold desc="Artisan functions">
    Route::get('/exception', function () {
        throw new Exception('Probando excepciones y enrutamiento. La prueba ha concluido exitosamente.');
    });

    Route::get('/foo', function () {
        if (file_exists(public_path('storage'))) { return 'Ya existe'; }
        App('files')->link( storage_path('App/public'), public_path('storage') );
        return 'Listo';
    });

    Route::get('/clear-c', function () {
        // Artisan::call('optimize');
        Artisan::call('optimize:clear');
        return "Optimizacion finalizada";
        // throw new Exception('Optimizacion finalizada!');
    });

    Route::get('/tmantenimiento-intel', function () {
        echo Artisan::call('down --secret="token-it"');
        return "Aplicación abajo: token-it";
    });
    Route::get('/Arriba', function () {
        echo Artisan::call('up');
        return "Aplicación funcionando";
    });
//</editor-fold>

//en el kernel
//protected $routeMiddleware = [
//    // ...
//    'errorHandler' => \App\Http\Middleware\ErrorHandlerMiddleware::class,
//];
//Route::middleware('errorHandler')->group(function () {
//// Rutas aquí
//});
?>

