<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);

    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::get('/user/{uuid}', [UserController::class, 'getUserByUuid']);

    Route::put('/users/{uuid}', [UserController::class, 'updateUser']);
    Route::delete('/users/{uuid}', [UserController::class, 'deleteUser']);

    Route::post('/users/search', [UserController::class, 'search']);

    Route::post('/books', [BooksController::class, 'create']);
    Route::put('/books/{uuid}', [BooksController::class, 'update']);
    Route::delete('/books/{uuid}', [BooksController::class, 'delete']);

    Route::get('/books', [BooksController::class, 'getBooks']);
    Route::get('/book/{uuid}', [BooksController::class, 'getBookByUuid']);

    Route::get('/books/status/{status}', [BooksController::class, 'getBooksByStatus']);

    Route::post('/books/search', [BooksController::class, 'search']);


    Route::post('/books/import', [BooksController::class, 'import']);
    Route::get('/books/export', [BooksController::class, 'export']);


    Route::post('/borrow', [BorrowController::class, 'create']);
    Route::put('/borrow/{uuid}', [BorrowController::class, 'update']);
    Route::delete('/borrow/{uuid}', [BorrowController::class, 'delete']);

    Route::get('/borrows', [BorrowController::class, 'getBorrows']);
    Route::get('/borrows/{uuid}', [BorrowController::class, 'getBorrowByUuid']);

    Route::post('/borrows/search', [BorrowController::class, 'search']);

    Route::post('/returns/{id}', [BorrowController::class, 'returnBook']);
    Route::get('/returned-books', [BorrowController::class, 'listReturnedBooks']);

    Route::post('/notifications/send-overdue', [NotificationController::class, 'sendOverdueNotifications']);

    Route::get('/borrows/reports/user/{userId}', [BorrowController::class, 'getBorrowsByUser']);
    Route::get('/borrows/reports/book/{bookId}', [BorrowController::class, 'getBorrowsByBook']);

});
