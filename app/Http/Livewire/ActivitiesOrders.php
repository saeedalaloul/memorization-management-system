<?php

namespace App\Http\Livewire;

use App\Models\Activity;
use App\Models\ActivityMember;
use App\Models\ActivityOrder;
use App\Models\ActivityType;
use App\Models\Group;
use App\Models\Supervisor;
use App\Models\User;
use App\Notifications\AcceptActivityOrderForActivityMemberNotify;
use App\Notifications\AcceptActivityOrderForTeacherNotify;
use App\Notifications\FailureActivityOrderForTeacherNotify;
use App\Notifications\NewActivityOrderForActivitiesSupervisorNotify;
use App\Notifications\RejectionActivityOrderForTeacherNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ActivitiesOrders extends HomeComponent
{
    use NotificationTrait;

    public $activity_members, $activity_type_name, $students, $activity_date, $students_ids, $activity_type_id
    , $teacher_name, $activity_type, $activity_member_id, $notes;


    public function render()
    {
        return view('livewire.activities-orders', ['activities_orders' => $this->all_Activity_Orders(), 'activities_types' => $this->all_Activity_Types()]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->students = $this->all_Students();
        $this->activity_members = $this->all_Activties_Members();
        $this->link = 'manage_activities_orders/';
    }

    public function rules()
    {
        return [
            'activity_date' => 'required|date|date_format:Y-m-d\TH:i|after_or_equal:' . Carbon::today()->format('Y-m-d\TH:i'),
            'students_ids' => 'required|array|min:2',
        ];
    }

    public function messages()
    {
        return [
            'activity_date.required' => 'حقل تاريخ النشاط مطلوب',
            'activity_date.date' => 'حقل تاريخ النشاط يجب أن يكون تاريخ ووقت',
            'activity_date.date_format' => 'حقل تاريخ النشاط يجب أن يكون من نوع تاريخ ووقت',
            'activity_date.after_or_equal' => 'حقل تاريخ النشاط يجب أن يكون أكبر من أو يساوي التاريخ الحالي',
            'students_ids.required' => 'حقل اختيار الطلاب مطلوب',
            'students_ids.array' => 'حقل اختيار الطلاب يجب أن يكون قائمة',
            'students_ids.min' => 'يجب أن لا يقل عدد الطلاب عن 2 طلاب',
            'activity_member_id.required' => 'اسم المنشط مطلوب',
            'activity_member_id.numeric' => 'حقل المنشط يجب أن يكون رقم',
            'notes.required' => 'حقل الملاحظات مطلوب',
            'notes.string' => 'حقل الملاحظات يجب أن يحتوي على نص',
        ];
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->modalId = '';
        $this->activity_type_name = null;
        $this->activity_type_id = null;
        $this->activity_date = null;
        $this->students_ids = null;
        $this->catchError = '';
    }

    public function activityRequest($id)
    {
        $this->clearForm();
        $activity_type = ActivityType::where('id', $id)->first();
        if ($activity_type) {
            $this->activity_type_id = $id;
            $this->activity_type_name = $activity_type->name;
            $this->dispatchBrowserEvent('showDialog');
        }
    }

    public function storeActivityRequest()
    {
        $this->validate();
        DB::beginTransaction();
        try {
            $activityOrder = ActivityOrder::create(
                [
                    'datetime' => $this->activity_date,
                    'activity_type_id' => $this->activity_type_id,
                    'teacher_id' => auth()->id(),
                ]
            );

            $activityOrder->students()->attach($this->students_ids);
            DB::commit();
            // start push notifications to activities supervisor
            $role = Role::where('name', User::ACTIVITIES_SUPERVISOR_ROLE)->first();
            $role_users = $role->users();
            if ($role_users->first()) {
                $role_users->first()->notify(new NewActivityOrderForActivitiesSupervisorNotify($activityOrder));
                $title = "طلب نشاط جديد";
                $message = "لقد قام المحفظ: " . $activityOrder->teacher->user->name . " بطلب نشاط جديد للحلقة بتاريخ: " . Carbon::parse($activityOrder->datetime)->format('Y-m-d') . " يرجى مراجعة طلب النشاط.";
                $this->push_notification($message, $title, $this->link . $activityOrder->id ?? null, [$role_users->first()->user_fcm_token->device_token ?? null]);
            }
            // end push notifications to activities supervisor
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم إجراء طلب النشاط بنجاح.']);
            $this->modalFormReset();
            $this->dispatchBrowserEvent('hideDialog');
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function getActivityOrder($id)
    {
        $this->clearForm();
        $activityOrder = ActivityOrder::with(['teacher.user', 'activity_type'])->where('id', $id)->first();
        if ($activityOrder) {
            $this->modalId = $activityOrder->id;
            $this->teacher_name = $activityOrder->teacher->user->name;
            $this->activity_type = $activityOrder->activity_type->name;
        }
    }

    public function activityOrderApproval($id)
    {
        $this->validate([
            'activity_member_id' => 'required|numeric',
        ]);

        if ($this->current_role === 'مشرف الأنشطة' || $this->current_role === 'أمير المركز') {
            $activityOrder = ActivityOrder::where('id', $id)->first();

            $activityOrder->update([
                'status' => ActivityOrder::ACCEPTABLE_STATUS,
                'activity_member_id' => $this->activity_member_id,
            ]);

            // start push notifications to teacher
            $activityOrder->teacher->user->notify(new AcceptActivityOrderForTeacherNotify($activityOrder));
            $title = "طلب نشاط معتمد";
            $message = "لقد قام مشرف الأنشطة باعتماد طلب نشاط: " . $activityOrder->activity_type->name . " وتعيين المنشط: " . $activityOrder->activity_member->user->name . " للإشراف على النشاط.";
            $this->push_notification($message, $title, $this->link . $activityOrder->id ?? null, [$activityOrder->teacher->user->user_fcm_token->device_token ?? null]);
            // end push notifications to teacher

            // start push notifications to activity member
            $activityOrder->activity_member->user->notify(new AcceptActivityOrderForActivityMemberNotify($activityOrder));
            $message = "لقد قام مشرف الأنشطة بتعيينك منشط طلب نشاط حلقة المحفظ: " . $activityOrder->teacher->user->name . " بتاريخ: " . Carbon::parse($activityOrder->datetime)->format('Y-m-d') . " يرجى مراجعة تفاصيل النشاط للمتابعة.";
            $this->push_notification($message, $title, $this->link . $activityOrder->id ?? null, [$activityOrder->activity_member->user->user_fcm_token->device_token ?? null]);
            // end push notifications to activity member


            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد طلب النشاط بنجاح.']);
            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();
        }
    }

    public function activityOrderRefusal($id)
    {
        $this->validate([
            'notes' => 'required|string',
        ]);

        if ($this->current_role === User::ACTIVITIES_SUPERVISOR_ROLE || $this->current_role === User::ADMIN_ROLE) {
            $activityOrder = ActivityOrder::where('id', $id)->first();

            $activityOrder->update([
                'status' => ActivityOrder::REJECTED_STATUS,
                'notes' => $this->notes,
                'activity_member_id' => null,
            ]);

            // start push notifications to teacher
            $activityOrder->teacher->user->notify(new RejectionActivityOrderForTeacherNotify($activityOrder));
            $title = "طلب نشاط مرفوض";
            $message = "لقد قام مشرف الأنشطة برفض طلب نشاط: " . $activityOrder->activity_type->name . " وذلك بسبب: " . $activityOrder->notes;
            $this->push_notification($message, $title, $this->link . $activityOrder->id ?? null, [$activityOrder->teacher->user->user_fcm_token->device_token ?? null]);
            // end push notifications to teacher

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية رفض طلب النشاط بنجاح.']);
            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();
        }
    }

    public function activity_completed($id)
    {
        $activityOrder = ActivityOrder::where('id', $id)->first();
        if ($activityOrder) {
            DB::beginTransaction();
            try {
                $activity = Activity::create([
                    'datetime' => $activityOrder->datetime,
                    'activity_type_id' => $activityOrder->activity_type_id,
                    'teacher_id' => $activityOrder->teacher_id,
                    'activity_member_id' => $activityOrder->activity_member_id,
                ]);

                $activity->students()->attach($activityOrder->students);


                $activityOrder->delete();

                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية إجراء النشاط بنجاح.']);
                DB::commit();
                $this->clearForm();
            } catch (\Exception $e) {
                DB::rollback();
                $this->catchError = $e->getMessage();
            }
        }
    }

    public function activity_failed()
    {
        $this->validate([
            'notes' => 'required|string',
        ]);

        if ($this->modalId) {
            $activityOrder = ActivityOrder::where('id', $this->modalId)->first();

            $activityOrder->update([
                'status' => ActivityOrder::FAILURE_STATUS,
                'notes' => $this->notes,
            ]);

            // start push notifications to teacher
            $activityOrder->teacher->user->notify(new FailureActivityOrderForTeacherNotify($activityOrder));
            $title = "طلب نشاط فشل إجراؤه";
            $message = "لقد قام المنشط بتغيير حالة طلب نشاط حلقة المحفظ: " . $activityOrder->teacher->user->name . " وذلك بسبب: " . $activityOrder->notes;
            $this->push_notification($message, $title, $this->link . $activityOrder->id ?? null, [$activityOrder->teacher->user->user_fcm_token->device_token ?? null]);
            // end push notifications to teacher

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية تغيير حالة طلب النشاط إلى فشل إجراء النشاط بنجاح.']);
            $this->dispatchBrowserEvent('hideDialog');
            $this->clearForm();
        }
    }

    public function destroy()
    {
        if ($this->modalId) {
            $activity_order = ActivityOrder::find($this->modalId);
            if ($activity_order->status === ActivityOrder::IN_PENDING_STATUS || $activity_order->status === ActivityOrder::REJECTED_STATUS || $activity_order->status == ActivityOrder::FAILURE_STATUS) {
                $activity_order->delete();
                $this->dispatchBrowserEvent('hideDialog');
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'تم حذف طلب النشاط بنجاح.']);
            }
        }
    }

    public function all_Activity_Orders()
    {
        return ActivityOrder::query()
            ->with(['students.user', 'activity_member.user', 'activity_type', 'teacher.user'])
            ->withCount(['students'])
            ->search($this->search)
            ->when($this->current_role === 'محفظ', function ($q, $v) {
                $q->where('teacher_id', auth()->id());
            })
            ->when($this->current_role === 'منشط', function ($q, $v) {
                $q->where('activity_member_id', auth()->id());
            })
            ->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->whereHas('students', function ($q) {
                    $q->where('grade_id', '=', Supervisor::whereId(auth()->id())->first()->grade_id);
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
        $this->all_Activity_Orders();
    }

    public function all_Activity_Types()
    {
        return ActivityType::query()
            ->with('activities_orders')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function all_Students()
    {
        if ($this->current_role === 'محفظ') {
            $students = Group::query()->where('teacher_id', auth()->id())
                ->with('students.user', function ($query) {
                    $query->select('id', 'name');
                })->with('students_sunnah.user', function ($query) {
                    $query->select('id', 'name');
                })->first();
            if ($students !== null) {
                if ($students->type === Group::SUNNAH_TYPE) {
                    return $students->toArray()['students_sunnah'];
                }

                return $students->toArray()['students'];
            }
            return [];
        }
        return [];
    }

    public function all_Activties_Members()
    {
        if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الأنشطة') {
            if (!Cache::has(ActivityMember::CACHE_KEY)) {
                Cache::rememberForever(ActivityMember::CACHE_KEY, static function () {
                    return ActivityMember::with('user')->get();
                });
            }
            return Cache::get(ActivityMember::CACHE_KEY);
        }
        return [];
    }

    private function clearForm()
    {
        $this->modalId = '';
        $this->activity_type = null;
        $this->teacher_name = null;
        $this->activity_member_id = null;
        $this->notes = null;
    }

}
