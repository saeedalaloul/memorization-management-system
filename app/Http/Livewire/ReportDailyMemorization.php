<?php

namespace App\Http\Livewire;

use App\Exports\DailyMemorizationExport;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentDailyMemorization;
use App\Models\Supervisor;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class ReportDailyMemorization extends HomeComponent
{

    public $groups = [], $grades = [], $students = [];

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
        $this->searchReportType = StudentDailyMemorization::MEMORIZE_TYPE;
        $this->all_Grades();
    }

    public function all_Grades()
    {
        if ($this->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->grade_id)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->group_id = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId');

        if ($this->current_role == 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role == 'أمير المركز') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role == 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }
    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role == 'أمير المركز') {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role == 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
        }
    }

    public function getReportsByStudentId()
    {
        return StudentDailyMemorization::query()
            ->with(['student.user', 'quranSuraFrom', 'quranSuraTo'])
            ->search($this->search)
            ->when($this->searchReportType != null && !empty($this->searchReportType), function ($q, $v) {
                $q->where('type', $this->searchReportType);
            })
            ->when($this->searchDateFrom != null && !empty($this->searchDateFrom) && $this->searchDateTo != null && !empty($this->searchDateTo), function ($q, $v) {
                $q->whereBetween(DB::raw('DATE(datetime)'), [$this->searchDateFrom, $this->searchDateTo]);
            })
            ->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('grade_id', '=', $this->grade_id);
                });
            })
            ->when($this->selectedGradeId != null && !empty($this->selectedGradeId), function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('grade_id', '=', $this->selectedGradeId);
                });
            })
            ->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('group_id', '=', $this->group_id);
                });
            })
            ->when($this->selectedTeacherId != null && !empty($this->selectedTeacherId), function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('group_id', '=', $this->selectedTeacherId);
                });
            })
            ->when($this->selectedStudentId != null && !empty($this->selectedStudentId), function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('id', '=', $this->selectedStudentId);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->getReportsByStudentId();
    }

    public function export()
    {
        return (new DailyMemorizationExport($this->getReportsByStudentId()))->download('Memorization and review report.xlsx', Excel::XLSX);
    }
}
