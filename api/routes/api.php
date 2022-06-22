<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\V1\ImportPostJobController;
use App\Http\Controllers\V1\PostController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::prefix('v1')->group(function () {
    Route::group(['prefix' => 'auth'], function ($router) {
        // TODO identify requirement of withoutMiddleware(('auth') since we are bypassing auth via AUthServiceProvider which is being called regardless of middleware skip
        Route::post('create', [AuthController::class, 'create'])->withoutMiddleware(('auth'));
    });
    Route::group(['prefix' => 'job'], function ($router) {
        Route::resource('fetch-from-remote-blog-api', ImportPostJobController::class)->only(['store']);
    });
    Route::apiResource('post', PostController::class);
});
