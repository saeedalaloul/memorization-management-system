<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\StudentReportsStatus;
use App\Models\Supervisor;
use App\Traits\SendMessageWhatsappApiTrait;
use Illuminate\Support\MessageBag;

class StudentsReportsStatus extends HomeComponent
{
    use SendMessageWhatsappApiTrait;

    public $grades = [], $groups = [], $students = [];
    public $selectedGradeId, $selectedTeacherId, $selectedStatus;
    public $student_name, $ret_whatsapp_number, $whatsapp_number, $country_code;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
    ];

    public function render()
    {
        return view('livewire.students_reports_status', ['failure_reports' => $this->all_Failure_Reports()]);
    }

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function all_Failure_Reports()
    {
        return StudentReportsStatus::query()
            ->with(['student.user:id,name', 'student.group.teacher.user:id,name'])
            ->search($this->search)
            ->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('grade_id', '=', $this->selectedGradeId)
                        ->when($this->selectedTeacherId !== null, function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId)
                                ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                        });
                });
            })
            ->when($this->current_role === 'أمير المركز', function ($q, $v) {
                $q->when($this->selectedGradeId !== null, function ($q, $v) {
                    $q->whereHas('student', function ($q) {
                        $q->where('grade_id', '=', $this->selectedGradeId)
                            ->when($this->selectedTeacherId !== null, function ($q, $v) {
                                $q->where('group_id', '=', $this->selectedTeacherId)
                                    ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                            });
                    });
                });
            })->when($this->selectedStatus !== null && $this->selectedStatus !== "", function ($q, $v) {
                $q->where('status', '=', $this->selectedStatus);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Failure_Reports();
    }


    public function all_Grades()
    {
        if ($this->current_role === 'مشرف') {
            $this->selectedGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->selectedGradeId)->get();
        } else if ($this->current_role === 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId');

        if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if (($this->current_role === 'أمير المركز') && $this->selectedGradeId) {
            $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
        }
    }

    public function rules()
    {
        return [
            'whatsapp_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'country_code' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'country_code.required' => 'كود الدولة مطلوب',
            'country_code.string' => 'كود الدولة يجب أن يكون نص',
            'whatsapp_number.required' => 'حقل رقم الواتس اب مطلوب',
            'whatsapp_number.regex' => 'حقل رقم الواتس اب يجب أن يكون رقم',
            'whatsapp_number.min' => 'يجب أن لا يقل طول رقم الواتس اب عن 10 أرقام',
            'whatsapp_number.max' => 'يجب أن لا يزيد طول رقم الواتس اب عن 10 أرقام',
        ];
    }

    public function clearForm()
    {
        $this->modalId = '';
        $this->student_name = null;
        $this->ret_whatsapp_number = null;
        $this->whatsapp_number = null;
        $this->country_code = null;
        $this->resetValidation();
    }

    public function updateStudentWhatsapp()
    {
        $this->validate();
        $messageBag = new MessageBag();
        if ($this->ret_whatsapp_number === $this->country_code . (int)$this->whatsapp_number) {
            $messageBag->add('whatsapp_number', 'عذرا, يجب إدخال رقم واتساب جديد.');
            $this->setErrorBag($messageBag);
        } else {
            $studentReportsStatus = StudentReportsStatus::whereId($this->modalId)->first();
            $studentReportsStatus->update(['status' => StudentReportsStatus::READY_TO_SEND_STATUS]);
            $studentReportsStatus->student->update(['whatsapp_number' => $this->country_code . (int)$this->whatsapp_number]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تم تحديث واتساب الطالب بنجاح.']);
            $this->dispatchBrowserEvent('hideModal');
        }
    }

    /**
     * @throws \JsonException
     */
    public function sendReportStudentWhatsapp($id)
    {
        $failureReport = StudentReportsStatus::whereId($id)->first();
        if ($failureReport) {
            $status = $this->push_message($failureReport->student->whatsapp_number, json_decode($failureReport->details, true, 512, JSON_THROW_ON_ERROR));
            if ($status === 200) {
               $failureReport->delete();
               $this->dispatchBrowserEvent('alert',
                   ['type' => 'success', 'message' => 'تم إرسال التقرير بنجاح.']);
           }else{
               $failureReport->update(['status' => StudentReportsStatus::SEND_FAILURE_STATUS]);
               $this->dispatchBrowserEvent('alert',
                   ['type' => 'error', 'message' => 'عذرا لم يتم إرسال التقرير بنجاح, يرجى التحقق من رقم الواتساب.']);
           }
        }
    }

    public function lunchModalEdit($id)
    {
        $this->clearForm();
        $this->modalId = $id;
        $failureReport = StudentReportsStatus::with(['student.user'])->where('id', $id)->first();
        if ($failureReport) {
            $this->student_name = $failureReport->student->user->name;
            $this->ret_whatsapp_number = $failureReport->student->whatsapp_number;
            $this->country_code = substr($failureReport->student->whatsapp_number, 0, 4);
            $this->whatsapp_number = '0' . substr($failureReport->student->whatsapp_number, 4, 12);
        }
        $this->dispatchBrowserEvent('showModalEdit');
    }
}
