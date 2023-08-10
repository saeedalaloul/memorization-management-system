<?php

namespace App\Http\Livewire;

use App\Models\Activity;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\TrackStudentTransfer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TrackStudentTransfers extends HomeComponent
{
    public $grades = [], $groups = [],$students = [];
    public $selectedGradeId, $selectedTeacherId,$selectedStudentId;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getStudentsByTeacherId' => 'getStudentsByTeacherId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function render()
    {
        return view('livewire.track-student-transfers', ['track_student_transfers' => $this->all_TrackStudentTransfers()]);
    }


    public function all_TrackStudentTransfers()
    {
        return DB::table('track_student_transfers','transfers')
            ->select(['std_users.name as student_name','old_grade.name as old_grade_name','new_grade.name as new_grade_name',
            'old_teacher.name as old_teacher_name','new_teacher.name as new_teacher_name','user_signature.name as user_signature_name',
                'user_signature_role.name as user_role_name','transfers.created_at as created_at'])
            ->join('users as std_users', function ($join) {
                $join->on('transfers.student_id', '=', 'std_users.id')
                    ->when($this->current_role === User::ADMIN_ROLE || $this->current_role === User::SUPERVISOR_ROLE, function ($q, $v) {
                        $q->when(!empty($this->selectedStudentId), function ($q, $v) {
                            $q->on('std_users.id', '=', DB::raw("(select id from `students` where `id` = '$this->selectedStudentId' LIMIT 1)"));
                        });
                    });
            })
            ->join('grades as old_grade','transfers.old_grade_id','=','old_grade.id')
            ->join('grades as new_grade','transfers.new_grade_id','=','new_grade.id')
            ->leftJoin('users as old_teacher','transfers.old_teacher_id','=','old_teacher.id')
            ->leftJoin('users as new_teacher','transfers.new_teacher_id','=','new_teacher.id')
            ->join('users as user_signature','transfers.user_signature_id','=','user_signature.id')
            ->join('roles as user_signature_role','transfers.user_signature_role_id','=','user_signature_role.id')
            ->when(!empty($this->search), function ($q, $v) {
                $q->where('std_users.name', 'LIKE', "%$this->search%")
                    ->orWhere('old_teacher.name', 'LIKE', "%$this->search%")
                    ->orWhere('new_teacher.name', 'LIKE', "%$this->search%")
                    ->orWhere('user_signature.name', 'LIKE', "%$this->search%");
            })
            ->when($this->current_role === User::TEACHER_ROLE, function ($q, $v) {
                $q->where('transfers.old_teacher_id', auth()->id())
                    ->orWhere('transfers.new_teacher_id', auth()->id());
            })
            ->when($this->current_role === User::SUPERVISOR_ROLE, function ($q, $v) {
                $q->where('transfers.user_signature_id', auth()->id());
            })
            ->when(!empty((string)\Request::segment(2) && strval(\Request::segment(2)) !== 'message'), function ($q, $v) {
                $q->where('transfers.id', \Request::segment(2));
            })
            ->orderBy('transfers.' . $this->sortBy, $this->sortDirection)
            ->orderByDesc('transfers.updated_at')
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_TrackStudentTransfers();
    }

    public function all_Grades()
    {
        if ($this->current_role === 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } elseif ($this->current_role === 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->current_role === 'أمير المركز') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role === 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }

    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role === 'أمير المركز') {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)->get();
        }
    }
}
