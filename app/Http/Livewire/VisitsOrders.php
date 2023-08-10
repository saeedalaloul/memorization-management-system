<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Visit;
use App\Models\VisitOrder;
use App\Notifications\NewVisitForAdminNotify;
use App\Notifications\SendVisitOrderForOversightSupervisorNotify;
use App\Notifications\UpdateVisitOrderForOversightMemberNotify;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class VisitsOrders extends HomeComponent
{
    use NotificationTrait;

    public $visibleDetailsModalId;
    public $visitOrder, $notes, $suggestions, $recommendations;
    public $selectedVisitTypeId, $selectedStatusId;

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->link = 'manage_visits_orders/';
    }

    public function render()
    {
        return view('livewire.visits-orders', [
            'visit_orders' => $this->all_Visits_Orders(),]);
    }

    public function showDetailsModal($id)
    {
        $this->visibleDetailsModalId = $id;
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

    public function sendVisit($id)
    {
        if ($this->current_role === 'مراقب') {
            $visitOrder = VisitOrder::where('id', $id)->first();

            $visitOrder->update([
                'status' => VisitOrder::IN_APPROVAL_STATUS,
            ]);

            // start push notifications to oversight supervisor
            $role = Role::where('name', User::OVERSIGHT_SUPERVISOR_ROLE)->first();
            $role_users = $role->users();
            if ($role_users->first()) {
                $title = "طلب زيارة قيد الإعتماد";
                $message = "";
                $hostname = "";
                if ($visitOrder->hostable_type === 'App\Models\Teacher') {
                    $hostname = $visitOrder->hostable->user->name;
                    $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المحفظ " . $hostname . " يرجى مراجعة طلب الزيارة. ";
                } else if ($visitOrder->hostable_type === 'App\Models\Tester') {
                    $hostname = $visitOrder->hostable->user->name;
                    $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المختبر " . $hostname . " يرجى مراجعة طلب الزيارة. ";
                } else if ($visitOrder->hostable_type === 'App\Models\ActivityMember') {
                    $hostname = $visitOrder->hostable->user->name;
                    $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المنشط " . $hostname . " يرجى مراجعة طلب الزيارة. ";
                }

                $role_users->first()->notify(new SendVisitOrderForOversightSupervisorNotify([
                    'id' => $visitOrder->id,
                    'hostname' => $hostname,
                    'host_type' => $visitOrder->hostable_type,
                    'oversight_member_name' => $visitOrder->oversight_member->user->name,
                    'datetime' => $visitOrder->datetime,
                ]));
                $this->push_notification($message, $title,$this->link.$visitOrder->id, [$role_users->first()->user_fcm_token->device_token ?? null]);
            }
            // end push notifications to oversight supervisor

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية إرسال الزيارة بنجاح.']);
        }
    }

    public function sendVisitAfterEdit()
    {
        if ($this->current_role === 'مراقب') {
            $this->visitOrder->update([
                'status' => VisitOrder::IN_APPROVAL_STATUS,
            ]);

            // start push notifications to oversight supervisor
            $role = Role::where('name', User::OVERSIGHT_SUPERVISOR_ROLE)->first();
            $role_users = $role->users();
            if ($role_users->first()) {
                $title = "طلب زيارة قيد الإعتماد";
                $message = "";
                $hostname = "";
                if ($this->visitOrder->hostable_type === 'App\Models\Teacher') {
                    $hostname = $this->visitOrder->hostable->user->name;
                    $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المحفظ " . $hostname . " يرجى مراجعة طلب الزيارة. ";
                } else if ($this->visitOrder->hostable_type === 'App\Models\Tester') {
                    $hostname = $this->visitOrder->hostable->user->name;
                    $message = "لقد قام عضو الرقابة: " . auth()->user()->name . " بإرسال زيارة المختبر " . $hostname . " يرجى مراجعة طلب الزيارة. ";
                } else if ($this->visitOrder->hostable_type === 'App\Models\ActivityMember') {
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
                $this->push_notification($message, $title,$this->link.$this->visitOrder->id, [$role_users->first()->user_fcm_token->device_token ?? null]);
            }
            // end push notifications to oversight supervisor

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية إرسال الزيارة بنجاح.']);

            $this->clearForm();
        }
    }

    public function visitEditRequest($id)
    {
        $visitOrder = VisitOrder::where('id', $id)->first();
        if ($this->current_role === 'مشرف الرقابة') {
            $visitOrder->update([
                'status' => VisitOrder::IN_SENDING_STATUS,
            ]);

            // start push notifications to oversight member
            $title = "تعديل طلب زيارة";
            $message = "";
            $hostname = "";
            if ($visitOrder->hostable_type === 'App\Models\Teacher') {
                $hostname = $visitOrder->hostable->user->name;
                $message = "لقد قام مشرف الرقابة بطلب تعديل تفاصيل زيارة حلقة المحفظ: " . $hostname . " يرجى مراجعة تفاصيل الزيارة.";
            } else if ($visitOrder->hostable_type === 'App\Models\Tester') {
                $hostname = $visitOrder->hostable->user->name;
                $message = "لقد قام مشرف الرقابة بطلب تعديل تفاصيل زيارة المختبر: " . $hostname . " يرجى مراجعة تفاصيل الزيارة.";
            } else if ($visitOrder->hostable_type === 'App\Models\ActivityMember') {
                $hostname = $visitOrder->hostable->user->name;
                $message = "لقد قام مشرف الرقابة بطلب تعديل تفاصيل زيارة المنشط: " . $hostname . " يرجى مراجعة تفاصيل الزيارة.";
            }
            $visitOrder->oversight_member->user->notify(new UpdateVisitOrderForOversightMemberNotify([
                'id' => $visitOrder->id,
                'hostname' => $hostname,
                'host_type' => $visitOrder->hostable_type,
                'datetime' => $visitOrder->datetime,
            ]));

            $this->push_notification($message, $title,$this->link.$visitOrder->id, [$visitOrder->oversight_member->user->user_fcm_token->device_token ?? null]);
            // end push notifications to oversight member

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية طلب تعديل الزيارة بنجاح.']);
        }
    }

    public function approvalVisit($id)
    {
        $visitOrder = VisitOrder::where('id', $id)->first();
        if ($this->current_role === 'مشرف الرقابة') {
            DB::beginTransaction();
            try {
                $visit = Visit::create([
                    'hostable_type' => $visitOrder->hostable_type,
                    'hostable_id' => $visitOrder->hostable_id,
                    'datetime' => $visitOrder->datetime,
                    'oversight_member_id' => $visitOrder->oversight_member_id,
                    'notes' => $visitOrder->notes,
                    'suggestions' => $visitOrder->suggestions,
                    'recommendations' => $visitOrder->recommendations,
                ]);

                $visitOrder->delete();

                // start push notifications to admin
                $role = Role::where('name', User::ADMIN_ROLE)->first();
                $role_users = $role->users();
                if ($role_users->first()) {
                    $title = "زيارة جديدة";
                    $message = "";
                    $hostname = "";
                    if ($visit->hostable_type === 'App\Models\Teacher') {
                        $hostname = $visit->hostable->user->name;
                        $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد زيارة المحفظ " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                    } else if ($visit->hostable_type === 'App\Models\Tester') {
                        $hostname = $visit->hostable->user->name;
                        $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد زيارة المختبر " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                    } else if ($visit->hostable_type === 'App\Models\ActivityMember') {
                        $hostname = $visit->hostable->user->name;
                        $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد زيارة المنشط " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                    }

                    $role_users->first()->notify(new NewVisitForAdminNotify([
                        'id' => $visit->id,
                        'hostname' => $hostname,
                        'host_type' => $visit->hostable_type,
                        'oversight_member_name' => $visit->oversight_member->user->name,
                        'datetime' => $visit->datetime,
                    ]));
                    $this->push_notification($message, $title, 'manage_visits/'.$visit->id,[$role_users->first()->user_fcm_token->device_token ?? null]);
                }
                // end push notifications to admin

                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية اعتماد الزيارة بنجاح.']);
                DB::commit();
                $this->clearForm();
            } catch (\Exception $e) {
                DB::rollback();
                $this->catchError = $e->getMessage();
            }
        }
    }

    public function visitDetailsShow($id)
    {
        $this->process_type = 'visitDetailsShow';
        $this->visitOrder = VisitOrder::where('id', $id)->first();
    }

    public function visitDetailsEdit($id)
    {
        $this->process_type = 'visitDetailsEdit';
        $this->visitOrder = VisitOrder::where('id', $id)->first();
        $this->notes = $this->visitOrder->notes;
        $this->suggestions = $this->visitOrder->suggestions;
        $this->recommendations = $this->visitOrder->recommendations;
    }

    public function clearForm()
    {
        $this->process_type = '';
        $this->reset('notes', 'suggestions', 'recommendations');
        $this->resetValidation();
    }

    public function all_Visits_Orders()
    {
        return VisitOrder::query()
            ->with(['oversight_member.user'])
            ->search($this->search)
            ->when(!empty($this->selectedStatusId), function ($q, $v) {
                $q->where('status', $this->selectedStatusId);
            })
            ->when(!empty($this->selectedVisitTypeId), function ($q, $v) {
                $q->where('hostable_type', $this->selectedVisitTypeId);
            })
            ->when($this->current_role === 'مراقب', function ($q, $v) {
                $q->where('oversight_member_id', auth()->id())
                    ->when(empty($this->selectedStatusId), function ($q, $v) {
                        $q->whereIn('status', [VisitOrder::IN_PENDING_STATUS, VisitOrder::IN_SENDING_STATUS]);
                    });
            })
            ->when(!empty(strval(\Request::segment(2)) && strval(\Request::segment(2)) !== 'message'), function ($q, $v) {
                $q->where('id', \Request::segment(2));
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Visits_Orders();
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
}
