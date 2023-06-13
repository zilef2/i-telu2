<?php

use App\Http\Controllers\CarrerasController;
use App\Http\Controllers\EjerciciosController;
use App\Http\Controllers\MateriasController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubtopicosController;
use App\Http\Controllers\TemasController;
use App\Http\Controllers\UniversidadsController;
use App\Http\Controllers\UserController;

use App\Models\Permission; use App\Models\Role; use App\Models\User;

use Illuminate\Foundation\Application; use Illuminate\Support\Facades\Artisan; use Illuminate\Support\Facades\Route; use Illuminate\Support\Facades\Session; use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
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

    Route::resource('/user', UserController::class)->except('create', 'show', 'edit'); Route::post('/user/destroy-bulk', [UserController::class, 'destroyBulk'])->name('user.destroy-bulk');
    
    Route::resource('/role', RoleController::class)->except('create', 'show', 'edit'); Route::post('/role/destroy-bulk', [RoleController::class, 'destroyBulk'])->name('role.destroy-bulk');

    Route::resource('/permission', PermissionController::class)->except('create', 'show', 'edit'); Route::post('/permission/destroy-bulk', [PermissionController::class, 'destroyBulk'])->name('permission.destroy-bulk');
    
    Route::resource('/universidad', UniversidadsController::class);
    Route::resource('/carrera', CarrerasController::class);

    Route::resource('/materia', MateriasController::class);
    Route::get('/materiaEstudiar/{id}/{temaSelec?}/{subtopicoSelec?}/{ejercicioSelec?}', [MateriasController::class,'VistaTema'])->name('materia.VistaTema');

    Route::resource('/tema', TemasController::class);
    Route::resource('/subtopico', SubtopicosController::class);
    Route::resource('/ejercicio', EjerciciosController::class);

});

require __DIR__.'/auth.php';

// <editor-fold desc="Artisan">
    Route::get('/exception',function(){
        throw new Exception('Probando excepciones y enrutamiento. La prueba ha concluido exitosamente.');
    });

    Route::get('/foo', function () {
        if (file_exists(public_path('storage'))){
            return 'Ya existe';
        }
        App('files')->link(
            storage_path('App/public'), public_path('storage')
        );return 'Listo';
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

//</editor-fold>
