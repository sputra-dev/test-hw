<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RestoreController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth', 'checkRole:admin'])->group(function () {
    Route::post('/admin/category', [CategoryController::class, 'create']);
    Route::put('/admin/category/{id}', [CategoryController::class, 'update']);
    Route::delete('/admin/category/{id}', [CategoryController::class, 'delete']);
    Route::post('/admin/book', [BookController::class, 'create']);
    Route::put('/admin/book/{id}', [BookController::class, 'update']);
    Route::delete('/admin/book/{id}', [BookController::class, 'delete']);
    Route::get('/admin/book/loan-list', [BookController::class, 'loanBooks']);
    Route::put('/admin/{loanId}/restore', [LoanController::class, 'updateRestore']);
});

Route::middleware(['auth', 'checkRole:member'])->group(function () {
    Route::post('/member/loan', [LoanController::class, 'createLoan']);
});
