<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VisitProcessingReminder;
use App\Notifications\ReminderOfVisitForAdminNotify;
use App\Notifications\ReminderOfVisitForOversightSupervisorNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CheckVisitReminderStatusCron extends Command
{
    use NotificationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckVisitReminderStatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Check Visit Reminder Status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $visitProcessingReminders = VisitProcessingReminder::all();

        if (!empty($visitProcessingReminders)) {

            // start push notifications
            $title = "تذكير بمعالجة زيارة";
            $message = "";
            $hostname = "";

            // end push notifications

            foreach ($visitProcessingReminders as $value) {
                $reminder_datetime = Carbon::parse($value['reminder_datetime'])->format('Y-m-d');
                if ($reminder_datetime <= date('Y-m-d', time())) {
                    if ($value->visit->hostable_type == 'App\Models\Group') {
                        $hostname = $value->visit->hostable->teacher->user->name;
                        $message = "تذكير يعتبر تاريخ: " . $reminder_datetime . " المحدد لمعالجة الزيارة الخاصة بحلقة المحفظ: " . $hostname . " يرجى معالجة الزيارة. ";
                    } else if ($value->visit->hostable_type == 'App\Models\Tester') {
                        $hostname = $value->visit->hostable->user->name;
                        $message = "تذكير يعتبر تاريخ: " . $reminder_datetime . " المحدد لمعالجة الزيارة الخاصة بالمختبر: " . $hostname . " يرجى معالجة الزيارة. ";
                    } else if ($value->visit->hostable_type == 'App\Models\ActivityMember') {
                        $hostname = $value->visit->hostable->user->name;
                        $message = "تذكير يعتبر تاريخ: " . $reminder_datetime . " المحدد لمعالجة الزيارة الخاصة بالمنشط: " . $hostname . " يرجى معالجة الزيارة. ";
                    }

                    $role_admin = Role::where('name', User::ADMIN_ROLE)->first();
                    $role_users_admin = $role_admin->users();
                    if ($role_users_admin->first()) {
                        $role_users_admin->first()->notify(new ReminderOfVisitForAdminNotify([
                            'id' => $value->visit->id,
                            'hostname' => $hostname,
                            'host_type' => $value->visit->hostable_type,
                            'reminder_datetime' => $reminder_datetime,
                        ]));
                    }

                    $role_oversight_supervisor = Role::where('name', User::OVERSIGHT_SUPERVISOR_ROLE)->first();
                    $role_users_oversight_supervisor = $role_oversight_supervisor->users();
                    if ($role_users_oversight_supervisor->first()) {
                        $role_users_oversight_supervisor->first()->notify(new ReminderOfVisitForOversightSupervisorNotify([
                            'id' => $value->visit->id,
                            'hostname' => $hostname,
                            'host_type' => $value->visit->hostable_type,
                            'reminder_datetime' => $reminder_datetime,
                        ]));
                    }

                    $this->push_notification($message, $title, [$role_users_admin->first()->user_fcm_token->device_token]);
                    $this->push_notification($message, $title, [$role_users_oversight_supervisor->first()->user_fcm_token->device_token]);
                }
            }
        }
    }
}
