<?php

namespace App\Http\Livewire;

use App\Models\OversightMember;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class OversightMembers extends HomeComponent
{
    public $search_ = '';
    public $roles;
    public $oversight_member_id;
    public $selectedRoleId;

    protected $queryString = ['search_' => ['except' => '']];


    public function render()
    {
        return view('livewire.oversight-members', ['oversight_members' => $this->all_Oversight_Members(), 'users' => $this->all_Users(),]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Roles();
    }

    public function modalFormReset()
    {
        $this->resetValidation();
        $this->modalId = '';
    }

    public function store($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user) {
                OversightMember::create(['id' => $id]);
                $roleId = Role::select('*')->where('name', '=', 'مراقب')->get();
                $user->assignRole([$roleId]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم إضافة عضو الرقابة بنجاح.']);
                Cache::forget(OversightMember::CACHE_KEY);
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function getModalData($id)
    {
        $this->modalId = $id;
    }

    public function destroy($id)
    {
        $oversightMember = OversightMember::find($id);
        if ($oversightMember !== null) {
            if ($oversightMember->visits->count() > 0) {
                $this->catchError = "عذرا لا يمكن حذف المراقب بسبب وجود زيارات مسجلة باسم المراقب";
                $this->dispatchBrowserEvent('hideDialog');
            } else if ($oversightMember->visits_orders->count() > 0) {
                $this->catchError = "عذرا لا يمكن حذف المراقب بسبب وجود طلبات زيارات لديه يرجى إجرائها أو حذفها";
                $this->dispatchBrowserEvent('hideDialog');
            } else {
                $roleId = Role::select('*')->where('name', '=', 'مراقب')->first();
                $oversightMember->user->removeRole($roleId);
                $oversightMember->delete();
                $this->dispatchBrowserEvent('hideDialog');
                Cache::forget(OversightMember::CACHE_KEY);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'تم حذف عضو الرقابة بنجاح.']);
                $this->modalFormReset();
            }
        }
    }

    public function delete()
    {
        $this->destroy($this->modalId);
    }


    public function all_Oversight_Members()
    {
        return OversightMember::query()
            ->with(['user'])
            ->withCount(['visits'])
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Oversight_Members();
    }

    public function all_Users()
    {
        return User::query()
            ->with(['oversight_member'])
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

    public function submitSearch_(){
        $this->all_Users();
    }

    public function all_Roles()
    {
        $this->roles = Role::query()->whereNotIn('name', ['طالب', 'ولي أمر الطالب'])->get();
    }
}
