<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use Livewire\Component;
use Livewire\WithPagination;

class Grades extends Component
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
        return view('livewire.grades', ['grades' => $this->all_Grades()]);
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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'name' => 'required|unique:grades,name,' . $this->modalId,
        ]);
    }

    public function loadModalData($id)
    {
        $this->modalFormReset();
        $data = Grade::find($id);
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
        return ['name' => 'required|unique:grades,name,' . $this->modalId];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل اسم المرحلة مطلوب',
            'name.unique' => 'اسم المرحلة موجود مسبقا',
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

        Grade::create($this->modelData());

        $this->modalFormReset();
        $this->emit('gradeAdded');
        session()->flash('message', 'تم حفظ معلومات المرحلة بنجاح.');
    }

    public function update()
    {
        $this->validate();
        $Grade = Grade::where('id', $this->modalId)->first();
        $Grade->update($this->modelData());
        $this->modalFormReset();
        $this->emit('gradeEdited');
        session()->flash('message', 'تم تحديث معلومات المرحلة بنجاح.');
    }

    public function destroy($id)
    {
        Grade::where('id', $id)->delete();
        $this->emit('gradeDeleted');
        session()->flash('message', 'تم حذف المرحلة بنجاح.');
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'أمير المركز') {
            return Grade::query()
                ->search($this->search)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }
        return [];
    }
}
