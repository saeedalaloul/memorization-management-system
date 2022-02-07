<?php

namespace App\Http\Livewire;

use App\Models\BoxComplaintSuggestion;
use Livewire\Component;
use Livewire\WithPagination;

class BoxComplaintSuggestions extends Component
{
    use WithPagination;

    public $isAddComplaint = false;
    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $catchError = '';
    public $search = '';
    public $subject;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        return view('livewire.box-complaint-suggestions', [
            'box_complaint_suggestions' => $this->all_Box_Complaint_Suggestions(),]);
    }

    public function mount()
    {
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

//    public function updated($propertyName)
//    {
//        $this->validateOnly($propertyName, [
//        ]);
//    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function rules()
    {
        return [
            'subject' => 'required'
        ];
    }

    public function messages()
    {
        return [
        ];
    }

    public function addComplaint($isShow)
    {
        $this->isAddComplaint = $isShow;
    }

    public function all_Box_Complaint_Suggestions()
    {
        return BoxComplaintSuggestion::query()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function store(){
        dd($this->subject);
    }
}
