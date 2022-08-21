<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Visit;
use App\Models\VisitProcessingReminder;
use App\Notifications\FailureProcessingOfVisitForAdminNotify;
use App\Notifications\ReplyToVisitForOversightSupervisorNotify;
use App\Notifications\SolvedVisitForAdminNotify;
use App\Traits\NotificationTrait;
use Spatie\Permission\Models\Role;

class Visits extends HomeComponent
{
    use NotificationTrait;

    public $visibleDetailsModalId;
    public $visit, $reply, $notes, $suggestions, $recommendations, $visit_processing_date;
    public $selectedVisitTypeId, $selectedStatusId;

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
    }

    public function sendMessage($msg)
    {
        $this->catchError = $msg;
    }

    public function render()
    {
        return view('livewire.visits', [
            'visits' => $this->all_Visits(),]);
    }

    public function showDetailsModal($id)
    {
        $this->visibleDetailsModalId = $id;
    }

    public function visitDetailsShow($id)
    {
        $this->process_type = 'visitDetailsShow';
        $this->visit = Visit::where('id', $id)->first();
    }

    public function visitReply($id)
    {
        $this->process_type = 'visitReply';
        $this->visit = Visit::where('id', $id)->first();
        $this->notes = $this->visit->notes;
        $this->suggestions = $this->visit->suggestions;
        $this->recommendations = $this->visit->recommendations;
    }

    public function storeReplyVisit()
    {
        $this->validate([
            'reply' => 'required|string',
        ]);

        if ($this->current_role == 'أمير المركز') {
            $this->visit->update([
                'reply' => $this->reply,
                'status' => Visit::REPLIED_STATUS,
            ]);

            // start push notifications to oversight supervisor
            $role = Role::where('name', User::OVERSIGHT_SUPERVISOR_ROLE)->first();
            $role_users = $role->users();
            if ($role_users->first()) {
                $title = "الرد على زيارة";
                $message = "";
                $hostname = "";
                if ($this->visit->hostable_type == 'App\Models\Teacher') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام أمير المركز: " . auth()->user()->name . " بالرد على زيارة المحفظ: " . $hostname . " يرجى مراجعة تفاصيل الرد. ";
                } else if ($this->visit->hostable_type == 'App\Models\Tester') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام أمير المركز: " . auth()->user()->name . " بالرد على زيارة المختبر: " . $hostname . " يرجى مراجعة تفاصيل الرد. ";
                } else if ($this->visit->hostable_type == 'App\Models\ActivityMember') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام أمير المركز: " . auth()->user()->name . " بالرد على زيارة المنشط: " . $hostname . " يرجى مراجعة تفاصيل الرد. ";
                }

                $role_users->first()->notify(new ReplyToVisitForOversightSupervisorNotify([
                    'id' => $this->visit->id,
                    'hostname' => $hostname,
                    'host_type' => $this->visit->hostable_type,
                    'oversight_member_name' => $this->visit->oversight_member->user->name,
                    'datetime' => $this->visit->datetime,
                ]));

                $this->push_notification($message, $title, [$role_users->first()->user_fcm_token->device_token]);
            }
            // end push notifications to oversight supervisor

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية الرد على الزيارة بنجاح.']);
            $this->clearForm();
        }
    }

    public function visitSolved($id)
    {
        if ($this->current_role == 'مشرف الرقابة') {
            $this->visit = Visit::where('id', $id)->first();

            $this->visit->update([
                'status' => Visit::SOLVED_STATUS,
            ]);

            $this->visit->visit_processing_reminder?->delete();

            // start push notifications to admin
            $role = Role::where('name', User::ADMIN_ROLE)->first();
            $role_users = $role->users();
            if ($role_users->first()) {
                $title = "تمت عملية معالجة زيارة";
                $message = "";
                $hostname = "";
                if ($this->visit->hostable_type == 'App\Models\Teacher') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد تمت عملية معالجة زيارة المحفظ " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                } else if ($this->visit->hostable_type == 'App\Models\Tester') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد تمت عملية معالجة زيارة المختبر " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                } else if ($this->visit->hostable_type == 'App\Models\ActivityMember') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد تمت عملية معالجة زيارة المنشط " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                }

                $role_users->first()->notify(new SolvedVisitForAdminNotify([
                    'id' => $this->visit->id,
                    'hostname' => $hostname,
                    'host_type' => $this->visit->hostable_type,
                ]));
                $this->push_notification($message, $title, [$role_users->first()->user_fcm_token->device_token]);
            }
            // end push notifications to admin


            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية معالجة الزيارة بنجاح.']);
        }
    }

    public function lunchModalVisitProcessing($id)
    {
        if ($this->current_role == 'مشرف الرقابة') {
            $this->visit = Visit::where('id', $id)->first();
        }
    }

    public function visitProcessing()
    {
        if ($this->current_role == 'مشرف الرقابة') {
            $this->validate([
                'visit_processing_date' => 'required|date|date_format:Y-m-d',
            ]);

            $visitProcessingReminder = VisitProcessingReminder::find($this->visit->id);

            if ($visitProcessingReminder) {
                $visitProcessingReminder->update(['reminder_datetime' => $this->visit_processing_date . ' ' . date('H:i:s', time())]);
            } else {
                VisitProcessingReminder::create([
                        'id' => $this->visit->id,
                        'reminder_datetime' => $this->visit_processing_date . ' ' . date('H:i:s', time()),
                    ]
                );
            }
            $this->visit->update(['status' => Visit::IN_PROCESS_STATUS,]);
            $this->dispatchBrowserEvent('hideDialog');

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية تغيير حالة الزيارة إلى جاري المعالجة بنجاح.']);
            $this->clearForm();
        }
    }

    public function visitFailed($id)
    {
        if ($this->current_role == 'مشرف الرقابة') {
            $this->visit = Visit::where('id', $id)->first();

            $this->visit->update([
                'status' => Visit::FAILURE_STATUS,
            ]);

            $this->visit->visit_processing_reminder?->delete();

            // start push notifications to admin
            $role = Role::where('name', User::ADMIN_ROLE)->first();
            $role_users = $role->users();
            if ($role_users->first()) {
                $title = "فشل معالجة زيارة";
                $message = "";
                $hostname = "";
                if ($this->visit->hostable_type == 'App\Models\Teacher') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد فشل معالجة زيارة المحفظ " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                } else if ($this->visit->hostable_type == 'App\Models\Tester') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد فشل معالجة زيارة المختبر " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                } else if ($this->visit->hostable_type == 'App\Models\ActivityMember') {
                    $hostname = $this->visit->hostable->user->name;
                    $message = "لقد قام مشرف الرقابة: " . auth()->user()->name . " بإعتماد فشل معالجة زيارة المنشط " . $hostname . " يرجى مراجعة تفاصيل الزيارة. ";
                }

                $role_users->first()->notify(new FailureProcessingOfVisitForAdminNotify([
                    'id' => $this->visit->id,
                    'hostname' => $hostname,
                    'host_type' => $this->visit->hostable_type,
                ]));
                $this->push_notification($message, $title, [$role_users->first()->user_fcm_token->device_token]);
            }
            // end push notifications to admin

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية فشل معالجة الزيارة بنجاح.']);
        }
    }

    public function clearForm()
    {
        $this->process_type = '';
        $this->reset('visibleDetailsModalId', 'visit', 'reply', 'notes', 'suggestions', 'recommendations', 'visit_processing_date');
        $this->resetValidation();
    }

    public function all_Visits()
    {
        return Visit::query()
            ->with(['oversight_member.user:id,name', 'visit_processing_reminder'])
            ->search($this->search)
            ->when(!empty($this->selectedStatusId), function ($q, $v) {
                $q->where('status', $this->selectedStatusId);
            })
            ->when(!empty($this->selectedVisitTypeId), function ($q, $v) {
                $q->where('hostable_type', $this->selectedVisitTypeId);
            })
            ->when($this->current_role == 'مراقب', function ($q, $v) {
                $q->where('oversight_member_id', auth()->id());
            })
            ->when(!empty(strval(\Request::segment(2)) && strval(\Request::segment(2)) != 'message'), function ($q, $v) {
                $q->where('id', \Request::segment(2));
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Visits();
    }

    public function messages()
    {
        return [
            'reply.required' => 'حقل الرد مطلوب',
            'reply.string' => 'حقل الرد يجب أن يحتوي على نص',
            'visit_processing_date.required' => 'حقل تاريخ المعالجة مطلوب',
            'visit_processing_date.date' => 'حقل تاريخ المعالجة يجب أن يكون تاريخ',
            'visit_processing_date.date_format' => 'حقل تاريخ المعالجة يجب أن يكون من نوع تاريخ',
        ];
    }
}
