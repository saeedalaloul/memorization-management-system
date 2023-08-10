<?php

namespace App\Http\Livewire;

use App\Exports\GradeStudentsExport;
use App\Exports\GradeTeachersExport;
use App\Exports\GroupStudentsExport;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Sponsorship;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;

class Groups extends HomeComponent
{
    public $name;
    public $type;
    public $grade_id;
    public $new_grade_id;
    public $teacher_id;
    public $retGroup;
    public $grades = [], $teachers = [], $sponsorships = [];
    public $selectedGradeId, $sponsorships_ids;
    public $selectedType, $selectedSponsorshipId;
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
        $this->getSponsorships();
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
        $this->type = $data->type;
        $this->grade_id = $data->grade_id;
        $this->teacher_id = $data->teacher_id;
        $this->is_moving = $isMoving;
        $this->sponsorships_ids = $data->sponsorships()->pluck('id')->toArray();
        $this->getTeachersByGradeId();
    }

    public function modelData()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'grade_id' => $this->grade_id,
            'teacher_id' => $this->teacher_id,
        ];
    }

    public function rules()
    {
        return ['name' => 'required|unique:groups,name,' . $this->modalId,
            'type' => 'required|string',
            'grade_id' => 'required',
            'teacher_id' => 'required|unique:groups,teacher_id,' . $this->modalId,];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم الحلقة مطلوب',
            'name.unique' => 'اسم الجلقة موجود مسبقا',
            'type.required' => 'حقل نوع الحلقة مطلوب',
            'type.string' => 'حقل نوع الحلقة يجب أن يكون نص',
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
        $this->type = null;
        $this->sponsorships_ids = null;
        $this->new_grade_id = null;
        $this->teacher_id = null;
        $this->modalId = '';
        $this->is_moving = false;
    }

    public function store()
    {
        $this->validate();
        $group = Group::create($this->modelData())->first();

        if (!empty($this->sponsorships_ids)) {
            $group?->sponsorships()->attach($this->sponsorships_ids);
        }

        $this->modalFormReset();
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حفظ معلومات الحلقة بنجاح.']);
    }

    public function validateUpdate()
    {
        $this->validate();
        $messageBag = new MessageBag;
        $isCompleteUpdate = false;

        if ($this->retGroup->type === Group::QURAN_TYPE) {
            $student_found = Student::find($this->teacher_id);
            if ($student_found !== null && $student_found->group !== null
                && $student_found->group->id === $this->retGroup->id) {
                $messageBag->add('teacher_id', 'عذرا, لا يمكن اختيار الحلقة للمحفظ لأنه طالب في نفس الحلقة');
                $this->setErrorBag($messageBag);
            } else {
                $isCompleteUpdate = true;
            }
        } else {
            $isCompleteUpdate = true;
        }

        if ($isCompleteUpdate) {
            if ($this->retGroup->grade_id !== $this->grade_id && $this->retGroup->students->count() > 0) {
                $messageBag->add('grade_id', 'عذرا لم يتم تحديث الحلقة بسبب وجود طلاب داخل الحلقة');
                $this->setErrorBag($messageBag);
            } else if ($this->retGroup->teacher !== null && $this->retGroup->teacher->grade_id !== $this->grade_id) {
                $messageBag->add('grade_id', 'عذرا لم يتم تحديث الحلقة بسبب عدم تطابق المرحلة للمحفظ والحلقة');
                $this->setErrorBag($messageBag);
                $this->grade_id = null;
                $this->teacher_id = null;
            } else if ($this->retGroup->type !== $this->type) {
                if ($this->retGroup->type === Group::SUNNAH_TYPE && $this->retGroup->students_sunnah->count() > 0) {
                    $messageBag->add('type', 'عذرا لم يتم تحديث الحلقة بسبب وجود طلاب داخل الحلقة');
                    $this->setErrorBag($messageBag);
                } else if ($this->retGroup->students->count() > 0) {
                    $messageBag->add('type', 'عذرا لم يتم تحديث الحلقة بسبب وجود طلاب داخل الحلقة');
                    $this->setErrorBag($messageBag);
                } else {
                    $this->update();
                }
            } else {
                $this->update();
            }
        }
    }

    public function update()
    {
        $Group = Group::where('id', $this->modalId)->first();
        $Group->update($this->modelData());
        if (!empty($this->sponsorships_ids)) {
            $Group?->sponsorships()->sync($this->sponsorships_ids);
        } else {
            $Group?->sponsorships()->detach();
        }
        $this->modalFormReset();
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم تحديث معلومات الحلقة بنجاح.']);
    }

    public function move()
    {
        if ($this->new_grade_id !== null) {
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

    public function lunchBoxOfModalData($type, $id, $teacher_id)
    {
        $this->modalId = $id;
        if ($type === 'delete') {
            $this->dispatchBrowserEvent('showModalDeleteGroup');
        } else {
            $this->teacher_id = $teacher_id;
            $this->dispatchBrowserEvent('showModalPullTeacher');
        }
    }

    public function pullATeacherOutOfTheGroup()
    {
        if ($this->modalId !== null && $this->teacher_id !== null) {
            $this->dispatchBrowserEvent('hideDialog');
            $teacher = Teacher::where('id', $this->teacher_id)->first();
            if ($teacher->exam_order->count() > 0) {
                $this->catchError = "عذرا , يوجد طلبات اختبارات لهذه الحلقة يجب إجرائها أو حذفها حتى تتمكن من سحب المحفظ";
            } else {
                $group = Group::find($this->modalId);
                if ($group->teacher_id !== null) {
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
        if ($this->new_grade_id !== null) {
            if ($this->new_grade_id === $this->grade_id) {
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

    public function destroy()
    {
        $group = Group::where('id', $this->modalId)->first();

        if ($group->students->count() > 0) {
            $this->catchError = "عذرا لا يمكنك حذف هذه الحلقة بسبب وجود طلاب داخل الحلقة.";
        } else {
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'عذرا لا يمكنك حذف الحلقة بتاتا.']);
//            $group->delete();
        }
        $this->dispatchBrowserEvent('hideDialog');
    }

    public function all_Groups()
    {
        return Group::query()
            ->with(['grade', 'teacher.user', 'sponsorships:id,name'])
            ->withCount(['students', 'students_sunnah'])
            ->search($this->search)
            ->when($this->current_role === User::SUPERVISOR_ROLE, function ($q) {
                $q->where('grade_id', '=', $this->grade_id);
            })
            ->when($this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE, function ($q) {
                $q->withWhereHas('sponsorships', function ($q) {
                    $q->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray());
                });
            })
            ->when($this->current_role === User::ADMIN_ROLE && !empty($this->selectedGradeId), function ($q) {
                $q->where('grade_id', '=', $this->selectedGradeId);
            })
            ->when($this->selectedType !== null, function ($q) {
                $q->where('type', '=', $this->selectedType);
            })->when(!empty($this->selectedSponsorshipId), function ($q) {
                $q->whereHas('sponsorships', function ($q) {
                    $q->where('sponsorship_id', $this->selectedSponsorshipId);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Groups();
    }

    public function all_Grades()
    {
        if ($this->current_role === User::SUPERVISOR_ROLE) {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            return Grade::where('id', $this->grade_id)->get();
        }

        if ($this->current_role === User::ADMIN_ROLE || $this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            return Grade::all();
        }
        return [];
    }

    public function getSponsorships()
    {
        $this->reset('sponsorships_ids', 'sponsorships');

        if ($this->current_role === User::ADMIN_ROLE || $this->current_role === User::SUPERVISOR_ROLE) {
            $this->sponsorships = Sponsorship::query()->get();
        }
        return [];
    }

    public function all_teachers_export()
    {
        $supervisor = Supervisor::with('grade:id,name')->where('id', auth()->id())->first();
        $teachers = DB::table('teachers')
            ->select(['name', 'identification_number', 'phone', 'dob', 'email', 'economic_situation', 'recitation_level', 'academic_qualification'])
            ->join('users', 'teachers.id', '=', 'users.id')
            ->join('user_infos', 'users.id', '=', 'user_infos.id')
            ->where('teachers.grade_id', '=', $supervisor->grade_id)
            ->get();
        return (new GradeTeachersExport($teachers, $supervisor->grade->name))->download('Database of all ' . $supervisor->grade->name . ' teachers' . '.xlsx', Excel::XLSX);
    }

    public function export($id, $type)
    {
        if (!empty($id) && !empty($type)) {
            $students = DB::table('groups')
                ->select(['users_student.name as student_name',
                    'users_student.identification_number as student_identification_number',
                    'users_father.identification_number as father_identification_number',
                    'users_father.phone as father_phone', 'students.whatsapp_number as student_whatsapp_number'
                    , 'users_student.dob as student_dob', 'user_info_father.economic_situation as economic_situation',
                    'quran_part_count.total_preservation_parts',
                    DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                    DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`"),
                    DB::raw("(GROUP_CONCAT(sunnah_part_count.name,' (',sunnah_part_count.total_hadith_parts,') حديث')) as `sunnah_part_individual`"),
                    DB::raw("(GROUP_CONCAT(sunnah_part_deserved.name,' (',sunnah_part_deserved.total_hadith_parts,') حديث')) as `sunnah_part_deserved`")])
                ->when($type == Group::QURAN_TYPE, function ($q) {
                    $q->join('students', 'students.group_id', '=', 'groups.id');
                })
                ->when($type == Group::SUNNAH_TYPE, function ($q) {
                    $q->join('students', 'students.group_sunnah_id', '=', 'groups.id');
                })
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
                })->leftJoin('quran_parts as quran_part_count', 'exams_count.quran_part_id', '=', 'quran_part_count.id')
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
                ->leftJoin('sunnah_exams as sunnah_exams_deserved', function ($join) {
                    $join->on('students.id', '=', 'sunnah_exams_deserved.student_id')
                        ->on('sunnah_exams_deserved.id', '=', DB::raw("(SELECT sunnah_exams.id FROM sunnah_exams
                  JOIN sunnah_parts ON sunnah_part_id = sunnah_parts.id
                  AND sunnah_parts.type = 'deserved' JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id
                  AND sunnah_exams.mark >= exam_success_mark.mark WHERE student_id = students.id ORDER BY datetime DESC LIMIT 1)"));
                })
                ->leftJoin('sunnah_parts as sunnah_part_deserved', 'sunnah_exams_deserved.sunnah_part_id', '=', 'sunnah_part_deserved.id')
                ->leftJoin('sunnah_exams as sunnah_exams_count', function ($join) {
                    $join->on('students.id', '=', 'sunnah_exams_count.student_id')
                        ->on('sunnah_exams_count.id', '=', DB::raw("(SELECT sunnah_exams.id FROM sunnah_exams
                  JOIN sunnah_parts ON sunnah_part_id = sunnah_parts.id
                  AND sunnah_parts.type = 'individual' JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id
                  AND sunnah_exams.mark >= exam_success_mark.mark WHERE student_id = students.id ORDER BY datetime DESC LIMIT 1)"));
                })->leftJoin('sunnah_parts as sunnah_part_count', 'sunnah_exams_count.sunnah_part_id', '=', 'sunnah_part_count.id')
                ->where('groups.id', '=', $id)
                ->groupBy(['student_name', 'quran_part_count.total_preservation_parts', 'sunnah_part_count.total_hadith_parts'])
                ->get();
            $teacher_id = Group::where('id', $id)->first()->teacher_id ?? null;
            if ($teacher_id !== null) {
                $teacher_name = Teacher::with('user:id,name')->where('id', $teacher_id)->first()->user->name;
            } else {
                $teacher_name = 'لا يوجد محفظ';
            }
            return (new GroupStudentsExport($students, $teacher_name))->download('Database of all ' . $teacher_name . ' students' . '.xlsx', Excel::XLSX);
        }
        return;
    }

    public function grade_students_export()
    {
        $supervisor = Supervisor::with('grade:id,name')->where('id', auth()->id())->first();
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
        return \Maatwebsite\Excel\Facades\Excel::raw(new GradeStudentsExport($students, $supervisor->grade->name), Excel::XLSX);

//        return \Maatwebsite\Excel\Facades\Excel::download(new GradeStudentsExport($students, $supervisor->grade->name),'Database of all ' . $supervisor->grade->name . ' students' . '.xlsx',Excel::XLSX);
//        return (new GradeStudentsExport($students, $supervisor->grade->name))->download('Database of all ' . $supervisor->grade->name . ' students' . '.xlsx', Excel::XLSX);
    }
}
