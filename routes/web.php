<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mongodb-ping', function () {
    try {
        $db = DB::connection('mongodb')->getMongoClient();
        $result = $db->admin->command(['ping' => 1]);
        return 'MongoDB Ping: ' . json_encode($result->toArray());
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/pay-penalty/{borrowing}', [PaymentController::class, 'showPaymentForm'])->name('pay.penalty');
Route::post('/process-payment/{borrowing}', [PaymentController::class, 'processPayment'])->name('process.payment');

Route::get('/payment-success/{borrowing}', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment-cancel/{borrowing}', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');

