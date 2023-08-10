<?php

namespace App\Http\Livewire;

use App\Http\Traits\AttachFilesTrait;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Settings extends Component
{
    use AttachFilesTrait, WithFileUploads;

    public $center_name, $current_session, $center_title, $phone, $center_email
    , $address, $end_first_term, $end_second_term, $logo, $logo_ret;
    public $catchError;

    public function render()
    {
        return view('livewire.settings');
    }

    public function mount()
    {
        $setting = Setting::all();

        $setting = $setting->flatMap(function ($collection) {
            return [$collection->key => $collection->value];
        });

        $this->center_name = $setting['center_name'];
        $this->current_session = $setting['current_session'];
        $this->center_title = $setting['center_title'];
        $this->phone = $setting['phone'];
        $this->center_email = $setting['center_email'];
        $this->address = $setting['address'];
        $this->end_first_term = $setting['end_first_term'];
        $this->end_second_term = $setting['end_second_term'];
        $this->logo_ret = $setting['logo'];
    }

    public function rules()
    {
        return [
            'center_name' => 'required|string',
            'current_session' => 'required|string',
            'center_title' => 'string',
            'phone' => 'string',
            'center_email' => 'string',
            'address' => 'required|string',
            'end_first_term' => 'string',
            'end_second_term' => 'string',
        ];
    }

    public function messages()
    {
        return [
            'center_name.required' => 'حقل الاسم مطلوب',
            'center_name.string' => 'يجب إدخال نص في حقل الاسم',
            'current_session.required' => 'حقل العام الحالي مطلوب',
            'current_session.string' => 'يجب إدخال نص في حقل العام الحالي',
            'center_title.required' => 'حقل عنوان الموقع مطلوب',
            'center_title.string' => 'يجب إدخال نص في حقل عنوان الموقع',
            'phone.required' => 'حقل رقم الجوال مطلوب',
            'phone.string' => 'يجب إدخال نص في حقل رقم الجوال',
            'center_email.required' => 'حقل البريد الإلكتروني مطلوب',
            'center_email.string' => 'يجب إدخال نص في حقل البريد الإلكتروني',
            'address.required' => 'حقل عنوان المركز مطلوب',
            'address.string' => 'يجب إدخال نص في حقل عنوان المركز',
            'end_first_term.required' => 'حقل نهاية الفصل الأول مطلوب',
            'end_first_term.string' => 'يجب إدخال نص في حقل نهاية الفصل الأول',
            'end_second_term.required' => 'حقل نهاية الفصل الثاني مطلوب',
            'end_second_term.string' => 'يجب إدخال نص في حقل نهاية الفصل الثاني',
        ];
    }

    public function update()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            foreach ($this->modelSetting() as $key => $value) {
                Setting::where('key', $key)->update(['value' => $value]);
            }

            if ($this->logo !== null) {
                $this->logo->storeAs("logo", "logo.jpeg", $disk = 'upload_attachments');
                Setting::where('key', 'logo')->update(['value' => "logo.jpeg"]);
            }

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تحديث إعدادات الموقع بنجاح.']);
            DB::commit();
        } catch (Exception $e) {
            $this->catchError = $e->getMessage();
            DB::rollback();
        }

    }

    public function modelSetting()
    {
        return [
            'center_name' => $this->center_name,
            'current_session' => $this->current_session,
            'center_title' => $this->center_title,
            'phone' => $this->phone,
            'center_email' => $this->center_email,
            'address' => $this->address,
            'end_first_term' => $this->end_first_term,
            'end_second_term' => $this->end_second_term,
        ];
    }

    public function resetMessage()
    {
        $this->catchError = null;
    }
}
