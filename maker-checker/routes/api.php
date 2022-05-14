<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\RequestController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('admin/login', [ApiController::class, 'login']);
Route::post('admin/new', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [ApiController::class, 'logout']);

    Route::get('pending-requests', [RequestController::class, 'pendingRequests']);
    Route::get('approve/{request_id}', [RequestController::class, 'approveRequest']);
    Route::get('decline/{request_id}', [RequestController::class, 'declineRequest']);

    Route::get('users', [RequestController::class, 'allUsers']);

    Route::post('users/add', [RequestController::class, 'addUserRequest']);
    Route::patch('users/edit', [RequestController::class, 'updateUserRequest']);
    Route::delete('users/delete/{user_id}', [RequestController::class, 'deleteUserRequest']);

});
