<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\StudentBlock;
use App\Models\StudentWarning;
use App\Notifications\NewStudentBlockForTeacherNotify;
use App\Notifications\NewStudentWarningForTeacherNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckStudentStatusCron extends Command
{
    use NotificationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckStudentStatus:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command Check Student Status';

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
        $month = Date('m');
        $year = Date('Y');
        $link = 'manage_student/';
        $studentWarningsAbsence = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_warnings.warning_expiry_date',
                DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = 'absence') AS 'attendance_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                 AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS warning_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'warning' AND punitive_measures.reason = 'absence' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_warnings', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_warnings.student_id')
                    ->on('student_warnings.id', '=', DB::raw("(SELECT id FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                              AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = punitive_measures.reason)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_warnings.warning_expiry_date', 'warning_count', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->warning_expiry_date === null) {
                $studentWarningsAbsence[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::ABSENCE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->attendance_count / $student->number_times) > $student->warning_count) {
                $studentWarningsAbsence[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::ABSENCE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentWarningsAbsence)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentWarningsAbsence as $item) {
                    $student_warning = StudentWarning::create($item);
                    if ($student_warning) {
                        $student_name = $student_warning->student->user->name;
                        $teacher = $student_warning->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentWarningForTeacherNotify([
                            'id' => $item['id'],
                            'student_name' => $student_name,
                            'student_id' => $student_warning->student_id,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "إنذار طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentWarning::ABSENCE_REASON) {
                            $message = "لقد تم إعطاء الطالب: " . $student_name . " إنذار نهائي بسبب غيابه المتكرر لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
                        }
                        $this->push_notification($message, $title, $link . $student_warning->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception $e) {
                DB::rollback();
            }
        }

/////////////////////////////////////////////////////////////


        $studentBlocksAbsence = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_blocks.block_expiry_date',
                DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = 'absence') AS 'attendance_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                 AND year(block_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS block_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'block' AND punitive_measures.reason = 'absence' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_blocks', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_blocks.student_id')
                    ->on('student_blocks.id', '=', DB::raw("(SELECT id FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                              AND year(block_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_blocks')
                    ->where('students.id', '=', DB::raw('student_blocks.student_id'))
                    ->where('student_blocks.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('block_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->whereExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc');
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = punitive_measures.reason)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_blocks.block_expiry_date', 'block_count', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->block_expiry_date === null) {
                $studentBlocksAbsence[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::ABSENCE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->attendance_count / $student->number_times) > $student->block_count) {
                $studentBlocksAbsence[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::ABSENCE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentBlocksAbsence)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentBlocksAbsence as $item) {
                    $student_block = StudentBlock::create($item);
                    if ($student_block) {
                        $student_name = $student_block->student->user->name;
                        $teacher = $student_block->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentBlockForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_block->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "حظر طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentBlock::ABSENCE_REASON) {
                            $message = "لقد تم حظر الطالب: " . $student_name . " بسبب غيابه المتكرر لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
                        }
                        $this->push_notification($message, $title, $link . $student_block->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }
//////////////////////////////////////////////////////////////////////////////////////////

        $studentWarningsAuthorized = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_warnings.warning_expiry_date',
                DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = 'authorized') AS 'attendance_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                 AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS warning_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'warning' AND punitive_measures.reason = 'authorized' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_warnings', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_warnings.student_id')
                    ->on('student_warnings.id', '=', DB::raw("(SELECT id FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                              AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = punitive_measures.reason)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_warnings.warning_expiry_date', 'warning_count', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->warning_expiry_date === null) {
                $studentWarningsAuthorized[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::AUTHORIZED_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if ((int)($student->attendance_count / $student->number_times) > $student->warning_count) {
                $studentWarningsAuthorized[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::AUTHORIZED_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentWarningsAuthorized)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentWarningsAuthorized as $item) {
                    $student_warning = StudentWarning::create($item);
                    if ($student_warning) {
                        $student_name = $student_warning->student->user->name;
                        $teacher = $student_warning->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentWarningForTeacherNotify([
                            'id' => $item['id'],
                            'student_name' => $student_name,
                            'student_id' => $student_warning->student_id,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "إنذار طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentWarning::AUTHORIZED_REASON) {
                            $message = "لقد تم إعطاء الطالب: " . $student_name . " إنذار نهائي بسبب الأذونات المتكررة لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
                        }
                        $this->push_notification($message, $title, $link . $student_warning->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception $e) {
                DB::rollback();
            }
        }

/////////////////////////////////////////////////////////////


        $studentBlocksAuthorized = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_blocks.block_expiry_date',
                DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = 'authorized') AS 'attendance_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                 AND year(block_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS block_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'block' AND punitive_measures.reason = 'authorized' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_blocks', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_blocks.student_id')
                    ->on('student_blocks.id', '=', DB::raw("(SELECT id FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                              AND year(block_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_blocks')
                    ->where('students.id', '=', DB::raw('student_blocks.student_id'))
                    ->where('student_blocks.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('block_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->whereExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc');
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = punitive_measures.reason)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_blocks.block_expiry_date', 'block_count', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->block_expiry_date === null) {
                $studentBlocksAuthorized[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::AUTHORIZED_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if ((int)($student->attendance_count / $student->number_times) > $student->block_count) {
                $studentBlocksAuthorized[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::AUTHORIZED_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentBlocksAuthorized)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentBlocksAuthorized as $item) {
                    $student_block = StudentBlock::create($item);
                    if ($student_block) {
                        $student_name = $student_block->student->user->name;
                        $teacher = $student_block->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentBlockForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_block->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "حظر طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentBlock::AUTHORIZED_REASON) {
                            $message = "لقد تم حظر الطالب: " . $student_name . " بسبب الأذونات المتكررة لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
                        }
                        $this->push_notification($message, $title, $link . $student_block->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }
//////////////////////////////////////////////////////////////////////////////////////////


        $studentWarningsLate = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_warnings.warning_expiry_date',
                DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = 'late') AS 'attendance_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                 AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS warning_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'warning' AND punitive_measures.reason = 'late' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_warnings', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_warnings.student_id')
                    ->on('student_warnings.id', '=', DB::raw("(SELECT id FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                              AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = punitive_measures.reason)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_warnings.warning_expiry_date', 'warning_count', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->warning_expiry_date === null) {
                $studentWarningsLate[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::LATE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->attendance_count / $student->number_times) > $student->warning_count) {
                $studentWarningsLate[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::LATE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentWarningsLate)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentWarningsLate as $item) {
                    $student_warning = StudentWarning::create($item);
                    if ($student_warning) {
                        $student_name = $student_warning->student->user->name;
                        $teacher = $student_warning->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentWarningForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_warning->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "إنذار طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentWarning::LATE_REASON) {
                            $message = "لقد تم إعطاء الطالب: " . $student_name . " إنذار نهائي بسبب تأخره المتكرر لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
                        }
                        $this->push_notification($message, $title, $link . $student_warning->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }

/////////////////////////////////////////////////////////////


        $studentBlocksLate = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_blocks.block_expiry_date',
                DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = 'late') AS 'attendance_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                 AND year(block_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS block_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'block' AND punitive_measures.reason = 'late' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_blocks', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_blocks.student_id')
                    ->on('student_blocks.id', '=', DB::raw("(SELECT id FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                              AND year(block_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_blocks')
                    ->where('students.id', '=', DB::raw('student_blocks.student_id'))
                    ->where('student_blocks.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('block_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->whereExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc');
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from student_attendances WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND STATUS = punitive_measures.reason)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_blocks.block_expiry_date', 'block_count', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->block_expiry_date === null) {
                $studentBlocksLate[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::LATE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->attendance_count / $student->number_times) > $student->block_count) {
                $studentBlocksLate[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::LATE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentBlocksLate)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentBlocksLate as $item) {
                    $student_block = StudentBlock::create($item);
                    if ($student_block) {
                        $student_name = $student_block->student->user->name;
                        $teacher = $student_block->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentBlockForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_block->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "حظر طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentBlock::ABSENCE_REASON) {
                            $message = "لقد تم حظر الطالب: " . $student_name . " بسبب تأخره المتكرر لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
                        }
                        $this->push_notification($message, $title, $link . $student_block->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }

////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $studentWarningsMemorize = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_warnings.warning_expiry_date',
                DB::raw("(select count(id) from students_daily_memorization WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND type = 'memorize' AND number_pages <= punitive_measures.quantity ) AS 'daily_memorization_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                 AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS warning_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'warning' AND punitive_measures.reason = 'memorize' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_warnings', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_warnings.student_id')
                    ->on('student_warnings.id', '=', DB::raw("(SELECT id FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                              AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from students_daily_memorization WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND type = 'memorize' AND number_pages <= punitive_measures.quantity)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_warnings.warning_expiry_date', 'warning_count', 'punitive_measures.quantity', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->warning_expiry_date === null) {
                $studentWarningsMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times, 'number_pages' => $student->quantity], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->daily_memorization_count / $student->number_times) > $student->warning_count) {
                $studentWarningsMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times, 'number_pages' => $student->quantity], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentWarningsMemorize)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentWarningsMemorize as $item) {
                    $student_warning = StudentWarning::create($item);
                    if ($student_warning) {
                        $student_name = $student_warning->student->user->name;
                        $teacher = $student_warning->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentWarningForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_warning->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                            'number_pages' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_pages,
                        ]));
                        $title = "إنذار طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentWarning::MEMORIZE_REASON) {
                            $message = "لقد تم إعطاء الطالب: " . $student_name . " إنذار نهائي بسبب تسميعه المتكرر أقل من " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_pages . " صفحة لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
                        }
                        $this->push_notification($message, $title, $link . $student_warning->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }


        ///////////////////////////////////////////////////////////////////////////////////


        $studentBlocksMemorize = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_blocks.block_expiry_date',
                DB::raw("(select count(id) from students_daily_memorization WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND type = 'memorize' AND number_pages <= punitive_measures.quantity ) AS 'daily_memorization_count'"),
                DB::raw("(SELECT COUNT(id) FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                 AND year(block_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS block_count"), 'punitive_measures.number_times')
            ->Join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->on('punitive_measure_groups.punitive_measure_id', '=', DB::raw("(SELECT measure_groups.punitive_measure_id FROM punitive_measure_groups measure_groups
                  JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
                  AND punitive_measures.type = 'block' AND punitive_measures.reason = 'memorize' WHERE measure_groups.group_id = punitive_measure_groups.group_id LIMIT 1)"));
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_blocks', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'student_blocks.student_id')
                    ->on('student_blocks.id', '=', DB::raw("(SELECT id FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                              AND year(block_expiry_date) = $year AND reason = punitive_measures.reason order by `updated_at` DESC LIMIT 1)"));
            })
            ->whereNotExists(function ($query) {
                $query->from('student_blocks')
                    ->where('students.id', '=', DB::raw('student_blocks.student_id'))
                    ->where('student_blocks.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('block_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->whereExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc');
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->where(DB::raw("(select count(id) from students_daily_memorization WHERE students.id = student_id and month(DATETIME) = $month and year(DATETIME) = $year AND type = 'memorize' AND number_pages <= punitive_measures.quantity)"), '>=', DB::raw('punitive_measures.number_times'))
            ->groupBy('students.id', 'student_blocks.block_expiry_date', 'block_count', 'punitive_measures.quantity', 'punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->block_expiry_date === null) {
                $studentBlocksMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times, 'number_pages' => $student->quantity], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->daily_memorization_count / $student->number_times) > $student->block_count) {
                $studentBlocksMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times, 'number_pages' => $student->quantity], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentBlocksMemorize)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentBlocksMemorize as $item) {
                    $student_block = StudentBlock::create($item);
                    if ($student_block) {
                        $student_name = $student_block->student->user->name;
                        $teacher = $student_block->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentBlockForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_block->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                            'number_pages' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_pages,
                        ]));
                        $title = "حظر طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentBlock::ABSENCE_REASON) {
                            $message = "لقد تم حظر الطالب: " . $student_name . " بسبب تسميعه المتكرر أقل من " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_pages . " صفحة لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
                        }
                        $this->push_notification($message, $title, $link . $student_block->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }

        //////////////////////////////////////////////////////////////////////////////////////

        $studentWarningsNotMemorize = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_warnings.warning_expiry_date', DB::raw("(select COUNT(student_attendances.id) from student_attendances
                           LEFT join students_daily_memorization ON student_attendances.student_id = students_daily_memorization.student_id
                           AND CONVERT(students_daily_memorization.datetime,DATE) = CONVERT(student_attendances.datetime,DATE)
                           WHERE students_daily_memorization.id IS NULL AND student_attendances.student_id = students.id and
                           month(student_attendances.DATETIME) = $month and year(student_attendances.DATETIME) = $year
                           AND student_attendances.`status` IN ('presence','late')) AS memorization_count"),
                DB::raw("(SELECT COUNT(id) FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                 AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS warning_count"), 'punitive_measures.number_times')
            ->join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->whereRaw("punitive_measure_groups.punitive_measure_id = (SELECT measure_groups.punitive_measure_id
               FROM punitive_measure_groups measure_groups
               JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
               AND punitive_measures.type = 'warning'
               AND punitive_measures.reason = 'did-not-memorize'
               WHERE measure_groups.group_id = punitive_measure_groups.group_id
              LIMIT 1)");
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_warnings', function ($join) use ($year, $month) {
                $join->on('students.id', '=', 'student_warnings.student_id')
                    ->whereRaw("student_warnings.id = (SELECT id FROM student_warnings WHERE student_id = students.id AND month(warning_expiry_date) = $month
                       AND year(warning_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY updated_at DESC LIMIT 1)");
            })
            ->whereNotExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->where('groups.type', '=', Group::QURAN_TYPE)
            ->whereRaw("(select COUNT(student_attendances.id) from student_attendances
                           LEFT join students_daily_memorization ON student_attendances.student_id = students_daily_memorization.student_id
                           AND CONVERT(students_daily_memorization.datetime,DATE) = CONVERT(student_attendances.datetime,DATE)
                           WHERE students_daily_memorization.id IS NULL AND student_attendances.student_id = students.id and
                           month(student_attendances.DATETIME) = $month and year(student_attendances.DATETIME) = $year
                           AND student_attendances.`status` IN ('presence','late')) >= punitive_measures.number_times")
            ->groupByRaw('students.id, student_warnings.warning_expiry_date, memorization_count, warning_count, punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->warning_expiry_date === null) {
                $studentWarningsNotMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::DID_NOT_MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->memorization_count / $student->number_times) > $student->warning_count) {
                $studentWarningsNotMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentWarning::DID_NOT_MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentWarningsNotMemorize)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentWarningsNotMemorize as $item) {
                    $student_warning = StudentWarning::create($item);
                    if ($student_warning) {
                        $student_name = $student_warning->student->user->name;
                        $teacher = $student_warning->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentWarningForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_warning->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "إنذار طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentWarning::DID_NOT_MEMORIZE_REASON) {
                            $message = "لقد تم إعطاء الطالب: " . $student_name . " إنذار نهائي بسبب عدم الحفظ المتكرر لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
                        }
                        $this->push_notification($message, $title, $link . $student_warning->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }
        /////////////////////////////////////////////////////////////////////////////////////////////

        $studentBlocksNotMemorize = [];
        $students = DB::table('groups')
            ->select('students.id', 'student_blocks.block_expiry_date', DB::raw("(select COUNT(student_attendances.id) from student_attendances
                           LEFT join students_daily_memorization ON student_attendances.student_id = students_daily_memorization.student_id
                           AND CONVERT(students_daily_memorization.datetime,DATE) = CONVERT(student_attendances.datetime,DATE)
                           WHERE students_daily_memorization.id IS NULL AND student_attendances.student_id = students.id and
                           month(student_attendances.DATETIME) = $month and year(student_attendances.DATETIME) = $year
                           AND student_attendances.`status` IN ('presence','late')) AS memorization_count"),
                DB::raw("(SELECT COUNT(id) FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                 AND year(block_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY `updated_at`) AS block_count"), 'punitive_measures.number_times')
            ->join('punitive_measure_groups', function ($join) {
                $join->on('groups.id', '=', 'punitive_measure_groups.group_id')
                    ->whereRaw("punitive_measure_groups.punitive_measure_id = (SELECT measure_groups.punitive_measure_id
               FROM punitive_measure_groups measure_groups
               JOIN punitive_measures ON measure_groups.punitive_measure_id = punitive_measures.id
               AND punitive_measures.type = 'block'
               AND punitive_measures.reason = 'did-not-memorize'
               WHERE measure_groups.group_id = punitive_measure_groups.group_id
              LIMIT 1)");
            })
            ->join('punitive_measures', 'punitive_measure_groups.punitive_measure_id', '=', 'punitive_measures.id')
            ->join('students', 'groups.id', '=', 'students.group_id')
            ->leftJoin('student_blocks', function ($join) use ($year, $month) {
                $join->on('students.id', '=', 'student_blocks.student_id')
                    ->whereRaw("student_blocks.id = (SELECT id FROM student_blocks WHERE student_id = students.id AND month(block_expiry_date) = $month
                       AND year(block_expiry_date) = $year AND reason = punitive_measures.reason ORDER BY updated_at DESC LIMIT 1)");
            })
            ->whereNotExists(function ($query) {
                $query->from('student_blocks')
                    ->where('students.id', '=', DB::raw('student_blocks.student_id'))
                    ->where('student_blocks.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('block_expiry_date')
                    ->orderBy('updated_at', 'desc')
                    ->limit(1);
            })
            ->whereExists(function ($query) {
                $query->from('student_warnings')
                    ->where('students.id', '=', DB::raw('student_warnings.student_id'))
                    ->where('student_warnings.reason', '=', DB::raw('punitive_measures.reason'))
                    ->whereNull('warning_expiry_date')
                    ->orderBy('updated_at', 'desc');
            })
            ->whereRaw("(select COUNT(student_attendances.id) from student_attendances
                           LEFT join students_daily_memorization ON student_attendances.student_id = students_daily_memorization.student_id
                           AND CONVERT(students_daily_memorization.datetime,DATE) = CONVERT(student_attendances.datetime,DATE)
                           WHERE students_daily_memorization.id IS NULL AND student_attendances.student_id = students.id and
                           month(student_attendances.DATETIME) = $month and year(student_attendances.DATETIME) = $year
                           AND student_attendances.`status` IN ('presence','late')) >= punitive_measures.number_times")
            ->groupByRaw('students.id, student_blocks.block_expiry_date, memorization_count, block_count, punitive_measures.number_times')
            ->get();

        foreach ($students as $student) {
            if ($student->block_expiry_date === null) {
                $studentBlocksNotMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::DID_NOT_MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            } else if (intval($student->memorization_count / $student->number_times) > $student->block_count) {
                $studentBlocksNotMemorize[] = [
                    'id' => Str::uuid()->toString(),
                    'student_id' => $student->id,
                    'reason' => StudentBlock::DID_NOT_MEMORIZE_REASON,
                    'details' => json_encode(['number_times' => $student->number_times], JSON_THROW_ON_ERROR),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }

        if (!empty($studentBlocksNotMemorize)) {
            DB::beginTransaction();
            try {
                DB::commit();
                foreach ($studentBlocksNotMemorize as $item) {
                    $student_block = StudentBlock::create($item);
                    if ($student_block) {
                        $student_name = $student_block->student->user->name;
                        $teacher = $student_block->student->group->teacher ?? null;
                        $teacher_user = $teacher->user ?? null;
                        $teacher_user?->notify(new NewStudentBlockForTeacherNotify([
                            'id' => $item['id'],
                            'student_id' => $student_block->student_id,
                            'student_name' => $student_name,
                            'reason' => $item['reason'],
                            'number_times' => json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times,
                        ]));
                        $title = "حظر طالب جديد";
                        $message = "";
                        if ($item['reason'] === StudentBlock::ABSENCE_REASON) {
                            $message = "لقد تم حظر الطالب: " . $student_name . " بسبب عدم الحفظ المتكرر لمدة " . json_decode($item['details'], false, 512, JSON_THROW_ON_ERROR)->number_times . " أيام,راجع أمير المركز!";
                        }
                        $this->push_notification($message, $title, $link . $student_block->student_id, [$teacher_user?->user_fcm_token->device_token ?? null]);
                    }
                }
            } catch (\Exception) {
                DB::rollback();
            }
        }
    }
}
