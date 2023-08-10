<?php

namespace App\Http\Livewire;

use App\Exports\DailyMemorizationExport;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentDailyMemorization;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportDailyMemorization extends HomeComponent
{

    public $groups = [], $grades = [], $students = [], $reports = [];

    public $selectedGradeId, $selectedTeacherId, $selectedStudentId, $searchReportType,
        $searchDateFrom, $searchDateTo, $group_id, $grade_id;

    public function render()
    {
        return view('livewire.report-daily-memorization', ['reports_daily_memorization' => $this->getReportsByStudentId()]);
    }

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getStudentsByTeacherId' => 'getStudentsByTeacherId',
        'getReportsByStudentId' => 'getReportsByStudentId',
    ];


    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->searchDateFrom = date('Y-m-d');
        $this->searchDateTo = date('Y-m-d');
        if ($this->current_role === User::TEACHER_ROLE) {
            $this->perPage = 25;
        }
        $this->all_Grades();
    }

    public function all_Grades()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role === User::SUPERVISOR_ROLE) {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->grade_id)->get();
        } else if ($this->current_role === User::TEACHER_ROLE) {
            $this->group_id = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === User::ADMIN_ROLE || $this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId');

        if ($this->current_role === User::SUPERVISOR_ROLE) {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::QURAN_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === User::ADMIN_ROLE) {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::QURAN_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            if ($this->selectedGradeId) {
                $groups_ids = DB::table('sponsorship_groups')
                    ->select(['group_id'])
                    ->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray())
                    ->distinct()
                    ->pluck('group_id')->toArray();
                $this->groups = Group::query()->with(['teacher.user'])
                    ->whereIn('id', $groups_ids)->get();
            }
        } elseif ($this->current_role === User::TEACHER_ROLE) {
            $this->groups = Group::query()
                ->where('type', Group::QURAN_TYPE)
                ->where('teacher_id', auth()->id())->get();
        }
    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role === User::ADMIN_ROLE || $this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
        }
    }

    public function getReportsByStudentId()
    {
        $reports = DB::table('student_attendances', 'attendance')
            ->select([DB::raw('attendance.datetime datetime'), DB::raw('users.name student_name'), DB::raw('user_tea.name teacher_name'),
                DB::raw('attendance.status attendance_status'), DB::raw('daily_memorization.type daily_memorization_type'),
                DB::raw("(select total_number_aya from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id where daily_memorization_details.id = daily_memorization.id
                      order by daily_memorization_details.sura_id desc limit 1) as number_aya_from"), DB::raw("(select total_number_aya from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id where daily_memorization_details.id = daily_memorization.id
                      order by daily_memorization_details.sura_id asc limit 1) as number_aya_to"), DB::raw("(select name from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id where daily_memorization_details.id = daily_memorization.id
                      order by daily_memorization_details.sura_id desc limit 1) as sura_from_name"), DB::raw("(select name from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id where daily_memorization_details.id = daily_memorization.id
                      order by daily_memorization_details.sura_id asc limit 1) as sura_to_name"), DB::raw("(select aya_from from daily_memorization_details where daily_memorization_details.id = daily_memorization.id
                      order by daily_memorization_details.sura_id desc limit 1) as aya_from"), DB::raw("(select aya_to from daily_memorization_details where daily_memorization_details.id = daily_memorization.id
                      order by daily_memorization_details.sura_id asc limit 1) as aya_to"), DB::raw('daily_memorization.evaluation evaluation'), DB::raw('daily_memorization.number_pages number_pages'),
                DB::raw("(GROUP_CONCAT(quran_parts.name,' ',quran_parts.description SEPARATOR '')) as `quran_part_name`"),
                DB::raw('exam.mark mark'), DB::raw('exam_success_mark.mark success_mark')])
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->join('groups', 'students.group_id', '=', 'groups.id')
            ->join('users as user_tea', 'groups.teacher_id', '=', 'user_tea.id')
            ->join('users', function ($join) {
                $join->on('students.id', '=', 'users.id')
                    ->when($this->current_role === 'أمير المركز', function ($q, $v) {
                        $q->when($this->selectedGradeId !== null, function ($q, $v) {
                            $q->on('students.grade_id', '=', DB::raw("(select id from `grades` where `id` = '$this->selectedGradeId' LIMIT 1)"));
                        })->when($this->selectedTeacherId !== null, function ($q, $v) {
                            $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                        })->when($this->selectedStudentId !== null, function ($q, $v) {
                            $q->on('students.id', '=', DB::raw("(select id from `students` where `id` = '$this->selectedStudentId' LIMIT 1)"));
                        });
                    })->when($this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE, function ($q, $v) {
                        $groups_ids = DB::table('sponsorship_groups')
                            ->select(['group_id'])
                            ->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray())
                            ->distinct()
                            ->pluck('group_id')->toArray();
                        $q->whereIn('students.group_id', $groups_ids)
                            ->when($this->selectedGradeId !== null, function ($q, $v) {
                                $q->on('students.grade_id', '=', DB::raw("(select id from `grades` where `id` = '$this->selectedGradeId' LIMIT 1)"));
                            })->when($this->selectedTeacherId !== null, function ($q, $v) {
                                $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                            })->when($this->selectedStudentId !== null, function ($q, $v) {
                                $q->on('students.id', '=', DB::raw("(select id from `students` where `id` = '$this->selectedStudentId' LIMIT 1)"));
                            });
                    })
                    ->when($this->current_role === 'مشرف', function ($q, $v) {
                        $q->on('students.grade_id', '=', DB::raw("(select id from `grades` where `id` = '$this->grade_id' or `id` = '$this->selectedGradeId' LIMIT 1)"))
                            ->when($this->selectedTeacherId !== null, function ($q, $v) {
                                $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                            })->when($this->selectedStudentId !== null, function ($q, $v) {
                                $q->on('students.id', '=', DB::raw("(select id from `students` where `id` = '$this->selectedStudentId' LIMIT 1)"));
                            });
                    })->when($this->current_role === 'محفظ', function ($q, $v) {
                        $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->group_id' or `id` = '$this->selectedTeacherId' LIMIT 1)"))
                            ->when($this->selectedStudentId !== null, function ($q, $v) {
                                $q->on('students.id', '=', DB::raw("(select id from `students` where `id` = '$this->selectedStudentId' LIMIT 1)"));
                            });
                    });
            })
            ->leftJoin('students_daily_memorization as daily_memorization', function ($join) {
                $join->on('students.id', '=', 'daily_memorization.student_id')
                    ->on(DB::raw('date(daily_memorization.datetime)'), '=', DB::raw('date(attendance.datetime)'));
            })
            ->when($this->searchReportType !== null, function ($q, $v) {
                $q->when($this->searchReportType === StudentDailyMemorization::MEMORIZE_TYPE
                    || $this->searchReportType === StudentDailyMemorization::REVIEW_TYPE
                    || $this->searchReportType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE, function ($q, $v) {
                    $q->where('daily_memorization.type', $this->searchReportType);
                })->when($this->searchReportType === StudentAttendance::AUTHORIZED_STATUS
                    || $this->searchReportType === StudentAttendance::ABSENCE_STATUS, function ($q, $v) {
                    $q->where('attendance.status', $this->searchReportType);
                })->when($this->searchReportType === 'exam', function ($q, $v) {
                    $q->join('exams', function ($join) {
                        $join->on('students.id', '=', 'exams.student_id')
                            ->on(DB::raw('date(exams.datetime)'), '=', DB::raw('date(attendance.datetime)'));
                    })->whereIn('attendance.status', [StudentAttendance::PRESENCE_STATUS, StudentAttendance::LATE_STATUS]);
                });
            })
            ->leftJoin('exams as exam', function ($join) {
                $join->on('students.id', '=', 'exam.student_id')
                    ->on(DB::raw('date(exam.datetime)'), '=', DB::raw('date(attendance.datetime)'));
            })
            ->leftJoin('quran_parts', 'exam.quran_part_id', '=', 'quran_parts.id')
            ->leftJoin('exam_success_mark', 'exam.exam_success_mark_id', '=', 'exam_success_mark.id')
            ->when($this->searchReportType !== null, function ($q, $v) {
                $q->when($this->searchReportType === StudentDailyMemorization::MEMORIZE_TYPE
                    || $this->searchReportType === StudentDailyMemorization::REVIEW_TYPE
                    || $this->searchReportType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE, function ($q, $v) {
                    $q->where('daily_memorization.type', $this->searchReportType);
                })->when($this->searchReportType === StudentAttendance::AUTHORIZED_STATUS
                    || $this->searchReportType === StudentAttendance::ABSENCE_STATUS, function ($q, $v) {
                    $q->where('attendance.status', $this->searchReportType);
                })->when($this->searchReportType === 'no-memorize', function ($q, $v) {
                    $q->whereNull('exam.id')
                        ->whereNull('daily_memorization.id')
                        ->whereIn('attendance.status', [StudentAttendance::PRESENCE_STATUS, StudentAttendance::LATE_STATUS]);
                })->when($this->searchReportType === 'exam', function ($q, $v) {
                    $q->whereNotNull('exam.id')
                        ->whereNull('daily_memorization.id')
                        ->whereIn('attendance.status', [StudentAttendance::PRESENCE_STATUS, StudentAttendance::LATE_STATUS]);
                });
            })->when($this->searchDateFrom !== null && $this->searchDateTo !== null, function ($q, $v) {
                $q->whereDate('attendance.datetime', '>=', $this->searchDateFrom)
                    ->whereDate('attendance.datetime', '<=', $this->searchDateTo);
            })
            ->when(!empty($this->search), function ($q, $v) {
                $q->where('users.name', 'LIKE', "%$this->search%")
                    ->orWhere('users.identification_number', 'LIKE', "%$this->search%");
            })
            ->groupBy(['students.id', 'datetime', 'student_name', 'attendance_status', 'daily_memorization_type',
                'number_aya_from', 'sura_from_name', 'aya_from', 'mark', 'success_mark',
                'number_aya_to', 'sura_to_name', 'aya_to', 'evaluation', 'number_pages', 'attendance.id'])
            ->orderBy('attendance.' . $this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $this->reports = $reports->toArray();
        return $reports;
    }

    public function submitSearch()
    {
        $this->getReportsByStudentId();
    }

    public function export()
    {
        return (new DailyMemorizationExport($this->reports['data']))->download('Memorization and review report.xlsx', Excel::XLSX);
    }
}
