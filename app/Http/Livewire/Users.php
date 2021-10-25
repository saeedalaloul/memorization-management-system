<?php

namespace App\Http\Livewire;

use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination, WithFileUploads;

    public $show_table = true, $catchError;
    public $modalId, $name, $password, $password_confirm, $proccess_type;
    public $sortBy = 'id', $sortDirection = 'asc', $perPage = 10, $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {

        return view('livewire.users', ['users' => $this->all_Users()]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'password' => 'required|min:8|max:10',
            'password_confirm' => 'required|same:password'
        ]);
    }

    public function messages()
    {
        return [
            'password.required' => 'حقل كلمة المرور مطلوب',
            'password.min' => 'يجب أن لا يقل طول كلمة المرور عن 8 حروف',
            'password.max' => 'يجب أن لا يزيد طول كلمة المرور عن 10 حروف',
            'password_confirm.required' => 'حقل تأكيد كلمة المرور مطلوب',
            'password_confirm.same' => 'يجب أن تكون كلمة المرور متطابقة',
        ];
    }

    public function rules()
    {
        return [
            'password' => 'required|min:8|max:10',
            'password_confirm' => 'required|same:password'
        ];
    }

    public function resetPasswordUser()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            User::find($this->modalId)->update([
                'password' => Hash::make($this->password)
            ]);
            $this->modalFormReset();
            $this->show_table = true;
            session()->flash('message', 'تمت عملية إعادة تعيين كلمة المرور بنجاح.');
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }


    public function loadModalData($id)
    {
        $this->modalFormReset();
        $data = User::find($id);
        $this->modalId = $data->id;
        $this->name = $data->name;
        $this->show_table = false;
        $this->proccess_type = 'reset';
    }

    public function activeEmail($id)
    {
        $user = User::find($id);
        if ($user != null) {
            if ($user->email_verified_at == null) {
                $user->markEmailAsVerified();
                event(new Verified($user));
                session()->flash('success_message', 'تمت عملية تفعيل البريد الإلكتروني بنجاح.');
            } else {
                $user->update(['email_verified_at' => null]);
                session()->flash('success_message', 'تمت عملية إلغاء تفعيل البريد الإلكتروني بنجاح.');
            }
        }
    }

    public function modalFormReset()
    {
        $this->modalId = null;
        $this->name = null;
        $this->show_table = false;
        $this->proccess_type = null;
        $this->catchError = null;
        $this->password = null;
        $this->password_confirm = null;
        $this->search = null;
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

    public function all_Users()
    {
        return User::query()
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }
}
