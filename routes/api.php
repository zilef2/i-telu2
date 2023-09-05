<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/userexample', function () {
    return User::all('name','email','sexo','fecha_nacimiento');
});
// Route::get('/getToken', function () {
//     $user = User::find(1); // Replace with your user retrieval logic
//     $token = $user->createToken('token-name')->plainTextToken;

//     return $token;
// });

Route::middleware('auth:sanctum')->get('/user1', function () {
    return User::select('name','email','sexo','fecha_nacimiento')
        ->whereNotIn('name',[
            'Admin IntelU',
            'Super',
            ])->get()
    ;
});
