<?php

namespace App\Http\Livewire;

use App\Exports\AllStudentsExport;
use App\Exports\AllTeachersExport;
use App\Exports\GradeStudentsExport;
use App\Exports\GradeTeachersExport;
use App\Models\Grade;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;

class Grades extends HomeComponent
{
    public $name;

    public function render()
    {
        return view('livewire.grades', ['grades' => $this->all_Grades()]);
    }

    public function mount()
    {
        $this->sortBy = "name";
    }

    public function loadModalData($id)
    {
        $this->modalFormReset();
        $data = Grade::find($id);
        $this->modalId = $data->id;
        $this->name = $data->name;
    }

    public function modelData()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function rules()
    {
        return ['name' => 'required|unique:grades,name,' . $this->modalId];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم المرحلة مطلوب',
            'name.unique' => 'اسم المرحلة موجود مسبقا',
        ];
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->name = null;
        $this->modalId = '';
    }

    public function store()
    {
        $this->validate();
        Grade::create($this->modelData());

        $this->modalFormReset();
        $this->dispatchBrowserEvent('hideDialog');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حفظ معلومات المرحلة بنجاح.']);
    }

    public function update()
    {
        $this->validate();
        $Grade = Grade::where('id', $this->modalId)->first();
        $Grade->update($this->modelData());
        $this->modalFormReset();
        $this->dispatchBrowserEvent('hideDialog');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم تحديث معلومات المرحلة بنجاح.']);
    }

    public function destroy($id)
    {
        Grade::where('id', $id)->delete();
        $this->dispatchBrowserEvent('hideDialog');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => 'تم حذف المرحلة بنجاح.']);
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'أمير المركز') {
            return Grade::query()
                ->withCount('teachers')
                ->withCount('groups')
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }
        return [];
    }

    public function submitSearch(){
        $this->all_Grades();
    }

    public function all_teachers_export()
    {
        $teachers = DB::table('teachers')
            ->select(['users.name', 'identification_number', 'phone', 'dob', 'economic_situation',
                'recitation_level', 'academic_qualification','grades.name as grade_name'])
            ->join('users', 'teachers.id', '=', 'users.id')
            ->join('users_info', 'users.id', '=', 'users_info.id')
            ->join('grades', 'teachers.grade_id', '=', 'grades.id')
            ->get();
        return (new AllTeachersExport($teachers))->download('Database of all teachers of the center.xlsx', Excel::XLSX);

    }

    public function grade_teachers_export($id)
    {
        if (!empty($id)) {
            $grade_name = Grade::where('id', $id)->first()->name;
            $teachers = DB::table('teachers')
                ->select(['name', 'identification_number', 'phone', 'dob', 'economic_situation', 'recitation_level', 'academic_qualification'])
                ->join('users', 'teachers.id', '=', 'users.id')
                ->join('users_info', 'users.id', '=', 'users_info.id')
                ->where('teachers.grade_id', '=', $id)
                ->get();
            return (new GradeTeachersExport($teachers, $grade_name))->download('Database of all ' . $grade_name . ' teachers' . '.xlsx', Excel::XLSX);
        }
        return;
    }

    public function grade_students_export($id)
    {
        if (!empty($id)) {
            $grade_name = Grade::where('id', $id)->first()->name;
            $students = DB::table('grades')
                ->select(['users_student.name as student_name',
                    'users_student.identification_number as student_identification_number',
                    'users_father.identification_number as father_identification_number',
                    'users_father.phone as father_phone', 'users_student.dob as student_dob',
                    'users_info_father.economic_situation as economic_situation',
                    'users_teacher.name as teacher_name',
                    'quran_part_count.total_preservation_parts',
                    DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                    DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`")])
                ->join('students', 'students.grade_id', '=', 'grades.id')
                ->join('groups', 'students.group_id', '=', 'groups.id')
                ->join('fathers', 'students.father_id', '=', 'fathers.id')
                ->join('users as users_student', 'students.id', '=', 'users_student.id')
                ->join('users as users_father', 'fathers.id', '=', 'users_father.id')
                ->join('users_info as users_info_father', 'users_father.id', '=', 'users_info_father.id')
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
                ->where('grades.id', '=', $id)
                ->groupBy(['student_name', 'quran_part_count.total_preservation_parts'])
                ->get();
            return (new GradeStudentsExport($students, $grade_name))->download('Database of all ' . $grade_name . ' students' . '.xlsx', Excel::XLSX);
        }
        return;
    }

    public function all_students_export()
    {
        $students = DB::table('students')
            ->select(['users_student.name as student_name',
                'users_student.identification_number as student_identification_number',
                'users_father.identification_number as father_identification_number',
                'users_father.phone as father_phone', 'users_student.dob as student_dob',
                'users_info_father.economic_situation as economic_situation',
                'grades.name as grade_name', 'users_teacher.name as teacher_name',
                'quran_part_count.total_preservation_parts',
                DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`")])
            ->join('grades', 'students.grade_id', '=', 'grades.id')
            ->join('groups', 'students.group_id', '=', 'groups.id')
            ->join('fathers', 'students.father_id', '=', 'fathers.id')
            ->join('users as users_student', 'students.id', '=', 'users_student.id')
            ->join('users as users_father', 'fathers.id', '=', 'users_father.id')
            ->join('users_info as users_info_father', 'users_father.id', '=', 'users_info_father.id')
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
            ->groupBy(['student_name', 'quran_part_count.total_preservation_parts'])
            ->get();
        return (new AllStudentsExport($students))->download('Database of all students of the center.xlsx', Excel::XLSX);
    }


}
