<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\VisitOrder;
use App\Notifications\SendVisitOrderForOversightSupervisorNotify;
use App\Traits\NotificationTrait;
use Spatie\Permission\Models\Role;

class TodayVisits extends HomeComponent
{
    use NotificationTrait;

    public $visitOrder, $notes, $suggestions, $recommendations;
    public $isVisitOfStart = false;

    public function render()
    {
        return view('livewire.today-visits', [
            'visits_today' => $this->all_Visits_Today(),
        ]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
    }

    public function messages()
    {
        return [
            'notes.required' => 'حقل الملاحظات مطلوب',
            'notes.string' => 'حقل الملاحظات يجب أن يحتوي على نص',
            'suggestions.required' => 'حقل الاقتراحات مطلوب',
            'suggestions.string' => 'حقل الاقتراحات يجب أن يحتوي على نص',
            'recommendations.required' => 'حقل التوصيات مطلوب',
            'recommendations.string' => 'حقل التوصيات يجب أن يحتوي على نص',
        ];
    }

    public function visitOfStart($id)
    {
        $this->visitOrder = VisitOrder::where('id', $id)->first();
        if ($this->visitOrder) {
            if ($this->visitOrder->status == VisitOrder::IN_PENDING_STATUS) {
                if ($this->current_role == 'مراقب') {
                    $this->initializeVisitStartInputs();
                }
            }
        }
    }

    private function initializeVisitStartInputs()
    {
        $this->isVisitOfStart = true;
        $this->modalId = $this->visitOrder->id;
    }

    public function storeVisit()
    {
        $this->validate([
            'notes' => 'required|string',
            'suggestions' => 'required|string',
            'recommendations' => 'required|string',
        ]);

        $this->visitOrder->update([
            'notes' => $this->notes,
            'suggestions' => $this->suggestions,
            'recommendations' => $this->recommendations,
            'status' => VisitOrder::IN_SENDING_STATUS,
        ]);

        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية حفظ الزيارة بنجاح.']);
        $this->clearForm();
    }

    public function sendVisit()
    {
        $this->validate([
            'notes' => 'required|string',
            'suggestions' => 'required|string',
            'recommendations' => 'required|string',
        ]);

        $this->visitOrder->update([
            'notes' => $this->notes,
            'suggestions' => $this->suggestions,
            'recommendations' => $this->recommendations,
            'status' => VisitOrder::IN_APPROVAL_STATUS,
        ]);

        // start push notifications to oversight supervisor
        $role = Role::where('name', User::OVERSIGHT_SUPERVISOR_ROLE)->first();
        $role_users = $role->users();
        if ($role_users->first()) {
            $title = "طلب زيارة قيد الإعتماد";
            $message = "";
            $hostname = "";
            if ($this->visitOrder->hostable_type == 'App\Models\Teacher') {
                $hostname = $this->visitOrder->hostable->user->name;
                $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المحفظ " . $hostname . " يرجى مراجعة طلب الزيارة. ";
            } else if ($this->visitOrder->hostable_type == 'App\Models\Tester') {
                $hostname = $this->visitOrder->hostable->user->name;
                $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المختبر " . $hostname . " يرجى مراجعة طلب الزيارة. ";
            } else if ($this->visitOrder->hostable_type == 'App\Models\ActivityMember') {
                $hostname = $this->visitOrder->hostable->user->name;
                $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المنشط " . $hostname . " يرجى مراجعة طلب الزيارة. ";
            }

            $role_users->first()->notify(new SendVisitOrderForOversightSupervisorNotify([
                'id' => $this->visitOrder->id,
                'hostname' => $hostname,
                'host_type' => $this->visitOrder->hostable_type,
                'oversight_member_name' => $this->visitOrder->oversight_member->user->name,
                'datetime' => $this->visitOrder->datetime,
            ]));

            $this->push_notification($message, $title, [$role_users->first()->user_fcm_token->device_token]);
        }
        // end push notifications to oversight supervisor
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية إرسال الزيارة بنجاح.']);
        $this->clearForm();
    }

    public function clearForm()
    {
        $this->modalId = '';
        $this->visitOrder = null;
        $this->notes = null;
        $this->suggestions = null;
        $this->recommendations = null;
        $this->visitOrder = null;
        $this->isVisitOfStart = false;
        $this->resetValidation();
    }

    public function all_Visits_Today()
    {
        return VisitOrder::query()
            ->with(['oversight_member.user'])
            ->search($this->search)
            ->todayvisits()
            ->when($this->current_role == 'مراقب', function ($q, $v) {
                $q->where('oversight_member_id', auth()->id());
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Visits_Today();
    }
}
