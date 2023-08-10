<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Student;

class ManageStudentSunnah extends HomeComponent
{
    public $student_name, $group_id, $ret_group_id;
    public $groups;
    public bool $isUpdate = false;
    public $listeners = [
        'add_student_sunnah' => 'lunchModalAddStudentSunnah',
        'update_student_sunnah' => 'lunchModalUpdateStudentSunnah',
    ];

    public function mount()
    {
        $this->all_groups();
    }

    public function render()
    {
        return view('livewire.manage-student-sunnah');
    }

    public function lunchModalAddStudentSunnah($student_id)
    {
        $this->modalId = intval($student_id);
        $student = Student::where('id', $this->modalId)->first();
        if ($student->group_sunnah_id !== null) {
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'عذرا الطالب مضاف مسبقا إلى حلقة سنة.']);
        } else {
            $this->student_name = $student->user->name;
            $this->dispatchBrowserEvent('showModalAddStudentSunnah');
        }
    }

    public function lunchModalUpdateStudentSunnah($student_id)
    {
        $this->modalId = intval($student_id);
        $student = Student::where('id', $this->modalId)->first();
        if ($student->group_sunnah_id === null) {
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'عذرا الطالب غير مضاف إلى أي حلقة سنة.']);
        } else {
            $this->isUpdate = true;
            $this->student_name = $student->user->name;
            $this->group_id = $student->group_sunnah_id;
            $this->ret_group_id = $student->group_sunnah_id;
            $this->dispatchBrowserEvent('showModalAddStudentSunnah');
        }
    }

    public function store()
    {
        $this->validate(
            ['group_id' => 'required|string'],
            ['group_id.required' => 'حقل اختيار الحلقة مطلوب',
                'group_id.string' => 'حقل اختيار الحلقة يجب أن يكون نص']
        );
        Student::whereId($this->modalId)->update(['group_sunnah_id' => $this->group_id]);
        $this->dispatchBrowserEvent('hideDialog');
        $this->clearForm();
        $this->emit('refresh');

        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'لقد تم إضافة الطالب إلى حلقة السنة بنجاح.']);
    }

    public function update()
    {
        $this->validate(
            ['group_id' => 'required|string'],
            ['group_id.required' => 'حقل اختيار الحلقة مطلوب',
                'group_id.string' => 'حقل اختيار الحلقة يجب أن يكون نص']
        );
        if ($this->ret_group_id !== $this->group_id) {
            Student::whereId($this->modalId)->update(['group_sunnah_id' => $this->group_id]);
            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'لقد تم تحديث حلقة السنة للطالب بنجاح.']);
        } else {
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'عذرا الطالب مضاف في الحلقة مسبقا.']);
        }
    }

    public function pull_student()
    {
        if ($this->group_id !== null && $this->isUpdate) {
            Student::whereId($this->modalId)->update(['group_sunnah_id' => null]);
            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();
            $this->emit('refresh');

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'لقد تم سحب الطالب من حلقة السنة بنجاح.']);
        }
    }

    public function all_groups()
    {
        $this->groups = Group::query()->with(['teacher.user'])->where('type', '=', Group::SUNNAH_TYPE)->get();
    }

    private function clearForm()
    {
        $this->modalId = '';
        $this->student_name = null;
        $this->group_id = null;
        $this->isUpdate = false;
    }
}
