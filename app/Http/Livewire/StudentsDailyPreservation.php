<?php

namespace App\Http\Livewire;

use App\Models\DailyPreservationEvaluation;
use App\Models\DailyPreservationType;
use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\QuranSuras;
use App\Models\Student;
use App\Models\StudentDailyPreservation;
use App\Models\Supervisor;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;
use Livewire\Component;
use Livewire\WithPagination;

class StudentsDailyPreservation extends Component
{
    use WithPagination;

    public $successMessage = '';

    public $catchError, $groups, $grades, $suras_from, $suras_to, $evaluations, $types, $ayas_from, $ayas_to;
    public $sortBy = 'id', $sortDirection = 'desc', $perPage = 10, $search = '';

    public $type_name, $sura_from_name, $sura_to_name, $aya_from_name, $aya_to_name, $evaluation_name;

    public $searchGradeId, $searchGroupId, $retStudent, $student_name, $dayOfWeek,
        $sura_from_id, $sura_to_id, $aya_from_id, $aya_to_id, $type_id, $evaluation_id;
    public $ret_sura_from_id, $ret_aya_from_id, $ret_sura_to_id, $ret_aya_to_id, $ret_type_id, $isFoundModal = false;
    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $this->all_Groups();
        $this->getLastDataModalByType();
        $this->all_total_number_aya();
        return view('livewire.students-daily-preservation', ['students' => $this->all_Students(),]);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'type_id' => 'required|numeric',
            'sura_from_id' => 'required|numeric',
            'sura_to_id' => 'required|numeric',
            'aya_from_id' => 'required|numeric',
            'aya_to_id' => 'required|numeric',
            'evaluation_id' => 'required',
        ]);
    }

    public function rules()
    {
        return [
            'type_id' => 'required|numeric',
            'sura_from_id' => 'required|numeric',
            'sura_to_id' => 'required|numeric',
            'aya_from_id' => 'required|numeric',
            'aya_to_id' => 'required|numeric',
            'evaluation_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'type_id.required' => 'حقل النوع مطلوب',
            'type_id.numeric' => 'يجب اختيار صالح لحقل النوع',
            'sura_from_id.required' => 'حقل سورة البداية مطلوب',
            'sura_from_id.numeric' => 'يجب اختيار صالح لحقل سورة البداية',
            'sura_to_id.required' => 'حقل سورة النهاية مطلوب',
            'sura_to_id.numeric' => 'يجب اختيار صالح لحقل سورة النهاية',
            'aya_from_id.required' => 'حقل رقم أية البداية مطلوب',
            'aya_from_id.numeric' => 'يجب اختيار صالح لحقل رقم أية البداية',
            'aya_to_id.required' => 'حقل رقم أية النهاية مطلوب',
            'aya_to_id.numeric' => 'يجب اختيار صالح لحقل رقم أية النهاية',
            'evaluation_id.required' => 'حقل التقييم مطلوب',
            'evaluation_id.numeric' => 'يجب اختيار صالح لحقل التقييم',
        ];
    }

    public function validateModal()
    {
        $this->validate();
        $dailyPreservation = StudentDailyPreservation::query()
            ->where('student_id', $this->retStudent->id)
            ->where('type', $this->type_id)
            ->where('daily_preservation_date', date('Y-m-d'))->first();

        $messageBag = new MessageBag;
        if ($dailyPreservation != null) {
            if ($dailyPreservation->type == 1) {
                $messageBag->add('type_id', 'عذرا لقد تم إدخال متابعة حفظ الطالب مسبقا من خلال تاريخ اليوم');
            } else {
                $messageBag->add('type_id', 'عذرا لقد تم إدخال متابعة مراجعة الطالب مسبقا من خلال تاريخ اليوم');
            }
            $this->setErrorBag($messageBag);
        } else {
            if (!$this->isFoundModal) {
                // عملية تحقق المدخلات لتسميع الطالب الجديد
                if ($this->sura_to_id <= $this->sura_from_id) {
                    if ($this->aya_to_id >= $this->aya_from_id) {
                        $this->store();
                    } else {
                        $messageBag->add('aya_to_id', 'عذرا يجب أن يكون هنا اختيار صحيح');
                        $this->setErrorBag($messageBag);
                    }
                } else {
                    $messageBag->add('sura_to_id', 'عذرا يجب أن يكون هنا اختيار صحيح');
                    $this->setErrorBag($messageBag);
                }
            } else {
                // عملية تحقق المدخلات لتسميع الطالب القديم
                if ($this->sura_from_id == $this->ret_sura_from_id) {
                    if ($this->aya_from_id == $this->ret_aya_from_id) {
                        if ($this->sura_to_id <= $this->ret_sura_to_id) {
                            // إذا سورة البداية نفس سورة النهاية
                            if ($this->sura_from_id == $this->sura_to_id) {
                                if ($this->aya_to_id >= $this->ret_aya_from_id) {
                                    $this->store();
                                } else {
                                    $messageBag->add('aya_to_id', 'عذرا يجب أن يكون هنا اختيار صحيح');
                                    $this->setErrorBag($messageBag);
                                }
                            } else {
                                $this->store();
                            }
                        } else {
                            $messageBag->add('sura_to_id', 'عذرا يجب أن يكون هنا اختيار صحيح');
                            $this->setErrorBag($messageBag);
                        }
                    } else {
                        $messageBag->add('aya_from_id', 'عذرا يجب أن يتم اختيار نفس أية البداية');
                        $this->setErrorBag($messageBag);
                    }
                } else {
                    $messageBag->add('sura_from_id', 'عذرا يجب أن يتم اختيار نفس سورة البداية');
                    $this->setErrorBag($messageBag);
                }
            }
        }
    }

    public function store()
    {
        StudentDailyPreservation::create([
            'student_id' => $this->retStudent->id,
            'teacher_id' => $this->retStudent->group->teacher_id,
            'type' => $this->type_id,
            'from_sura' => $this->sura_from_id,
            'to_sura' => $this->sura_to_id,
            'from_aya' => $this->aya_from_id,
            'to_aya' => $this->aya_to_id,
            'evaluation' => $this->evaluation_id,
            'daily_preservation_date' => date('Y-m-d'),
        ]);
        $this->emit('hideDialogAddDailyPreservation');
        if ($this->type_id == 1) {
            session()->flash('success_message', 'تمت عملية إضافة متابعة حفظ الطالب بنجاح.');
        } else {
            session()->flash('success_message', 'تمت عملية إضافة متابعة مراجعة الطالب بنجاح.');
        }
        $this->modalFormReset();
    }


    public function loadModalData($id, $type_id)
    {
        $this->modalFormReset();
        $student = Student::where('id', $id)->first();
        $this->retStudent = $student;
        $this->student_name = $student->user->name;

        // هنا يتم تنفيذ عملية إضافة تسميع الطلاب
        if ($type_id == -1) {
            $this->dayOfWeek = Carbon::now()->translatedFormat('l');
            $dailyPreservation = StudentDailyPreservation::query()
                ->where('student_id', $student->id)
                ->orderByDesc('daily_preservation_date')->first();

            if ($dailyPreservation != null) {
                $this->isFoundModal = true;
                $this->type_id = $dailyPreservation->type;
                $this->ret_type_id = $dailyPreservation->type;
                $this->evaluation_id = 1;
                $this->hhhhh($dailyPreservation);
            } else {
                $this->sura_from_id = 114;
                $this->sura_to_id = 114;
                $this->aya_from_id = 1;
                $this->aya_to_id = 1;
                $this->ret_sura_to_id = 114;
                $this->ret_aya_to_id = 1;
                $this->ret_sura_from_id = 114;
                $this->ret_aya_from_id = 1;
                $this->type_id = 1;
                $this->ret_type_id = 1;
                $this->evaluation_id = 1;
            }
            $this->all_QuranSuras();
            $this->emit('showDialogAddDailyPreservation');
        } else {
            // هنا يتم تنفيذ عملية عرض تسميع الطلاب
            $dailyPreservation = StudentDailyPreservation::query()
                ->where('student_id', $student->id)
                ->where('type', $type_id)
                ->orderByDesc('daily_preservation_date')->first();
            if ($dailyPreservation != null) {
                $this->dayOfWeek = Carbon::parse($dailyPreservation->daily_preservation_date)
                        ->translatedFormat('l') . '  ' . $dailyPreservation->daily_preservation_date;
                $this->type_name = $dailyPreservation->dailyPreservationType->name;
                $this->sura_from_name = $dailyPreservation->quranSuraFrom->name;
                $this->sura_to_name = $dailyPreservation->quranSuraTo->name;
                $this->aya_from_name = $dailyPreservation->fromaya();
                $this->aya_to_name = $dailyPreservation->toaya();
                $this->evaluation_name = $dailyPreservation->dailyPreservationEvaluation->name;
                $this->emit('showDialogShowDailyPreservation');
            }
        }
    }

    public function getLastDataModalByType()
    {
        if ($this->retStudent != null && $this->type_id != $this->ret_type_id) {
            $dailyPreservation = StudentDailyPreservation::query()
                ->where('student_id', $this->retStudent->id)
                ->where('type', $this->type_id)
                ->orderByDesc('daily_preservation_date')->first();
            // حتى لا يتم تكرار إرسال البيانات إلى الواجهة
            $this->ret_type_id = $this->type_id;
            if ($dailyPreservation != null) {
                $this->hhhhh($dailyPreservation);
            } else {
                $this->sura_from_id = 114;
                $this->sura_to_id = 114;
                $this->aya_from_id = 1;
                $this->aya_to_id = 1;
                $this->ret_sura_from_id = 114;
                $this->ret_aya_from_id = 1;
                $this->ret_sura_to_id = 114;
                $this->ret_aya_to_id = 1;
            }
            $this->all_QuranSuras();
        }
    }

    public function mount()
    {
        $this->all_Grades();
        $this->all_DailyPreservationType();
        $this->all_DailyPreservationEvaluation();
    }


    public
    function modalFormReset()
    {
        $this->resetValidation();
        $this->retStudent = null;
        $this->suras_from = null;
        $this->suras_to = null;
        $this->student_name = null;
        $this->dayOfWeek = null;
        $this->sura_from_id = null;
        $this->sura_to_id = null;
        $this->aya_from_id = null;
        $this->aya_to_id = null;
        $this->ret_sura_from_id = null;
        $this->ret_aya_from_id = null;
        $this->ret_sura_to_id = null;
        $this->ret_aya_to_id = null;
        $this->ret_type_id = null;
        $this->type_id = null;
        $this->evaluation_id = null;
        $this->isFoundModal = false;
    }

    public
    function all_QuranSuras()
    {
        if ($this->sura_to_id == 114) {
            $this->suras_from = QuranSuras::query()
                ->whereBetween('id', [1, $this->sura_to_id])
                ->orderByDesc('id')->get();
        } else {
            $this->suras_from = QuranSuras::query()
                ->whereBetween('id', [$this->sura_to_id, $this->sura_to_id])
                ->orderByDesc('id')->get();
        }

        $this->suras_to = QuranSuras::query()
            ->whereBetween('id', [1, $this->sura_to_id])
            ->orderByDesc('id')->get();
    }

    public
    function all_DailyPreservationEvaluation()
    {
        $this->evaluations = DailyPreservationEvaluation::all();
    }

    public
    function all_DailyPreservationType()
    {
        $this->types = DailyPreservationType::all();
    }

    public
    function all_total_number_aya()
    {
        if ($this->sura_to_id != null) {
            if ($this->sura_to_id == $this->ret_sura_to_id) {

                $total_number_aya = QuranSuras::find($this->sura_from_id)->total_number_aya;
                $this->ayas_from = [];
                for ($i = $this->ret_aya_to_id; $i <= $total_number_aya; $i++) {
                    $this->ayas_from[$i] = $i;
                }

                $total_number_aya = QuranSuras::find($this->sura_to_id)->total_number_aya;
                $this->ayas_to = [];
                for ($i = $this->ret_aya_to_id; $i <= $total_number_aya; $i++) {
                    $this->ayas_to[$i] = $i;
                }
            } else {
                if (!$this->isFoundModal) {
                    $total_number_aya = QuranSuras::find($this->sura_from_id)->total_number_aya;
                    $this->ayas_from = [];
                    for ($i = 1; $i <= $total_number_aya; $i++) {
                        $this->ayas_from[$i] = $i;
                    }
                }

                $total_number_aya = QuranSuras::find($this->sura_to_id)->total_number_aya;
                $this->ayas_to = [];
                for ($i = 1; $i <= $total_number_aya; $i++) {
                    $this->ayas_to[$i] = $i;
                }
            }
        }
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'مشرف') {
            $this->searchGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
        } else if (auth()->user()->current_role == 'اداري') {
            $this->searchGradeId = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
        } else {
            $this->grades = Grade::all();
        }
    }

    public
    function all_Groups()
    {
        if (auth()->user()->current_role == 'مشرف' || auth()->user()->current_role == 'اداري') {
            if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        } else if (auth()->user()->current_role == 'أمير المركز') {
            if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        }
    }

    public
    function all_Students()
    {
        if (auth()->user()->current_role == 'مشرف') {
            if (!empty($this->searchGroupId)) {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->where('group_id', '=', $this->searchGroupId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else if (auth()->user()->current_role == 'اداري') {
            if (!empty($this->searchGroupId)) {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->where('group_id', '=', $this->searchGroupId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Student::query()
                    ->search($this->search)
                    ->where('grade_id', '=', $this->searchGradeId)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } else if (auth()->user()->current_role == 'محفظ') {
            return Student::query()
                ->search($this->search)
                ->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id)
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            if (empty($this->searchGradeId)) {
                return Student::query()
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                if (empty($this->searchGroupId)) {
                    return Student::query()
                        ->search($this->search)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    return Student::query()
                        ->search($this->search)
                        ->where('grade_id', '=', $this->searchGradeId)
                        ->where('group_id', '=', $this->searchGroupId)
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                }
            }
        }
    }

    public
    function sortBy($field)
    {
        if ($this->sortDirection == 'asc') {
            $this->sortDirection = 'desc';
        } else {
            $this->sortDirection = 'asc';
        }

        return $this->sortBy = $field;
    }


    public function hhhhh($dailyPreservation)
    {
        // هنا يتم فحص إذا الطالب أنجز حفظ السورة كاملة أو لا
        if ($dailyPreservation->quranSuraTo->total_number_aya == $dailyPreservation->to_aya) {
            $this->sura_from_id = $dailyPreservation->to_sura - 1;
            $this->sura_to_id = $dailyPreservation->to_sura - 1;
            $this->aya_from_id = 1;
            $this->aya_to_id = 1;
            $this->ret_sura_to_id = $dailyPreservation->to_sura - 1;
            $this->ret_aya_to_id = 1;
            $this->ret_sura_from_id = $dailyPreservation->to_sura - 1;
            $this->ret_aya_from_id = 1;
        } else {
            $this->sura_from_id = $dailyPreservation->to_sura;
            $this->sura_to_id = $dailyPreservation->to_sura;
            $this->aya_from_id = $dailyPreservation->to_aya + 1;
            $this->aya_to_id = $dailyPreservation->to_aya + 1;
            $this->ret_sura_to_id = $dailyPreservation->to_sura;
            $this->ret_aya_to_id = $dailyPreservation->to_aya + 1;
            $this->ret_sura_from_id = $dailyPreservation->to_sura;
            $this->ret_aya_from_id = $dailyPreservation->to_aya + 1;
        }
    }

}
