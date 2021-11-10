<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Supervisor;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsAttendance extends Component
{
    use WithPagination;

    public $successMessage = '';

    public $catchError, $groups, $grades, $selectedStudents = [];
    public $sortBy = 'id', $sortDirection = 'desc', $perPage = 10, $search = '';

    public $searchGradeId, $searchGroupId, $isSelectedRadioBtn1 = false,$isSelectedRadioBtn2 = false, $isSelectedRadioBtn0 = false;
    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $this->all_Grades();
        $this->all_Groups();

        return view('livewire.students-attendance', ['students' => $this->all_Students(),]);
    }

    public function checkAllRadioBtn($type)
    {
        if ($type == 0) {
            $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn0;
            if ($this->isSelectedRadioBtn0 == true) {
                $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn0;
                $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn0;
                $this->selectedStudents = [];
                for ($i = 0; $i < count($this->all_Students()); $i++) {
                    if ($this->all_Students()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedStudents, [
                            'id' => $this->all_Students()[$i]->id,
                            'grade_id' => $this->all_Students()[$i]->grade_id,
                            'group_id' => $this->all_Students()[$i]->group_id,
                            'teacher_id' => $this->all_Students()[$i]->group->teacher_id,
                            'status' => false
                        ]);
                    } else {
                        if ($this->all_Students()[$i]->attendance()->latest()->first()->attendance_date != date('Y-m-d')) {
                            array_push($this->selectedStudents, [
                                'id' => $this->all_Students()[$i]->id,
                                'grade_id' => $this->all_Students()[$i]->grade_id,
                                'group_id' => $this->all_Students()[$i]->group_id,
                                'teacher_id' => $this->all_Students()[$i]->group->teacher_id,
                                'status' => false
                            ]);
                        }
                    }
                }
            } else {
                foreach ($this->selectedStudents as $key => $value) {
                    if ($this->selectedStudents[$key]['status'] == false) {
                        unset($this->selectedStudents[$key]);
                    }
                }
            }
        } else if ($type == 1) {
            $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn1;
            if ($this->isSelectedRadioBtn1 == true) {
                $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn1;
                $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn1;
                $this->selectedStudents = [];
                for ($i = 0; $i < count($this->all_Students()); $i++) {
                    if ($this->all_Students()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedStudents, [
                            'id' => $this->all_Students()[$i]->id,
                            'grade_id' => $this->all_Students()[$i]->grade_id,
                            'group_id' => $this->all_Students()[$i]->group_id,
                            'teacher_id' => $this->all_Students()[$i]->group->teacher_id,
                            'status' => true
                        ]);
                    } else {
                        if ($this->all_Students()[$i]->attendance()->latest()->first()->attendance_date != date('Y-m-d')) {
                            array_push($this->selectedStudents, [
                                'id' => $this->all_Students()[$i]->id,
                                'grade_id' => $this->all_Students()[$i]->grade_id,
                                'group_id' => $this->all_Students()[$i]->group_id,
                                'teacher_id' => $this->all_Students()[$i]->group->teacher_id,
                                'status' => true
                            ]);
                        }
                    }
                }
            } else {
                foreach ($this->selectedStudents as $key => $value) {
                    if ($this->selectedStudents[$key]['status'] == true) {
                        unset($this->selectedStudents[$key]);
                    }
                }
            }
        } else if ($type == 2) {
            $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn2;
            if ($this->isSelectedRadioBtn2 == true) {
                $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn2;
                $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn2;
                $this->selectedStudents = [];
                for ($i = 0; $i < count($this->all_Students()); $i++) {
                    if ($this->all_Students()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedStudents, [
                            'id' => $this->all_Students()[$i]->id,
                            'grade_id' => $this->all_Students()[$i]->grade_id,
                            'group_id' => $this->all_Students()[$i]->group_id,
                            'teacher_id' => $this->all_Students()[$i]->group->teacher_id,
                            'status' => 2
                        ]);
                    } else {
                        if ($this->all_Students()[$i]->attendance()->latest()->first()->attendance_date != date('Y-m-d')) {
                            array_push($this->selectedStudents, [
                                'id' => $this->all_Students()[$i]->id,
                                'grade_id' => $this->all_Students()[$i]->grade_id,
                                'group_id' => $this->all_Students()[$i]->group_id,
                                'teacher_id' => $this->all_Students()[$i]->group->teacher_id,
                                'status' => 2
                            ]);
                        }
                    }
                }
            } else {
                foreach ($this->selectedStudents as $key => $value) {
                    if ($this->selectedStudents[$key]['status'] == 2) {
                        unset($this->selectedStudents[$key]);
                    }
                }
            }
        }
    }

    public function store()
    {
        for ($i = 0; $i < count($this->selectedStudents); $i++) {
            StudentAttendance::updateOrCreate([
                'student_id' => $this->selectedStudents[$i]['id'],
                'grade_id' => $this->selectedStudents[$i]['grade_id'],
                'group_id' => $this->selectedStudents[$i]['group_id'],
                'teacher_id' => $this->selectedStudents[$i]['teacher_id'],
                'attendance_date' => date('Y-m-d'),
                'attendance_status' => $this->selectedStudents[$i]['status']
            ]);
            session()->flash('success_message', 'تمت عملية اعتماد حضور وغياب الطلاب بنجاح.');
        }
        $this->isSelectedRadioBtn0 = false;
        $this->isSelectedRadioBtn1 = false;
        $this->isSelectedRadioBtn2 = false;
        $this->selectedStudents = [];
    }


    public
    function studentStatusChange($id, $status)
    {
        $student = $this->all_Students()->firstWhere('id', $id);
        if (count($this->selectedStudents) > 0) {
            $key_ = -1;
            foreach ($this->selectedStudents as $key => $value) {
                if ($value['id'] == $id) {
                    $key_ = $key;
                    break;
                }
            }
            if ($key_ == -1) {
                array_push($this->selectedStudents, [
                    'id' => $id,
                    'grade_id' => $student->grade_id,
                    'group_id' => $student->group_id,
                    'teacher_id' => $student->group->teacher_id,
                    'status' => $status,
                ]);
            } else {
                $this->selectedStudents[$key_]['status'] = $status;
            }
        } else {
            array_push($this->selectedStudents, [
                'id' => $id,
                'grade_id' => $student->grade_id,
                'group_id' => $student->group_id,
                'teacher_id' => $student->group->teacher_id,
                'status' => $status,
            ]);
        }
    }

    public
    function all_Grades()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->searchGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
        } else if (auth()->user()->current_role == 'اداري') {
            $this->searchGradeId = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
        } else {
            $this->grades = Grade::all();
        }
    }

    public
    function all_Groups()
    {
        if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري') {
            if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        } else if (auth()->user()->current_role == 'أمير المركز') {
            if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        }
    }

    public
    function all_Students()
    {
        if (auth()->user()->current_role == 'مشرف') {
            if (!empty($this->searchGroupId)) {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->where('group_id', '=', $this->searchGroupId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else if (auth()->user()->current_role == 'اداري') {
            if (!empty($this->searchGroupId)) {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->where('group_id', '=', $this->searchGroupId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else if (auth()->user()->current_role == 'محفظ') {
            return Student::query()
                ->search($this->search)
                ->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            if (empty($this->searchGradeId)) {
                return Student::query()
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                if (empty($this->searchGroupId)) {
                    return Student::query()
                        ->search($this->search)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return Student::query()
                        ->search($this->search)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->where('group_id', '=', $this->searchGroupId)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                }
            }
        }
    }

    public
    function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }

}
