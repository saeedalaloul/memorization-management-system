<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Livewire\Component;
use Livewire\WithPagination;

class TeachersAttendance extends Component
{
    use WithPagination;

    public $successMessage = '';

    public $catchError, $groups, $grades, $selectedTeachers = [];
    public $sortBy = 'id', $sortDirection = 'asc', $perPage = 10, $search = '';

    public $searchGradeId, $isSelectedRadioBtn1 = false,$isSelectedRadioBtn2 = false, $isSelectedRadioBtn0 = false;
    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $this->all_Grades();

        return view('livewire.teachers-attendance', ['teachers' => $this->all_Teachers(),]);
    }

    public function checkAllRadioBtn($type)
    {
        if ($type == 0) {
            $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn0;
            if ($this->isSelectedRadioBtn0 == true) {
                $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn0;
                $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn0;
                $this->selectedTeachers = [];
                for ($i = 0; $i < count($this->all_Teachers()); $i++) {
                    if ($this->all_Teachers()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedTeachers, [
                            'id' => $this->all_Teachers()[$i]->id,
                            'grade_id' => $this->all_Teachers()[$i]->grade_id,
                            'status' => false
                        ]);
                    } else {
                        if ($this->all_Teachers()[$i]->attendance()->latest()->first()->attendance_date != date('Y-m-d')) {
                            array_push($this->selectedTeachers, [
                                'id' => $this->all_Teachers()[$i]->id,
                                'grade_id' => $this->all_Teachers()[$i]->grade_id,
                                'status' => false
                            ]);
                        }
                    }
                }
            } else {
                foreach ($this->selectedTeachers as $key => $value) {
                    if ($this->selectedTeachers[$key]['status'] == false) {
                        unset($this->selectedTeachers[$key]);
                    }
                }
            }
        } else if ($type == 1) {
            $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn1;
            if ($this->isSelectedRadioBtn1 == true) {
                $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn1;
                $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn1;
                $this->selectedTeachers = [];
                for ($i = 0; $i < count($this->all_Teachers()); $i++) {
                    if ($this->all_Teachers()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedTeachers, [
                            'id' => $this->all_Teachers()[$i]->id,
                            'grade_id' => $this->all_Teachers()[$i]->grade_id,
                            'status' => true
                        ]);
                    } else {
                        if ($this->all_Teachers()[$i]->attendance()->latest()->first()->attendance_date != date('Y-m-d')) {
                            array_push($this->selectedTeachers, [
                                'id' => $this->all_Teachers()[$i]->id,
                                'grade_id' => $this->all_Teachers()[$i]->grade_id,
                                'status' => true
                            ]);
                        }
                    }
                }
            } else {
                foreach ($this->selectedTeachers as $key => $value) {
                    if ($this->selectedTeachers[$key]['status'] == true) {
                        unset($this->selectedTeachers[$key]);
                    }
                }
            }
        } else if ($type == 2) {
            $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn2;
            if ($this->isSelectedRadioBtn2 == true) {
                $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn2;
                $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn2;
                $this->selectedTeachers = [];
                for ($i = 0; $i < count($this->all_Teachers()); $i++) {
                    if ($this->all_Teachers()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedTeachers, [
                            'id' => $this->all_Teachers()[$i]->id,
                            'grade_id' => $this->all_Teachers()[$i]->grade_id,
                            'status' => 2
                        ]);
                    } else {
                        if ($this->all_Teachers()[$i]->attendance()->latest()->first()->attendance_date != date('Y-m-d')) {
                            array_push($this->selectedTeachers, [
                                'id' => $this->all_Teachers()[$i]->id,
                                'grade_id' => $this->all_Teachers()[$i]->grade_id,
                                'status' => 2
                            ]);
                        }
                    }
                }
            } else {
                foreach ($this->selectedTeachers as $key => $value) {
                    if ($this->selectedTeachers[$key]['status'] == 2) {
                        unset($this->selectedTeachers[$key]);
                    }
                }
            }
        }
    }

    public function store()
    {
        for ($i = 0; $i < count($this->selectedTeachers); $i++) {
            TeacherAttendance::updateOrCreate([
                'teacher_id' => $this->selectedTeachers[$i]['id'],
                'grade_id' => $this->selectedTeachers[$i]['grade_id'],
                'attendance_date' => date('Y-m-d'),
                'attendance_status' => $this->selectedTeachers[$i]['status']
            ]);
            session()->flash('success_message', 'تمت عملية اعتماد حضور وغياب المحفظين بنجاح.');
        }
        $this->isSelectedRadioBtn0 = false;
        $this->isSelectedRadioBtn1 = false;
        $this->isSelectedRadioBtn2 = false;
        $this->selectedTeachers = [];
    }


    public
    function teacherStatusChange($id, $status)
    {
        $teacher = $this->all_Teachers()->firstWhere('id', $id);
        if (count($this->selectedTeachers) > 0) {
            $key_ = -1;
            foreach ($this->selectedTeachers as $key => $value) {
                if ($value['id'] == $id) {
                    $key_ = $key;
                    break;
                }
            }
            if ($key_ == -1) {
                array_push($this->selectedTeachers, [
                    'id' => $id,
                    'grade_id' => $teacher->grade_id,
                    'status' => $status,
                ]);
            } else {
                $this->selectedTeachers[$key_]['status'] = $status;
            }
        } else {
            array_push($this->selectedTeachers, [
                'id' => $id,
                'grade_id' => $teacher->grade_id,
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
    function all_Teachers()
    {
        if (auth()->user()->current_role == 'مشرف') {
            return Teacher::query()
                ->search($this->search)
                ->where('grade_id', '=', $this->searchGradeId)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else if (auth()->user()->current_role == 'اداري') {
            return Teacher::query()
                ->search($this->search)
                ->where('grade_id', '=', $this->searchGradeId)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            if (empty($this->searchGradeId)) {
                return Teacher::query()
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Teacher::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
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
