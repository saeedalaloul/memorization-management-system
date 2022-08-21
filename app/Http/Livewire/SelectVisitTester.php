<?php

namespace App\Http\Livewire;

use App\Models\ExamOrder;
use App\Models\OversightMember;
use App\Models\Tester;
use App\Models\VisitOrder;
use App\Notifications\NewVisitOrderForOversightMemberNotify;
use App\Traits\NotificationTrait;
use Illuminate\Support\MessageBag;

class SelectVisitTester extends HomeComponent
{
    use NotificationTrait;

    public $oversight_members;
    public $tester_id, $oversight_member_id, $visit_date,
        $tester_name, $exams_count;

    public function render()
    {
        return view('livewire.select-visit-tester', ['testers_' => $this->all_Testers_()]);
    }

    public function mount()
    {
        $this->oversight_members = $this->all_Oversight_Members();
    }

    public function loadModalData($id)
    {
        $tester = Tester::with(['user:id,name'])->withCount('tester_exams')->find($id);
        $this->clearForm();
        $this->tester_id = $tester->id;
        $this->tester_name = $tester->user->name;
        $this->exams_count = $tester->tester_exams_count;
    }

    public function visitApproval()
    {
        $this->validate(['oversight_member_id' => 'required|numeric',
            'visit_date' => 'required|date|date_format:Y-m-d',
        ]);
        $messageBag = new MessageBag();

        if ($this->exams_count > 0) {
            if ($this->visit_date >= date('Y-m-d', time())) {
                if (ExamOrder::query()->where('tester_id', $this->tester_id)->where('status', ExamOrder::ACCEPTABLE_STATUS)->whereDate('datetime', $this->visit_date)->first() != null) {
                    $visitOrder = VisitOrder::create([
                        'hostable_type' => 'App\Models\Tester',
                        'hostable_id' => $this->tester_id,
                        'oversight_member_id' => $this->oversight_member_id,
                        'datetime' => $this->visit_date . ' ' . date('H:i:s', time()),
                    ]);

                    // start push notifications to oversight member
                    $visitOrder->oversight_member->user->notify(new NewVisitOrderForOversightMemberNotify([
                        'id' => $visitOrder->id,
                        'hostname' => $this->tester_name,
                        'host_type' => $visitOrder->hostable_type,
                        'datetime' => $visitOrder->datetime,
                    ]));
                    $title = "طلب زيارة جديد";
                    $message = "لقد قام مشرف الرقابة بتعيينك زائر (رقابة) على اختبارات المختبر: " . $this->tester_name . " بتاريخ: " . $this->visit_date . " يرجى مراجعة تفاصيل الزيارة.";
                    $this->push_notification($message, $title, [$visitOrder->oversight_member->user->user_fcm_token->device_token]);
                    // end push notifications to oversight member

                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية اعتماد طلب الزيارة بنجاح.']);
                    $this->dispatchBrowserEvent('hideDialog');

                    $this->clearForm();
                } else {
                    $messageBag->add('visit_date', 'عذرا لا يمكن حجز زيارة بسبب اختيار تاريخ غير متوافق مع لجنة الإختبارات!');
                    $this->setErrorBag($messageBag);
                }
            } else {
                $messageBag->add('visit_date', 'عذرا لا يمكن حجز زيارة بسبب اختيار تاريخ قديم!');
                $this->setErrorBag($messageBag);
            }
        } else {
            $messageBag->add('tester_name', 'عذرا لا يمكن حجز زيارة بسبب عدم وجود طلبات اختبارات معتمدة لدى المختبر!');
            $this->setErrorBag($messageBag);
        }
    }


    public function clearForm()
    {
        $this->oversight_member_id = null;
        $this->tester_name = null;
        $this->tester_id = null;
        $this->exams_count = null;
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

    public function all_Testers_()
    {
        return Tester::query()
            ->withCount(['tester_exams'])
            ->with(['user:id,name', 'visit_orders'])
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Testers_();
    }

    public function all_Oversight_Members()
    {
        return OversightMember::with(['user:id,name'])->get();
    }
}
