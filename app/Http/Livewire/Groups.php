<?php

namespace App\Http\Livewire;

use App\Exports\GradeStudentsExport;
use App\Exports\GradeTeachersExport;
use App\Exports\GroupStudentsExport;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;

class Groups extends HomeComponent
{
    public $name;
    public $grade_id;
    public $new_grade_id;
    public $teacher_id;
    public $retGroup;
    public $grades;
    public $teachers;
    public $selectedGradeId;
    public $is_moving = false;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getGroupsByGradeId' => 'all_Groups',
    ];

    public function render()
    {
        return view('livewire.groups', ['groups' => $this->all_Groups()]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->grades = $this->all_Grades();
        $this->sortBy = "name";
    }

    public function getTeachersByGradeId()
    {
        $this->teachers = Teacher::with('user')->where("grade_id", $this->grade_id)->get();
    }

    public function loadModalData($id, $isMoving)
    {
        $this->modalFormReset();
        $data = Group::where('id', $id)->first();
        $this->retGroup = $data;
        $this->modalId = $data->id;
        $this->name = $data->name;
        $this->grade_id = $data->grade_id;
        $this->teacher_id = $data->teacher_id;
        $this->is_moving = $isMoving;
        $this->getTeachersByGradeId();
    }

    public function modelData()
    {
        return [
            'name' => $this->name,
            'grade_id' => $this->grade_id,
            'teacher_id' => $this->teacher_id,
        ];
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
        $this->modalId = '';
        $this->is_moving = false;
    }

    public function store()
    {
        $this->validate();
        Group::create($this->modelData());

        $this->modalFormReset();
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حفظ معلومات الحلقة بنجاح.']);
    }

    public function update()
    {
        $this->validate();
        $student_found = Student::find($this->teacher_id);
        $messageBag = new MessageBag;
        if ($student_found != null && $student_found->group != null
            && $student_found->group->id == $this->retGroup->id) {
            $messageBag->add('teacher_id', 'عذرا, لا يمكن اختيار الحلقة للمحفظ لأنه طالب في نفس الحلقة');
            $this->setErrorBag($messageBag);
        } else {
            if ($this->retGroup->grade_id != $this->grade_id && $this->retGroup->students->count() > 0) {
                $messageBag->add('grade_id', 'عذرا لم يتم تحديث الحلقة بسبب وجود طلاب داخل الحلقة');
                $this->setErrorBag($messageBag);
            } else {
                if ($this->retGroup->teacher != null && $this->retGroup->teacher->grade_id != $this->grade_id) {
                    $messageBag->add('grade_id', 'عذرا لم يتم تحديث الحلقة بسبب عدم تطابق المرحلة للمحفظ والحلقة');
                    $this->setErrorBag($messageBag);
                    $this->grade_id = null;
                    $this->teacher_id = null;
                } else {
                    $Group = Group::where('id', $this->modalId)->first();
                    $Group->update($this->modelData());
                    $this->modalFormReset();
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تم تحديث معلومات الحلقة بنجاح.']);
                }
            }
        }
    }

    public function move()
    {
        if ($this->new_grade_id != null) {
            DB::beginTransaction();
            try {
                $this->retGroup->teacher?->update(['grade_id' => $this->new_grade_id]);
                $this->retGroup->students()->update(['grade_id' => $this->new_grade_id]);
                $this->retGroup->update(['grade_id' => $this->new_grade_id]);
                $this->dispatchBrowserEvent('hideDialog');
                $this->modalFormReset();
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم نقل الحلقة إلى مرحلة جديدة بنجاح.']);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                $this->dispatchBrowserEvent('hideDialog');
                $this->catchError = $e->getMessage();
            }
        }
    }

    public function pullATeacherOutOfTheGroup($id, $teacher_id)
    {
        if ($id != null && $teacher_id != null) {
            $this->dispatchBrowserEvent('hideDialog');
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
                $this->dispatchBrowserEvent('showDialog');
            }
        } else {
            $messageBag->add('new_grade_id', 'يجب إختيار المرحلة الجديدة');
            $this->setErrorBag($messageBag);
        }
    }

    public function destroy($id)
    {
        Group::where('id', $id)->delete();
        $this->dispatchBrowserEvent('hideDialog');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حذف الحلقة بنجاح.']);
    }

    public function all_Groups()
    {
        return Group::query()
            ->with(['grade', 'teacher.user'])
            ->withCount(['students'])
            ->search($this->search)
            ->when($this->current_role == 'مشرف', function ($q) {
                $q->where('grade_id', '=', $this->grade_id);
            })
            ->when($this->current_role == 'أمير المركز' && !empty($this->selectedGradeId), function ($q) {
                $q->where('grade_id', '=', $this->selectedGradeId);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Groups();
    }

    public function all_Grades()
    {
        if ($this->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            return Grade::where('id', $this->grade_id)->get();
        } else if ($this->current_role == 'أمير المركز') {
            return Grade::all();
        }
        return [];
    }

    public function all_teachers_export()
    {
        $supervisor = Supervisor::with('grade:id,name')->where('id',auth()->id())->first();
        $teachers = DB::table('teachers')
            ->select(['name', 'identification_number', 'phone', 'dob', 'economic_situation', 'recitation_level', 'academic_qualification'])
            ->join('users', 'teachers.id', '=', 'users.id')
            ->join('user_infos', 'users.id', '=', 'user_infos.id')
            ->where('teachers.grade_id', '=', $supervisor->grade_id)
            ->get();
        return (new GradeTeachersExport($teachers, $supervisor->grade->name))->download('Database of all ' . $supervisor->grade->name . ' teachers' . '.xlsx', Excel::XLSX);
    }

    public function export($id)
    {
        if (!empty($id)) {
            $students = DB::table('groups')
                ->select(['users_student.name as student_name',
                    'users_student.identification_number as student_identification_number',
                    'users_father.identification_number as father_identification_number',
                    'users_father.phone as father_phone', 'users_student.dob as student_dob',
                    'user_info_father.economic_situation as economic_situation',
                    'quran_part_count.total_preservation_parts',
                    DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                    DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`")])
                ->join('students', 'students.group_id', '=', 'groups.id')
                ->join('fathers', 'students.father_id', '=', 'fathers.id')
                ->join('users as users_student', 'students.id', '=', 'users_student.id')
                ->join('users as users_father', 'fathers.id', '=', 'users_father.id')
                ->join('user_infos as user_info_father', 'users_father.id', '=', 'user_info_father.id')
                ->leftJoin('exams as exams_count', function ($join) {
                    $join->on('students.id', '=', 'exams_count.student_id')
                        ->on('exams_count.id', '=', DB::raw("(SELECT exams.id FROM exams
                  JOIN quran_parts ON quran_part_id = quran_parts.id
                  AND quran_parts.type = 'individual'
                  JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id
                  AND exams.mark >= exam_success_mark.mark
                  WHERE student_id = students.id
                  ORDER BY datetime DESC
                  LIMIT 1)"));
                })
                ->leftJoin('quran_parts as quran_part_count', 'exams_count.quran_part_id', '=', 'quran_part_count.id')
                ->leftJoin('exams as exams_deserved', function ($join) {
                    $join->on('students.id', '=', 'exams_deserved.student_id')
                        ->on('exams_deserved.id', '=', DB::raw("(SELECT exams.id FROM exams
                  JOIN quran_parts ON quran_part_id = quran_parts.id
                  AND quran_parts.type = 'deserved'
                  JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id
                  AND exams.mark >= exam_success_mark.mark
                  WHERE student_id = students.id
                  ORDER BY datetime DESC
                  LIMIT 1)"));
                })
                ->leftJoin('quran_parts as part_deserved', 'exams_deserved.quran_part_id', '=', 'part_deserved.id')
                ->where('groups.id', '=', $id)
                ->groupBy(['student_name', 'quran_part_count.total_preservation_parts'])
                ->get();
            $teacher_name = Teacher::with('user:id,name')->where('id',Group::where('id',$id)->first()->teacher_id)->first()->user->name;

            return (new GroupStudentsExport($students,$teacher_name))->download('Database of all ' . $teacher_name . ' students' . '.xlsx', Excel::XLSX);
        }
        return;
    }

    public function grade_students_export(){
       $supervisor = Supervisor::with('grade:id,name')->where('id',auth()->id())->first();
        $students = DB::table('grades')
            ->select(['users_student.name as student_name',
                'users_student.identification_number as student_identification_number',
                'users_father.identification_number as father_identification_number',
                'users_father.phone as father_phone', 'users_student.dob as student_dob',
                'user_info_father.economic_situation as economic_situation',
                'users_teacher.name as teacher_name',
                'quran_part_count.total_preservation_parts',
                DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`")])
            ->join('students', 'students.grade_id', '=', 'grades.id')
            ->join('groups', 'students.group_id', '=', 'groups.id')
            ->join('fathers', 'students.father_id', '=', 'fathers.id')
            ->join('users as users_student', 'students.id', '=', 'users_student.id')
            ->join('users as users_father', 'fathers.id', '=', 'users_father.id')
            ->join('user_infos as user_info_father', 'users_father.id', '=', 'user_info_father.id')
            ->join('users as users_teacher', 'groups.teacher_id', '=', 'users_teacher.id')
            ->leftJoin('exams as exams_count', function ($join) {
                $join->on('students.id', '=', 'exams_count.student_id')
                    ->on('exams_count.id', '=', DB::raw("(SELECT exams.id FROM exams
                  JOIN quran_parts ON quran_part_id = quran_parts.id
                  AND quran_parts.type = 'individual'
                  JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id
                  AND exams.mark >= exam_success_mark.mark
                  WHERE student_id = students.id
                  ORDER BY datetime DESC
                  LIMIT 1)"));
            })
            ->leftJoin('quran_parts as quran_part_count', 'exams_count.quran_part_id', '=', 'quran_part_count.id')
            ->leftJoin('exams as exams_deserved', function ($join) {
                $join->on('students.id', '=', 'exams_deserved.student_id')
                    ->on('exams_deserved.id', '=', DB::raw("(SELECT exams.id FROM exams
                  JOIN quran_parts ON quran_part_id = quran_parts.id
                  AND quran_parts.type = 'deserved'
                  JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id
                  AND exams.mark >= exam_success_mark.mark
                  WHERE student_id = students.id
                  ORDER BY datetime DESC
                  LIMIT 1)"));
            })
            ->leftJoin('quran_parts as part_deserved', 'exams_deserved.quran_part_id', '=', 'part_deserved.id')
            ->where('grades.id', '=', $supervisor->grade_id ?? null)
            ->groupBy(['student_name', 'quran_part_count.total_preservation_parts'])
            ->get();
        return (new GradeStudentsExport($students,$supervisor->grade->name))->download('Database of all ' . $supervisor->grade->name . ' students' . '.xlsx', Excel::XLSX);
    }
}
