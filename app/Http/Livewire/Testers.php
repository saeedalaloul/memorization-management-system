<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
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
    public $teacher_id, $group_id;
    public $modalId;
    public $grades, $groups, $students;
    public $teachers;
    public $catchError, $show_table = true, $show_exams = false, $tester_id;
    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    public $searchGradeId, $searchGroupId, $searchStudentId;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->all_Groups();
        $this->all_Students();
        if (!empty($this->grade_id)) {
            $this->teachers = Teacher::where("grade_id", $this->grade_id)->get();
        }
        return view('livewire.testers', ['testers' => $this->all_Testers(), 'exams' => $this->getExamsByTester()]);
    }

    public function mount()
    {
        $this->grades = $this->all_Grades();
    }

    public function updatedSearch()
    {
        $this->resetPage();
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

    public function show_exams_table($id)
    {
        $this->show_exams = true;
        $this->show_table = false;
        $this->tester_id = $id;
    }

    public function getExamsByTester()
    {
        if ($this->show_exams == true) {
            if (auth()->user()->current_role == 'أمير المركز' ||
                auth()->user()->current_role == 'مشرف الإختبارات') {
                if (empty($this->searchGradeId)) {
                    return Exam::query()
                        ->where('tester_id', $this->tester_id)
                        ->search($this->search)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    if (empty($this->searchGroupId)) {
                        return Exam::query()
                            ->where('tester_id', $this->tester_id)
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q->where('grade_id', '=', $this->searchGradeId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        if (empty($this->searchStudentId)) {
                            return Exam::query()
                                ->where('tester_id', $this->tester_id)
                                ->search($this->search)
                                ->whereHas('student', function ($q) {
                                    return $q->where('grade_id', '=', $this->searchGradeId)
                                        ->where('group_id', '=', $this->searchGroupId);
                                })
                                ->orderBy($this->sortBy, $this->sortDirection)
                                ->paginate($this->perPage);
                        } else {
                            return Exam::query()
                                ->where('tester_id', $this->tester_id)
                                ->search($this->search)
                                ->whereHas('student', function ($q) {
                                    return $q
                                        ->where('grade_id', '=', $this->searchGradeId)
                                        ->where('group_id', '=', $this->searchGroupId)
                                        ->where('id', '=', $this->searchStudentId);
                                })
                                ->orderBy($this->sortBy, $this->sortDirection)
                                ->paginate($this->perPage);
                        }
                    }
                }
            }
        }
        return [];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'grade_id' => 'required',
            'teacher_id' => 'required|unique:testers,id,' . $this->modalId,
        ]);
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
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم إضافة المختبر بنجاح.']);
            }
            $this->show_table = true;
            $this->modalFormReset();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function destroy($id)
    {
        $tester = Tester::find($id);
        if ($tester != null) {
            $this->emit('delete_tester');
            if ($tester->exams->count() > 0) {
                $this->catchError = "عذرا لا يمكن حذف المختبر بسبب وجود اختبارات مسجلة باسم المختبر";
            } else {
                if ($tester->exams_orders->count() > 0) {
                    $this->catchError = "عذرا لا يمكن حذف المختبر بسبب وجود طلبات اختبارات لديه يرجى إجرائها أو حذفها";
                } else {
                    $roleId = Role::select('*')->where('name', '=', 'مختبر')->first();
                    $tester->user->removeRole($roleId);
                    $tester->delete();
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'تم حذف المختبر بنجاح.']);
                }
            }
        }
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

    public function all_Groups()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            if ($this->grade_id) {
                $this->groups = Group::query()->where('grade_id', $this->grade_id)->get();
            } else if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        }
    }

    public function all_Students()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            if ($this->group_id) {
                $this->students = Student::query()->where('group_id', $this->group_id)->get();
            } else if ($this->searchGroupId) {
                $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
            }
        }
    }

}
