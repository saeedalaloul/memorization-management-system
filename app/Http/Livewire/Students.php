<?php

namespace App\Http\Livewire;

use App\Exports\GroupStudentsExport;
use App\Exports\SelectedStudentsExport;
use App\Models\Father;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentBlock;
use App\Models\StudentWarning;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\ExpiredStudentBlockForTeacherNotify;
use App\Notifications\ExpiredStudentWarningForTeacherNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Excel;
use Spatie\Permission\Models\Role;

class Students extends HomeComponent
{
    use NotificationTrait;

    public $photo, $photo_ret, $groups = [], $grades = [], $ages = [], $warning_cancel_notes,
        $block_cancel_notes, $current_group_type;


    public $currentStep = 1, $father_id, $student_id,

        // Father_INPUTS
        $father_identification_number, $father_name, $father_gender, $father_first_name, $father_second_name, $father_phone,
        $father_third_name, $father_last_name, $economic_situation,

        // Student_INPUTS
        $student_identification_number, $student_name, $student_first_name, $student_second_name,
        $student_third_name, $student_last_name, $dob, $grade_id, $group_id, $whatsapp_number,
        $country_code, $student, $student_exams, $student_sunnah_exams;

    public $selectedGradeId, $selectedTeacherId, $selectedAge, $selectedNumberQuranPart, $studentSelectedIds;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'refresh' => 'all_Students',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->process_type = '';
        $this->link = 'manage_student/';
        $this->all_Grades();
        if ($this->current_role === User::ADMIN_ROLE) {
            $dob = DB::table('students')->select(DB::raw('MAX(Year(dob)) as max_dob'), DB::raw('MIN(Year(dob)) as min_dob'))
                ->join('users', 'students.id', '=', 'users.id')->first();
            for ($i = $dob->min_dob; $i <= $dob->max_dob; $i++) {
                $this->ages[] = date('Y') - ($dob->min_dob++);
            }
        }

        if ($this->current_role === User::TEACHER_ROLE) {
            $this->perPage = 25;
            $this->current_group_type = Group::where('teacher_id', auth()->id())->first()->type ?? null;
        }
    }


    public function render()
    {
        if ($this->process_type === 'show' && $this->student_id !== null) {
            $this->showStudentData($this->student_id);
        }

        return view('livewire.students', ['students' => $this->all_Students(),]);
    }

    public function fatherFound()
    {
        if (strlen($this->father_identification_number) === 9) {
            $father = Father::whereHas('user', function ($q) {
                return $q->select('*')->where('identification_number', '=', $this->father_identification_number);
            })->first();
            if ($father) {
                $this->clearForm();
                $this->father_identification_number = $father->user->identification_number;
                $this->father_id = $father->id;
                $this->father_name = $father->user->name;
                $this->father_gender = $father->user->gender;
                $this->father_phone = $father->user->phone;
                if ($father->user->user_info !== null) {
                    $this->economic_situation = $father->user->user_info->economic_situation ?? null;
                }
                $this->successMessage = 'لقد تم اعتماد معلومات الأب من خلال رقم الهوية المدخل مسبقا في النظام, يرجى متابعة إدخال باقي البيانات أو تعديل رقم الهوية المدخل...';
                $this->resetValidation(['father_identification_number', 'father_name', 'father_gender', 'father_first_name', 'father_phone',
                    'father_second_name', 'father_third_name', 'father_last_name', 'economic_situation']);
            } else {
                $this->father_id = null;
                $this->successMessage = '';
            }
        } else {
            $this->father_id = null;
            $this->successMessage = '';
        }
    }


    //firstStepSubmit

    public function firstStepSubmit()
    {
        if ($this->father_id === null) {
            $this->father_name = trim($this->father_first_name) . ' ' . trim($this->father_second_name) . ' ' . trim($this->father_third_name) . ' ' . trim($this->father_last_name);
            $this->validate(
                [
                    'father_first_name' => 'required|string',
                    'father_second_name' => 'required|string',
                    'father_third_name' => 'required|string',
                    'father_last_name' => 'required|string',
                ]);
        }

        $this->validate(
            [
                'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->father_id,
                'father_name' => 'required|string|unique:users,name,' . $this->father_id,
                'father_gender' => 'required|string',
                'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
                'economic_situation' => 'required',
            ]);

        $this->currentStep = 2;
    }

    //secondStepSubmit_edit

    public function secondStepSubmit()
    {
        $this->student_name = trim($this->student_first_name) . ' ' . trim($this->student_second_name) . ' ' . trim($this->student_third_name) . ' ' . trim($this->student_last_name);

        $this->validate([
            'student_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->student_id,
            'student_name' => 'unique:users,name,' . $this->student_id,
            'student_first_name' => 'required|string',
            'student_second_name' => 'required|string',
            'student_third_name' => 'required|string',
            'student_last_name' => 'required|string',
            'dob' => 'required|date|date_format:Y-m-d',
            'grade_id' => 'required',
            'group_id' => 'required',
            'whatsapp_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'country_code' => 'required|string',
        ]);

        $this->currentStep = 3;
    }

    public function submitForm()
    {
        if (!empty($this->photo)) {
            $this->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:1024|unique:users,profile_photo,' . $this->student_id,
            ]);
        }
        DB::beginTransaction();

        try {
            $userFather = null;
            // حفظ بيانات الأب في جدول المستخدمين
            if (!$this->father_id) {
                $userFather = User::create([
                    'name' => $this->father_name,
                    'gender' => $this->father_gender,
                    'phone' => $this->father_phone,
                    'identification_number' => $this->father_identification_number,
                ]);
            }

            // حفظ بيانات الإبن في جدول المستخدمين
            $userStudent = User::create([
                'name' => $this->student_name,
                'gender' => Grade::whereId($this->grade_id)->first()->section,
                'identification_number' => $this->student_identification_number,
                'dob' => $this->dob,
            ]);

            // حفظ البيانات في جدول الأباء
            $retFather = Father::find($this->father_id);

            if (is_null($retFather) && !is_null($userFather)) {
                $retFather = Father::create(['id' => $userFather->id]);
                $userFather->user_info()->create($this->modelUserInfo($userFather->id));
            }

            // حفظ البيانات في جدول الطلاب
            Student::create([
                'id' => $userStudent->id,
                'father_id' => is_null($userFather) ? $this->father_id : $userFather->id,
                'grade_id' => $this->grade_id,
                'group_id' => $this->group_id,
                'whatsapp_number' => $this->country_code . (int)$this->whatsapp_number,
            ]);

            $roleId = Role::select('*')->where('name', '=', 'طالب')->get();
            $userStudent->assignRole([$roleId]);

            $roleId = Role::select('*')->where('name', '=', 'ولي أمر الطالب')->get();
            if ($userFather !== null) {
                $userFather->assignRole([$roleId]);
            } else {
                $retFather->user->assignRole([$roleId]);
            }

            if (!empty($this->photo)) {
                $this->uploadImage($this->photo,
                    $this->student_identification_number . Carbon::now()->timestamp . '.' . $this->photo->getClientOriginalExtension(),
                    $userStudent->id);
            }

            DB::commit();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم حفظ معلومات الطالب بنجاح.']);
            $this->clearForm();
            $this->currentStep = 1;
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function modelUserInfo($father_id)
    {
        return [
            'id' => $father_id,
            'economic_situation' => $this->economic_situation,
        ];
    }

    public function clearForm()
    {
        // clear inputs father
        $this->father_id = null;
        $this->father_name = null;
        $this->father_gender = null;
        $this->father_first_name = null;
        $this->father_second_name = null;
        $this->father_third_name = null;
        $this->father_last_name = null;
        $this->father_phone = null;
        $this->economic_situation = null;
        $this->father_identification_number = null;

        // clear inputs student
        $this->student = null;
        $this->student_exams = null;
        $this->student_name = null;
        $this->student_first_name = null;
        $this->student_second_name = null;
        $this->student_third_name = null;
        $this->student_last_name = null;
        $this->student_identification_number = null;
        $this->dob = null;
        $this->grade_id = null;
        $this->group_id = null;
        $this->photo = null;
        $this->photo_ret = null;
        $this->country_code = null;
        $this->whatsapp_number = null;
        $this->currentStep = 1;
        $this->process_type = '';

        // clear inputs submit exam request
        $this->student_id = null;
        $this->catchError = '';
        $this->successMessage = '';
        $this->warning_cancel_notes = null;
        $this->block_cancel_notes = null;
        $this->resetValidation();
    }

    public function process_data($id, $process_type)
    {
        if ($process_type === 'add_to_sunnah') {
            $this->emit('add_student_sunnah', $id);
        } elseif ($process_type === 'update_to_sunnah') {
            $this->emit('update_student_sunnah', $id);
        } elseif ($process_type === 'submit_exam_order') {
            $this->emit('submit_exam_order', $id);
        } elseif ($process_type === 'submit_exam_sunnah_order') {
            $this->emit('submit_exam_sunnah_order', $id);
        } else if ($process_type === 'edit') {
            $this->clearForm();
            $this->edit($id);
        } else if ($process_type === 'move_student') {
            $this->emit('move_student', $id);
        } else if ($process_type === 'reset_daily_memorization') {
            $this->emit('reset_daily_memorization', $id);
        } else {
            $this->showStudentData($id);
        }
        $this->process_type = $process_type;
    }


    public function edit($id)
    {
        $this->resetValidation();
        $student = Student::with(['user', 'father.user.user_info'])->whereId($id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
        $this->student_identification_number = $student->user->identification_number;
        $this->country_code = substr($student->whatsapp_number, 0, 4);
        $this->whatsapp_number = '0' . substr($student->whatsapp_number, 4, 12);
        $this->photo_ret = $student->user->profile_photo_url;
        $this->dob = $student->user->dob;
        $this->father_phone = $student->father->user->phone;
        $this->father_id = $student->father_id;

        if ($student->father->user->user_info !== null) {
            $this->economic_situation = $student->father->user->user_info->economic_situation ?? null;
        }
    }

    public function validate_edit()
    {
        $this->validate([
            'student_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->student_id,
            'student_name' => 'required|string|unique:users,name,' . $this->student_id,
            'dob' => 'required|date|date_format:Y-m-d',
            'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
            'economic_situation' => 'required',
            'whatsapp_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'country_code' => 'required|string',
        ]);

        $this->submitForm_edit();
    }

    public function submitForm_edit()
    {
        if ($this->student_id) {

            if (!empty($this->photo)) {
                $this->validate([
                    'photo' => 'required|image|mimes:jpg,jpeg,png|max:1024|unique:users,profile_photo,' . $this->student_id,
                ]);
            }

            $student = Student::whereId($this->student_id)->first();

            $student->user->update([
                'name' => $this->student_name,
                'identification_number' => $this->student_identification_number,
                'dob' => $this->dob,
            ]);

            $student->father->user->update([
                'phone' => $this->father_phone,
            ]);

            $student->father->user->user_info->update([
                'economic_situation' => $this->economic_situation,
            ]);

            $student->update([
                'whatsapp_number' => $this->country_code . (int)$this->whatsapp_number,
            ]);

            $roleId = Role::select('*')->where('name', '=', 'طالب')->get();
            $student->user->assignRole([$roleId]);

            if (!empty($this->photo)) {
                $this->deleteImage($student->user->profile_photo);
                $this->uploadImage($this->photo,
                    $this->student_identification_number . Carbon::now()->timestamp . '.' . $this->photo->getClientOriginalExtension(),
                    $this->student_id);
            }

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تحديث معلومات الطالب بنجاح.']);
            $this->clearForm();
        }
    }


    public function back($step)
    {
        $this->currentStep = $step;
    }

    public function getStudent($id)
    {
        $this->resetValidation();
        $student = Student::where('id', $id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
    }


    public function warningCancel()
    {
        $this->validate(['warning_cancel_notes' => 'required|string'], ['warning_cancel_notes.required' => 'حقل الملاحظات مطلوب',
            'warning_cancel_notes.string' => 'حقل الملاحظات يجب أن يكون نص']);

        $studentWarningCount = null;

        if ($this->current_role === 'مشرف') {
            $date = Student::where('id', $this->student_id)->first()->student_is_warning->created_at ?? null;
            $studentWarningCount = StudentWarning::query()
                ->where('student_id', $this->student_id)
                ->whereMonth('created_at', Carbon::parse($date)->format('m'))
                ->whereYear('created_at', Carbon::parse($date)->format('Y'))
                ->count();
        }


        if ($studentWarningCount >= 2) {
            $messageBag = new MessageBag();
            $messageBag->add('warning_cancel_notes', 'عذرا لا يمكنك إلغاء إنذار الطالب بسبب حصول الطالب على أكثر من إنذار خلال الشهر, راجع أمير المركز.');
            $this->setErrorBag($messageBag);
        } else {
            $studentWarning = StudentWarning::query()
                ->where('student_id', $this->student_id)
                ->whereNull('warning_expiry_date')
                ->orderByDesc('updated_at')
                ->first();

            if ($studentWarning !== null) {
                $studentWarning->update(['warning_expiry_date' => Date('Y-m-d'), 'notes' => $this->warning_cancel_notes,]);
                // start push notifications to teacher
                $studentWarning->student->group->teacher->user->notify(new ExpiredStudentWarningForTeacherNotify($studentWarning));
                $title = "إلغاء إنذار جديد";
                $message = "";
                if ($this->current_role === "أمير المركز") {
                    $message = "لقد قام أمير المركز بإلغاء إنذار الطالب: " . $studentWarning->student->user->name . " وأرفق الملاحظة التالية: " . $studentWarning->notes;
                } elseif ($this->current_role === "مشرف") {
                    $message = "لقد قام مشرف المرحلة بإلغاء إنذار الطالب: " . $studentWarning->student->user->name;
                }
                $this->push_notification($message, $title, $this->link . $studentWarning->student_id, [$studentWarning->student->group->teacher->user->user_fcm_token->device_token ?? null]);
                // end push notifications to teacher
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم إلغاء إنذار الطالب بنجاح.']);
                $this->dispatchBrowserEvent('hideDialog');
                $this->clearForm();
            }
        }
    }

    public function blockCancel()
    {
        $this->validate(['block_cancel_notes' => 'required|string'], ['block_cancel_notes.required' => 'حقل الملاحظات مطلوب',
            'block_cancel_notes.string' => 'حقل الملاحظات يجب أن يكون نص']);

        $studentBlock = StudentBlock::query()
            ->where('student_id', $this->student_id)
            ->whereNull('block_expiry_date')
            ->orderByDesc('updated_at')
            ->first();

        if ($studentBlock !== null) {
            $studentBlock->update(['block_expiry_date' => Date('Y-m-d'), 'notes' => $this->block_cancel_notes,]);
            // start push notifications to teacher
            $studentBlock->student->group->teacher->user->notify(new ExpiredStudentBlockForTeacherNotify($studentBlock));
            $title = "إلغاء حظر جديد";
            $message = "لقد قام أمير المركز بإلغاء حظر الطالب: " . $studentBlock->student->user->name . " وأرفق الملاحظة التالية: " . $studentBlock->notes;
            $this->push_notification($message, $title, $this->link . $studentBlock->student_id, [$studentBlock->student->group->teacher->user->user_fcm_token->device_token ?? null]);
            // end push notifications to teacher
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم فك حظر الطالب بنجاح.']);
            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();
        }
    }

    public function setMessage($reason, $details, $type)
    {
        $this->process_type = '';
        if ($type === 'block') {
            if ($reason === StudentBlock::ABSENCE_REASON) {
                $this->catchError = "لقد تم حظر عمليات الطالب بسبب غيابه المتكرر لمدة " . $details['number_times'] . " أيام,راجع أمير المركز!";
            } elseif ($reason === StudentBlock::MEMORIZE_REASON) {
                $this->catchError = "لقد تم حظر عمليات الطالب بسبب تسميعه المتكرر أقل من " . $details['number_pages'] . " صفحة لمدة " . $details['number_times'] . " أيام,راجع أمير المركز!";
            } elseif ($reason === StudentBlock::DID_NOT_MEMORIZE_REASON) {
                $this->catchError = "لقد تم حظر عمليات الطالب بسبب عدم الحفظ المتكرر لمدة " . $details['number_times'] . " أيام,راجع أمير المركز!";
            } elseif ($reason === StudentBlock::LATE_REASON) {
                $this->catchError = "لقد تم حظر عمليات الطالب بسبب تأخره المتكرر لمدة " . $details['number_times'] . " أيام,راجع أمير المركز!";
            } elseif ($reason === StudentBlock::AUTHORIZED_REASON) {
                $this->catchError = "لقد تم حظر عمليات الطالب بسبب الأذونات المتكررة لمدة " . $details['number_times'] . " أيام,راجع أمير المركز!";
            }
        } elseif ($type === 'warning') {
            if ($reason === StudentWarning::ABSENCE_REASON) {
                $this->catchError = "لقد تم إعطاء الطالب إنذار نهائي بسبب غيابه المتكرر لمدة " . $details['number_times'] . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
            } elseif ($reason === StudentWarning::MEMORIZE_REASON) {
                $this->catchError = "لقد تم إعطاء الطالب إنذار نهائي بسبب تسميعه المتكرر أقل من " . $details['number_pages'] . " صفحة لمدة " . $details['number_times'] . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
            } elseif ($reason === StudentWarning::DID_NOT_MEMORIZE_REASON) {
                $this->catchError = "لقد تم إعطاء الطالب إنذار نهائي بسبب عدم الحفظ المتكرر لمدة " . $details['number_times'] . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
            } elseif ($reason === StudentWarning::LATE_REASON) {
                $this->catchError = "لقد تم إعطاء الطالب إنذار نهائي بسبب تأخره المتكرر لمدة " . $details['number_times'] . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
            } elseif ($reason === StudentWarning::AUTHORIZED_REASON) {
                $this->catchError = "لقد تم إعطاء الطالب إنذار نهائي بسبب الأذونات المتكررة لمدة " . $details['number_times'] . " أيام,راجع مشرف المرحلة حتى لا يتم تجميد عمليات الطالب!";
            }
        }
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => $this->catchError]);
    }

    public function all_Grades()
    {
        if ($this->current_role === User::SUPERVISOR_ROLE) {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->grade_id)->get();
        } else if ($this->current_role === User::TEACHER_ROLE) {
            $this->group_id = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === User::ADMIN_ROLE || $this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            $this->grades = Grade::all();
        }
    }

    public function updatedSelectedTeacherId($id)
    {
        $this->current_group_type = $this->groups[array_search($id, array_column($this->groups->toArray(), 'id'), true)]->type;
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId', 'current_group_type', 'group_id');
        $grade_id = null;
        if ($this->selectedGradeId !== null) {
            $grade_id = $this->selectedGradeId;
        } elseif ($this->grade_id !== null) {
            $grade_id = $this->grade_id;
        }

        if ($this->current_role === User::TEACHER_ROLE) {
            $this->groups = Group::query()->with(['teacher.user'])->where('teacher_id', auth()->id())->get();
        } elseif ($this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE) {
            $groups_ids = DB::table('sponsorship_groups')
                ->select(['group_id'])
                ->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray())
                ->distinct()
                ->pluck('group_id')->toArray();
            $this->groups = Group::query()->with(['teacher.user'])
                ->whereIn('id', $groups_ids)->get();
        } else if ($grade_id !== null) {
            $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $grade_id)->get();
        }
    }

    public function all_Students()
    {
        if ($this->current_role === 'محفظ' && $this->current_group_type === null) {
            return [];
        }

        $students = DB::table('students')
            ->when(!empty($this->search), function ($q, $v) {
                $q->where('students.whatsapp_number', 'like', '%' . $this->search . '%')
                    ->orWhere('users.name', 'LIKE', "%$this->search%")
                    ->orWhere('users.identification_number', 'LIKE', "%$this->search%");
            })->when(!empty((string)\Request::segment(2) && strval(\Request::segment(2)) !== 'message'), function ($q, $v) {
                $q->where('students.id', \Request::segment(2));
            })->when(!empty($this->selectedNumberQuranPart), function ($q, $v) {
                $q->join('exams', 'students.id', '=', 'exams.student_id')
                    ->join('exam_success_mark', function ($join) {
                        $join->on('exams.exam_success_mark_id', '=', 'exam_success_mark.id')
                            ->on('exams.mark', '>=', 'exam_success_mark.mark');
                    })
                    ->join('quran_parts', function ($join) {
                        $join->on('exams.quran_part_id', '=', 'quran_parts.id')
                            ->on('quran_parts.total_preservation_parts', '=', DB::raw($this->selectedNumberQuranPart));
                    });
            })->select(['students.id', 'users.name as student_name', 'users.identification_number as student_identification_number',
                'users.profile_photo', 'students.whatsapp_number as student_whatsapp_number', 'users.dob as dob', 'grades.name as grade_name', 'teachers.name as teacher_name', 'sunnah_teachers.name as teacher_sunnah_name',
                'group_sun.id as group_sunnah_id', 'student_warnings.details as student_warning_details', 'student_warnings.reason as student_warning_reason', 'student_blocks.details as student_block_details', 'student_blocks.reason as student_block_reason'
                , DB::raw("(GROUP_CONCAT(last_quran_part.name,' ',last_quran_part.description SEPARATOR '')) as `quran_part_last`")
            ])
            ->join('users', function ($join) {
                $join->on('students.id', '=', 'users.id')
                    ->when(!empty($this->selectedAge), function ($q, $v) {
                        $q->on(DB::raw('year(users.dob)'), '=', DB::raw(date('Y') - (int)$this->selectedAge));
                    })
                    ->when($this->current_role === 'أمير المركز', function ($q, $v) {
                        $q->when(!empty($this->selectedGradeId), function ($q, $v) {
                            $q->on('students.grade_id', '=', DB::raw("(select id from `grades` where `id` = '$this->selectedGradeId' LIMIT 1)"));
                        })->when(!empty($this->selectedTeacherId) && $this->current_group_type === Group::QURAN_TYPE || $this->current_group_type === Group::MONTADA_TYPE, function ($q, $v) {
                            $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                        })->when(!empty($this->selectedTeacherId) && $this->current_group_type === Group::SUNNAH_TYPE, function ($q, $v) {
                            $q->on('students.group_sunnah_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                        });
                    })
                    ->when($this->current_role === 'مشرف', function ($q, $v) {
                        $q->on('students.grade_id', '=', DB::raw("(select id from `grades` where `id` = '$this->grade_id' or `id` = '$this->selectedGradeId' LIMIT 1)"))
                            ->when($this->selectedTeacherId !== null && $this->current_group_type === Group::QURAN_TYPE || $this->current_group_type === Group::MONTADA_TYPE, function ($q) {
                                $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                            })
                            ->when($this->selectedTeacherId !== null && $this->current_group_type === Group::SUNNAH_TYPE, function ($q, $v) {
                                $q->on('students.group_sunnah_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                            });
                    })
                    ->when($this->current_role === User::SPONSORSHIP_SUPERVISORS_ROLE, function ($q, $v) {
                        $groups_ids = DB::table('sponsorship_groups')
                            ->select(['group_id'])
                            ->whereIn('sponsorship_id', auth()->user()->sponsorships->pluck('id')->toArray())
                            ->distinct()
                            ->pluck('group_id')->toArray();
                        $q->whereIn('students.group_id', $groups_ids)
                            ->when(!empty($this->selectedGradeId), function ($q, $v) {
                                $q->on('students.grade_id', '=', DB::raw("(select id from `grades` where `id` = '$this->selectedGradeId' LIMIT 1)"));
                            })->when(!empty($this->selectedTeacherId) && $this->current_group_type === Group::QURAN_TYPE || $this->current_group_type === Group::MONTADA_TYPE, function ($q) {
                                $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->selectedTeacherId' LIMIT 1)"));
                            });
                    })
                    ->when($this->current_role === 'محفظ' && $this->current_group_type === Group::QURAN_TYPE || $this->current_group_type === Group::MONTADA_TYPE, function ($q) {
                        $q->on('students.group_id', '=', DB::raw("(select id from `groups` where `id` = '$this->group_id' or `id` = '$this->selectedTeacherId' LIMIT 1)"));
                    })
                    ->when($this->current_role === 'محفظ' && $this->current_group_type === Group::SUNNAH_TYPE, function ($q, $v) {
                        $q->on('students.group_sunnah_id', '=', DB::raw("(select id from `groups` where `id` = '$this->group_id' or `id` = '$this->selectedTeacherId' LIMIT 1)"));
                    });
            })
            ->join('grades', 'students.grade_id', '=', 'grades.id')
            ->join('groups', 'students.group_id', '=', 'groups.id')
            ->leftJoin('users as teachers', 'groups.teacher_id', '=', 'teachers.id')
            ->leftJoin('groups as group_sun', 'students.group_sunnah_id', '=', 'group_sun.id')
            ->leftJoin('users as sunnah_teachers', 'group_sun.teacher_id', '=', 'sunnah_teachers.id')
            ->leftJoin('student_warnings', function ($join) {
                $join->on('students.id', '=', 'student_warnings.student_id')
                    ->on('student_warnings.id', '=', DB::raw("(SELECT id FROM student_warnings WHERE student_id = students.id
                              AND warning_expiry_date IS NULL order by `created_at` DESC LIMIT 1)"));
            })
            ->leftJoin('student_blocks', function ($join) {
                $join->on('students.id', '=', 'student_blocks.student_id')
                    ->on('student_blocks.id', '=', DB::raw("(SELECT id FROM student_blocks WHERE student_id = students.id
                              AND block_expiry_date IS NULL order by `created_at` DESC LIMIT 1)"));
            })
            ->leftJoin('exams as last_exam', function ($join) {
                $join->on('students.id', '=', 'last_exam.student_id')
                    ->on('last_exam.id', '=', DB::raw("(SELECT exams.id FROM exams JOIN exam_success_mark ON exam_success_mark_id = exam_success_mark.id AND exams.mark >= exam_success_mark.mark
                                  WHERE student_id = students.id ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('quran_parts as last_quran_part', 'last_exam.quran_part_id', '=', 'last_quran_part.id')
            ->groupBy(['students.id', 'student_warning_details', 'student_warning_reason', 'student_block_details', 'student_block_reason'])
            ->orderBy('students.' . $this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $this->studentSelectedIds = $students->pluck('id')->toArray();
        return $students;
    }

    public function submitSearch()
    {
        $this->all_Students();
    }


    public
    function messages()
    {
        return [
            'father_identification_number.required' => 'حقل رقم الهوية مطلوب',
            'father_identification_number.regex' => 'حقل رقم الهوية يجب أن يكون رقم',
            'father_identification_number.unique' => 'رقم الهوية المدخل موجود مسبقا',
            'father_identification_number.min' => 'يجب أن لا يقل طول رقم الهوية عن 9 أرقام',
            'father_identification_number.max' => 'يجب أن لا يزيد طول رقم الهوية عن 9 أرقام',
            'father_name.required' => 'الاسم رباعي مطلوب',
            'father_name.string' => 'الاسم رباعي يجب أن يكون نص',
            'father_name.unique' => 'الاسم رباعي موجود مسبقا',
            'father_gender.required' => 'جنس ولى الأمر مطلوب',
            'father_gender.string' => 'جنس ولى الأمر يجب أن يكون نص',
            'father_first_name.required' => 'حقل الإسم الأول مطلوب',
            'father_first_name.string' => 'يجب ادخال نص في حقل الاسم الأول',
            'father_second_name.required' => 'حقل اسم ولى الأمر مطلوب',
            'father_second_name.string' => 'يجب ادخال نص في حقل اسم ولى الأمر',
            'father_third_name.required' => 'حقل اسم الجد مطلوب',
            'father_third_name.string' => 'يجب ادخال نص في حقل اسم الجد',
            'father_last_name.required' => 'حقل اسم العائلة مطلوب',
            'father_last_name.string' => 'يجب ادخال نص في حقل اسم العائلة',
            'father_phone.required' => 'حقل رقم الجوال مطلوب',
            'father_phone.regex' => 'حقل رقم الجوال يجب أن يكون رقم',
            'father_phone.unique' => 'رقم الجوال المدخل موجود مسبقا',
            'father_phone.min' => 'يجب أن لا يقل طول رقم الجوال عن 10 أرقام',
            'father_phone.max' => 'يجب أن لا يزيد طول رقم الجوال عن 10 أرقام',
            'economic_situation.required' => 'الوضع المادي مطلوب',
            'student_identification_number.required' => 'حقل رقم الهوية مطلوب',
            'student_identification_number.regex' => 'حقل رقم الهوية يجب أن يكون رقم',
            'student_identification_number.unique' => 'رقم الهوية المدخل موجود مسبقا',
            'student_identification_number.min' => 'يجب أن لا يقل طول رقم الهوية عن 9 أرقام',
            'student_identification_number.max' => 'يجب أن لا يزيد طول رقم الهوية عن 9 أرقام',
            'student_name.required' => 'الاسم رباعي مطلوب',
            'student_name.string' => 'الاسم رباعي يجب أن يكون نص',
            'student_name.unique' => 'الاسم رباعي موجود مسبقا',
            'student_first_name.required' => 'حقل الإسم الأول مطلوب',
            'student_first_name.string' => 'يجب ادخال نص في حقل الاسم الأول',
            'student_second_name.required' => 'حقل اسم ولى الأمر مطلوب',
            'student_second_name.string' => 'يجب ادخال نص في حقل اسم ولى الأمر',
            'student_third_name.required' => 'حقل اسم الجد مطلوب',
            'student_third_name.string' => 'يجب ادخال نص في حقل اسم الجد',
            'student_last_name.required' => 'حقل اسم العائلة مطلوب',
            'student_last_name.string' => 'يجب ادخال نص في حقل اسم العائلة',
            'dob.required' => 'حقل تاريخ الميلاد مطلوب',
            'dob.date' => 'حقل تاريخ الميلاد يجب أن يكون تاريخ',
            'dob.date_format' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'group_id.required' => 'اسم الحلقة مطلوب',
            'country_code.required' => 'كود الدولة مطلوب',
            'country_code.string' => 'كود الدولة يجب أن يكون نص',
            'whatsapp_number.required' => 'حقل رقم الواتس اب مطلوب',
            'whatsapp_number.regex' => 'حقل رقم الواتس اب يجب أن يكون رقم',
            'whatsapp_number.min' => 'يجب أن لا يقل طول رقم الواتس اب عن 10 أرقام',
            'whatsapp_number.max' => 'يجب أن لا يزيد طول رقم الواتس اب عن 10 أرقام',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 1024 كيلو بايت',
            'photo.unique' => 'عذرا يوجد صورة بهذا الاسم مسبقا',
            'student_id.required' => 'حقل الطالب مطلوب',
        ];
    }

    public
    function export()
    {
        $students = DB::table('groups')
            ->select(['users_student.name as student_name',
                'users_student.identification_number as student_identification_number',
                'users_father.identification_number as father_identification_number',
                'users_father.phone as father_phone', 'students.whatsapp_number as student_whatsapp_number'
                , 'users_student.dob as student_dob', 'user_infos_father.economic_situation as economic_situation',
                'quran_part_count.total_preservation_parts',
                DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`"),
                DB::raw("(GROUP_CONCAT(sunnah_part_count.name,' (',sunnah_part_count.total_hadith_parts,') حديث')) as `sunnah_part_individual`"),
                DB::raw("(GROUP_CONCAT(sunnah_part_deserved.name,' (',sunnah_part_deserved.total_hadith_parts,') حديث')) as `sunnah_part_deserved`")])
            ->when($this->current_group_type == Group::QURAN_TYPE || $this->current_group_type == Group::MONTADA_TYPE, function ($q) {
                $q->join('students', 'students.group_id', '=', 'groups.id');
            })
            ->when($this->current_group_type == Group::SUNNAH_TYPE, function ($q) {
                $q->join('students', 'students.group_sunnah_id', '=', 'groups.id');
            })
            ->join('fathers', 'students.father_id', '=', 'fathers.id')
            ->join('users as users_student', 'students.id', '=', 'users_student.id')
            ->join('users as users_father', 'fathers.id', '=', 'users_father.id')
            ->join('user_infos as user_infos_father', 'users_father.id', '=', 'user_infos_father.id')
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
            ->where('groups.id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null)
            ->groupBy(['student_name', 'quran_part_count.total_preservation_parts', 'sunnah_part_count.total_hadith_parts'])
            ->get();
        $teacher_name = Teacher::with('user:id,name')->where('id', auth()->id())->first()->user->name;

        return (new GroupStudentsExport($students, $teacher_name))->download('Database of all ' . $teacher_name . ' students' . '.xlsx', Excel::XLSX);
    }

    public function export_selected_student()
    {
        if (!empty($this->studentSelectedIds)) {
            $students = DB::table('students')
                ->select(['users_student.name as student_name',
                    'users_student.identification_number as student_identification_number',
                    'users_father.identification_number as father_identification_number',
                    'users_father.phone as father_phone', 'users_student.dob as student_dob',
                    'user_infos_father.economic_situation as economic_situation',
                    'grades.name as grade_name',
                    'users_teacher.name as teacher_name',
                    'quran_part_count.total_preservation_parts',
                    DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                    DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`")])
                ->join('grades', 'students.grade_id', '=', 'grades.id')
                ->join('groups', 'students.group_id', '=', 'groups.id')
                ->join('fathers', 'students.father_id', '=', 'fathers.id')
                ->join('users as users_student', 'students.id', '=', 'users_student.id')
                ->join('users as users_father', 'fathers.id', '=', 'users_father.id')
                ->join('user_infos as user_infos_father', 'users_father.id', '=', 'user_infos_father.id')
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
                ->whereIn('students.id', $this->studentSelectedIds)
                ->groupBy(['student_name', 'quran_part_count.total_preservation_parts'])
                ->get();

            return (new SelectedStudentsExport($students))->download('Database of selected center students' . '.xlsx', Excel::XLSX);
        }
        return;
    }


    private function showStudentData($id)
    {
        $year = Date('Y');
        $month = Date('m');
        $this->student_id = $id;
        $this->student = DB::table('students')->select([DB::raw('user_stu.name student_name'),
            DB::raw('user_stu.gender student_gender'),
            DB::raw('user_stu.identification_number student_identification_number'), DB::raw('user_stu.dob'),
            DB::raw('students.whatsapp_number'), DB::raw('user_stu.profile_photo'),
            DB::raw('grades.name grade_name'), DB::raw('user_tea.name teacher_name'),
            DB::raw('user_tea_sunnah.name teacher_sunnah_name'), DB::raw('students.group_sunnah_id group_sunnah_id'),
            DB::raw('user_fat.name father_name'), DB::raw('user_fat.gender father_gender'), DB::raw('user_fat.identification_number father_identification_number')
            , DB::raw('user_fat.phone'), DB::raw("(SELECT SUM(number_pages) FROM students_daily_memorization
               WHERE student_id = students.id and month(datetime) = '$month' and year(datetime) = '$year' and type = 'memorize') AS 'number_memorize_pages'"), DB::raw("(SELECT SUM(number_pages) FROM students_daily_memorization
               WHERE student_id = students.id and month(datetime) = '$month' and year(datetime) = '$year' and type = 'review') AS 'number_review_pages'"), DB::raw("(SELECT COUNT(id) FROM student_attendances
               WHERE student_id = students.id and month(datetime) = '$month' and year(datetime) = '$year' and status = 'presence') AS 'number_presence_days'"), DB::raw("(SELECT COUNT(id) FROM student_attendances
               WHERE student_id = students.id and month(datetime) = '$month' and year(datetime) = '$year' and status = 'absence') AS 'number_absence_days'"), DB::raw('quran_part_count.total_preservation_parts total_preservation_parts')
            , DB::raw("(GROUP_CONCAT(last_quran_part.name,'-',last_quran_part.description)) last_quran_part"), DB::raw('last_exam.mark last_exam_mark'), DB::raw('exam_success_mark.mark exam_success_mark'), DB::raw('exam_imp.mark exam_improvement'),
            DB::raw('last_sunnah_exam.mark last_sunnah_exam_mark'), DB::raw('sunnah_success_mark.mark sunnah_exam_success_mark'),
            DB::raw('sunnah_exam_imp.mark sunnah_exam_improvement'), DB::raw('student_warnings.details as student_warning'),
            DB::raw('student_blocks.details as student_block'), DB::raw("GROUP_CONCAT(last_sunnah_part.name,' (',last_sunnah_part.total_hadith_parts,') حديث') last_sunnah_part"),
            DB::raw("(select count(id) from student_sunnah_attendances WHERE students.id = student_id and month(datetime) = '$month' and year(datetime) = '$year' AND status = 'presence') AS 'number_presence_days_sunnah'"),
            DB::raw("(select count(id) from student_sunnah_attendances WHERE students.id = student_id and month(datetime) = '$month' and year(datetime) = '$year' AND status = 'absence') AS 'number_absence_days_sunnah'"),
            DB::raw('sunnah_books.name book_name'), DB::raw('start_daily_memorization.hadith_from memorize_hadith_from'),
            DB::raw('end_daily_memorization.hadith_to memorize_hadith_to'), DB::raw('start_daily_review.hadith_from review_hadith_from'),
            DB::raw('end_daily_review.hadith_to review_hadith_to'),
        ])
            ->join('users as user_stu', 'students.id', '=', 'user_stu.id')
            ->join('groups', 'students.group_id', '=', 'groups.id')
            ->leftJoin('groups as group_sunnah', 'students.group_sunnah_id', '=', 'group_sunnah.id')
            ->join('grades', 'students.grade_id', '=', 'grades.id')
            ->join('users as user_tea', 'groups.teacher_id', '=', 'user_tea.id')
            ->leftJoin('users as user_tea_sunnah', 'group_sunnah.teacher_id', '=', 'user_tea_sunnah.id')
            ->join('users as user_fat', 'students.father_id', '=', 'user_fat.id')
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
            ->leftJoin('exams as last_exam', function ($join) {
                $join->on('students.id', '=', 'last_exam.student_id')
                    ->on('last_exam.id', '=', DB::raw("(SELECT exams.id FROM exams
                  WHERE student_id = students.id ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('quran_parts as last_quran_part', 'last_exam.quran_part_id', '=', 'last_quran_part.id')
            ->leftJoin('improvement_exams as exam_imp', 'last_exam.id', '=', 'exam_imp.id')
            ->leftJoin('exam_success_mark', 'last_exam.exam_success_mark_id', '=', 'exam_success_mark.id')
            ->leftJoin('student_warnings', function ($join) {
                $join->on('students.id', '=', 'student_warnings.student_id')
                    ->on('student_warnings.id', '=', DB::raw("(SELECT id FROM student_warnings WHERE student_id = students.id
                              AND warning_expiry_date IS NULL order by `created_at` DESC LIMIT 1)"));
            })
            ->leftJoin('student_blocks', function ($join) {
                $join->on('students.id', '=', 'student_blocks.student_id')
                    ->on('student_blocks.id', '=', DB::raw("(SELECT id FROM student_blocks WHERE student_id = students.id
                              AND block_expiry_date IS NULL order by `created_at` DESC LIMIT 1)"));
            })
            ->leftJoin('students_sunnah_daily_memorization as start_daily_memorization', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'start_daily_memorization.student_id')
                    ->on('start_daily_memorization.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='memorize' AND YEAR(datetime) = '$year' AND MONTH(datetime) = '$month' ORDER BY datetime ASC LIMIT 1)"));
            })
            ->leftJoin('students_sunnah_daily_memorization as end_daily_memorization', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'end_daily_memorization.student_id')
                    ->on('end_daily_memorization.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='memorize' AND YEAR(datetime) = '$year' AND MONTH(datetime) = '$month' ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('sunnah_books', 'start_daily_memorization.book_id', '=', 'sunnah_books.id')
            ->leftJoin('students_sunnah_daily_memorization as start_daily_review', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'start_daily_review.student_id')
                    ->on('start_daily_review.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='review' AND YEAR(datetime) = '$year' AND MONTH(datetime) = '$month' ORDER BY datetime ASC LIMIT 1)"));
            })
            ->leftJoin('students_sunnah_daily_memorization as end_daily_review', function ($join) use ($month, $year) {
                $join->on('students.id', '=', 'end_daily_review.student_id')
                    ->on('end_daily_review.id', '=', DB::raw("(SELECT id FROM students_sunnah_daily_memorization WHERE student_id = students.id
                   And type ='review' AND YEAR(datetime) = '$year' AND MONTH(datetime) = '$month' ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('sunnah_exams as last_sunnah_exam', function ($join) {
                $join->on('students.id', '=', 'last_sunnah_exam.student_id')
                    ->on('last_sunnah_exam.id', '=', DB::raw("(SELECT sunnah_exams.id FROM sunnah_exams
                  WHERE student_id = students.id ORDER BY datetime DESC LIMIT 1)"));
            })
            ->leftJoin('sunnah_parts as last_sunnah_part', 'last_sunnah_exam.sunnah_part_id', '=', 'last_sunnah_part.id')
            ->leftJoin('sunnah_improvement_exams as sunnah_exam_imp', 'last_sunnah_exam.id', '=', 'sunnah_exam_imp.id')
            ->leftJoin('exam_success_mark as sunnah_success_mark', 'last_sunnah_exam.exam_success_mark_id', '=', 'sunnah_success_mark.id')
            ->where('students.id', '=', $id)
            ->groupBy(['student_name', 'total_preservation_parts', 'last_exam_mark', 'exam_success_mark',
                'exam_improvement', 'student_warning', 'student_block', 'last_sunnah_exam_mark',
                'sunnah_exam_success_mark', 'sunnah_exam_improvement', 'book_name', 'memorize_hadith_from',
                'memorize_hadith_to', 'review_hadith_from', 'review_hadith_to'])
            ->get();

        $this->student_exams = DB::table('exams')
            ->select([DB::raw('user_stu.name student_name'),
                DB::raw("(GROUP_CONCAT(quran_part.name,'-',quran_part.description)) quran_part_name"),
                DB::raw('exams.mark'), DB::raw('user_tea.name teacher_name'),
                DB::raw('user_tes.name tester_name'), DB::raw('exams.datetime'),
                DB::raw('exams.notes'), DB::raw('exam_success_mark.mark exam_success_mark'), DB::raw('exam_imp.mark exam_improvement')])
            ->join('users as user_stu', 'exams.student_id', '=', 'user_stu.id')
            ->join('users as user_tea', 'exams.teacher_id', '=', 'user_tea.id')
            ->join('users as user_tes', 'exams.tester_id', '=', 'user_tes.id')
            ->join('quran_parts as quran_part', 'exams.quran_part_id', '=', 'quran_part.id')
            ->join('exam_success_mark', 'exams.exam_success_mark_id', '=', 'exam_success_mark.id')
            ->leftJoin('improvement_exams as exam_imp', 'exams.id', '=', 'exam_imp.id')
            ->where('exams.student_id', '=', $id)
            ->groupBy(['student_name', 'exams.mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark.mark', 'exam_improvement'])
            ->get();

        $this->student_sunnah_exams = DB::table('sunnah_exams')
            ->select([DB::raw('user_stu.name student_name'),
                DB::raw("GROUP_CONCAT(sunnah_part.name,' (',sunnah_part.total_hadith_parts,') حديث') sunnah_part_name"),
                DB::raw('sunnah_exams.mark'), DB::raw('user_tea.name teacher_name'),
                DB::raw('user_tes.name tester_name'), DB::raw('sunnah_exams.datetime'),
                DB::raw('sunnah_exams.notes'), DB::raw('exam_success_mark.mark exam_success_mark'), DB::raw('exam_imp.mark exam_improvement')])
            ->join('users as user_stu', 'sunnah_exams.student_id', '=', 'user_stu.id')
            ->join('users as user_tea', 'sunnah_exams.teacher_id', '=', 'user_tea.id')
            ->join('users as user_tes', 'sunnah_exams.tester_id', '=', 'user_tes.id')
            ->join('sunnah_parts as sunnah_part', 'sunnah_exams.sunnah_part_id', '=', 'sunnah_part.id')
            ->join('exam_success_mark', 'sunnah_exams.exam_success_mark_id', '=', 'exam_success_mark.id')
            ->leftJoin('improvement_exams as exam_imp', 'sunnah_exams.id', '=', 'exam_imp.id')
            ->where('sunnah_exams.student_id', '=', $id)
            ->groupBy(['student_name', 'sunnah_exams.mark', 'teacher_name', 'tester_name', 'datetime', 'notes', 'exam_success_mark.mark', 'exam_improvement'])
            ->get();
    }


}
