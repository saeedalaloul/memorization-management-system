<?php

namespace App\Http\Livewire;

use App\Actions\Fortify\PasswordValidationRules;
use App\Actions\Fortify\UpdateUserPassword;
use Exception;
use Illuminate\Support\Facades\Hash;

class ManagePassword extends HomeComponent
{
    use PasswordValidationRules;

    public $current_password, $password, $password_confirmation;

    public function render()
    {
        return view('livewire.manage-password');
    }

    public function messages()
    {
        return [
            'password.required' => 'حقل كلمة المرور مطلوب',
            'password.string' => 'حقل كلمة المرور يجب أن يحتوي على نص',
            'current_password.required' => 'حقل كلمة المرور الحالية مطلوب',
            'confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password_confirmation.same' => 'تأكيد كلمة المرور غير متطابق.',];
    }

    public function rules()
    {
        return [
            'password' => $this->passwordRules(),
            'current_password' => 'required|string',
        ];
    }

    public function setPasswordUser()
    {
        $this->validate([
            'password' => $this->passwordRules(),
        ]);

        try {
            auth()->user()->forceFill([
                'password' => Hash::make($this->password),
            ])->save();

            $this->modalFormReset();
            redirect()->route('dashboard');
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية تعيين كلمة المرور بنجاح.']);
        } catch (Exception $e) {
            $this->catchError = $e->getMessage();
        }
    }

    public function changePassword(UpdateUserPassword $updateUserPassword)
    {
        $updateUserPassword->update(auth()->user(), ['current_password' => $this->current_password, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation]);
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية تحديث كلمة المرور بنجاح.']);
        $this->modalFormReset();
    }


    public
    function modalFormReset()
    {
        $this->resetValidation();
        $this->catchError = '';
        $this->password = null;
        $this->password_confirmation = null;
        $this->current_password = null;
    }
}
