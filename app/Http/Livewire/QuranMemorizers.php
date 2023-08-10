<?php

namespace App\Http\Livewire;

use App\Exports\QuranMemorizersExport;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Group;
use App\Models\QuranPart;
use App\Models\Supervisor;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class QuranMemorizers extends HomeComponent
{
    public $grades = [], $groups = [];
    public $selectedGradeId, $selectedTeacherId, $searchDateFrom, $searchDateTo;

    public function render()
    {
        return view('livewire.quran-memorizers', [
            'quran_memorizers' => $this->all_Quran_Memorizers(),]);
    }

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
        $this->sortBy = 'datetime';
    }

    public function all_Quran_Memorizers()
    {
        return Exam::query()
            ->with(['student.user:id,name,identification_number,dob','exam_success_mark:id,mark', 'external_exam', 'exam_improvement', 'teacher.user:id,name'])
            ->search($this->search)
            ->whereHas('external_exam')
            ->whereHas('exam_success_mark', function ($q) {
                $q->where(DB::raw('exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
            })
            ->whereHas('QuranPart', function ($q) {
                $q->where('quran_part_id', '=',QuranPart::QURAN_MEMORIZER_PART);
            })
            ->when($this->searchDateFrom !== null && $this->searchDateTo !== null, function ($q, $v) {
                $q->whereBetween(DB::raw('DATE(datetime)'), [$this->searchDateFrom, $this->searchDateTo]);
            })
            ->when($this->current_role === 'محفظ', function ($q, $v) {
                $q->where('teacher_id', '=', auth()->id());
            })
            ->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('grade_id', '=', Supervisor::find(auth()->id())->grade_id)
                        ->when($this->selectedTeacherId !== null, function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        });
                });
            })
            ->when($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->when(!empty($this->selectedGradeId), function ($q, $v) {
                        $q->where('grade_id', '=', $this->selectedGradeId);
                    })
                        ->when(!empty($this->selectedTeacherId), function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        });
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Quran_Memorizers();
    }

    public function all_Grades()
    {
        if ($this->current_role === 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::QURAN_TYPE)
                    ->where('grade_id', $this->selectedGradeId)
                    ->get();
            }
        } else if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::QURAN_TYPE)
                    ->where('grade_id', $this->selectedGradeId)
                    ->get();
            }
        } else if (($this->current_role === 'محفظ') && $this->selectedGradeId) {
            $this->groups = Group::query()
                ->where('type', Group::QURAN_TYPE)
                ->where('teacher_id', auth()->id())
                ->get();
        }

    }

    public function export()
    {
        return (new QuranMemorizersExport($this->all_Quran_Memorizers()))->download('Quran Memorizers Report.xlsx', Excel::XLSX);
    }
}
