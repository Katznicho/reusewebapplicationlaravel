<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

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
    return redirect('/admin');
});

//redirect to dashboard
// Route::get('/{any}', function () {
//     return redirect('/admin');
// })->where('any', '.*');

Route::get('finishPayment', [PaymentController::class, 'finishPayment'])->name('finishPayment');
Route::get('cancelPayment', [PaymentController::class, 'cancelPayment'])->name('cancelPayment');
