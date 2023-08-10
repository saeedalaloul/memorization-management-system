<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Roles extends HomeComponent
{
    public $permissions = [], $category_permission_id, $permission_id, $minute, $hour, $day,
        $week, $month, $current_timeStamp, $isWithoutTime = false, $isFoundPermission = false;
    public string $role_name = '';

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
    }

    public function render()
    {
        $this->calcCurrentTimeStamp();
        return view('livewire.roles', ['roles' => $this->all_Roles()]);
    }

    public function edit_permission($id)
    {
        $role = Role::findById($id);
        if ($role !== null) {
            $this->modalId = $id;
            $this->role_name = $role->name;
            $this->dispatchBrowserEvent('showDialogEditPermission');
        }
    }

    public function updatedCategoryPermissionId()
    {
        if ($this->category_permission_id === 'المراحل') {
            $this->permissions = Permission::query()->whereIn('id', [27, 28, 29, 30])->get();
        } elseif ($this->category_permission_id === 'المجموعات') {
            $this->permissions = Permission::query()->whereIn('id', [21, 22, 23, 24, 25, 26])->get();
        } elseif ($this->category_permission_id === 'الكفالات') {
            $this->permissions = Permission::query()->whereIn('id', [ 66, 67, 68,69,70,71,72,73])->get();
        } elseif ($this->category_permission_id === 'مشرفي المراحل') {
            $this->permissions = Permission::query()->whereIn('id', [17, 18, 19, 20])->get();
        } elseif ($this->category_permission_id === 'المحفظين') {
            $this->permissions = Permission::query()->whereIn('id', [12, 13, 14, 15, 16])->get();
        } elseif ($this->category_permission_id === 'الطلاب') {
            $this->permissions = Permission::query()->whereIn('id', [3, 4, 5, 6, 7, 8, 9, 10, 11, 63, 64])->get();
        } elseif ($this->category_permission_id === 'الإختبارات') {
            $this->permissions = Permission::query()->whereIn('id', [31, 32, 33, 34,65])->get();
        } elseif ($this->category_permission_id === 'طلبات الإختبارات') {
            $this->permissions = Permission::query()->whereIn('id', [35, 36, 37])->get();
        } elseif ($this->category_permission_id === 'المختبرين') {
            $this->permissions = Permission::query()->whereIn('id', [38, 39, 40])->get();
        } elseif ($this->category_permission_id === 'الأنشطة') {
            $this->permissions = Permission::query()->whereIn('id', [47, 48, 49, 50, 51, 52])->get();
        } elseif ($this->category_permission_id === 'أعضاء الأنشطة') {
            $this->permissions = Permission::query()->whereIn('id', [45, 46])->get();
        } elseif ($this->category_permission_id === 'أعضاء الرقابة') {
            $this->permissions = Permission::query()->whereIn('id', [43, 44])->get();
        } elseif ($this->category_permission_id === 'صندوق الشكاوي والرقابة') {
            $this->permissions = Permission::query()->whereIn('id', [41, 42])->get();
        } elseif ($this->category_permission_id === 'الإجراءات العقابية') {
            $this->permissions = Permission::query()->whereIn('id', [57, 58, 59, 60])->get();
        } elseif ($this->category_permission_id === 'إدارة التقارير') {
            $this->permissions = Permission::query()->whereIn('id', [61, 62])->get();
        } elseif ($this->category_permission_id === 'المستخدمين') {
            $this->permissions = Permission::query()->whereIn('id', [1, 2, 53, 54, 55, 56])->get();
        } else {
            $this->permissions = [];
            $this->permission_id = null;
        }
    }

    public function updateUserPermission()
    {
        $this->validate([
            'category_permission_id' => 'required|string',
            'permission_id' => 'required|string',
        ]);

        $messageBag = new MessageBag();
        $isComplete = true;
        if (!$this->isWithoutTime && $this->current_timeStamp === null) {
            $messageBag->add('minute', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('hour', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('day', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('week', 'يجب تحديد خيار واحد على الأقل.');
            $messageBag->add('month', 'يجب تحديد خيار واحد على الأقل.');
            $this->setErrorBag($messageBag);
            $isComplete = false;
        }

        if ($isComplete) {
            $current_time = Carbon::now()->addMinutes($this->minute)
                ->addHours($this->hour)
                ->addDays($this->day)
                ->addWeeks($this->week)
                ->addMonths($this->month)->toDateTime();

            DB::table('role_has_permissions')
                ->updateOrInsert(
                    ['permission_id' => $this->permission_id,
                        'role_id' => $this->modalId],
                    ['expiration_datetime' => !$this->isWithoutTime ? $current_time : null]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تعيين صلاحية للدور بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
            $this->modalFormReset();
        }
    }

    public function deleteUserPermission()
    {
        if ($this->permission_id !== null && $this->modalId !== '') {
            DB::table('role_has_permissions')
                ->where('permission_id', $this->permission_id)
                ->where('role_id', $this->modalId)->delete();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم سحب الصلاحية من الدور بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
            $this->modalFormReset();
        }
    }

    public function updatedPermissionId()
    {
        $this->isFoundPermission = false;
        $this->isWithoutTime = false;
        if ($this->permission_id !== null && $this->modalId !== '') {
            $permission = DB::table('role_has_permissions')
                ->where('permission_id', $this->permission_id)
                ->where('role_id', $this->modalId)->first();
            $this->isFoundPermission = $permission !== null;
            if ($permission !== null) {
                $this->isWithoutTime = $permission->expiration_datetime === null;
            }
        }
    }

    public function updatedIsWithoutTime($value)
    {
        if ($value === true) {
            $this->reset('current_timeStamp', 'minute', 'hour', 'day', 'week', 'month');
            $this->resetValidation([$this->current_timeStamp, $this->minute, $this->hour, $this->day, $this->week, $this->month]);
        }
    }

    private function calcCurrentTimeStamp()
    {
        if ($this->minute !== null || $this->hour !== null || $this->day !== null || $this->week !== null || $this->month !== null) {
            $this->current_timeStamp = Carbon::parse(Carbon::now()->addMinutes($this->minute)
                ->addHours($this->hour)
                ->addDays($this->day)
                ->addWeeks($this->week)
                ->addMonths($this->month)->toDateTime())->translatedFormat('l j F Y h:i a');
        }
    }

    public function all_Roles()
    {
        return Role::query()
            ->withCount('users')
            ->when(!empty($this->search), function ($q) {
                $q->search($this->search);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Roles();
    }

    public function messages()
    {
        return [
            'category_permission_id.required' => 'حقل تصنيف الصلاحية مطلوب',
            'category_permission_id.string' => 'يجب إدخال نص في حقل تصنيف الصلاحية',
            'permission_id.required' => 'حقل الصلاحية مطلوب',
            'permission_id.string' => 'يجب إدخال نص في حقل الصلاحية',
        ];
    }

    public
    function modalFormReset()
    {
        $this->resetValidation();
        $this->modalId = '';
        $this->category_permission_id = null;
        $this->permission_id = null;
        $this->current_timeStamp = null;
        $this->minute = null;
        $this->hour = null;
        $this->day = null;
        $this->week = null;
        $this->month = null;
        $this->isWithoutTime = false;
        $this->isFoundPermission = false;
    }
}
