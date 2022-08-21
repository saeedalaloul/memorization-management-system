<?php

namespace App\Http\Livewire;

use Spatie\Permission\Models\Role;

class Roles extends HomeComponent
{
    public array $role_permissions = [];
    public string $role_name = '';

    public function render()
    {
        return view('livewire.roles', ['roles' => $this->all_Roles()]);
    }

    public function edit_permission($id)
    {
        $role = Role::findById($id);
        if ($role) {
            $this->process_type = 'edit_permission';
            $this->modalId = $id;
            $this->role_name = $role->name;
            $this->role_permissions = $role->permissions()->get()->toArray();
        }
    }

    public function update_role_permission($permission)
    {
        if (!empty($this->modalId)) {
            $role = Role::findById($this->modalId);
            if ($role->hasPermissionTo($permission)) {
                if ($role->name == 'أمير المركز') {
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'error', 'message' => 'عذرا لا يمكنك سحب صلاحيات أمير المركز.']);
                } else {
                    $role->revokePermissionTo($permission);
                }
            } else {
                $role->givePermissionTo($permission);
            }
        }
    }

    public function all_Roles()
    {
        return Role::query()
            ->withCount('users')
            ->when(!empty($this->search),function ($q){
              $q->search($this->search);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Roles();
    }

}
