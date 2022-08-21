<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;

class TeachersAttendance extends HomeComponent
{
    public $groups, $grades, $selectedTeachers = [];
    public $selectedGradeId, $isSelectedRadioBtn1 = false, $isSelectedRadioBtn2 = false, $isSelectedRadioBtn3 = false, $isSelectedRadioBtn0 = false;

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function render()
    {
        return view('livewire.teachers-attendance', ['teachers' => $this->all_Teachers(),]);
    }

    public function checkAllRadioBtn($type)
    {
        if ($type == TeacherAttendance::ABSENCE_STATUS) {
            $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn0;
            if ($this->isSelectedRadioBtn0 == true) {
                $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn0;
                $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn0;
                $this->isSelectedRadioBtn3 = !$this->isSelectedRadioBtn0;
                $this->selectedTeachers = [];
                for ($i = 0; $i < count($this->all_Teachers()); $i++) {
                    if ($this->all_Teachers()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedTeachers, [
                            'id' => $this->all_Teachers()[$i]->id,
                            'grade_id' => $this->all_Teachers()[$i]->grade_id,
                            'status' => TeacherAttendance::ABSENCE_STATUS,
                        ]);
                    } else {
                        if (Carbon::parse($this->all_Teachers()[$i]->attendance()->latest()->first()->datetime)->format('Y-m-d') != date('Y-m-d')) {
                            array_push($this->selectedTeachers, [
                                'id' => $this->all_Teachers()[$i]->id,
                                'grade_id' => $this->all_Teachers()[$i]->grade_id,
                                'status' => TeacherAttendance::ABSENCE_STATUS,
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
        } else if ($type == TeacherAttendance::PRESENCE_STATUS) {
            $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn1;
            if ($this->isSelectedRadioBtn1 == true) {
                $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn1;
                $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn1;
                $this->isSelectedRadioBtn3 = !$this->isSelectedRadioBtn1;
                $this->selectedTeachers = [];
                for ($i = 0; $i < count($this->all_Teachers()); $i++) {
                    if ($this->all_Teachers()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedTeachers, [
                            'id' => $this->all_Teachers()[$i]->id,
                            'grade_id' => $this->all_Teachers()[$i]->grade_id,
                            'status' => TeacherAttendance::PRESENCE_STATUS
                        ]);
                    } else {
                        if (Carbon::parse($this->all_Teachers()[$i]->attendance()->latest()->first()->datetime)->format('Y-m-d') != date('Y-m-d')) {
                            array_push($this->selectedTeachers, [
                                'id' => $this->all_Teachers()[$i]->id,
                                'grade_id' => $this->all_Teachers()[$i]->grade_id,
                                'status' => TeacherAttendance::PRESENCE_STATUS
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
        } else if ($type == TeacherAttendance::LATE_STATUS) {
            $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn2;
            if ($this->isSelectedRadioBtn2 == true) {
                $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn2;
                $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn2;
                $this->isSelectedRadioBtn3 = !$this->isSelectedRadioBtn2;
                $this->selectedTeachers = [];
                for ($i = 0; $i < count($this->all_Teachers()); $i++) {
                    if ($this->all_Teachers()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedTeachers, [
                            'id' => $this->all_Teachers()[$i]->id,
                            'grade_id' => $this->all_Teachers()[$i]->grade_id,
                            'status' => TeacherAttendance::LATE_STATUS
                        ]);
                    } else {
                        if (Carbon::parse($this->all_Teachers()[$i]->attendance()->latest()->first()->datetime)->format('Y-m-d') != date('Y-m-d')) {
                            array_push($this->selectedTeachers, [
                                'id' => $this->all_Teachers()[$i]->id,
                                'grade_id' => $this->all_Teachers()[$i]->grade_id,
                                'status' => TeacherAttendance::LATE_STATUS
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
        } else if ($type == TeacherAttendance::AUTHORIZED_STATUS) {
            $this->isSelectedRadioBtn3 = !$this->isSelectedRadioBtn3;
            if ($this->isSelectedRadioBtn3 == true) {
                $this->isSelectedRadioBtn0 = !$this->isSelectedRadioBtn3;
                $this->isSelectedRadioBtn1 = !$this->isSelectedRadioBtn3;
                $this->isSelectedRadioBtn2 = !$this->isSelectedRadioBtn3;
                $this->selectedTeachers = [];
                for ($i = 0; $i < count($this->all_Teachers()); $i++) {
                    if ($this->all_Teachers()[$i]->attendance()->count() == 0) {
                        array_push($this->selectedTeachers, [
                            'id' => $this->all_Teachers()[$i]->id,
                            'grade_id' => $this->all_Teachers()[$i]->grade_id,
                            'status' => TeacherAttendance::AUTHORIZED_STATUS
                        ]);
                    } else {
                        if (Carbon::parse($this->all_Teachers()[$i]->attendance()->latest()->first()->datetime)->format('Y-m-d') != date('Y-m-d')) {
                            array_push($this->selectedTeachers, [
                                'id' => $this->all_Teachers()[$i]->id,
                                'grade_id' => $this->all_Teachers()[$i]->grade_id,
                                'status' => TeacherAttendance::AUTHORIZED_STATUS
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
                'datetime' => now(),
                'status' => $this->selectedTeachers[$i]['status']
            ]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد حضور وغياب المحفظين بنجاح.']);
        }
        $this->isSelectedRadioBtn0 = false;
        $this->isSelectedRadioBtn1 = false;
        $this->isSelectedRadioBtn2 = false;
        $this->isSelectedRadioBtn3 = false;
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
        if ($this->current_role == 'مشرف') {
            $this->selectedGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::where('id', $this->selectedGradeId)->get();
        } else {
            $this->grades = Grade::all();
        }
    }

    public function all_Teachers()
    {
        return Teacher::query()
            ->with(['user', 'grade', 'attendance_today'])
            ->search($this->search)
            ->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->where('grade_id', Supervisor::where('id', auth()->id())->first()->grade_id);
            })
            ->when($this->current_role == 'أمير المركز' && !empty($this->selectedGradeId), function ($q, $v) {
                $q->where('grade_id', '=', $this->selectedGradeId);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Teachers();
    }

}
