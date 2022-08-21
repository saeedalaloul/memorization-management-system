<?php

namespace App\Http\Livewire;

use App\Models\ActivityMember;
use App\Models\ActivityOrder;
use App\Models\OversightMember;
use App\Models\VisitOrder;
use App\Notifications\NewVisitOrderForOversightMemberNotify;
use App\Traits\NotificationTrait;
use Illuminate\Support\MessageBag;

class SelectVisitActivity extends HomeComponent
{
    use NotificationTrait;

    public $oversight_members;
    public $activity_member_id, $oversight_member_id, $visit_date,
        $activity_member_name, $activities_orders_count;

    public function render()
    {
        return view('livewire.select-visit-activity-member', ['activity_members' => $this->all_Activity_Members()]);
    }

    public function mount()
    {
        $this->oversight_members = $this->all_Oversight_Members();
    }

    public function loadModalData($id)
    {
        $activity_member = ActivityMember::with(['user:id,name'])->withCount(['activities_orders_acceptable'])->find($id);
        $this->clearForm();
        $this->activity_member_id = $activity_member->id;
        $this->activity_member_name = $activity_member->user->name;
        $this->activities_orders_count = $activity_member->activities_orders_acceptable_count;
    }

    public function visitApproval()
    {
        $this->validate(['oversight_member_id' => 'required|numeric',
            'visit_date' => 'required|date|date_format:Y-m-d',
        ]);
        $messageBag = new MessageBag();

        if ($this->activities_orders_count > 0) {
            if ($this->visit_date >= date('Y-m-d', time())) {
                if (ActivityOrder::query()->where('activity_member_id', $this->activity_member_id)->where('status', ActivityOrder::ACCEPTABLE_STATUS)->whereDate('datetime', $this->visit_date)->first() != null) {
                    $visitOrder = VisitOrder::create([
                        'hostable_type' => 'App\Models\ActivityMember',
                        'hostable_id' => $this->activity_member_id,
                        'oversight_member_id' => $this->oversight_member_id,
                        'datetime' => $this->visit_date . ' ' . date('H:i:s', time()),
                    ]);

                    // start push notifications to oversight member
                    $visitOrder->oversight_member->user->notify(new NewVisitOrderForOversightMemberNotify([
                        'id' => $visitOrder->id,
                        'hostname' => $this->activity_member_name,
                        'host_type' => $visitOrder->hostable_type,
                        'datetime' => $visitOrder->datetime,
                    ]));
                    $title = "طلب زيارة جديد";
                    $message = "لقد قام مشرف الرقابة بتعيينك زائر (رقابة) على أنشطة المنشط: " . $this->activity_member_name . " بتاريخ: " . $this->visit_date . " يرجى مراجعة تفاصيل الزيارة.";
                    $this->push_notification($message, $title, [$visitOrder->oversight_member->user->user_fcm_token->device_token]);
                    // end push notifications to oversight member

                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية اعتماد طلب الزيارة بنجاح.']);
                    $this->dispatchBrowserEvent('hideDialog');

                    $this->clearForm();
                } else {
                    $messageBag->add('visit_date', 'عذرا لا يمكن حجز زيارة بسبب اختيار تاريخ غير متوافق مع لجنة الأنشطة!');
                    $this->setErrorBag($messageBag);
                }
            } else {
                $messageBag->add('visit_date', 'عذرا لا يمكن حجز زيارة بسبب اختيار تاريخ قديم!');
                $this->setErrorBag($messageBag);
            }
        } else {
            $messageBag->add('tester_name', 'عذرا لا يمكن حجز زيارة بسبب عدم وجود طلبات أنشطة معتمدة لدى المنشط!');
            $this->setErrorBag($messageBag);
        }
    }


    public function clearForm()
    {
        $this->oversight_member_id = null;
        $this->activity_member_name = null;
        $this->activity_member_id = null;
        $this->activities_orders_count = null;
        $this->visit_date = null;
        $this->resetValidation();
    }

    public function messages()
    {
        return [
            'oversight_member_id.required' => 'اسم المراقب مطلوب',
            'oversight_member_id.numeric' => 'حقل المراقب يجب أن يكون رقم',
            'visit_date.required' => 'حقل تاريخ الزيارة مطلوب',
            'visit_date.date' => 'حقل تاريخ الزيارة يجب أن يكون تاريخ',
            'visit_date.date_format' => 'حقل تاريخ الزيارة يجب أن يكون من نوع تاريخ',
        ];
    }

    public function all_Activity_Members()
    {
        return ActivityMember::query()
            ->withCount(['activities_orders_acceptable'])
            ->with(['user:id,name', 'visit_orders'])
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Activity_Members();
    }

    public function all_Oversight_Members()
    {
        return OversightMember::with(['user:id,name'])->get();
    }
}
