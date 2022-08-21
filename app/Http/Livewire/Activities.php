<?php

namespace App\Http\Livewire;

use App\Models\Activity;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Supervisor;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;

class Activities extends HomeComponent
{
    public $grades = [], $groups = [];
    public $selectedGradeId, $selectedTeacherId, $searchDateFrom, $searchDateTo;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function all_Grades()
    {
        if ($this->current_role == 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } elseif ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الأنشطة' || $this->current_role == 'منشط') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الأنشطة' || $this->current_role == 'منشط') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role == 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role == 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }

    }

    public function render()
    {
        return view('livewire.activities', ['activities' => $this->all_Activity()]);
    }


    public function all_Activity()
    {
        return Activity::query()
            ->with(['students.user', 'activity_member.user', 'activity_type', 'teacher.user'])
            ->withCount(['students'])
            ->when(!empty($this->searchDateFrom) && !empty($this->searchDateTo), function ($q, $v) {
                $q->whereBetween(DB::raw('DATE(datetime)'), [$this->searchDateFrom, $this->searchDateTo]);
            })->search($this->search)
            ->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->where('teacher_id', auth()->id());
            })->when($this->current_role == 'منشط', function ($q, $v) {
                $q->where('activity_member_id', auth()->id())
                    ->whereHas('students', function ($q) {
                        $q->when(!empty($this->selectedGradeId), function ($q, $v) {
                            $q->where('grade_id', '=', $this->selectedGradeId);
                        })->when(!empty($this->selectedTeacherId), function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        });
                    });
            })->when($this->current_role == 'أمير المركز' || $this->current_role == 'مشرف الأنشطة', function ($q, $v) {
                $q->whereHas('students', function ($q) {
                    $q->when(!empty($this->selectedGradeId), function ($q, $v) {
                        $q->where('grade_id', '=', $this->selectedGradeId);
                    })->when(!empty($this->selectedTeacherId), function ($q, $v) {
                        $q->where('group_id', '=', $this->selectedTeacherId);
                    });
                });
            })->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->whereHas('students', function ($q) {
                    $q->where('grade_id', '=', Supervisor::find(auth()->id())->first()->grade_id)
                        ->when(!empty($this->selectedTeacherId), function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId);
                        });
                });
            })->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Activity();
    }
}
