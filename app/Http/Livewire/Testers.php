<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Tester;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Testers extends Component
{
    use WithPagination;

    public $grade_id;
    public $teacher_id;
    public $modalId;
    public $grades;
    public $teachers;
    public $catchError;
    public $sortBy = 'id';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->grades = $this->all_Grades();
        if (!empty($this->grade_id)) {
            $this->teachers = Teacher::where("grade_id", $this->grade_id)->get();
        }

        return view('livewire.testers', ['testers' => $this->all_Testers()]);
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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'grade_id' => 'required',
            'teacher_id' => 'required|unique:testers,id,' . $this->modalId,
        ]);
    }

    public function loadModalData($id)
    {
        $this->modalFormReset();
        $data = Tester::where('id', $id)->first();
        $this->modalId = $data->id;
    }

    public function modelData()
    {
        $data = [
            'id' => $this->teacher_id,
        ];
        return $data;
    }

    public function rules()
    {
        return [
            'grade_id' => 'required',
            'teacher_id' => 'required|unique:testers,id,' . $this->modalId,];
    }

    public function messages()
    {
        return [
            'grade_id.required' => 'حقل المرحلة مطلوب',
            'teacher_id.required' => 'حقل المحفظ مطلوب',
            'teacher_id.unique' => 'المختبر موجود مسبقا',
        ];
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->grade_id = null;
        $this->teacher_id = null;
        $this->modalId = null;
    }

    public function store()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $teacher = Teacher::where('id', $this->teacher_id)->where('grade_id', $this->grade_id)->first();
            if (is_null($teacher)) {
                $this->teacher_id = null;
            } else {
                Tester::create($this->modelData());
                $roleId = Role::select('*')->where('name', '=', 'مختبر')->get();
                $user = User::where('id', $this->teacher_id)->first();
                $user->assignRole([$roleId]);
                $this->modalFormReset();
                $this->emit('add_tester');
                session()->flash('message', 'تم اضافة المختبر بنجاح.');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function update()
    {
        $this->validate();
        $teacher = Teacher::where('id', $this->teacher_id)->where('grade_id', $this->grade_id)->first();
        if (is_null($teacher)) {
            $this->teacher_id = null;
        } else {
            $tester = Tester::where('id', $this->modalId)->first();
            $tester->update($this->modelData());
            $this->modalFormReset();
            //$this->emit('groupEdited');
            session()->flash('message', 'تم تحديث المختبر بنجاح.');
        }
    }

    public function destroy($id)
    {
        Tester::where('id', $id)->delete();
        $this->emit('delete_tester');
        session()->flash('message', 'تم حذف المختبر بنجاح.');
    }

    public function all_Testers()
    {
        return Tester::query()
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function all_Grades()
    {
        return Grade::all();
    }
}
