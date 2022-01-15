<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['guest']], function () {

    Route::get('/', function () {
        return view('auth.login');
    });
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::group(
    [
        'middleware' => ['auth', 'verified']
    ], function () {

    //==============================dashboard============================
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('switch_account/{current_role}', [HomeController::class, 'switchAccountUser']);
    Route::post('check_user_subscribe_notifications', [HomeController::class, 'checkUserSubscribeNotifications']);

    //==============================Grades============================
    Route::view('manage_grade', 'pages.grades.index');

    //==============================Groups============================
    Route::view('manage_group', 'pages.groups.index');

    //==============================Supervisors============================
    Route::view('manage_supervisor', 'pages.supervisors.index');

    //==============================LowerSupervisors============================
    Route::view('manage_lower_supervisor', 'pages.lower_supervisors.index');

    //==============================Teachers============================
    Route::view('manage_teacher', 'pages.teachers.index');

    //==============================Teachers Attendance============================
    Route::view('manage_teachers_attendance', 'pages.teachers_attendance.index');

    //==============================Students============================
    Route::view('manage_student', 'pages.students.index');

    //==============================Students Attendance============================
    Route::view('manage_students_attendance', 'pages.students_attendance.index');

    //==============================Students Daily Preservation============================
    Route::view('manage_students_daily_preservation', 'pages.students_daily_preservation.index');

    //==============================Report Daily Preservation============================
    Route::view('manage_report_daily_preservation', 'pages.report_daily_preservation.index');

    //==============================Exams============================
    Route::view('manage_exams', 'pages.exams.index');

    //==============================Exam Orders============================
    Route::view('manage_exams_orders', 'pages.exams_orders.index');

    //==============================Exams Today============================
    Route::view('manage_today_exams', 'pages.today_exams.index');

    //==============================Exams Settings============================
    Route::view('manage_exams_settings', 'pages.exams_settings.index');

    //==============================Testers============================
    Route::view('manage_testers', 'pages.testers.index');

    //==============================Users============================
    Route::view('manage_users', 'pages.users.index');

    //==============================Settings============================
    Route::view('manage_settings', 'pages.settings.index');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
