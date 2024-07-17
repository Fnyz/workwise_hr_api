<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\GithubAuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SentEmailController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PayslipController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\WelcomeEmailController;
use App\Http\Controllers\CompensationController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\RatesController;
use App\Http\Controllers\PayrollApprovedController;



Route::middleware('auth:sanctum')->group(function () {
 
    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('position', PositionController::class);
    Route::apiResource('department', DepartmentController::class);
    Route::apiResource('employee', EmployeeController::class);
    Route::apiResource('holiday', HolidayController::class);
    Route::apiResource('leave_type', LeaveTypeController::class);
    Route::apiResource('notification', NotificationController::class);
    Route::apiResource('compensation', PayrollController::class);
    Route::apiResource('rates', RatesController::class);
    Route::apiResource('payroll_approved', PayrollApprovedController::class);
    Route::apiResource('payslip', PayslipController::class);

    Route::get('/leave/employee/{id}', [LeaveController::class, 'allEmployeeLeave']);
    Route::get('/verify-email', [SentEmailController::class, 'sendEmail']);
    
    Route::apiResource('leave', LeaveController::class)->names([
        'index' => 'leave.index',
        'store' => 'leave.store',
        'show' => 'leave.show',
        'update' => 'leave.update',
        'destroy' => 'leave.destroy',
    ]);
    


    Route::get('/attendance/employee/{id}', [AttendanceController::class, 'allEmployeeAttendance']);


    Route::apiResource('attendance', AttendanceController::class)->names([
        'index' => 'attendance.index',
        'store' => 'attendance.store',
        'show' => 'attendance.show',
        'update' => 'attendance.update',
        'destroy' => 'attendance.destroy',
    ]);

    Route::post('/change-password', [PasswordController::class, 'changePassword']);
    Route::put('/users/{id}/email', [AuthController::class, 'updateEmail']);


});

Route::get('/test', function(Request $request){
   return $request->json([
    'message' => 'working api'
   ]);
});


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/send-password-reset-email', [SentEmailController::class, 'sendPasswordResetEmail']);
Route::post('/send-welcome-email', [WelcomeEmailController::class, 'sendWelcomeEmail']);


