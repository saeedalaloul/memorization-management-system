<?php

namespace App\Http\Livewire;

use App\Models\ComplaintBoxCategory;
use Livewire\Component;
use Livewire\WithPagination;

class ComplaintBoxCategories extends Component
{
    use WithPagination;

    public $name;
    public $modalId;
    public $sortBy = 'name';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.complaint-box-categories', ['complaint_box_categories' => $this->all_ComplaintBoxCategories()]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'name' => 'required|unique:complaint_box_categories,name,' . $this->modalId,
        ]);
    }

    public function loadModalData($id)
    {
        $this->modalFormReset();
        $data = ComplaintBoxCategory::find($id);
        $this->modalId = $data->id;
        $this->name = $data->name;
    }

    public function modelData()
    {
        $data = [
            'name' => $this->name,
        ];
        return $data;
    }

    public function rules()
    {
        return ['name' => 'required|unique:complaint_box_categories,name,' . $this->modalId];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم التصنيف مطلوب',
            'name.unique' => 'اسم التصنيف موجود مسبقا',
        ];
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->name = null;
        $this->modalId = null;
    }

    public function store()
    {

        $this->validate();

        ComplaintBoxCategory::create($this->modelData());

        $this->modalFormReset();
        $this->emit('complaintBoxCategoryAdded');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم حفظ معلومات التصنيف بنجاح.']);
    }

    public function update()
    {
        $this->validate();
        $ComplaintBoxCategory = ComplaintBoxCategory::where('id', $this->modalId)->first();
        $ComplaintBoxCategory->update($this->modelData());
        $this->modalFormReset();
        $this->emit('complaintBoxCategoryEdited');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تم تحديث معلومات التصنيف بنجاح.']);
    }

    public function destroy($id)
    {
        ComplaintBoxCategory::where('id', $id)->delete();
        $this->emit('complaintBoxCategoryDeleted');
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => 'تم حذف التصنيف بنجاح.']);
    }

    public function all_ComplaintBoxCategories()
    {
        if (auth()->user()->current_role == 'أمير المركز') {
            return ComplaintBoxCategory::query()
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }
        return [];
    }
}
