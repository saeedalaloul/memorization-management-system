<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\ExamOrder;
use App\Models\ExamSettings;
use App\Models\Father;
use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Students extends Component
{
    use WithFileUploads, WithPagination;

    public $successMessage = '';

    public $catchError, $updateMode = false, $isFoundFather = false,
        $photo, $show_table = true, $quran_parts, $groups, $grades;

    public $sortBy = 'id', $sortDirection = 'asc', $perPage = 10, $search = '';

    public $currentStep = 1, $father_id, $student_id, $quran_part_id,

        // Father_INPUTS
        $father_identification_number, $father_name,
        $father_phone, $father_password, $father_address,
        $father_dob, $father_email,

        // Student_INPUTS
        $student_identification_number, $student_name,
        $student_email, $student_password, $student_phone,
        $dob, $grade_id, $group_id, $student_address;

    public $searchGradeId, $searchGroupId;
    protected $paginationTheme = 'bootstrap';

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->father_id,
            'father_name' => 'required|string|unique:users,name,' . $this->father_id,
            'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
            'father_email' => 'required|email|unique:users,email,' . $this->father_id,
            'father_password' => 'required|min:8|max:10',
            'father_dob' => 'required|date|date_format:Y-m-d',
            'father_address' => 'required',
///////////////////////////
            'student_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->student_id,
            'student_name' => 'required|string|unique:users,name,' . $this->student_id,
            'student_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->student_id,
            'student_email' => 'required|email|unique:users,email,' . $this->student_id,
            'student_password' => 'required|min:8|max:10',
            'dob' => 'required|date|date_format:Y-m-d',
            'grade_id' => 'required',
            'group_id' => 'required',
            'student_address' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
            ////////////////////////////////////
            'quran_part_id' => 'required',
            'student_id' => 'required|unique:exams_orders,student_id,',
        ]);
    }

    public function messages()
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
            'father_email.required' => 'حقل البريد الإلكتروني مطلوب',
            'father_email.email' => 'يجب ادخال بريد الكتروني صالح',
            'father_email.unique' => 'البريد الإلكتروني المدخل موجود مسبقا',
            'father_password.required' => 'حقل كلمة المرور مطلوب',
            'father_password.min' => 'يجب أن لا يقل طول كلمة المرور عن 8 حروف',
            'father_password.max' => 'يجب أن لا يزيد طول كلمة المرور عن 10 حروف',
            'father_dob.required' => 'حقل تاريخ الميلاد مطلوب',
            'father_dob.date' => 'حقل تاريخ الميلاد يجب أن يكون تاريخ',
            'father_dob.date_format' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'father_address.required' => 'حقل العنوان مطلوب',
            'student_identification_number.required' => 'حقل رقم الهوية مطلوب',
            'student_identification_number.regex' => 'حقل رقم الهوية يجب أن يكون رقم',
            'student_identification_number.unique' => 'رقم الهوية المدخل موجود مسبقا',
            'student_identification_number.min' => 'يجب أن لا يقل طول رقم الهوية عن 9 أرقام',
            'student_identification_number.max' => 'يجب أن لا يزيد طول رقم الهوية عن 9 أرقام',
            'student_name.required' => 'حقل الاسم مطلوب',
            'student_name.string' => 'يجب ادخال نص في حقل الاسم',
            'student_name.unique' => 'الاسم المدخل موجود مسبقا',
            'student_phone.required' => 'حقل رقم الجوال مطلوب',
            'student_phone.regex' => 'حقل رقم الجوال يجب أن يكون رقم',
            'student_phone.unique' => 'رقم الجوال المدخل موجود مسبقا',
            'student_phone.min' => 'يجب أن لا يقل طول رقم الجوال عن 10 أرقام',
            'student_phone.max' => 'يجب أن لا يزيد طول رقم الجوال عن 10 أرقام',
            'student_email.required' => 'حقل البريد الإلكتروني مطلوب',
            'student_email.email' => 'يجب ادخال بريد الكتروني صالح',
            'student_email.unique' => 'البريد الإلكتروني المدخل موجود مسبقا',
            'student_password.required' => 'حقل كلمة المرور مطلوب',
            'student_password.min' => 'يجب أن لا يقل طول كلمة المرور عن 8 حروف',
            'student_password.max' => 'يجب أن لا يزيد طول كلمة المرور عن 10 حروف',
            'dob.required' => 'حقل تاريخ الميلاد مطلوب',
            'dob.date' => 'حقل تاريخ الميلاد يجب أن يكون تاريخ',
            'dob.date_format' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'student_address.required' => 'حقل العنوان مطلوب',
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'group_id.required' => 'اسم الحلقة مطلوب',
            'address.required' => 'حقل عنوان الطالب مطلوب',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 2048 كيلو بايت',
            'student_id.required' => 'حقل الطالب مطلوب',
            'student_id.unique' => 'عذرا يوجد طلب مسبق لهذا الطالب',
            'quran_part_id.required' => 'حقل الجزء مطلوب',
        ];
    }

    public function render()
    {
        $this->all_Grades();
        $this->all_Groups();

        if (!$this->updateMode) {
            if (strlen($this->father_identification_number) == 9) {
                $father = Father::whereHas('user', function ($q) {
                    return $q->select('*')->where('identification_number', '=', $this->father_identification_number);
                })->first();
                if (!is_null($father)) {
                    $this->father_id = $father->id;
                    $this->father_name = $father->user->name;
                    $this->father_phone = $father->user->phone;
                    $this->father_address = $father->user->address;
                    $this->father_email = $father->user->email;
                    $this->father_dob = $father->user->dob;
                    $this->isFoundFather = true;
                    $this->successMessage = 'لقد تم اعتماد معلومات الأب من خلال رقم الهوية المدخل مسبقا في النظام, يرجى متابعة إدخال باقي البيانات أو تعديل رقم الهوية المدخل...';
                    $this->resetValidation(['father_identification_number', 'father_name', 'father_phone', 'father_address', 'father_password', 'father_dob', 'father_email']);
                } else {
                    $this->father_id = null;
                    $this->isFoundFather = false;
                    $this->successMessage = null;
                }
            } else {
                $this->successMessage = null;
                $this->father_id = null;
                $this->isFoundFather = false;
            }
        }
        if ($this->currentStep == 2 || $this->currentStep == 3) {
            $this->successMessage = null;
        }
        return view('livewire.students', ['students' => $this->all_Students(),]);
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->searchGradeId = $this->grade_id;
            $this->grades = Grade::where('id', $this->grade_id)->get();
        } else if (auth()->user()->current_role == 'اداري') {
            $this->grade_id = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
            $this->searchGradeId = $this->grade_id;
            $this->grades = Grade::where('id', $this->grade_id)->get();
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->grade_id = Teacher::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::where('id', $this->grade_id)->get();
        } else {
            $this->grades = Grade::all();
        }
    }

    public function all_Groups()
    {
        if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري') {
            if ($this->grade_id) {
                $this->groups = Group::query()->where('grade_id', $this->grade_id)->get();
            } else if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        } else if (auth()->user()->current_role == 'محفظ') {
            $teacher_id = auth()->id();
            $this->groups = Group::where('teacher_id', $teacher_id)->get();
        } else if (auth()->user()->current_role == 'أمير المركز') {
            if ($this->grade_id) {
                $this->groups = Group::query()->where('grade_id', $this->grade_id)->get();
            } else if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        }
    }


    //firstStepSubmit

    public function all_Students()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            if (!empty($this->searchGroupId)) {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->grade_id)
                    ->where('group_id', '=', $this->searchGroupId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->grade_id)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else if (auth()->user()->current_role == 'اداري') {
            $this->grade_id = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
            if (!empty($this->searchGroupId)) {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->grade_id)
                    ->where('group_id', '=', $this->searchGroupId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->grade_id)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->grade_id = Teacher::where('id', auth()->id())->first()->grade_id;
            return Student::query()
                ->search($this->search)
                ->where('grade_id', '=', $this->grade_id)
                ->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            if (empty($this->searchGradeId)) {
                return Student::query()
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                if (empty($this->searchGroupId)) {
                    return Student::query()
                        ->search($this->search)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return Student::query()
                        ->search($this->search)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->where('group_id', '=', $this->searchGroupId)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                }
            }
        }
    }

    //secondStepSubmit

    public function all_Quran_Parst($id, $isSuccess)
    {
        if ($id != null) {
            $this->quran_parts =
                QuranPart::query()->orderBy('id')->find($isSuccess == true ? $id - 1 : $id)->toArray();
        } else {
            $this->quran_parts = QuranPart::query()->orderBy('id')->get();
        }
    }

    public function checkLastExamStatus($id)
    {
        $this->getStudent($id);
        $exam = Exam::where('student_id', $id)->orderBy('exam_date', 'desc')->first();
        if ($exam) {
            $sum = 0;
            for ($i = 1; $i <= count($exam->marks_questions); $i++) {
                $sum += $exam->marks_questions[$i];
            }
            $exam_mark = $sum / count($exam->marks_questions);
            if ($exam_mark >= $exam->examSuccessMark->mark) {
                $this->all_Quran_Parst($exam->quran_part_id, true);
                $this->emit('showDialogExamRequest');
            } else {
                $to = Carbon::createFromFormat('Y-m-d', date('Y-m-d', Carbon::now()->timestamp));
                $from = Carbon::createFromFormat('Y-m-d', $exam->exam_date);

                $diff_in_days = $to->diffInDays($from);
                $number_days_exam = ExamSettings::find(1)->number_days_exam;
                $days = ($diff_in_days - $number_days_exam);
                if ($days > 0) {
                    $this->all_Quran_Parst($exam->quran_part_id, false);
                    $this->emit('showDialogExamRequest');
                } else {
                    if (abs($days) == 0) {
                        session()->flash('failure_message', 'عذرا متبقي لهذا الطالب يوم حتى تتمكن من طلب اختبار جديد');
                    } else if (abs($days) == 1) {
                        session()->flash('failure_message', 'عذرا متبقي لهذا الطالب يومان حتى تتمكن من طلب اختبار جديد');
                    } else if (abs($days) == 2) {
                        session()->flash('failure_message', 'عذرا متبقي لهذا الطالب ثلاث أيام حتى تتمكن من طلب اختبار جديد');
                    } else if (in_array(abs($days), range(3, 10))) {
                        session()->flash('failure_message', 'عذرا متبقي لهذا الطالب ' . abs($days) . ' أيام حتى تتمكن من طلب اختبار جديد');
                    } else if (in_array(abs($days), range(11, 15))) {
                        session()->flash('failure_message', 'عذرا متبقي لهذا الطالب ' . abs($days) . ' يوم حتى تتمكن من طلب اختبار جديد');
                    }
                }
            }
        } else {
            $this->emit('showDialogExamRequest');
            $this->all_Quran_Parst(null, null);
        }
    }

    public function showformadd($isShow)
    {
        $this->show_table = $isShow;
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

    //firstStepSubmit

    public function firstStepSubmit()
    {
        if ($this->isFoundFather) {
            $this->validate(['father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->father_id,
                'father_name' => 'required|string|unique:users,name,' . $this->father_id,
                'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
                'father_email' => 'required|email|unique:users,email,' . $this->father_id,
                'father_dob' => 'required|date|date_format:Y-m-d',
                'father_address' => 'required',
            ]);
        } else {
            $this->validate(['father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->father_id,
                'father_name' => 'required|string|unique:users,name,' . $this->father_id,
                'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
                'father_email' => 'required|email|unique:users,email,' . $this->father_id,
                'father_password' => 'required|min:8|max:10',
                'father_dob' => 'required|date|date_format:Y-m-d',
                'father_address' => 'required',
            ]);
        }

        $this->currentStep = 2;
    }

    //secondStepSubmit_edit

    public function secondStepSubmit()
    {
        $this->validate([
            'student_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->student_id,
            'student_name' => 'required|string|unique:users,name,' . $this->student_id,
            'student_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->student_id,
            'student_email' => 'required|email|unique:users,email,' . $this->student_id,
            'student_password' => 'required|min:8|max:10',
            'dob' => 'required|date|date_format:Y-m-d',
            'grade_id' => 'required',
            'group_id' => 'required',
            'student_address' => 'required',
        ]);

        $group = Group::where("id", $this->group_id)->where("grade_id", $this->grade_id)->first();
        if (is_null($group)) {
            $this->group_id = null;
        } else {
            $this->currentStep = 3;
        }
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
                    'password' => Hash::make($this->father_password),
                    'phone' => $this->father_phone,
                    'identification_number' => $this->father_identification_number,
                    'address' => $this->father_address,
                    'email' => $this->father_email,
                    'dob' => $this->father_dob,
                ]);
            }

            // حفظ بيانات الإبن في جدول المستخدمين
            $userStudent = User::create([
                'name' => $this->student_name,
                'password' => Hash::make($this->student_password),
                'identification_number' => $this->student_identification_number,
                'email' => $this->student_email,
                'address' => $this->student_address,
                'phone' => $this->student_phone,
                'dob' => $this->dob,
            ]);

            // حفظ البيانات في جدول الأباء
            $retFather = Father::find($this->father_id);

            if (is_null($retFather)) {
                if (!is_null($userFather)) {
                    Father::create(['id' => $userFather->id]);
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
            $userFather->assignRole([$roleId]);

            if (!empty($this->photo)) {
                $this->photo->storeAs($this->student_identification_number, $this->photo->getClientOriginalName(), $disk = 'students_images');
                $userStudent->update([
                    'profile_photo_url' => $this->photo->getClientOriginalName(),
                ]);
            }

            DB::commit();
            session()->flash('message', 'تم حفظ معلومات الطالب بنجاح.');
            $this->clearForm();
            $this->currentStep = 1;
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }

    }

    public function clearForm()
    {
        // clear inputs father
        $this->father_id = null;
        $this->father_name = null;
        $this->father_phone = null;
        $this->father_email = null;
        $this->father_password = null;
        $this->father_identification_number = null;
        $this->father_dob = null;
        $this->father_address = null;

        // clear inputs student
        $this->student_id = null;
        $this->student_name = null;
        $this->student_phone = null;
        $this->student_email = null;
        $this->student_password = null;
        $this->student_identification_number = null;
        $this->dob = null;
        $this->student_address = null;
        $this->grade_id = null;
        $this->group_id = null;
        $this->photo = null;
        $this->groups = null;
        $this->currentStep = 1;

        // clear inputs submit exam request
        $this->student_id = null;
        $this->quran_part_id = null;
        $this->resetValidation();
    }


    //clearForm

    public function edit($id)
    {
        $this->resetValidation();
        $this->reset();
        $this->show_table = false;
        $this->updateMode = true;
        $student = Student::where('id', $id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
        $this->student_phone = $student->user->phone;
        $this->student_identification_number = $student->user->identification_number;
        $this->student_email = $student->user->email;
        $this->dob = $student->user->dob;
        $this->student_address = $student->user->address;
        $this->grade_id = $student->grade_id;
        $this->group_id = $student->group_id;
        $this->father_id = $student->father_id;

        $this->father_name = $student->father->user->name;
        $this->father_email = $student->father->user->email;
        $this->father_phone = $student->father->user->phone;
        $this->father_identification_number = $student->father->user->identification_number;
        $this->father_dob = $student->father->user->dob;
        $this->father_address = $student->father->user->address;
    }


    //back

    public function firstStepSubmit_edit()
    {
        $this->validate([
            'father_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->father_id,
            'father_name' => 'required|string|unique:users,name,' . $this->father_id,
            'father_email' => 'required|email|unique:users,email,' . $this->father_id,
            'father_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->father_id,
            'father_dob' => 'required|date|date_format:Y-m-d',
            'father_address' => 'required',
        ]);

        $this->updateMode = true;
        $this->currentStep = 2;
    }

    public function secondStepSubmit_edit()
    {
        $this->validate([
            'student_identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->student_id,
            'student_name' => 'required|string|unique:users,name,' . $this->student_id,
            'student_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->student_id,
            'student_email' => 'required|email|unique:users,email,' . $this->student_id,
            'dob' => 'required|date|date_format:Y-m-d',
            'grade_id' => 'required',
            'group_id' => 'required',
            'student_address' => 'required',
        ]);

        $group = Group::where("id", $this->group_id)->where("grade_id", $this->grade_id)->get()->first();
        if (is_null($group)) {
            $this->group_id = null;
        } else {
            $this->updateMode = true;
            $this->currentStep = 3;
        }
    }

    public function submitForm_edit()
    {
        if ($this->father_id) {
            $father = Father::find($this->father_id);
            $father->user->update([
                'name' => $this->father_name,
                'email' => $this->father_email,
                'phone' => $this->father_phone,
                'identification_number' => $this->father_identification_number,
                'dob' => $this->father_dob,
                'address' => $this->father_address,
            ]);

            $roleId = Role::select('*')->where('name', '=', 'ولي أمر الطالب')->get();
            $father->user->assignRole([$roleId]);
        }

        if ($this->student_id) {
            $student = Student::find($this->student_id);
            $student->user->update([
                'name' => $this->student_name,
                'phone' => $this->student_phone,
                'email' => $this->student_email,
                'identification_number' => $this->student_identification_number,
                'dob' => $this->dob,
                'address' => $this->student_address,
            ]);
            $student->update([
                'grade_id' => $this->grade_id,
                'group_id' => $this->group_id,
            ]);

            $roleId = Role::select('*')->where('name', '=', 'طالب')->get();
            $student->user->assignRole([$roleId]);
        }

        if (!empty($this->photo)) {
            $this->photo->storeAs($this->student_identification_number, $this->photo->getClientOriginalName(), $disk = 'students_images');
            $student->user->update([
                'profile_photo_url' => $this->photo->getClientOriginalName(),
            ]);
        }

        session()->flash('message', 'تم تحديث معلومات الطالب بنجاح.');
        $this->clearForm();
        $this->show_table = true;
        $this->updateMode = false;
    }

    public function delete($id)
    {
        Father::findOrFail($id)->delete();
        session()->flash('message', 'تم حذف معلومات الطالب بنجاح.');
        $this->show_table = true;
        $this->updateMode = false;
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

        $readable = ["isReadableTeacher" => false, "isReadableSupervisor" => false,
            "isReadableTester" => false, "isReadableSupervisorExams" => false];

        $examOrder = ExamOrder::create([
            'status' => 0,
            'quran_part_id' => $this->quran_part_id,
            'student_id' => $this->student_id,
            'teacher_id' => Student::where('id', $this->student_id)->first()->group->teacher_id,
            'readable' => $readable,
        ]);

        $this->emit('add-exam');

        session()->flash('message', 'تمت عملية طلب الإختبار بنجاح.');
        $this->clearForm();

        // push notification
        if ($examOrder != null) {
            $arr_external_user_ids = [];
            $user_role_supervisor_id = Supervisor::where('grade_id', $examOrder->student->grade_id)->first()->id;
            array_push($arr_external_user_ids, "" . $user_role_supervisor_id);

            if (auth()->user()->current_role != 'محفظ') {
                array_push($arr_external_user_ids, "" . $examOrder->teacher_id);
            }
            if (auth()->user()->current_role == 'أمير المركز') {
                $message = "لقد قام أمير المركز بطلب اختبار: " . $examOrder->quranPart->name . " للطالب: " . $examOrder->student->user->name;
            } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
                $message = "لقد قام المحفظ: " . $examOrder->teacher->name . " بطلب اختبار: " . $examOrder->quranPart->name . " للطالب : " . $examOrder->student->user->name;
            }

            $this->push_notifications($arr_external_user_ids, $message);
        }
    }

    public function push_notifications($arr_external_user_ids, $message)
    {
        $fields = array(
            'app_id' => env("ONE_SIGNAL_APP_ID"),
            'include_external_user_ids' => $arr_external_user_ids,
            'channel_for_external_user_ids' => 'push',
            'data' => array("foo" => "bar"),
            'headings' => array(
                "en" => 'حالة طلب الاختبار',
                "ar" => 'حالة طلب الاختبار',
            ),
            'url' => 'https://memorization-management-system.herokuapp.com/manage_exams_orders',
            'contents' => array(
                "en" => $message,
                "ar" => $message,
            )
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('ONE_SIGNAL_AUTHORIZE')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public function getStudent($id)
    {
        $this->clearForm();
        $student = Student::where('id', $id)->first();
        $this->student_id = $student->id;
        $this->student_name = $student->user->name;
    }

}
