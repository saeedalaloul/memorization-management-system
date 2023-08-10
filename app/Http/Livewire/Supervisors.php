<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Supervisor;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class Supervisors extends HomeComponent
{
    public $name;
    public $grade_id;
    public $email;
    public $phone;
    public $identification_number;
    public $recitation_level;
    public $economic_situation;
    public $academic_qualification;
    public $dob;
    public $address;
    public $photo, $photo_ret;
    public $grades;

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function render()
    {
        return view('livewire.supervisors', ['supervisors' => $this->all_Supervisors()]);
    }

    public function loadModalData($id)
    {
        $this->modalFormReset();
        $data = Supervisor::find($id);
        $this->modalId = $data->id;
        $this->grade_id = $data->grade_id;
        $this->economic_situation = $data->user->user_info->economic_situation ?? null;
        $this->recitation_level = $data->user->user_info->recitation_level ?? null;
        $this->academic_qualification = $data->user->user_info->academic_qualification ?? null;
        $this->name = $data->user->name;
        $this->email = $data->user->email;
        $this->dob = $data->user->dob;
        $this->address = $data->user->address;
        $this->phone = $data->user->phone;
        $this->identification_number = $data->user->identification_number;
    }

    public function modelSupervisor($id)
    {
        return [
            'id' => $id,
            'grade_id' => $this->grade_id,
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

    public function modelUser()
    {
        return [
            'name' => $this->name,
            'gender' => Grade::whereId($this->grade_id)->first()->section,
            'email' => $this->email,
            'dob' => $this->dob,
            'phone' => $this->phone,
            'address' => $this->address,
            'identification_number' => $this->identification_number,
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
            'recitation_level.required' => 'أخر دورة أحكام مطلوب',
            'economic_situation.required' => 'الوضع المادي مطلوب',
            'academic_qualification.required' => 'المؤهل العلمي مطلوب',
            'dob.required' => 'حقل تاريخ الميلاد مطلوب',
            'dob.date' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'dob.date_format' => 'حقل تاريخ الميلاد يجب أن يكون من نوع تاريخ',
            'address.required' => 'حقل العنوان مطلوب',
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 1024 كيلو بايت',
            'photo.unique' => 'عذرا يوجد صورة بهذا الاسم مسبقا',
        ];
    }


    public function modalFormReset()
    {
        $this->grade_id = null;
        $this->name = null;
        $this->email = null;
        $this->phone = null;
        $this->identification_number = null;
        $this->dob = null;
        $this->address = null;
        $this->photo = null;
        $this->modalId = '';
        $this->catchError = '';
        $this->recitation_level = null;
        $this->economic_situation = null;
        $this->academic_qualification = null;
    }

    public function store()
    {
        $this->validate();

        if (!empty($this->photo)) {
            $this->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:1024|unique:users,profile_photo,' . $this->modalId,
            ]);
        }

        DB::beginTransaction();
        try {
            $user = User::create($this->modelUser());
            $user->user_info()->create($this->modelUserInfo($user->id));
            $roleId = Role::select('*')->where('name', '=', 'مشرف')->get();
            $user->assignRole([$roleId]);
            Supervisor::create($this->modelSupervisor($user->id));
            if (!empty($this->photo)) {
                $this->uploadImage($this->photo,
                    $this->identification_number . Carbon::now()->timestamp . '.' . $this->photo->getClientOriginalExtension(),
                    $user->id);
            }
            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم حفظ معلومات المشرف بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function rules()
    {
        return ['email' => 'required|email|unique:users,email,' . $this->modalId,
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

        if (!empty($this->photo)) {
            $this->validate([
                'photo' => 'required|image|mimes:jpg,jpeg,png|max:1024|unique:users,profile_photo,' . $this->modalId,
            ]);
        }

        DB::beginTransaction();
        try {
            $supervisor = Supervisor::where('id', $this->modalId)->first();
            $supervisor->update($this->modelSupervisor($this->modalId));
            $supervisor->user->update($this->modelUser());
            if ($supervisor->user->user_info === null) {
                $supervisor->user->user_info()->create($this->modelUserInfo($this->modalId));
            } else {
                $supervisor->user->user_info->update($this->modelUserInfo($this->modalId));
            }
            $roleId = Role::select('*')->where('name', '=', 'مشرف')->get();
            $supervisor->user->assignRole([$roleId]);
            if (!empty($this->photo)) {
                $this->deleteImage($supervisor->user->profile_photo);
                $this->uploadImage($this->photo,
                    $this->identification_number . Carbon::now()->timestamp . '.' . $this->photo->getClientOriginalExtension(),
                    $this->modalId);
            }
            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تحديث معلومات المشرف بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function destroy($id)
    {
//        $supervisor = Supervisor::find($id);
//        $supervisor->delete();
        $this->dispatchBrowserEvent('hideDialog');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => 'عذرا لا يمكنك حذف المشرف بتاتا.']);
    }

    public function all_Supervisors()
    {
        if ($this->current_role === 'أمير المركز') {
            return Supervisor::query()
                ->with(['user', 'grade'])
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }

        return [];
    }

    public function submitSearch()
    {
        $this->all_Supervisors();
    }

    public function all_Grades()
    {
        $this->grades = Grade::all();
    }
}
