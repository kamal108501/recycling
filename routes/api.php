<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

// Version 1 prefix
Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);

        Route::post('checkAppVersion', [AuthController::class, 'checkAppVersion']);
        // done
        Route::post('appUserLogin', [AuthController::class, 'appUserLogin']);
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::get('/password/reset/{token}', function ($token) {
            return response()->json([
                'message' => 'Password reset route placeholder.',
                'token'   => $token,
                'email'   => request('email'),
            ]);
        })->name('password.reset-password');
    });

    Route::post('auth/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:api')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
        // done
        Route::post('appUserLogout', [AuthController::class, 'appUserLogout']);
    });
});
