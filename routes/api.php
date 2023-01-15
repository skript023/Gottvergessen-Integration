<?php

use App\Http\Controllers\SignedInValidation;
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

Route::post('/v1/injection/access', [SignedInValidation::class, 'store_access']);

Route::group(['middleware' => 'hardware'], function() 
{
    Route::get('/v1/injection/begin/{hardware}', [SignedInValidation::class, 'allow_injection']);
});