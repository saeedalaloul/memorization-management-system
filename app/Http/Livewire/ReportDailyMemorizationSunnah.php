<?php

namespace App\Http\Livewire;

use App\Exports\DailyMemorizationSunnahExport;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentSunnahAttendance;
use App\Models\StudentSunnahDailyMemorization;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportDailyMemorizationSunnah extends HomeComponent
{

    public $groups = [], $grades = [], $students = [], $reports = [];

    public $selectedGradeId, $selectedTeacherId, $selectedStudentId, $searchReportType,
        $searchDateFrom, $searchDateTo, $group_id, $grade_id;

    public function render()
    {
        return view('livewire.report-daily-memorization-sunnah', ['reports_daily_memorization' => $this->getReportsByStudentId()]);
    }

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getStudentsByTeacherId' => 'getStudentsByTeacherId',
        'getReportsByStudentId' => 'getReportsByStudentId',
    ];


    public function mount()
    {
        $this->perPage = 25;
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
        if ($this->current_role === 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->grade_id)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->group_id = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId');

        if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::SUNNAH_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'أمير المركز') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::SUNNAH_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role === 'محفظ') {
            $this->groups = Group::query()
                ->where('type', Group::SUNNAH_TYPE)
                ->where('teacher_id', auth()->id())->get();
        }
    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role === 'أمير المركز') {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_sunnah_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_sunnah_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_sunnah_id', $this->selectedTeacherId)->get();
        }
    }

    public function getReportsByStudentId()
    {
        $reports = DB::table('student_sunnah_attendances', 'attendance')
            ->select([DB::raw('attendance.datetime datetime'), DB::raw('users.name student_name')
                , DB::raw('user_tea.name teacher_name'), DB::raw('attendance.status attendance_status')
                , DB::raw('daily_memorization.type daily_memorization_type'), DB::raw('sunnah_book.name book_name'), DB::raw('sunnah_book.total_number_hadith'),
                DB::raw('daily_memorization.hadith_from'), DB::raw('daily_memorization.hadith_to'), DB::raw('daily_memorization.evaluation evaluation'),
                DB::raw("GROUP_CONCAT(sunnah_part.name,' (',sunnah_part.total_hadith_parts,') حديث') sunnah_part_name"),
                DB::raw('exam.mark mark'), DB::raw('exam_success_mark.mark success_mark')])
            ->join('students', 'attendance.student_id', '=', 'students.id')
            ->join('groups', 'students.group_sunnah_id', '=', 'groups.id')
            ->join('users as user_tea', 'groups.teacher_id', '=', 'user_tea.id')
            ->join('users', 'students.id', '=', 'users.id')
            ->leftJoin('students_sunnah_daily_memorization as daily_memorization', function ($join) {
                $join->on('students.id', '=', 'daily_memorization.student_id')
                    ->on(DB::raw('date(daily_memorization.datetime)'), '=', DB::raw('date(attendance.datetime)'));
            })->when($this->searchReportType !== null, function ($q, $v) {
                $q->when($this->searchReportType === StudentSunnahDailyMemorization::MEMORIZE_TYPE
                    || $this->searchReportType === StudentSunnahDailyMemorization::REVIEW_TYPE, function ($q, $v) {
                    $q->where('daily_memorization.type', $this->searchReportType);
                })->when($this->searchReportType === StudentSunnahAttendance::AUTHORIZED_STATUS
                    || $this->searchReportType === StudentSunnahAttendance::ABSENCE_STATUS, function ($q, $v) {
                    $q->where('attendance.status', $this->searchReportType);
                })->when($this->searchReportType === 'exam', function ($q, $v) {
                    $q->join('sunnah_exams', function ($join) {
                        $join->on('students.id', '=', 'sunnah_exams.student_id')
                            ->on(DB::raw('date(sunnah_exams.datetime)'), '=', DB::raw('date(attendance.datetime)'));
                    })->whereIn('attendance.status', [StudentSunnahAttendance::PRESENCE_STATUS, StudentSunnahAttendance::LATE_STATUS]);
                });
            })
            ->leftJoin('sunnah_exams as exam', function ($join) {
                $join->on('students.id', '=', 'exam.student_id')
                    ->on(DB::raw('date(exam.datetime)'), '=', DB::raw('date(attendance.datetime)'));
            })
            ->leftJoin('sunnah_books as sunnah_book', 'daily_memorization.book_id', '=', 'sunnah_book.id')
            ->leftJoin('sunnah_parts as sunnah_part', 'exam.sunnah_part_id', '=', 'sunnah_part.id')
            ->leftJoin('exam_success_mark', 'exam.exam_success_mark_id', '=', 'exam_success_mark.id')
            ->when($this->searchReportType !== null, function ($q, $v) {
                $q->when($this->searchReportType === StudentSunnahDailyMemorization::MEMORIZE_TYPE
                    || $this->searchReportType === StudentSunnahDailyMemorization::REVIEW_TYPE, function ($q, $v) {
                    $q->where('daily_memorization.type', $this->searchReportType);
                })->when($this->searchReportType === StudentSunnahAttendance::AUTHORIZED_STATUS
                    || $this->searchReportType === StudentSunnahAttendance::ABSENCE_STATUS, function ($q, $v) {
                    $q->where('attendance.status', $this->searchReportType);
                })->when($this->searchReportType === 'no-memorize', function ($q, $v) {
                    $q->whereNull('exam.id')
                        ->whereNull('daily_memorization.id')
                        ->whereIn('attendance.status', [StudentSunnahAttendance::PRESENCE_STATUS, StudentSunnahAttendance::LATE_STATUS]);
                })->when($this->searchReportType === 'exam', function ($q, $v) {
                    $q->whereNotNull('exam.id')
                        ->whereNull('daily_memorization.id')
                        ->whereIn('attendance.status', [StudentSunnahAttendance::PRESENCE_STATUS, StudentSunnahAttendance::LATE_STATUS]);
                });
            })->when($this->searchDateFrom !== null && $this->searchDateTo !== null, function ($q, $v) {
                $q->whereDate('attendance.datetime', '>=', $this->searchDateFrom)
                    ->whereDate('attendance.datetime', '<=', $this->searchDateTo);
            })
            ->when(!empty($this->search), function ($q, $v) {
                $q->where('users.name', 'LIKE', "%$this->search%")
                    ->orWhere('users.identification_number', 'LIKE', "%$this->search%");
            })
            ->when($this->current_role === 'محفظ', function ($q, $v) {
                $q->where('students.group_sunnah_id', '=', $this->group_id);
            })
            ->when($this->selectedTeacherId !== null, function ($q, $v) {
                $q->where('students.group_sunnah_id', '=', $this->selectedTeacherId);
            })
            ->when($this->selectedStudentId !== null, function ($q, $v) {
                $q->where('students.id', '=', $this->selectedStudentId);
            })
            ->groupBy(['students.id', 'datetime', 'student_name', 'teacher_name', 'attendance_status', 'daily_memorization_type', 'mark', 'success_mark'
                , 'book_name', 'hadith_from', 'hadith_to', 'evaluation', 'attendance.id'])
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
        return (new DailyMemorizationSunnahExport($this->reports['data']))->download('Memorization and review Sunnah report.xlsx', Excel::XLSX);
    }
}
