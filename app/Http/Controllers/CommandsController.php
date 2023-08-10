<?php

namespace App\Http\Controllers;

class CommandsController extends Controller
{
    public function executeJobCheckStudent()
    {
        return \Artisan::call('CheckStudentStatus:cron');//CheckStudentStatus:cron is a command
    }

    public function executeJobPreparingSemiMonthlyReport()
    {
        return \Artisan::call('PreparingSemiMonthlyReportCron:cron');//PreparingSemiMonthlyReportCron:cron is a command
    }

    public function executeJobSendSemiMonthlyReport()
    {
        return \Artisan::call('SendSemiMonthlyReportCron:cron');//SendSemiMonthlyReportCron:cron is a command
    }

    public function executeJobCheckVisit()
    {
        return \Artisan::call('CheckVisitReminderStatus:cron');//CheckVisitReminderStatus:cron is a command
    }

    public function executeJobCheckPermission()
    {
        return \Artisan::call('CheckPermissionExpirationForUser:cron');//CheckPermissionExpirationForUser:cron is a command
    }

}
