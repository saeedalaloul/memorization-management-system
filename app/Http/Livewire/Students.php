<?php

namespace App\Http\Livewire;

use App\Exports\GroupStudentsExport;
use App\Models\Exam;
use App\Models\ExamOrder;
use App\Models\ExamSettings;
use App\Models\Father;
use App\Models\Grade;
use App\Models\Group;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\StudentBlock;
use App\Models\StudentDailyMemorization;
use App\Models\StudentWarning;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\ExpiredStudentBlockForTeacherNotify;
use App\Notifications\ExpiredStudentWarningForTeacherNotify;
use App\Notifications\NewExamOrderForExamsSupervisorNotify;
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

    public $photo, $photo_ret, $quran_parts = [], $groups = [], $grades = [], $warning_cancel_notes,
        $block_cancel_notes, $reset_data_daily_memorization_type, $message_warning_reset_data,
        $last_quran_part_id;


    public $currentStep = 1, $father_id, $student_id, $quran_part_id,

        // Father_INPUTS
        $father_identification_number, $father_name, $father_phone, $economic_situation,

        // Student_INPUTS
        $student_identification_number, $student_name, $dob, $grade_id, $group_id;

    public $selectedGradeId, $selectedTeacherId;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }


    public function render()
    {
        return view('livewire.students', ['students' => $this->all_Students(),]);
    }

    public function fatherFound()
    {
        if (strlen($this->father_identification_number) == 9) {
            $father = Father::whereHas('user', function ($q) {
                return $q->select('*')->where('identification_number', '=', $this->father_identification_number);
            })->first();
            if ($father) {
                $this->clearForm();
                $this->father_identification_number = $father->user->identification_number;
                $this->father_id = $father->id;
                $this->father_name = $father->user->name;
                $this->father_phone = $father->user->phone;
                if ($father->user->user_info != null) {
                    $this->economic_situation = $father->user->user_info->economic_situation ?? null;
                }
                $this->successMessage = 'لقد تم اعتماد معلومات الأب من خلال رقم الهوية المدخل مسبقا في النظام, يرجى متابعة إدخال باقي البيانات أو تعديل رقم الهوية المدخل...';
                $this->resetValidation(['father_identification_number', 'father_name', 'father_phone', 'economic_situation']);
            } else {
                $this->father_id = null;
                $this->successMessage = '';
            }
        } else {
            $this->father_id = null;
            $this->successMessage = '';
        }
    }


    public function requestExam($id)
    {
        $this->getStudent($id);

        $this->checkLastExamStatus();
    }

    public function getStudent($id)
    {
        $this->clearForm();
        $student = Student::where('id', $id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
    }


    public function checkLastExamStatus()
    {
        $exam = Exam::where('student_id', $this->student_id)
            ->whereHas('quranPart', function ($q) {
                $q->orderBy('arrangement', 'desc');
            })
            ->orderBy('datetime', 'desc')->first();

        if ($exam) {
            if ($exam->mark >= $exam->examSuccessMark->mark) {
                $this->all_Quran_Parts($exam->quranPart->arrangement, true);
                $this->dispatchBrowserEvent('showDialog');
            } else {
                $to = Carbon::createFromFormat('Y-m-d', date('Y-m-d', Carbon::now()->timestamp));
                $from = Carbon::createFromFormat('Y-m-d', Carbon::parse($exam->datetime)->format('Y-m-d'));
                $diff_in_days = $to->diffInDays($from);
                $number_days_exam = ExamSettings::find(1)->number_days_exam;
                $days = ($diff_in_days - $number_days_exam);
                if ($days > 0) {
                    $this->all_Quran_Parts($exam->quran_part_id, false);
                    $this->dispatchBrowserEvent('showDialog');
                } else {
                    if (abs($days) == 0) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب يوم حتى تتمكن من طلب اختبار جديد.']);
                    } else if (abs($days) == 1) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب يومان حتى تتمكن من طلب اختبار جديد.']);
                    } else if (abs($days) == 2) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب ثلاث أيام حتى تتمكن من طلب اختبار جديد.']);
                    } else if (in_array(abs($days), range(3, 10))) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب ' . abs($days) . ' أيام حتى تتمكن من طلب اختبار جديد']);
                    } else if (in_array(abs($days), range(11, 15))) {
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'error', 'message' => 'عذرا متبقي لهذا الطالب ' . abs($days) . ' يوم حتى تتمكن من طلب اختبار جديد']);
                    }
                }
            }
        } else {
            $this->dispatchBrowserEvent('showDialog');
            $this->all_Quran_Parts(null, null);
        }
    }

    //firstStepSubmit

    public function firstStepSubmit()
    {
        $this->validate(
            [
                'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->father_id,
                'father_name' => 'required|string|unique:users,name,' . $this->father_id,
                'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
                'economic_situation' => 'required',
            ]);

        $this->currentStep = 2;
    }

    //secondStepSubmit_edit

    public function secondStepSubmit()
    {
        $this->validate([
            'student_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->student_id,
            'student_name' => 'required|string|unique:users,name,' . $this->student_id,
            'dob' => 'required|date|date_format:Y-m-d',
            'grade_id' => 'required',
            'group_id' => 'required',
        ]);

        $this->currentStep = 3;
    }

    public function submitForm()
    {
        DB::beginTransaction();

        try {
            $userFather = null;
            // حفظ بيانات الأب في جدول المستخدمين
            if (!$this->father_id) {
                $userFather = User::create([
                    'name' => $this->father_name,
                    'phone' => $this->father_phone,
                    'identification_number' => $this->father_identification_number,
                ]);
            }

            // حفظ بيانات الإبن في جدول المستخدمين
            $userStudent = User::create([
                'name' => $this->student_name,
                'identification_number' => $this->student_identification_number,
                'dob' => $this->dob,
            ]);

            // حفظ البيانات في جدول الأباء
            $retFather = Father::find($this->father_id);

            if (is_null($retFather)) {
                if (!is_null($userFather)) {
                    $retFather = Father::create(['id' => $userFather->id]);
                    $userFather->user_info()->create($this->modelUserInfo($userFather->id));
                }
            }


            // حفظ البيانات في جدول الطلاب
            Student::create([
                'id' => $userStudent->id,
                'father_id' => is_null($userFather) ? $this->father_id : $userFather->id,
                'grade_id' => $this->grade_id,
                'group_id' => $this->group_id,
            ]);

            $roleId = Role::select('*')->where('name', '=', 'طالب')->get();
            $userStudent->assignRole([$roleId]);

            $roleId = Role::select('*')->where('name', '=', 'ولي أمر الطالب')->get();
            if ($userFather != null) {
                $userFather->assignRole([$roleId]);
            } else {
                $retFather->user->assignRole([$roleId]);
            }

            if (!empty($this->photo)) {
                $this->uploadImage($this->photo,
                    $this->student_identification_number . '.' . $this->photo->getClientOriginalExtension(),
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
        $this->father_phone = null;
        $this->economic_situation = null;
        $this->father_identification_number = null;

        // clear inputs student
        $this->student_id = null;
        $this->student_name = null;
        $this->student_identification_number = null;
        $this->dob = null;
        $this->grade_id = null;
        $this->group_id = null;
        $this->photo = null;
        $this->photo_ret = null;
        $this->groups = [];
        $this->currentStep = 1;
        $this->process_type = '';

        // clear inputs submit exam request
        $this->student_id = null;
        $this->quran_parts = null;
        $this->quran_part_id = null;
        $this->catchError = '';
        $this->successMessage = '';
        $this->warning_cancel_notes = null;
        $this->block_cancel_notes = null;
        $this->last_quran_part_id = null;
        $this->reset_data_daily_memorization_type = null;
        $this->message_warning_reset_data = null;
        $this->resetValidation();
    }

    public function process_data($id, $process_type)
    {
        $this->clearForm();
        $this->process_type = $process_type;
        if ($process_type == 'edit') {
            $this->edit($id);
        } else if ($process_type == 'reset') {
            $this->reset_data_daily_memorization($id);
        } else {
            $this->student_id = $id;
        }
    }

    public function reset_data_daily_memorization($id)
    {
        $this->resetValidation();
        $this->reset_data_daily_memorization_type = 0;
        $student = Student::where('id', $id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
        $this->dispatchBrowserEvent('showDialogDailyMemorization');
    }

    public function updatedResetDataDailyMemorizationType($type)
    {
        $dailyMemorization = StudentDailyMemorization::query()
            ->with(['quranSuraTo.quranPart'])
            ->where('student_id', $this->student_id)
            ->where('type', '=', 1)
            ->orderByDesc('datetime')->first();

        if ($type == 1) {
            // تصفير البيانات لبداية الجزء الحالي.
            if ($dailyMemorization != null) {
                $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة لسور الجزء الحالي " . $dailyMemorization->quranSuraTo->quranPart->description;
                $this->last_quran_part_id = $dailyMemorization->quranSuraTo->quranPart->id;
            } else {
                $this->message_warning_reset_data = "لا بيانات سابقة";
            }
        } elseif ($type == 2) {
            // تصفير جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب.

            $exam = Exam::where('student_id', $this->student_id)->orderBy('datetime', 'desc')->first();

            if ($exam != null) {
                if ($exam->mark >= $exam->examSuccessMark->mark) {
                    $countOfPartQuran = $exam->quranPart->total_memorization_parts;
                } else {
                    $countOfPartQuran = $exam->quranPart->total_memorization_parts - 1;
                }

                if ($countOfPartQuran == 0) {
                    $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب.";
                } elseif ($countOfPartQuran == 1) {
                    $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب والبالغ عددها اختبار واحد.";
                } else if (in_array($countOfPartQuran, range(2, 30))) {
                    $this->message_warning_reset_data = "سيتم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات أنجزها الطالب والبالغ عددها" . " ($countOfPartQuran) " . "اختبار.";
                }
            } else {
                if ($dailyMemorization == null) {
                    $this->message_warning_reset_data = "لا اختبارات أو بيانات سابقة";
                }
            }
        }
    }

    public function reset_daily_memorization()
    {
        if ($this->student_id != null && $this->last_quran_part_id != null) {
            $student = Student::find($this->student_id);
            if ($this->reset_data_daily_memorization_type == 1) {
                $student->daily_memorization()->whereHas('quranSuraFrom', function ($q) {
                    $q->where('quran_part_id', $this->last_quran_part_id);
                })->whereHas('quranSuraTo', function ($q) {
                    $q->where('quran_part_id', $this->last_quran_part_id);
                })->delete();
                $this->dispatchBrowserEvent('hideDialog');
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم حذف بيانات الحفظ والمراجعة للجزء الحالي للطالب بنجاح.']);
            } else if ($this->reset_data_daily_memorization_type == 2) {
                $student->daily_memorization()->delete(); // حذف جميع بيانات الحفظ والمراجعة.
                $student->exam_order()->delete(); // حذف جميع طلبات الإختبارات للطالب.
                $student->exams()->delete(); // حذف جميع اختبارات الطالب.

                $this->dispatchBrowserEvent('hideDialog');
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم حذف جميع بيانات الحفظ والمراجعة وأي اختبارات قرآنية أنجزها الطالب بنجاح.']);
            }
        }
    }

    public function edit($id)
    {
        $this->resetValidation();
        $student = Student::with(['user', 'father.user.user_info'])->where('id', $id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
        $this->student_identification_number = $student->user->identification_number;
        $this->photo_ret = $student->user->profile_photo_url;
        $this->dob = $student->user->dob;
        $this->grade_id = $student->grade_id;
        $this->father_id = $student->father_id;

        $this->father_name = $student->father->user->name;
        $this->father_phone = $student->father->user->phone;
        $this->father_identification_number = $student->father->user->identification_number;
        if ($student->father->user->user_info != null) {
            $this->economic_situation = $student->father->user->user_info->economic_situation ?? null;
        }
        $this->getTeachersByGradeId();
        $this->group_id = $student->group_id;
    }


    //back

    public function firstStepSubmit_edit()
    {
        $this->validate([
            'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->father_id,
            'father_name' => 'required|string|unique:users,name,' . $this->father_id,
            'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
            'economic_situation' => 'required',
        ]);

        $this->currentStep = 2;
    }

    public function secondStepSubmit_edit()
    {
        $this->validate([
            'student_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->student_id,
            'student_name' => 'required|string|unique:users,name,' . $this->student_id,
            'dob' => 'required|date|date_format:Y-m-d',
            'grade_id' => 'required',
            'group_id' => 'required',
        ]);

        $this->currentStep = 3;
    }

    public function submitForm_edit()
    {
        if ($this->student_id) {
            $isUpdated = true;
            $student = Student::find($this->student_id);
            if ($student->group_id != $this->group_id) {
                if ($student->exam_order->count() > 0) {
                    $isUpdated = false;
                    $this->catchError = "عذرا يوجد طلبات اختبارات لهذا الطالب مسجلة باسم محفظ الحلقة يجب إجرائها أو حذفها حتى تتمكن من تحديث الطالب";
                }
            }

            if ($isUpdated) {
                $student->user->update([
                    'name' => $this->student_name,
                    'identification_number' => $this->student_identification_number,
                    'dob' => $this->dob,
                ]);
                $student->update([
                    'grade_id' => $this->grade_id,
                    'group_id' => $this->group_id,
                ]);

                $roleId = Role::select('*')->where('name', '=', 'طالب')->get();
                $student->user->assignRole([$roleId]);

                if (!empty($this->photo)) {
                    $this->deleteImage($student->user->profile_photo);
                    $this->uploadImage($this->photo,
                        $this->student_identification_number . '.' . $this->photo->getClientOriginalExtension(),
                        $this->student_id);
                }

//                if ($this->father_id) {
//                    $father = Father::find($this->father_id);
//                    $father->user->update([
//                        'name' => $this->father_name,
//                        'phone' => $this->father_phone,
//                        'identification_number' => $this->father_identification_number,
//                    ]);
//
//                    $roleId = Role::select('*')->where('name', '=', 'ولي أمر الطالب')->get();
//                    $father->user->assignRole([$roleId]);
//                }
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم تحديث معلومات الطالب بنجاح.']);
                $this->clearForm();
            }
        }
    }

    public function delete($id)
    {
        Father::findOrFail($id)->delete();
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => 'تم حذف معلومات الطالب بنجاح.']);
    }

    public function back($step)
    {
        $this->currentStep = $step;
    }

    public function submitExamRequest()
    {
        $this->validate([
            'quran_part_id' => 'required',
            'student_id' => 'required|unique:exam_orders,student_id,',
        ]);

        $teacher_id = null;
        if ($this->current_role == 'أمير المركز') {
            $teacher_id = Student::with(['group'])->where('id', $this->student_id)->first()->group->teacher_id;
        } else if ($this->current_role == 'محفظ') {
            $teacher_id = auth()->id();
        }

        if ($teacher_id != null) {
            $examOrder = ExamOrder::create([
                'quran_part_id' => $this->quran_part_id,
                'student_id' => $this->student_id,
                'teacher_id' => $teacher_id,
                'user_signature_id' => auth()->id(),
                'status' => ExamOrder::IN_PENDING_STATUS,
                'type' => ExamOrder::NEW_TYPE,
            ]);

            // start push notifications to exams supervisor
            $role = Role::where('name', User::EXAMS_SUPERVISOR_ROLE)->first();
            $role_users = $role->users();
            if ($role_users->first()) {
                $role_users->first()->notify(new NewExamOrderForExamsSupervisorNotify($examOrder));
                $title = "طلب اختبار جديد";
                $message = "لقد قام المحفظ: " . $examOrder->teacher->user->name . " بطلب اختبار جديد للطالب " . $examOrder->student->user->name . " في الجزء: " . $examOrder->quranPart->name . ' ' . $examOrder->quranPart->description;
                $this->push_notification($message, $title, [$role_users->first()->user_fcm_token->device_token]);
            }
            // end push notifications to exams supervisor

            $this->dispatchBrowserEvent('hideDialog');

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية طلب الإختبار بنجاح.']);
            $this->clearForm();
        }
    }

    public function warningCancel()
    {
        $this->validate(['warning_cancel_notes' => 'required|string'], ['warning_cancel_notes.required' => 'حقل الملاحظات مطلوب',
            'warning_cancel_notes.string' => 'حقل الملاحظات يجب أن يكون نص']);

        $studentWarningCount = null;

        if ($this->current_role == 'مشرف') {
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

            if ($studentWarning != null) {
                $studentWarning->update(['warning_expiry_date' => Date('Y-m-d'), 'notes' => $this->warning_cancel_notes,]);
                // start push notifications to teacher
                $studentWarning->student->group->teacher->user->notify(new ExpiredStudentWarningForTeacherNotify($studentWarning));
                $title = "إلغاء إنذار جديد";
                $message = "";
                if ($this->current_role == "أمير المركز") {
                    $message = "لقد قام أمير المركز بإلغاء إنذار الطالب: " . $studentWarning->student->user->name . " وأرفق الملاحظة التالية: " . $studentWarning->notes;
                } elseif ($this->current_role == "مشرف") {
                    $message = "لقد قام مشرف المرحلة بإلغاء إنذار الطالب: " . $studentWarning->student->user->name;
                }
                $this->push_notification($message, $title, [$studentWarning->student->group->teacher->user->user_fcm_token->device_token]);
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

        if ($studentBlock != null) {
            $studentBlock->update(['block_expiry_date' => Date('Y-m-d'), 'notes' => $this->block_cancel_notes,]);
            // start push notifications to teacher
            $studentBlock->student->group->teacher->user->notify(new ExpiredStudentBlockForTeacherNotify($studentBlock));
            $title = "إلغاء حظر جديد";
            $message = "لقد قام أمير المركز بإلغاء حظر الطالب: " . $studentBlock->student->user->name . " وأرفق الملاحظة التالية: " . $studentBlock->notes;
            $this->push_notification($message, $title, [$studentBlock->student->group->teacher->user->user_fcm_token->device_token]);
            // end push notifications to teacher
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم فك حظر الطالب بنجاح.']);
            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();
        }
    }

    public
    function setMessage($message)
    {
        $this->catchError = $message;
    }

    public
    function all_Grades()
    {
        if ($this->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->grade_id)->get();
        } else if ($this->current_role == 'محفظ') {
            $this->group_id = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public
    function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId', 'group_id');
        $grade_id = null;
        if ($this->selectedGradeId != null) {
            $grade_id = $this->selectedGradeId;
        } elseif ($this->grade_id != null) {
            $grade_id = $this->grade_id;
        }

        if ($this->current_role == 'محفظ') {
            $this->groups = Group::query()->with(['teacher.user'])->where('teacher_id', auth()->id())->get();
        } else {
            if ($grade_id != null) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $grade_id)->get();
            }
        }
    }

    public
    function all_Students()
    {
        return Student::query()
            ->with(['user', 'group', 'grade', 'student_is_warning', 'student_is_block'])
            ->search($this->search)
            ->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null);
            })
            ->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->where('grade_id', '=', Supervisor::find(auth()->id())->first()->grade_id);
            })
            ->when($this->current_role == 'أمير المركز', function ($q, $v) {
                $q->when($this->selectedGradeId != null, function ($q, $v) {
                    $q->where('grade_id', '=', $this->selectedGradeId);
                })->when($this->selectedTeacherId != null, function ($q, $v) {
                    $q->where('group_id', '=', $this->selectedTeacherId);
                });
            })
            ->when(!empty(intval(\Request::segment(2))), function ($q, $v) {
                $q->where('id', \Request::segment(2));
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

//        DB::table('students')
//            ->search($this->search)
//            ->when($this->current_role == 'محفظ', function ($q, $v) {
//                $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null);
//            })
//            ->when($this->current_role == 'مشرف', function ($q, $v) {
//                $q->where('grade_id', '=', Supervisor::find(auth()->id())->first()->grade_id);
//            })
//            ->when($this->current_role == 'أمير المركز', function ($q, $v) {
//                $q->when($this->selectedGradeId != null, function ($q, $v) {
//                    $q->where('grade_id', '=', $this->selectedGradeId);
//                })->when($this->selectedTeacherId != null, function ($q, $v) {
//                    $q->where('group_id', '=', $this->selectedTeacherId);
//                });
//            })->
//            select(['users.name as student_name', 'users.identification_number as student_identification_number',
//                'users.dob as dob', 'grades.name as grade_name', 'groups.name as group_name',
//                'student_warnings.details as student_warning', 'student_blocks.details as student_block'])
//            ->join('users', 'students.id', '=', 'users.id')
//            ->join('groups', 'students.group_id', '=', 'groups.id')
//            ->join('grades', 'students.grade_id', '=', 'grades.id')
//            ->leftJoin('student_warnings', function ($join) {
//                $join->on('students.id', '=', 'student_warnings.student_id')
//                    ->on('student_warnings.id', '=', DB::raw("(SELECT id FROM student_warnings WHERE student_id = students.id
//                              AND warning_expiry_date IS NULL order by `created_at` DESC LIMIT 1)"));
//            })
//            ->leftJoin('student_blocks', function ($join) {
//                $join->on('students.id', '=', 'student_blocks.student_id')
//                    ->on('student_blocks.id', '=', DB::raw("(SELECT id FROM student_blocks WHERE student_id = students.id
//                              AND block_expiry_date IS NULL order by `created_at` DESC LIMIT 1)"));
//            })
//            ->orderBy('students.' . $this->sortBy, $this->sortDirection)
//            ->paginate($this->perPage)
    }

    public function submitSearch()
    {
        $this->all_Students();
    }


    public function all_Quran_Parts($arrangement, $isSuccess)
    {
        if ($arrangement != null) {
            if ($isSuccess) {
                $this->quran_parts = QuranPart::query()->orderBy('arrangement')->whereIn('arrangement', [$arrangement + 1, $arrangement + 2, $arrangement + 3])->get();
                if ($this->quran_parts[0]->type == QuranPart::INDIVIDUAL_TYPE) {
                    // إذا كان الاختبار التالي منفرد فسيتم عرض الأول
                    unset($this->quran_parts[1], $this->quran_parts[2]);
                } else if ($this->quran_parts[0]->type == QuranPart::DESERVED_TYPE && $this->quran_parts[1]->type == QuranPart::INDIVIDUAL_TYPE) {
                    // إذا كان الاختبار التالي (الأول) تجميعي والثاني منفرد فسيتم عرض الأول والثاني
                    unset($this->quran_parts[2]);
                } else if ($this->quran_parts[0]->type == QuranPart::DESERVED_TYPE && $this->quran_parts[1]->type == QuranPart::DESERVED_TYPE) {
                    // إذا كان الاختبار التالي (الأول) تجميعي والثاني تجميعي فسيتم عرض الأول والثالث
                    unset($this->quran_parts[1]);
                } else if ($this->quran_parts[1]->type == QuranPart::DESERVED_TYPE && $this->quran_parts[2]->type == QuranPart::INDIVIDUAL_TYPE) {
                    // إذا كان الاختبار التالي (الثاني) تجميعي والثالث منفرد فسيتم عرض الثاني والثالث
                    unset($this->quran_parts[0]);
                }
            } else {
                $this->quran_parts = QuranPart::query()->where('id', $arrangement)->get();
            }
        } else {
            $this->quran_parts = QuranPart::query()->orderBy('arrangement')->get();
        }
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
            'father_name.required' => 'حقل الاسم مطلوب',
            'father_name.string' => 'يجب ادخال نص في حقل الاسم',
            'father_name.unique' => 'الاسم المدخل موجود مسبقا',
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
            'student_name.required' => 'حقل الاسم مطلوب',
            'student_name.string' => 'يجب ادخال نص في حقل الاسم',
            'student_name.unique' => 'الاسم المدخل موجود مسبقا',
            'dob.required' => 'حقل تاريخ الميلاد مطلوب',
            'dob.date' => 'حقل تاريخ الميلاد يجب أن يكون تاريخ',
            'dob.date_format' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'group_id.required' => 'اسم الحلقة مطلوب',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 2048 كيلو بايت',
            'student_id.required' => 'حقل الطالب مطلوب',
            'student_id.unique' => 'عذرا يوجد طلب مسبق لهذا الطالب',
            'quran_part_id.required' => 'حقل الجزء مطلوب',
        ];
    }

    public
    function export()
    {
        $students = DB::table('groups')
            ->select(['users_student.name as student_name',
                'users_student.identification_number as student_identification_number',
                'users_father.identification_number as father_identification_number',
                'users_father.phone as father_phone', 'users_student.dob as student_dob',
                'users_info_father.economic_situation as economic_situation',
                'quran_part_count.total_preservation_parts',
                DB::raw("(GROUP_CONCAT(quran_part_count.name,' ',quran_part_count.description SEPARATOR '')) as `quran_part_individual`"),
                DB::raw("(GROUP_CONCAT(part_deserved.name,' ',part_deserved.description SEPARATOR '')) as `quran_part_deserved`")])
            ->join('students', 'students.group_id', '=', 'groups.id')
            ->join('fathers', 'students.father_id', '=', 'fathers.id')
            ->join('users as users_student', 'students.id', '=', 'users_student.id')
            ->join('users as users_father', 'fathers.id', '=', 'users_father.id')
            ->join('users_info as users_info_father', 'users_father.id', '=', 'users_info_father.id')
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
            ->where('groups.id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null)
            ->groupBy(['student_name', 'quran_part_count.total_preservation_parts'])
            ->get();
        $teacher_name = Teacher::with('user:id,name')->where('id', auth()->id())->first()->user->name;

        return (new GroupStudentsExport($students, $teacher_name))->download('Database of all ' . $teacher_name . ' students' . '.xlsx', Excel::XLSX);
    }


}
