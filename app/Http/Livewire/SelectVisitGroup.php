<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\OversightMember;
use App\Models\VisitOrder;
use App\Notifications\NewVisitOrderForOversightMemberNotify;
use App\Traits\NotificationTrait;

class SelectVisitGroup extends HomeComponent
{
    use NotificationTrait;

    public $grades, $oversight_members;
    public $teacher_id, $oversight_member_id, $visit_date, $group_name, $group_id, $teacher_name;
    public $selectedGradeId;

    protected $listeners = [
        'getTeachersByGradeId' => 'all_Groups',
    ];

    public function render()
    {
        return view('livewire.select-visit-group', ['groups' => $this->all_Groups()]);
    }

    public function mount()
    {
        $this->grades = $this->all_Grades();
        $this->oversight_members = $this->all_Oversight_Members();
    }

    public function loadModalData($id)
    {
        $this->clearForm();
        $group = Group::with('teacher.user')->find($id);
        $this->group_id = $group->id;
        $this->teacher_id = $group->teacher_id;
        $this->teacher_name = $group->teacher->user->name;
        $this->group_name = $group->name;
    }

    public function visitApproval()
    {
        $this->validate(['oversight_member_id' => 'required|numeric',
            'visit_date' => 'required|date|date_format:Y-m-d',
        ]);

        $visitOrder = VisitOrder::create([
            'hostable_type' => 'App\Models\Teacher',
            'hostable_id' => $this->teacher_id,
            'oversight_member_id' => $this->oversight_member_id,
            'datetime' => $this->visit_date . ' ' . date('H:i:s', time()),
        ]);

        // start push notifications to oversight member
        $visitOrder->oversight_member->user->notify(new NewVisitOrderForOversightMemberNotify([
            'id' => $visitOrder->id,
            'hostname' => $this->teacher_name,
            'host_type' => $visitOrder->hostable_type,
            'datetime' => $visitOrder->datetime,
        ]));
        $title = "طلب زيارة جديد";
        $message = "لقد قام مشرف الرقابة بتعيينك زائر (رقابة) على حلقة المحفظ: " . $this->teacher_name . " بتاريخ: " . $this->visit_date . " يرجى مراجعة تفاصيل الزيارة.";
        $this->push_notification($message, $title, [$visitOrder->oversight_member->user->user_fcm_token->device_token]);
        // end push notifications to oversight member

        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية اعتماد طلب الزيارة بنجاح.']);
        $this->dispatchBrowserEvent('hideDialog');
        $this->clearForm();
    }


    public function clearForm()
    {
        $this->oversight_member_id = null;
        $this->teacher_id = null;
        $this->teacher_name = null;
        $this->group_id = null;
        $this->group_name = null;
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

    public function all_Groups()
    {
        return Group::query()
            ->with(['teacher.user','teacher.visit_orders'])
            ->search($this->search)
            ->when(!empty($this->selectedGradeId), function ($q, $v) {
                $q->where('grade_id', '=', $this->selectedGradeId);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Groups();
    }

    public function all_Grades()
    {
        return Grade::all();
    }

    public function all_Oversight_Members()
    {
        return OversightMember::with(['user'])->get();
    }
}
