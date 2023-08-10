<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\StudentReportsStatus;
use App\Models\User;
use App\Traits\NotificationTrait;
use App\Traits\SendMessageWhatsappApiTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class PreparingSemiMonthlyReportCron extends Command
{
    use SendMessageWhatsappApiTrait,NotificationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PreparingSemiMonthlyReportCron:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Preparing a semi-monthly report for students';

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
     * @throws \JsonException
     */
    public function handle()
    {
        $currentDate = Carbon::now()->format('Y-m-d');
        if ($currentDate === Carbon::now()->endOfMonth()->format('Y-m-d')) {
            $start_date = Carbon::now()->setDay(16)->format('Y-m-d');
            $end_date = Carbon::now()->endOfMonth()->format('Y-m-d');
        } else if ($currentDate === Carbon::now()->setDay(15)->format('Y-m-d')) {
            $start_date = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end_date = Carbon::now()->setDay(15)->format('Y-m-d');
        } else {
            $start_date = null;
            $end_date = null;
        }

        if ($start_date !== null && $end_date !== null) {
            $reports = DB::table('groups')
                ->select('students.id', 'std_users.name as student_name', 'tea_users.name as teacher_name', 'students.whatsapp_number as student_whatsapp_number',
                    DB::raw("(SELECT SUM(number_pages) FROM students_daily_memorization WHERE student_id = students.id and date(datetime) between '$start_date' AND '$end_date' and type = 'memorize') AS 'number_memorize_pages'"),
                    DB::raw("(SELECT SUM(number_pages) FROM students_daily_memorization WHERE student_id = students.id and date(datetime) between '$start_date' AND '$end_date' and type = 'review') AS 'number_review_pages'"),
                    DB::raw("(SELECT SUM(number_pages) FROM students_daily_memorization WHERE student_id = students.id and date(datetime) between '$start_date' AND '$end_date' and type = 'cumulative-review') AS 'number_cumulative_review_pages'"),
                    DB::raw("(SELECT COUNT(id) FROM student_attendances WHERE student_id = students.id and date(datetime) between '$start_date' AND '$end_date' and status IN ('presence', 'late')) AS 'number_presence_days'"),
                    DB::raw("(SELECT COUNT(id) FROM student_attendances WHERE student_id = students.id and date(datetime) between '$start_date' AND '$end_date' and status IN ('absence', 'authorized')) AS 'number_absence_days'"),
                    DB::raw("GROUP_CONCAT('من سورة ',(select name from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id where daily_memorization_details.id = start_daily_memorization.id
                      order by sura_id desc limit 1),' آية ',(select aya_from from daily_memorization_details where daily_memorization_details.id = start_daily_memorization.id
                      order by sura_id desc limit 1)) as start_daily_memorization"),DB::raw("GROUP_CONCAT('إلى سورة ',(select name from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id where daily_memorization_details.id = end_daily_memorization.id
                      order by sura_id asc limit 1),' آية ',(select aya_to from daily_memorization_details where daily_memorization_details.id = end_daily_memorization.id
                      order by sura_id asc limit 1)) as end_daily_memorization"),
                    DB::raw("(GROUP_CONCAT(quran_parts.name,'-',quran_parts.description)) quran_part_name"), 'exams.mark', 'exam_success_mark.mark as exam_success_mark',
                    DB::raw("GROUP_CONCAT(current_part.name,'-',current_part.description) 'current_part_name'"))
                ->join('students', 'groups.id', '=', 'students.group_id')
                ->join('users as std_users', 'students.id', '=', 'std_users.id')
                ->join('users as tea_users', 'groups.teacher_id', '=', 'tea_users.id')
                ->leftJoin('students_daily_memorization as start_daily_memorization', function ($join) use ($end_date, $start_date) {
                    $join->on('students.id', '=', 'start_daily_memorization.student_id')
                        ->on('start_daily_memorization.id', '=', DB::raw("(SELECT students_daily_memorization.id FROM students_daily_memorization
                  WHERE student_id = students.id and type ='memorize' and date(datetime) between '$start_date' AND '$end_date' order by datetime asc LIMIT 1)"));
                })
                ->leftJoin('students_daily_memorization as end_daily_memorization', function ($join) use ($end_date, $start_date) {
                    $join->on('students.id', '=', 'end_daily_memorization.student_id')
                        ->on('end_daily_memorization.id', '=', DB::raw("(SELECT students_daily_memorization.id FROM students_daily_memorization
                  WHERE student_id = students.id and type ='memorize' and date(datetime) between '$start_date' AND '$end_date' order by datetime desc LIMIT 1)"));
                })
                ->leftJoin('quran_parts as current_part', 'students.current_part_id', '=', 'current_part.id')
                ->leftJoin('exams', function ($join) use ($end_date, $start_date) {
                    $join->on('students.id', '=', 'exams.student_id')
                        ->on('exams.id', '=', DB::raw("(SELECT exams.id FROM exams
                  WHERE student_id = students.id and date(datetime) between '$start_date' AND '$end_date' order by datetime desc LIMIT 1)"));
                })
                ->leftJoin('quran_parts', 'exams.quran_part_id', '=', 'quran_parts.id')
                ->leftJoin('exam_success_mark', 'exams.exam_success_mark_id', '=', 'exam_success_mark.id')
                ->where('groups.type', '=', Group::QURAN_TYPE)
                ->groupBy('students.id', 'exams.mark', 'exam_success_mark')
                ->get();

            foreach ($reports as $report) {
                $evaluation = 'ضعيف جدا';
                $notes = '-';
                if ($report->quran_part_name !== null && $report->mark >= $report->exam_success_mark) {
                    $evaluation = 'ممتاز';
                } else if ($report->number_memorize_pages > 3) {
                    $evaluation = 'ممتاز جدا';
                } elseif ($report->number_memorize_pages === 3) {
                    $evaluation = 'ممتاز';
                } elseif ($report->number_memorize_pages === 2) {
                    $evaluation = 'جيد جدا';
                } elseif ($report->number_memorize_pages <= 1) {
                    $evaluation = 'ضعيف';
                }

                if ($report->quran_part_name !== null) {
                    if ($report->mark >= $report->exam_success_mark) {
                        $notes = 'اجتاز الطالب اختبار جزء ' . $report->quran_part_name . ' بدرجة ' . $report->mark . '%.';
                    } else {
                        $notes = 'لم يجتاز الطالب اختبار جزء ' . $report->quran_part_name . ' بدرجة ' . $report->mark . '%.';
                    }
                }

                $array_body = [
                    [
                        'type' => 'text',
                        'text' => $start_date,
                    ],
                    [
                        'type' => 'text',
                        'text' => $end_date,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->student_name,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->teacher_name ?? '-',
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->number_memorize_pages ?? 0,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->number_review_pages ?? 0,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->number_cumulative_review_pages ?? 0,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->number_presence_days ?? 0,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->number_absence_days ?? 0,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->start_daily_memorization . ' ' . $report->end_daily_memorization,
                    ],
                    [
                        'type' => 'text',
                        'text' => $report->current_part_name ?? '-',
                    ],
                    [
                        'type' => 'text',
                        'text' => $evaluation,
                    ],
                    [
                        'type' => 'text',
                        'text' => $notes,
                    ],
                ];

                StudentReportsStatus::updateOrCreate(['student_id' => $report->id],[
                    'status' => StudentReportsStatus::READY_TO_SEND_STATUS,
                    'details' => json_encode($array_body, JSON_THROW_ON_ERROR)
                ]);
            }
        }

    }
}
