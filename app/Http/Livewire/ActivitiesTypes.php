<?php

namespace App\Http\Livewire;

use App\Models\ActivityType;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;

class ActivitiesTypes extends HomeComponent
{
    public $name, $place, $start_date, $end_date;

    public function render()
    {
        return view('livewire.activities-types', ['activities_types' => $this->all_Activity_Types()]);
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:activity_types,name,' . $this->modalId,
            'place' => 'required|string',
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم نوع النشاط مطلوب',
            'name.unique' => 'اسم نوع النشاط موجود مسبقا',
            'place.required' => 'حقل مكان النشاط مطلوب',
            'place.string' => 'حقل مكان النشاط يجب أن يكون نص',
            'start_date.required' => 'حقل تاريخ بداية النشاط مطلوب',
            'start_date.date' => 'حقل تاريخ بداية النشاط يجب أن يكون تاريخ',
            'start_date.date_format' => 'حقل تاريخ بداية النشاط يجب أن يكون من نوع تاريخ',
            'end_date.required' => 'حقل تاريخ نهاية النشاط مطلوب',
            'end_date.date' => 'حقل تاريخ نهاية النشاط يجب أن يكون تاريخ',
            'end_date.date_format' => 'حقل تاريخ نهاية النشاط يجب أن يكون من نوع تاريخ',
        ];
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->modalId = '';
        $this->name = null;
        $this->place = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->process_type = '';
    }

    public function modelData()
    {
        return [
            'name' => $this->name,
            'place' => $this->place,
            'start_datetime' => $this->start_date . ' ' . date('H:i:s', time()),
            'end_datetime' => $this->end_date . ' ' . date('H:i:s', time()),
        ];
    }

    public function store()
    {
        $this->validate();
        if ($this->end_date > $this->start_date) {
            ActivityType::create($this->modelData());

            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم حفظ معلومات نوع النشاط بنجاح.']);
        } else {
            $messageBag = new MessageBag();
            $messageBag->add('start_date', 'تاريخ بداية النشاط أكبر من تاريخ نهاية النشاط');
            $this->setErrorBag($messageBag);
        }
    }

    public function update()
    {
        $this->validate();
        if ($this->end_date > $this->start_date) {
            ActivityType::where('id', $this->modalId)->update($this->modelData());
            $this->modalFormReset();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تحديث معلومات نوع النشاط بنجاح.']);
        } else {
            $messageBag = new MessageBag();
            $messageBag->add('start_date', 'تاريخ بداية النشاط أكبر من تاريخ نهاية النشاط');
            $this->setErrorBag($messageBag);
        }
    }

    public function getModalData($id, $process_type)
    {
        $this->modalId = $id;
        $this->process_type = $process_type;
        if ($process_type == 'edit') {
            $activityType = ActivityType::where('id', $this->modalId)->first();
            $this->name = $activityType->name;
            $this->place = $activityType->place;
            $this->start_date = Carbon::parse($activityType->start_datetime)->format('Y-m-d');
            $this->end_date = Carbon::parse($activityType->end_datetime)->format('Y-m-d');
        }
    }

    public function destroy()
    {
        if ($this->modalId != null) {
            $activityType = ActivityType::where('id', $this->modalId)->first();
            if ($activityType != null) {
                if ($activityType->activities->count() > 0) {
                    $this->catchError = "عذرا لا يمكن حذف نوع النشاط بسبب وجود أنشطة لنوع النشاط";
                    $this->dispatchBrowserEvent('hideDialog');
                } else {
                    if ($activityType->activities_orders_types->count() > 0) {
                        $this->catchError = "عذرا لا يمكن حذف نوع النشاط بسبب وجود طلبات أنشطة لديه يرجى إجرائها أو حذفها";
                        $this->dispatchBrowserEvent('hideDialog');
                    } else {
                        $activityType->delete();
                        $this->dispatchBrowserEvent('hideDialog');
                        $this->dispatchBrowserEvent('alert',
                            ['type' => 'success', 'message' => 'تم حذف نوع النشاط بنجاح.']);
                    }
                }
            }

        }
    }

    public function all_Activity_Types()
    {
        return ActivityType::query()
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Activity_Types();
    }
}
