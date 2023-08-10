<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\PunitiveMeasure;
use Illuminate\Support\MessageBag;

class PunitiveMeasures extends HomeComponent
{
    public $grades = [], $groups = [];
    public $selectedGradeId, $groups_ids;
    public $selectedPunitiveMeasure, $selectedReason, $number_times, $quantity;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function render()
    {
        return view('livewire.punitive-measures', ['punitive_measures' => $this->all_Punitive_Measures(),]);
    }

    public function messages()
    {
        return [
            'selectedPunitiveMeasure.required' => 'حقل نوع الإجراء العقابي مطلوب',
            'selectedPunitiveMeasure.string' => 'حقل نوع الإجراء العقابي يجب أن يحتوي على نص',
            'selectedReason.required' => 'حقل سبب الإجراء العقابي مطلوب',
            'selectedReason.string' => 'حقل سبب الإجراء العقابي يجب أن يحتوي على نص',
            'number_times.required' => 'حقل عدد الأيام مطلوب',
            'number_times.numeric' => 'حقل عدد الأيام يجب أن يحتوي على رقم',
            'number_times.between' => 'حقل عدد الأيام يجب أن يكون بين 3 و 7 أيام',
            'quantity.required' => 'حقل عدد الأيام مطلوب',
            'quantity.numeric' => 'حقل كمية الحفظ يجب أن يحتوي على رقم',
            'quantity.between' => 'حقل كمية الحفظ يجب أن يكون بين 0.5 و 5 أيام',
            'selectedGradeId.required' => 'حقل المرحلة مطلوب',
            'selectedGradeId.string' => 'حقل المرحلة يجب أن يحتوي على نص',
            'groups_ids.required' => 'حقل اختيار الحلقات مطلوب',
            'groups_ids.array' => 'حقل اختيار الحلقات يجب أن يكون قائمة',
            'groups_ids.min' => 'يجب أن لا يقل عدد الحلقات عن 1 طلاب',
        ];
    }

    public function rules()
    {
        return [
            'selectedPunitiveMeasure' => 'required|string',
            'selectedReason' => 'required|string',
            'number_times' => 'required|numeric|between:3,7',
        ];
    }

    public function edit($id)
    {
        $punitiveMeasure = PunitiveMeasure::where('id', $id)->first();
        if ($punitiveMeasure) {
            $this->modalFormReset();
            $this->modalId = $id;
            $this->selectedPunitiveMeasure = $punitiveMeasure->type;
            $this->selectedReason = $punitiveMeasure->reason;
            $this->number_times = $punitiveMeasure->number_times;
            $this->quantity = $punitiveMeasure->quantity;
        }
    }

    public function showDialogGroupsCustom($id, $process_type)
    {
        $punitiveMeasure = PunitiveMeasure::where('id', $id)->first();
        if ($punitiveMeasure) {
            $this->modalFormReset();
            $this->process_type = $process_type;
            $this->modalId = $id;
            $this->selectedPunitiveMeasure = $punitiveMeasure->type;
            $this->selectedReason = $punitiveMeasure->reason;
            $this->number_times = $punitiveMeasure->number_times;
            if ($process_type === 'add') {
                $this->dispatchBrowserEvent('showModalSelect');
            } else {
                $this->dispatchBrowserEvent('showModalRemove');
            }
            $this->getTeachersByGradeId();
        }
    }

    public function showDialogDelete($id)
    {
        $this->modalId = $id;
        $this->dispatchBrowserEvent('showModalDelete');
    }

    public function store()
    {
        $this->validate();
        if ($this->selectedReason === PunitiveMeasure::MEMORIZE_REASON) {
            $this->validate(['quantity' => 'required|numeric|between:0.5,5',]);
        }
        $punitiveMeasure = PunitiveMeasure::where('type', $this->selectedPunitiveMeasure)
            ->where('reason', $this->selectedReason)
            ->where('number_times', $this->number_times)
            ->where('quantity', $this->quantity)->first();
        if (!$punitiveMeasure) {
            PunitiveMeasure::create([
                'type' => $this->selectedPunitiveMeasure,
                'reason' => $this->selectedReason,
                'number_times' => $this->number_times,
                'quantity' => $this->quantity ?? null,
            ]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد الإجراء العقابي بنجاح.']);
            $this->modalFormReset();
        } else {
            $messageBag = new MessageBag();
            $messageBag->add('selectedPunitiveMeasure', 'عذرا يوجد إجراء عقابي معتمد بنفس المعلومات');
            $this->setErrorBag($messageBag);
        }
    }

    public function update()
    {
        $this->validate();
        if ($this->selectedReason === PunitiveMeasure::MEMORIZE_REASON) {
            $this->validate(['quantity' => 'required|numeric|between:0.5,5',]);
        }
        $punitiveMeasure = PunitiveMeasure::where('id', '!=', $this->modalId)
            ->where('type', $this->selectedPunitiveMeasure)
            ->where('reason', $this->selectedReason)
            ->where('number_times', $this->number_times)
            ->where('quantity', $this->quantity)->first();
        if (!$punitiveMeasure) {
            $punitiveMeasure = PunitiveMeasure::where('id', $this->modalId)->first();
            $punitiveMeasure->update([
                'number_times' => $this->number_times,
                'quantity' => $this->quantity ?? null,
            ]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية تحديث الإجراء العقابي بنجاح.']);
            $this->modalFormReset();
        } else {
            $messageBag = new MessageBag();
            $messageBag->add('selectedPunitiveMeasure', 'عذرا يوجد إجراء عقابي معتمد بنفس المعلومات');
            $this->setErrorBag($messageBag);
        }
    }


    public function approval_on_group()
    {
        if ($this->process_type === 'remove') {
            $this->validate([
                'groups_ids' => 'array',
            ]);
        } else {
            $this->validate([
                'groups_ids' => 'required|array|min:1',
            ]);
        }

        $punitiveMeasure = PunitiveMeasure::where('id', $this->modalId)->first();
        if ($this->process_type === 'add') {
            $punitiveMeasure?->groups()->attach($this->groups_ids);
        } else {
            $punitiveMeasure?->groups()->sync($this->groups_ids);
        }
        $this->dispatchBrowserEvent('alert',
            ['type' => 'success', 'message' => 'تمت عملية اعتماد الإجراء العقابي على الحلقات بنجاح.']);
        $this->dispatchBrowserEvent('hideModal');
        $this->modalFormReset();
    }

    public function all_Punitive_Measures()
    {
        return PunitiveMeasure::query()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->modalId = '';
        $this->process_type = '';
        $this->selectedPunitiveMeasure = null;
        $this->selectedReason = null;
        $this->number_times = null;
        $this->quantity = null;
        $this->groups = [];
        $this->groups_ids = null;
        $this->selectedGradeId = null;
    }

    public function all_Grades()
    {
        if ($this->current_role === 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups_ids', 'groups');

        if ($this->current_role === 'أمير المركز') {
            $this->groups = Group::query()
                ->where('type','=',Group::QURAN_TYPE)
                ->with(['teacher.user', 'punitive_measures'])
                ->when($this->process_type === 'remove', function ($q, $v) {
                    $q->whereRelation('punitive_measures', function ($q) {
                        $q->where('type', $this->selectedPunitiveMeasure)
                            ->where('reason', $this->selectedReason)
                            ->where('number_times', $this->number_times);
                    });
                })->when($this->process_type === 'add', function ($q, $v) {
                    $q->whereDoesntHave('punitive_measures', function ($q) {
                        $q->where('type', $this->selectedPunitiveMeasure)
                            ->where('reason', $this->selectedReason);
                    });
                })
                ->when(!empty($this->selectedGradeId), function ($q, $v) {
                    $q->where('grade_id', $this->selectedGradeId);
                })->get();
        }
        return [];
    }

    public function destroy()
    {
        $punitiveMeasure = PunitiveMeasure::where('id', $this->modalId)->first();
        if ($punitiveMeasure) {
            $punitiveMeasure->delete();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية حذف الإجراء العقابي بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
            $this->modalFormReset();
        }
    }
}
