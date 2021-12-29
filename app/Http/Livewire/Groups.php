<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Livewire\Component;
use Livewire\WithPagination;

class Groups extends Component
{
    use WithPagination;

    public $name;
    public $grade_id;
    public $new_grade_id;
    public $teacher_id;
    public $modalId;
    public $retGroup;
    public $grades;
    public $teachers;
    public $sortBy = 'name';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    public $show_table = true;
    public $is_moving = false;
    public $searchGradeId, $catchError;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->grades = $this->all_Grades();
        if (!empty($this->grade_id)) {
            $this->teachers = Teacher::where("grade_id", $this->grade_id)->get();
        }

        return view('livewire.groups', ['groups' => $this->all_Groups()]);
    }

    public function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }

    public function showformadd($isShow)
    {
        $this->show_table = $isShow;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'name' => 'required|unique:groups,name,' . $this->modalId,
            'grade_id' => 'required',
            'teacher_id' => 'required|unique:groups,teacher_id,' . $this->modalId,
        ]);
    }

    public function loadModalData($id, $isMoving)
    {
        $this->modalFormReset();
        $data = Group::where('id', $id)->get()->first();
        $this->retGroup = $data;
        $this->modalId = $data->id;
        $this->name = $data->name;
        $this->grade_id = $data->grade_id;
        $this->teacher_id = $data->teacher_id;
        $this->show_table = false;
        $this->is_moving = $isMoving;
    }

    public function modelData()
    {
        $data = [
            'name' => $this->name,
            'grade_id' => $this->grade_id,
            'teacher_id' => $this->teacher_id,
        ];
        return $data;
    }

    public function rules()
    {
        return ['name' => 'required|unique:groups,name,' . $this->modalId,
            'grade_id' => 'required',
            'teacher_id' => 'required|unique:groups,teacher_id,' . $this->modalId,];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم الحلقة مطلوب',
            'name.unique' => 'اسم الجلقة موجود مسبقا',
            'grade_id.required' => 'حقل اسم المرحلة مطلوب',
            'teacher_id.required' => 'حقل اسم المحفظ مطلوب',
            'teacher_id.unique' => 'يجب أن لا يكون للمحفظ أكثر من حلقة',
        ];
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->retGroup = null;
        $this->name = null;
        $this->new_grade_id = null;
        $this->grade_id = null;
        $this->teacher_id = null;
        $this->modalId = null;
    }

    public function store()
    {
        $this->validate();
        $teacher = Teacher::where('id', $this->teacher_id)->where('grade_id', $this->grade_id)->get()->first();
        if (is_null($teacher)) {
            $this->teacher_id = null;
        } else {
            Group::create($this->modelData());

            $this->modalFormReset();
            $this->show_table = true;
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم حفظ معلومات الحلقة بنجاح.']);
        }
    }

    public function update()
    {
        $this->validate();
        $teacher = Teacher::where('id', $this->teacher_id)->where('grade_id', $this->grade_id)->get()->first();
        if (is_null($teacher)) {
            $this->teacher_id = null;
        } else {
            $isUpdated = true;
            $student_found = Student::find($this->teacher_id);
            $messageBag = new MessageBag;
            if ($student_found != null && $student_found->group != null
                && $student_found->group->id == $this->retGroup->id) {
                $messageBag->add('teacher_id', 'عذرا, لا يمكن اختيار الحلقة للمحفظ لأنه طالب في نفس الحلقة');
                $this->setErrorBag($messageBag);
            } else {
                if ($this->retGroup->grade_id != $this->grade_id) {
                    if ($this->retGroup->students->count() > 0) {
                        $isUpdated = false;
                    }
                }
                if ($isUpdated) {
                    $Group = Group::where('id', $this->modalId)->first();
                    $Group->update($this->modelData());
                    $this->modalFormReset();
                    $this->show_table = true;
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تم تحديث معلومات الحلقة بنجاح.']);
                } else {
                    $messageBag = new MessageBag;
                    $messageBag->add('grade_id', 'عذرا لم يتم تحديث الحلقة بسبب وجود طلاب داخل الحلقة');
                    $this->setErrorBag($messageBag);
                }
            }
        }
    }

    public function move()
    {
        if ($this->new_grade_id != null) {
            DB::beginTransaction();
            try {
                $this->retGroup->teacher->update(['grade_id' => $this->new_grade_id]);
                foreach ($this->retGroup->students as $key => $value) {
                    $this->retGroup->students[$key]->update(['grade_id' => $this->new_grade_id]);
                }
                $this->retGroup->update(['grade_id' => $this->new_grade_id]);
                $this->emit('groupMoveClose');
                $this->modalFormReset();
                $this->show_table = true;
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم نقل الحلقة إلى مرحلة جديدة بنجاح.']);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $this->catchError = $e->getMessage();
            }
        }
    }

    public function pullATeacherOutOfTheGroup($id, $teacher_id)
    {
        if ($id != null && $teacher_id != null) {
            $this->emit('groupPullTeacher');
            $teacher = Teacher::find($teacher_id);
            if ($teacher->exam_order->count() > 0) {
                $this->catchError = "عذرا , يوجد طلبات اختبارات لهذه الحلقة يجب إجرائها أو حذفها حتى تتمكن من سحب المحفظ";
            } else {
                $group = Group::find($id);
                if ($group->teacher_id != null) {
                    $group->update(['teacher_id' => null]);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'تم سحب المحفظ من الحلقة بنجاح.']);
                } else {
                    $this->catchError = "عذرا لا يوجد محفظ في المجموعة";
                }
            }
        }
    }

    public function validateMoveGroup()
    {
        $messageBag = new MessageBag;
        if ($this->new_grade_id != null) {
            if ($this->new_grade_id == $this->grade_id) {
                $messageBag->add('new_grade_id', 'يجب عدم إختيار نفس المرحلة الحالية');
                $this->setErrorBag($messageBag);
            } else {
                $this->emit('groupMove');
            }
        } else {
            $messageBag->add('new_grade_id', 'يجب إختيار المرحلة الجديدة');
            $this->setErrorBag($messageBag);
        }
    }

    public function resetMessage()
    {
        $this->catchError = null;
    }

    public function destroy($id)
    {
        Group::where('id', $id)->delete();
        $this->emit('groupDeleted');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حذف الحلقة بنجاح.']);
    }

    public function all_Groups()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            return Group::query()
                ->search($this->search)
                ->where('grade_id', '=', $this->grade_id)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else if (auth()->user()->current_role == 'اداري') {
            $this->grade_id = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
            return Group::query()
                ->search($this->search)
                ->where('grade_id', '=', $this->grade_id)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else if (auth()->user()->current_role == 'أمير المركز') {
            if (empty($this->searchGradeId)) {
                return Group::query()
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Group::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        }
        return [];
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            return Grade::where('id', $this->grade_id)->get();
        } else if (auth()->user()->current_role == 'اداري') {
            $this->grade_id = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
            return Grade::where('id', $this->grade_id)->get();
        } else if (auth()->user()->current_role == 'أمير المركز') {
            return Grade::all();
        }
        return [];
    }
}
