<?php

namespace App\Http\Livewire;

use App\Models\Tester;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class Testers extends HomeComponent
{
    public $selectedRoleId;
    public $roles = [], $search_;
    protected $queryString = ['search_' => ['except' => '']];

    public function render()
    {
        return view('livewire.testers', ['testers_' => $this->all_Testers_(), 'users' => $this->all_Users(),]);
    }

    public function mount()
    {
        $this->all_Roles();
    }

    public function store($id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);
            if ($user) {
                Tester::create(['id' => $id]);
                $roleId = Role::select('*')->where('name', '=', 'مختبر')->get();
                $user->assignRole([$roleId]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تم إضافة المختبر بنجاح.']);
                Cache::forget(Tester::CACHE_KEY);
                DB::commit();
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function destroy($id)
    {
        $tester = Tester::find($id);
        if ($tester != null) {
            $this->dispatchBrowserEvent('hideDialog');
            if ($tester->exams->count() > 0) {
                $this->catchError = "عذرا لا يمكن حذف المختبر بسبب وجود اختبارات مسجلة باسم المختبر";
            } else {
                if ($tester->exams_orders->count() > 0) {
                    $this->catchError = "عذرا لا يمكن حذف المختبر بسبب وجود طلبات اختبارات لديه يرجى إجرائها أو حذفها";
                } else {
                    $roleId = Role::select('*')->where('name', '=', 'مختبر')->first();
                    $tester->user->removeRole($roleId);
                    $tester->delete();
                    Cache::forget(Tester::CACHE_KEY);
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'تم حذف المختبر بنجاح.']);
                }
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

    public function all_Testers_()
    {
        return Tester::query()
            ->with(['user'])
            ->withCount(['exams'])
            ->search($this->search)
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Testers_();
    }

    public function all_Users()
    {
        return User::query()
            ->with(['tester'])
            ->select(['id', 'name'])
            ->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['طالب', 'ولي أمر الطالب']);
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
