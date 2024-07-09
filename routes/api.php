<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\UserController;

Route::post('/api/auth/register', [AuthController::class, 'register'])->name('register');
Route::post('/api/auth/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::get('/api/users/{id}', [UserController::class, 'show'])->name('show');

    Route::post('/api/organisations', [OrganisationController::class, 'store'])->name('store');;
    Route::get('/api/organisations', [OrganisationController::class, 'index'])->name('index');;
    Route::get('/api/organisations/{orgId}', [OrganisationController::class, 'show'])->name('show');;
});

Route::post('/api/organisations/{orgId}/users', [OrganisationController::class, 'addUser'])->name('show');
?>