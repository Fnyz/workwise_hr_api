<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SentEmailController;
use Illuminate\Http\Request;
use App\Http\Controllers\PasswordController;

use Illuminate\Support\Facades\DB;



Route::get('/', function () {
    return response()->json([
        "message" => "Welcome to workwiseHR API!"
    ], 200);
});

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        echo "Connected successfully to the database!";
    } catch (\Exception $e) {
        die("Could not connect to the database. Error: " . $e->getMessage());
    }
});



Route::get('/verify-email/{userId}/{verificationHash}', [SentEmailController::class, 'verifyEmail'])->name('verify');
Route::get('/verify-email', [SentEmailController::class, 'verifyEmail'])->name('verify');

Route::get('/reset-password', function (Request $request) {
    $token = $request->query('token');
    $email = $request->query('email');

    return view('reset_password', [
        'token' => $token,
        'email' => $email,
    ]);
    return view('reset_password');
})->name('password.reset');

Route::post('/password/update', [PasswordController::class, 'update_password'])->name('password.update');




