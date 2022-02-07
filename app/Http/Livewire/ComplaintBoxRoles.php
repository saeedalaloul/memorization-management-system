<?php

namespace App\Http\Livewire;

use App\Models\ComplaintBoxRole;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class ComplaintBoxRoles extends Component
{
    use WithPagination;

    public $modalId;
    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $roles;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.complaint-box-roles', ['complaint_box_roles' => $this->all_ComplaintBoxRoles()]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->all_Roles();
    }

    public function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }

    public function modelData()
    {
        $data = [
            'id' => $this->modalId,
        ];
        return $data;
    }

    public function rules()
    {
        return ['modalId' => 'required|unique:complaint_box_roles,id,'];
    }

    public function messages()
    {
        return [
            'modalId.required' => 'حقل اسم التصنيف مطلوب',
            'modalId.unique' => 'اسم التصنيف موجود مسبقا',
        ];
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->modalId = null;
    }

    public function store()
    {
        $this->validate();

        ComplaintBoxRole::create($this->modelData());

        $this->modalFormReset();
        $this->emit('complaintBoxRoleAdded');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حفظ معلومات الدور بنجاح.']);
    }

    public function destroy($id)
    {
        ComplaintBoxRole::where('id', $id)->delete();
        $this->emit('complaintBoxRoleDeleted');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => 'تم حذف الدور بنجاح.']);
    }

    public function all_ComplaintBoxRoles()
    {
        if (auth()->user()->current_role == 'أمير المركز') {
            return ComplaintBoxRole::query()
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }
        return [];
    }

    public function all_Roles()
    {
        $this->roles = Role::all();
    }
}
