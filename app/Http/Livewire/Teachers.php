<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Spatie\Permission\Models\Role;

class Teachers extends HomeComponent
{
    public $name;
    public $grade_id;
    public $email;
    public $phone;
    public $identification_number;
    public $dob;
    public $recitation_level;
    public $economic_situation;
    public $academic_qualification;
    public $photo, $photo_ret;
    public $grades, $selectedGradeId;

    protected $listeners = [
        'getTeachersByGradeId' => 'all_Teachers',
    ];


    public function mount()
    {
        $this->all_Grades();
        $this->current_role = auth()->user()->current_role;
    }

    public function render()
    {
        return view('livewire.teachers', ['teachers' => $this->all_Teachers()]);
    }

    public function loadModalData($teacher_id)
    {
        $this->modalFormReset();
        $data = Teacher::find($teacher_id);
        $this->modalId = $data->id;
        $this->grade_id = $data->grade_id;
        $this->economic_situation = $data->user->user_info->economic_situation?? null;
        $this->recitation_level = $data->user->user_info->recitation_level ?? null;
        $this->academic_qualification = $data->user->user_info->academic_qualification ?? null;
        $this->name = $data->user->name;
        $this->photo_ret = $data->user->profile_photo_url;
        $this->email = $data->user->email;
        $this->dob = $data->user->dob;
        $this->phone = $data->user->phone;
        $this->identification_number = $data->user->identification_number;
    }

    public function modelTeacher($teacher_id)
    {
        return [
            'id' => $teacher_id,
            'grade_id' => $this->grade_id,
        ];
    }

    public function modelUser()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'dob' => $this->dob,
            'phone' => $this->phone,
            'identification_number' => $this->identification_number,
        ];
    }

    public function modelUserInfo($teacher_id)
    {
        return [
            'id' => $teacher_id,
            'economic_situation' => $this->economic_situation,
            'recitation_level' => $this->recitation_level,
            'academic_qualification' => $this->academic_qualification,
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'حقل البريد الإلكتروني مطلوب',
            'email.email' => 'يجب ادخال بريد الكتروني صالح',
            'email.unique' => 'البريد الإلكتروني المدخل موجود مسبقا',
            'name.required' => 'حقل الاسم مطلوب',
            'name.string' => 'يجب ادخال نص في حقل الاسم',
            'name.unique' => 'الاسم المدخل موجود مسبقا',
            'phone.required' => 'حقل رقم الجوال مطلوب',
            'phone.unique' => 'رقم الجوال المدخل موجود مسبقا',
            'phone.min' => 'يجب أن لا يقل طول رقم الجوال عن 10 أرقام',
            'phone.max' => 'يجب أن لا يزيد طول رقم الجوال عن 10 أرقام',
            'identification_number.required' => 'حقل رقم الهوية مطلوب',
            'identification_number.regex' => 'حقل رقم الهوية يجب أن يكون رقم',
            'identification_number.unique' => 'رقم الهوية المدخل موجود مسبقا',
            'identification_number.min' => 'يجب أن لا يقل طول رقم الهوية عن 9 أرقام',
            'identification_number.max' => 'يجب أن لا يزيد طول رقم الهوية عن 9 أرقام',
            'dob.required' => 'حقل تاريخ الميلاد مطلوب',
            'dob.date' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'dob.date_format' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'recitation_level.required' => 'أخر دورة أحكام مطلوب',
            'economic_situation.required' => 'الوضع المادي مطلوب',
            'academic_qualification.required' => 'المؤهل العلمي مطلوب',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 2048 كيلو بايت',
        ];
    }


    public function modalFormReset()
    {
        $this->grade_id = null;
        $this->name = null;
        $this->photo_ret = null;
        $this->email = null;
        $this->phone = null;
        $this->identification_number = null;
        $this->dob = null;
        $this->recitation_level = null;
        $this->economic_situation = null;
        $this->academic_qualification = null;
        $this->photo = null;
        $this->modalId = '';
        $this->catchError = '';
    }

    public function store()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $user = User::create($this->modelUser());
            $user->user_info()->create($this->modelUserInfo($user->id));
            $roleId = Role::select('*')->where('name', '=', 'محفظ')->get();
            $user->assignRole([$roleId]);
            Teacher::create($this->modelTeacher($user->id));
            if (!empty($this->photo)) {
                $this->uploadImage($this->photo,
                    $this->identification_number . '.' . $this->photo->getClientOriginalExtension(),
                    $user->id);
            }
            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم حفظ معلومات المحفظ بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function rules()
    {
        return [
            'email' => 'required|email|unique:users,email,' . $this->modalId,
            'name' => 'required|string|unique:users,name,' . $this->modalId,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
            'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
            'dob' => 'required|date|date_format:Y-m-d',
            'grade_id' => 'required',
            'recitation_level' => 'required',
            'economic_situation' => 'required',
            'academic_qualification' => 'required',
        ];
    }

    public function update()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $isUpdated = true;
            $teacher = Teacher::where('id', $this->modalId)->first();
            if ($teacher->grade_id != $this->grade_id) {
                if ($teacher->group != null) {
                    $isUpdated = false;
                }
            }
            if ($isUpdated) {
                $teacher->update($this->modelTeacher($this->modalId));
                if ($teacher->user->user_info == null) {
                    $teacher->user->user_info()->create($this->modelUserInfo($this->modalId));
                } else {
                    $teacher->user->user_info->update($this->modelUserInfo($this->modalId));
                }
                $teacher->user->update($this->modelUser());
                $roleId = Role::select('*')->where('name', '=', 'محفظ')->get();
                $teacher->user->assignRole([$roleId]);
                if (!empty($this->photo)) {
                    $this->deleteImage($teacher->user->profile_photo);
                    $this->uploadImage($this->photo,
                        $this->identification_number . '.' . $this->photo->getClientOriginalExtension(),
                        $this->modalId);
                }
                $this->modalFormReset();
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم تحديث معلومات المحفظ بنجاح.']);
                DB::commit();
            } else {
                $messageBag = new MessageBag;
                $messageBag->add('grade_id', 'عذرا, لم يتم تحديث المرحلة للمحفظ بسبب وجود حلقة لدى المحفظ');
                $this->setErrorBag($messageBag);
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function destroy($teacher_id)
    {
        $teacher = Teacher::find($teacher_id);
        $teacher->delete();
        $this->dispatchBrowserEvent('hideDialog');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حذف المحفظ بنجاح.']);
    }

    public function all_Teachers()
    {
        return Teacher::query()
            ->with(['user', 'grade'])
            ->search($this->search)
            ->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->where('grade_id', '=', $this->grade_id);
            })
            ->when($this->current_role == 'أمير المركز' && !empty($this->selectedGradeId), function ($q, $v) {
                $q->where('grade_id', '=', $this->selectedGradeId);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Teachers();
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::where('id', $this->grade_id)->get();
        } else {
            $this->grades = Grade::all();
        }
    }
}
