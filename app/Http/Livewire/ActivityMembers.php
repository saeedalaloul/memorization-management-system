<?php

namespace App\Http\Livewire;

use App\Models\ActivityMember;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ActivityMembers extends HomeComponent
{
    public $selectedRoleId;
    public $roles = [], $search_;
    protected $queryString = ['search_' => ['except' => '']];

    public function render()
    {
        return view('livewire.activity-members', ['activity_members' => $this->all_Activity_Members(),
            'users' => $this->all_Users()]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Roles();
    }


    public function store($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user) {
                ActivityMember::create(['id' => $id]);
                $roleId = Role::select('*')->where('name', '=', 'منشط')->get();
                $user->assignRole([$roleId]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم إضافة عضو الأنشطة بنجاح.']);
                Cache::forget(ActivityMember::CACHE_KEY);
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function destroy($id)
    {
        $activityMember = ActivityMember::find($id);
        if ($activityMember !== null) {
            if ($activityMember->activities->count() > 0) {
                $this->catchError = "عذرا لا يمكن حذف المنشط بسبب وجود أنشطة مسجلة باسم المنشط";
                $this->dispatchBrowserEvent('hideDialog');
            } else if ($activityMember->activities_orders->count() > 0) {
                $this->catchError = "عذرا لا يمكن حذف المنشط بسبب وجود طلبات أنشطة لديه يرجى إجرائها أو حذفها";
                $this->dispatchBrowserEvent('hideDialog');
            } else {
                $roleId = Role::select('*')->where('name', '=', 'منشط')->first();
                $activityMember->user->removeRole($roleId);
                $activityMember->delete();
                $this->dispatchBrowserEvent('hideDialog');
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'تم حذف عضو الأنشطة بنجاح.']);
                Cache::forget(ActivityMember::CACHE_KEY);
            }
        }
    }

    public function delete()
    {
        $this->destroy($this->modalId);
    }

    public function getModalData($id)
    {
        $this->modalId = $id;
    }

    public function all_Activity_Members()
    {
        return ActivityMember::query()
            ->with(['user'])
            ->withCount(['activities'])
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Activity_Members();
    }

    public function all_Users()
    {
        return User::query()
            ->with(['activity_member'])
            ->select(['id', 'name'])
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', [User::ADMIN_ROLE, User::ACTIVITIES_SUPERVISOR_ROLE, User::ACTIVITY_MEMBER_ROLE,
                    User::OVERSIGHT_SUPERVISOR_ROLE, User::OVERSIGHT_MEMBER_ROLE, User::TEACHER_ROLE, User::EXAMS_SUPERVISOR_ROLE,
                    User::TESTER_ROLE, User::SUPERVISOR_ROLE, User::COURSES_SUPERVISOR_ROLE]);
            })
            ->when(!empty($this->selectedRoleId), function ($q, $v) {
                $q->whereRelation('roles', 'id', '=', $this->selectedRoleId);
            })
            ->search($this->search_)
            ->paginate($this->perPage);
    }

    public function submitSearch_()
    {
        $this->all_Users();
    }

    public function all_Roles()
    {
        $this->roles = Role::query()->whereNotIn('name', ['طالب', 'ولي أمر الطالب'])->get();
    }
}
