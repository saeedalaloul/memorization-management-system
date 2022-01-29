<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\LowerSupervisor;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\MessageBag;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Teachers extends Component
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
    public $grades, $searchGradeId;
    public $sortBy = 'id', $sortDirection = 'desc', $perPage = 10, $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->all_Grades();
        return view('livewire.teachers', ['teachers' => $this->all_Teachers()]);
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

    public function loadModalData($teacher_id)
    {
        $this->modalFormReset();
        $data = Teacher::find($teacher_id);
        $this->modalId = $data->id;
        $this->grade_id = $data->grade_id;
        $this->name = $data->user->name;
        $this->email = $data->user->email;
        $this->dob = $data->user->dob;
        $this->phone = $data->user->phone;
        $this->address = $data->user->address;
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
        if ($this->modalId == null) {
            return [
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'dob' => $this->dob,
                'address' => $this->address,
                'phone' => $this->phone,
                'identification_number' => $this->identification_number,
            ];
        } else {
            return [
                'name' => $this->name,
                'email' => $this->email,
                'dob' => $this->dob,
                'address' => $this->address,
                'phone' => $this->phone,
                'identification_number' => $this->identification_number,
            ];
        }
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
            'grade_id.required' => 'اسم المرحلة مطلوب',
            'address.required' => 'حقل العنوان مطلوب',
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
            $roleId = Role::select('*')->where('name', '=', 'محفظ')->get();
            $user->assignRole([$roleId]);
            $teacher = Teacher::create($this->modelTeacher($user->id));
            if (!empty($this->photo)) {
                $this->photo->storeAs($this->identification_number, $this->photo->getClientOriginalName(), $disk = 'teachers_images');
                $teacher->user->update([
                    'profile_photo_url' => $this->photo->getClientOriginalName(),
                ]);
            }
            $this->modalFormReset();
            session()->flash('message', 'تم حفظ معلومات المحفظ بنجاح.');
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function rules()
    {
        if ($this->modalId == null) {
            return ['email' => 'required|email|unique:users,email,' . $this->modalId,
                'password' => 'required|min:8|max:10',
                'name' => 'required|string|unique:users,name,' . $this->modalId,
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
                'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
                'dob' => 'required|date|date_format:Y-m-d',
                'address' => 'required',
                'grade_id' => 'required',
            ];
        } else {
            return ['email' => 'required|email|unique:users,email,' . $this->modalId,
                'name' => 'required|string|unique:users,name,' . $this->modalId,
                'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10|unique:users,phone,' . $this->modalId,
                'identification_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:9|unique:users,identification_number,' . $this->modalId,
                'dob' => 'required|date|date_format:Y-m-d',
                'address' => 'required',
                'grade_id' => 'required',
            ];
        }
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
                $teacher->user->update($this->modelUser());
                $roleId = Role::select('*')->where('name', '=', 'محفظ')->get();
                $teacher->user->assignRole([$roleId]);
                if (!empty($this->photo)) {
                    $this->photo->storeAs($this->identification_number, $this->photo->getClientOriginalName(), $disk = 'teachers_images');
                    $teacher->user->update([
                        'profile_photo_url' => $this->photo,
                    ]);
                }
                $this->modalFormReset();
                $this->show_table = true;
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
        $this->emit('delete_Teacher');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حذف المحفظ بنجاح.']);
    }

    public function all_Teachers()
    {
        if (auth()->user()->current_role == 'مشرف' ||
            auth()->user()->current_role == 'اداري') {
            if ($this->searchGradeId) {
                return Teacher::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Teacher::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->grade_id)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else {
            if ($this->searchGradeId) {
                if (!empty($this->search)) {
                    return Teacher::query()
                        ->search($this->search)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return Teacher::query()
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->paginate($this->perPage);
                }
            } else {
                if (!empty($this->search)) {
                    return Teacher::query()
                        ->search($this->search)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return Teacher::query()
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                }
            }
        }
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->grade_id = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::where('id', $this->grade_id)->get();
        } else if (auth()->user()->current_role == 'اداري') {
            $this->grade_id = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::where('id', $this->grade_id)->get();
        } else {
            $this->grades = Grade::all();
        }
    }
}
