<?php

use App\Http\Controllers\CommandsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationsController;
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

Route::group(['middleware' => ['guest']], static function () {

    Route::get('/', static function () {
        return view('auth.login');
    });

});

Route::get('/email/verify', static function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', static function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notifications', static function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::group(
    [
        'middleware' => ['auth', 'verified']
    ], static function () {

    //==============================dashboard============================
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::post('store-token', [NotificationsController::class, 'updateDeviceToken'])->name('store.token');

    Route::post('switch_account/', [HomeController::class, 'switchAccountUser'])->name('switch_account');
    Route::any('user/notifications/read/{id}', [NotificationsController::class, 'markAsReadAndRedirect'])->name('user/notifications/read');

    Route::group(['middleware' => ['role:أمير المركز']], static function () {
        //==============================Grades============================
        Route::view('manage_grade', 'pages.grades.index')->name('manage_grade');
        //==============================Supervisors============================
        Route::view('manage_supervisor', 'pages.supervisors.index')->name('manage_supervisor');
        //==============================Sponsorship Supervisors============================
        Route::view('manage_sponsorship_supervisors', 'pages.sponsorship_supervisors.index')->name('manage_sponsorship_supervisors');
        //==============================Roles============================
        Route::view('manage_roles', 'pages.roles.index')->name('manage_roles');
        //==============================Sponsorships============================
        Route::view('manage_sponsorships', 'pages.sponsorships.index')->name('manage_sponsorships');

        //==============================Public Statistics============================
        Route::view('manage_public_statistics', 'pages.public-statistics.index')->name('manage_public_statistics');

        //==============================Punitive Measures============================
        Route::view('manage_punitive_measures', 'pages.punitive_measures.index')->name('manage_punitive_measures');

        //==============================Users============================
        Route::view('manage_users', 'pages.users.index')->name('manage_users');

        //==============================Settings============================
        Route::view('manage_settings', 'pages.settings.index')->name('manage_settings');
    });

    Route::group(['middleware' => ['role:مشرف حلقات مكفولة|أمير المركز|مشرف']], static function () {
        //==============================Groups============================
        Route::view('manage_group', 'pages.groups.index')->name('manage_group');
        //==============================Teachers============================
        Route::view('manage_teacher', 'pages.teachers.index')->name('manage_teacher');

        //==============================Teachers============================
        Route::view('manage_students_reports_status', 'pages.students_reports_status.index')->name('manage_students_reports_status');
    });

    Route::group(['middleware' => ['role:مشرف']], static function () {
        //==============================Teachers Attendance============================
        Route::view('manage_teachers_attendance', 'pages.teachers_attendance.index')->name('manage_teachers_attendance');
    });

    Route::group(['middleware' => ['role:أمير المركز|مشرف|محفظ|مشرف حلقات مكفولة']], static function () {
        //==============================Students============================
        Route::view('manage_student/{id?}', 'pages.students.index')->name('manage_student');

        //==============================Students Daily Memorization============================
        Route::view('manage_students_daily_memorization', 'pages.students_daily_memorization.index')->name('manage_students_daily_memorization');

        //==============================Report Daily Memorization============================
        Route::view('manage_report_daily_memorization', 'pages.report_daily_memorization.index')->name('manage_report_daily_memorization');

        //==============================Report Daily Memorization Sunnah============================
        Route::view('manage_report_daily_memorization_sunnah', 'pages.report_daily_memorization_sunnah.index')->name('manage_report_daily_memorization_sunnah');

        //==============================Report Monthly Memorization============================
        Route::view('manage_report_monthly_memorization', 'pages.report_monthly_memorization.index')->name('manage_report_monthly_memorization');
    });

    Route::group(['middleware' => ['role:مشرف|محفظ']], static function () {
        //==============================Students Daily Memorization============================
        Route::view('manage_students_daily_memorization', 'pages.students_daily_memorization.index')->name('manage_students_daily_memorization');
    });

    Route::group(['middleware' => ['role:مشرف الإختبارات|أمير المركز|مشرف|محفظ|مختبر|مشرف حلقات مكفولة']], static function () {
        //==============================Exams============================
        Route::view('manage_exams/{id?}', 'pages.exams.index')->name('manage_exams');
        Route::view('manage_sunnah_exams/{id?}', 'pages.sunnah_exams.index')->name('manage_sunnah_exams');
    });

    Route::group(['middleware' => ['role:مشرف حلقات مكفولة|أمير المركز|مشرف الإختبارات|مشرف|محفظ|مختبر']], static function () {
        //==============================Exam Orders============================
        Route::view('manage_exams_orders/{id?}', 'pages.exams_orders.index')->name('manage_exams_orders');

        //==============================Exams Today============================
        Route::view('manage_today_exams', 'pages.today_exams.index')->name('manage_today_exams');
    });

    Route::group(['middleware' => ['role:مشرف الإختبارات']], static function () {
        //==============================Exams Settings============================
        Route::view('manage_exams_settings', 'pages.exams_settings.index')->name('manage_exams_settings');

        //==============================Public Exam Statistics============================
        Route::view('manage_public_exam_statistics', 'pages.public-exam-statistics.index')->name('manage_public_exam_statistics');

        //==============================Group Exam Statistics============================
        Route::view('manage_group_exam_statistics', 'pages.group-exam-statistics.index')->name('manage_group_exam_statistics');

        //==============================Testers============================
        Route::view('manage_testers', 'pages.testers.index')->name('manage_testers');
    });


    Route::group(['middleware' => ['role:مشرف الإختبارات|أمير المركز']], static function () {
        //==============================Add Exam============================
        Route::view('manage_add_exam', 'pages.add_exam.index')->name('manage_add_exam');
    });

    Route::group(['middleware' => ['role:مشرف الرقابة|أمير المركز|مشرف|محفظ']], static function () {
        //==============================Complaint Box Suggestion============================
        Route::view('manage_box_complaint_suggestions/{id?}', 'pages.box_complaint_suggestions.index')->name('manage_box_complaint_suggestions');
    });

    Route::group(['middleware' => ['role:مشرف الرقابة']], static function () {
        //==============================Oversight Members============================
        Route::view('manage_oversight_members', 'pages.oversight_members.index')->name('manage_oversight_members');
        //==============================Select Visit Group============================
        Route::view('manage_select_visit_groups', 'pages.select_visit_group.index')->name('manage_select_visit_groups');

        //==============================Select Visit Tester============================
        Route::view('manage_select_visit_testers', 'pages.select_visit_tester.index')->name('manage_select_visit_testers');

        //==============================Select Visit Activity Member============================
        Route::view('manage_select_visit_activity_members', 'pages.select_visit_activity_member.index')->name('manage_select_visit_activity_members');
    });

    Route::group(['middleware' => ['role:أمير المركز|مشرف الرقابة|مراقب']], static function () {
        //==============================Visits============================
        Route::view('manage_visits/{id?}', 'pages.visits.index')->name('manage_visits');
    });

    Route::group(['middleware' => ['role:مراقب|مشرف الرقابة']], static function () {
        //==============================Visits Orders============================
        Route::view('manage_visits_orders/{id?}', 'pages.visits_orders.index')->name('manage_visits_orders');

        //==============================Visits Orders Today============================
        Route::view('manage_visits_today', 'pages.today_visits.index')->name('manage_visits_today');
    });

    Route::group(['middleware' => ['role:مشرف الأنشطة']], static function () {
        //==============================Activity Members============================
        Route::view('manage_activity_members', 'pages.activity_members.index')->name('manage_activity_members');
        //==============================Activities Types============================
        Route::view('manage_activities_types', 'pages.activities_types.index')->name('manage_activities_types');
    });

    Route::group(['middleware' => ['role:مشرف الأنشطة|أمير المركز|مشرف|منشط|محفظ']], static function () {
        //==============================Activities============================
        Route::view('manage_activities', 'pages.activities.index')->name('manage_activities');
    });

    Route::group(['middleware' => ['role:مشرف الأنشطة|منشط|مشرف|محفظ']], static function () {
        //==============================Activities Orders============================
        Route::view('manage_activities_orders/{id?}', 'pages.activities_orders.index')->name('manage_activities_orders');
    });

    Route::group(['middleware' => ['role:مشرف حلقات مكفولة|مشرف الإختبارات|أمير المركز|مشرف|محفظ']], static function () {
        //==============================Quran Memorizers============================
        Route::view('manage_quran_memorizers', 'pages.quran_memorizers.index')->name('manage_quran_memorizers');

        //==============================External Exams============================
        Route::view('manage_external_exams/{id?}', 'pages.external_exams.index')->name('manage_external_exams');
    });

    Route::group(['middleware' => ['role:محفظ|أمير المركز|مشرف']], static function () {
        //==============================Track Student Transfers============================
        Route::view('manage_track_student_transfers/{id?}', 'pages.track_student_transfers.index')->name('manage_track_student_transfers');
    });

    //==============================Manage Password============================
    Route::view('manage_password', 'pages.manage_password.index')->name('manage_password');
});

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/follow-us', static function (){
    return view('follow-us');
})->name('follow-us');
Route::get('/executeJobCheckStudent', [CommandsController::class, 'executeJobCheckStudent'])->name('executeJobCheckStudent');
Route::get('/executeJobPreparingSemiMonthlyReport', [CommandsController::class, 'executeJobPreparingSemiMonthlyReport'])->name('executeJobPreparingSemiMonthlyReport');
Route::get('/executeJobSendSemiMonthlyReport', [CommandsController::class, 'executeJobSendSemiMonthlyReport'])->name('executeJobSendSemiMonthlyReport');
Route::get('/executeJobCheckVisit', [CommandsController::class, 'executeJobCheckVisit'])->name('executeJobCheckVisit');
Route::get('/executeJobCheckPermission', [CommandsController::class, 'executeJobCheckPermission'])->name('executeJobCheckPermission');
