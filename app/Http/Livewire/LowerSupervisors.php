<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class LowerSupervisors extends Component
{
    use WithPagination, WithFileUploads;

    public $name;
    public $grade_id;
    public $email;
    public $password;
    public $phone;
    public $identification_number;
    public $dob;
    public $address;
    public $photo, $show_table = true, $catchError;
    public $modalId;
    public $grades;
    public $sortBy = 'id', $sortDirection = 'asc', $perPage = 10, $search;

    public function render()
    {

        $this->grades = $this->all_Grades();
        return view('livewire.lower-supervisors', ['lower_supervisors' => $this->all_LowerSupervisors()]);
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'email' => 'required|email|unique:users,email,' . $this->modalId,
            'password' => 'required|min:8|max:10',
            'name' => 'required|string|unique:users,name,' . $this->modalId,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
            'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
            'dob' => 'required|date|date_format:Y-m-d',
            'address' => 'required',
            'grade_id' => 'required',
            'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
    }

    public function loadModalData($id)
    {
        $this->modalFormReset();
        $data = LowerSupervisor::find($id);
        $this->modalId = $data->id;
        $this->grade_id = $data->grade_id;
        $this->name = $data->user->name;
        $this->email = $data->user->email;
        $this->password = '12345678';
        $this->dob = $data->user->dob;
        $this->address = $data->user->address;
        $this->phone = $data->user->phone;
        $this->identification_number = $data->user->identification_number;
    }

    public function modelLowerSupervisor($id)
    {
        return [
            'id' => $id,
            'grade_id' => $this->grade_id,
        ];
    }

    public function modelUser()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'dob' => $this->dob,
            'address' => $this->address,
            'phone' => $this->phone,
            'identification_number' => $this->identification_number,
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'حقل البريد الإلكتروني مطلوب',
            'email.email' => 'يجب ادخال بريد الكتروني صالح',
            'email.unique' => 'البريد الإلكتروني المدخل موجود مسبقا',
            'password.required' => 'حقل كلمة المرور مطلوب',
            'password.min' => 'يجب أن لا يقل طول كلمة المرور عن 8 حروف',
            'password.max' => 'يجب أن لا يزيد طول كلمة المرور عن 10 حروف',
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
            'address.required' => 'حقل العنوان مطلوب',
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'photo.image' => 'حقل الصورة يجب أن يحتوي على صورة',
            'photo.mimes' => 'يجب أن تكون صيغة الصورة إما jpeg أو png أو jpg',
            'photo.max' => 'يجب أن لا يزيد حجم الصورة عن 2048 كيلو بايت',
        ];
    }


    public function modalFormReset()
    {
        $this->modalId = null;
        $this->grade_id = null;
        $this->name = null;
        $this->email = null;
        $this->password = null;
        $this->phone = null;
        $this->identification_number = null;
        $this->dob = null;
        $this->address = null;
        $this->photo = null;
        $this->show_table = false;
        $this->catchError = null;
        $this->search = null;
    }

    public function store()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $user = User::create($this->modelUser());
            $roleId = Role::select('*')->where('name', '=', 'اداري')->get();
            $user->assignRole([$roleId]);
            $lower_supervisor = LowerSupervisor::create($this->modelLowerSupervisor($user->id));
            if (!empty($this->photo)) {
                $this->photo->storeAs($this->identification_number, $this->photo->getClientOriginalName(), $disk = 'lower_supervisors_images');
                $lower_supervisor->user->update([
                    'profile_photo_url' => $this->photo->getClientOriginalName(),
                ]);
            }
            $this->modalFormReset();
            session()->flash('message', 'تم حفظ معلومات الإداري بنجاح.');
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function rules()
    {
        return ['email' => 'required|email|unique:users,email,' . $this->modalId,
            'password' => 'required|min:8|max:10',
            'name' => 'required|string|unique:users,name,' . $this->modalId,
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
            'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
            'dob' => 'required|date|date_format:Y-m-d',
            'address' => 'required',
            'grade_id' => 'required',
        ];
    }

    public function update()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $lower_supervisor = LowerSupervisor::where('id', $this->modalId)->first();
            $lower_supervisor->update($this->modelLowerSupervisor($this->modalId));
            $lower_supervisor->user->update($this->modelUser());
            $roleId = Role::select('*')->where('name', '=', 'اداري')->get();
            $lower_supervisor->user->assignRole([$roleId]);
            if (!empty($this->photo)) {
                $this->photo->storeAs($this->identification_number, $this->photo->getClientOriginalName(), $disk = 'Lower_supervisors_images');
                $lower_supervisor->user->update([
                    'profile_photo_url' => $this->photo,
                ]);
            }
            $this->modalFormReset();
            $this->show_table = true;
            session()->flash('message', 'تم تحديث معلومات الإداري بنجاح.');
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function destroy($id)
    {
        $lower_supervisor = LowerSupervisor::find($id);
        $lower_supervisor->delete();
        $this->emit('delete_LowerSupervisor');
        session()->flash('message', 'تم حذف الإداري بنجاح.');
    }

    public function all_LowerSupervisors()
    {
        if (!empty($this->search)) {
            return LowerSupervisor::query()
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            return LowerSupervisor::query()
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }

    }

    public function all_Grades()
    {
        return Grade::all();
    }
}
