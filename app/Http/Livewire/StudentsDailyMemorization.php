<?php

namespace App\Http\Livewire;

use App\Models\AyaDetails;
use App\Models\Grade;
use App\Models\Group;
use App\Models\QuranSuras;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentDailyMemorization;
use App\Models\Supervisor;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;

class StudentsDailyMemorization extends HomeComponent
{
    public $groups = [], $grades = [], $suras_from = [], $suras_to = [], $ayas_from = [], $ayas_to = [];

    public $type_name, $sura_from_name, $sura_to_name, $aya_from_name, $aya_to_name, $evaluation_name;

    public $selectedGradeId, $selectedTeacherId, $retStudent, $student_name, $dayOfWeek,
        $sura_from_id, $sura_to_id, $aya_from_id, $aya_to_id, $selectedType, $evaluation;
    public $ret_sura_from_id, $ret_aya_from_id, $ret_sura_to_id, $ret_aya_to_id, $ret_type, $isFoundModal = false;

    public function render()
    {
        return view('livewire.students-daily-memorization', ['students' => $this->all_Students(),]);
    }

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getLastDataModalByType' => 'getLastDataModalByType',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
    }

    public function rules()
    {
        return [
            'selectedType' => 'required|string',
            'sura_from_id' => 'required|numeric',
            'sura_to_id' => 'required|numeric',
            'aya_from_id' => 'required|numeric',
            'aya_to_id' => 'required|numeric',
            'evaluation' => 'required',
        ];
    }

    public function validateModal()
    {
        $this->validate();
        $dailyMemorization = StudentDailyMemorization::query()
            ->where('student_id', $this->retStudent->id)
            ->where('type', $this->selectedType)
            ->whereDate('datetime', date('Y-m-d'))->first();

        $messageBag = new MessageBag;
        if ($dailyMemorization != null) {
            if ($dailyMemorization->type == StudentDailyMemorization::MEMORIZE_TYPE) {
                $messageBag->add('selectedType', 'عذرا لقد تم إدخال متابعة حفظ الطالب مسبقا من خلال تاريخ اليوم');
            } else if ($dailyMemorization->type == StudentDailyMemorization::REVIEW_TYPE) {
                $messageBag->add('selectedType', 'عذرا لقد تم إدخال متابعة مراجعة الطالب مسبقا من خلال تاريخ اليوم');
            } else {
                $messageBag->add('selectedType', 'عذرا لقد تم إدخال متابعة مراجعة التجميعي الطالب مسبقا من خلال تاريخ اليوم');
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
        if ($this->sura_from_id == $this->sura_to_id) {
            $number_pages = round((AyaDetails::query()
                    ->where('sura_name', '=', QuranSuras::query()->select('name')->firstWhere('id', $this->sura_from_id)->name)
                    ->whereBetween('aya_number', [$this->aya_from_id, $this->aya_to_id])
                    ->sum('aya_percent')) / 15, 1);
        } else {
            // جلب أول سورة لحساب عدد الصفحات.
            $sura_start = (AyaDetails::query()
                    ->where('sura_name', '=', QuranSuras::query()->select('name')->firstWhere('id', $this->sura_from_id)->name)
                    ->whereBetween('aya_number', [$this->aya_from_id, QuranSuras::query()->select('total_number_aya')->firstWhere('id', $this->sura_from_id)->total_number_aya])
                    ->sum('aya_percent')) / 15;
            // جلب السور ما بين أول سور وأخر سورة لحساب عدد الصفحات.
            $suras_between = (AyaDetails::query()
                    ->whereIn('sura_name', QuranSuras::query()->select('name')->whereBetween('id', [$this->sura_to_id + 1, $this->sura_from_id - 1])->get()->toArray())
                    ->sum('aya_percent')) / 15;
            // جلب أخر سورة لحساب عدد الصفحات.
            $sura_end = (AyaDetails::query()
                    ->where('sura_name', '=', QuranSuras::query()->select('name')->firstWhere('id', $this->sura_to_id)->name)
                    ->whereBetween('aya_number', [1, $this->aya_to_id])
                    ->sum('aya_percent')) / 15;


            $number_pages = round($sura_start + $suras_between + $sura_end, 1);
        }

        if ($number_pages > 0.0) {
            StudentDailyMemorization::create([
                'student_id' => $this->retStudent->id,
                'teacher_id' => $this->retStudent->group->teacher_id,
                'type' => $this->selectedType,
                'sura_from_id' => $this->sura_from_id,
                'sura_to_id' => $this->sura_to_id,
                'aya_from' => $this->aya_from_id,
                'aya_to' => $this->aya_to_id,
                'evaluation' => $this->evaluation,
                'number_pages' => $number_pages,
                'datetime' => date('Y-m-d h:i:s'),
            ]);

            $this->emit('hideDialogAddDailyMemorization');
            if ($this->selectedType == StudentDailyMemorization::MEMORIZE_TYPE) {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة حفظ الطالب بنجاح.']);
            } else if ($this->selectedType == StudentDailyMemorization::REVIEW_TYPE) {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة مراجعة الطالب بنجاح.']);
            } else {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة مراجعة التجميعي للطالب بنجاح.']);
            }
            $this->modalFormReset();
        }
    }


    public function loadModalData($id, $selectedType)
    {
        $this->modalFormReset();
        $student = Student::with(['user'])->where('id', $id)->first();
        $this->retStudent = $student;
        $this->student_name = $student->user->name;

        // هنا يتم تنفيذ عملية إضافة تسميع الطلاب
        if ($selectedType == -1) {
            $this->dayOfWeek = Carbon::now()->translatedFormat('l');
            $dailyMemorization = StudentDailyMemorization::query()
                ->with(['quranSuraTo'])
                ->where('student_id', $student->id)
                ->orderByDesc('datetime')->first();

            if ($dailyMemorization != null) {
                $this->isFoundModal = true;
                $this->selectedType = $dailyMemorization->type;
                $this->ret_type = $dailyMemorization->type;
                $this->evaluation = StudentDailyMemorization::EXCELLENT_EVALUATION;
                $this->hhhhh($dailyMemorization);
            } else {
                $this->sura_from_id = 114;
                $this->sura_to_id = 114;
                $this->aya_from_id = 1;
                $this->aya_to_id = 1;
                $this->ret_sura_to_id = 114;
                $this->ret_aya_to_id = 1;
                $this->ret_sura_from_id = 114;
                $this->ret_aya_from_id = 1;
                $this->selectedType = StudentDailyMemorization::MEMORIZE_TYPE;
                $this->ret_type = StudentDailyMemorization::MEMORIZE_TYPE;
                $this->evaluation = StudentDailyMemorization::EXCELLENT_EVALUATION;
                $this->all_total_number_Aya_From();
                $this->all_total_number_Aya_To();
            }
            $this->all_QuranSuras();
            $this->emit('showDialogAddDailyMemorization');
        } else {
            // هنا يتم تنفيذ عملية عرض تسميع الطلاب
            $dailyMemorization = StudentDailyMemorization::query()
                ->with(['quranSuraFrom', 'quranSuraTo'])
                ->where('student_id', $student->id)
                ->where('type', $selectedType)
                ->orderByDesc('datetime')->first();
            if ($dailyMemorization != null) {
                $this->dayOfWeek = Carbon::parse($dailyMemorization->datetime)
                        ->translatedFormat('l') . '  ' . Carbon::parse($dailyMemorization->datetime)->format('Y-m-d');
                $this->type_name = $dailyMemorization->TypeName();
                $this->sura_from_name = $dailyMemorization->quranSuraFrom->name;
                $this->sura_to_name = $dailyMemorization->quranSuraTo->name;
                $this->aya_from_name = $dailyMemorization->AyaFrom();
                $this->aya_to_name = $dailyMemorization->AyaTo();
                $this->evaluation_name = $dailyMemorization->evaluation();
                $this->emit('showDialogShowDailyMemorization');
            }
        }
    }


    public function getLastDataModalByType()
    {
        if ($this->retStudent != null && $this->selectedType != $this->ret_type) {
            $dailyMemorization = StudentDailyMemorization::query()
                ->with(['quranSuraTo'])
                ->where('student_id', $this->retStudent->id)
                ->where('type', $this->selectedType)
                ->orderByDesc('datetime')->first();
            // حتى لا يتم تكرار إرسال البيانات إلى الواجهة
            $this->ret_type = $this->selectedType;
            if ($dailyMemorization != null) {
                $this->hhhhh($dailyMemorization);
            } else {
                $this->sura_from_id = 114;
                $this->sura_to_id = 114;
                $this->aya_from_id = 1;
                $this->aya_to_id = 1;
                $this->ret_sura_from_id = 114;
                $this->ret_aya_from_id = 1;
                $this->ret_sura_to_id = 114;
                $this->ret_aya_to_id = 1;
                $this->all_total_number_Aya_From();
                $this->all_total_number_Aya_To();
            }
            $this->all_QuranSuras();
        }
    }


    public function modalFormReset()
    {
        $this->resetValidation();
        $this->retStudent = null;
        $this->suras_from = null;
        $this->suras_to = null;
        $this->ayas_from = null;
        $this->ayas_to = null;
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
        $this->ret_type = null;
        $this->selectedType = null;
        $this->evaluation = null;
        $this->isFoundModal = false;
    }

    public function all_QuranSuras()
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

    public function updatedSuraToId()
    {
        $this->all_total_number_Aya_To();
    }

    public function updatedSuraFromId()
    {
        $this->all_total_number_Aya_From();
    }

    public function all_total_number_Aya_From()
    {
        if ($this->sura_to_id != null) {
            if ($this->sura_to_id == $this->ret_sura_to_id) {
                $total_number_aya = QuranSuras::find($this->sura_from_id)->total_number_aya;
                $this->ayas_from = [];
                for ($i = $this->ret_aya_to_id; $i <= $total_number_aya; $i++) {
                    $this->ayas_from[$i] = $i;
                }
            } else {
                $total_number_aya = QuranSuras::find($this->sura_from_id)->total_number_aya;
                $this->ayas_from = [];
                for ($i = 1; $i <= $total_number_aya; $i++) {
                    $this->ayas_from[$i] = $i;
                }
            }
        }
    }

    public function all_total_number_Aya_To()
    {
        if ($this->sura_to_id != null) {
            if ($this->sura_to_id == $this->ret_sura_to_id) {

                $total_number_aya = QuranSuras::find($this->sura_to_id)->total_number_aya;
                $this->ayas_to = [];
                for ($i = $this->ret_aya_to_id; $i <= $total_number_aya; $i++) {
                    $this->ayas_to[$i] = $i;
                }
            } else {
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
        if ($this->current_role == 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();;
        } else if ($this->current_role == 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role == 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->current_role == 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role == 'أمير المركز') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role == 'محفظ') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
            }
        }
    }


    public function all_Students()
    {
        return Student::query()
            ->with(['user', 'grade', 'group.teacher.user', 'student_is_block', 'student_is_warning', 'attendance_today'])
            ->search($this->search)
            ->when($this->current_role == 'أمير المركز', function ($q, $v) {
                $q->when($this->selectedGradeId != null, function ($q, $v) {
                    $q->where('grade_id', '=', $this->selectedGradeId);
                })->when($this->selectedTeacherId != null, function ($q, $v) {
                    $q->where('group_id', '=', $this->selectedTeacherId);
                });
            })->when($this->current_role == 'مشرف', function ($q, $v) {
                $q->where('grade_id', '=', Supervisor::find(auth()->id())->first()->grade_id);
            })->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Students();
    }


    public function hhhhh($dailyMemorization)
    {
        // هنا يتم فحص إذا الطالب أنجز حفظ السورة كاملة أو لا
        if ($dailyMemorization->quranSuraTo->total_number_aya == $dailyMemorization->aya_to) {
            $this->sura_from_id = $dailyMemorization->sura_to_id - 1;
            $this->sura_to_id = $dailyMemorization->sura_to_id - 1;
            $this->aya_from_id = 1;
            $this->aya_to_id = 1;
            $this->ret_sura_to_id = $dailyMemorization->sura_to_id - 1;
            $this->ret_aya_to_id = 1;
            $this->ret_sura_from_id = $dailyMemorization->sura_to_id - 1;
            $this->ret_aya_from_id = 1;
        } else {
            $this->sura_from_id = $dailyMemorization->sura_to_id;
            $this->sura_to_id = $dailyMemorization->sura_to_id;
            $this->aya_from_id = $dailyMemorization->aya_to + 1;
            $this->aya_to_id = $dailyMemorization->aya_to + 1;
            $this->ret_sura_to_id = $dailyMemorization->sura_to_id;
            $this->ret_aya_to_id = $dailyMemorization->aya_to + 1;
            $this->ret_sura_from_id = $dailyMemorization->sura_to_id;
            $this->ret_aya_from_id = $dailyMemorization->aya_to + 1;
        }
        $this->all_total_number_Aya_From();
        $this->all_total_number_Aya_To();
    }

    public function store_Attendance($id, $status)
    {
        $studentAttendance = StudentAttendance::where('student_id', $id)->whereDate('datetime', date('Y-m-d'))->first();
        if ($studentAttendance) {
            $this->dispatchBrowserEvent('alert',
                ['type' => 'error', 'message' => 'لقد تمت عملية اعتماد حضور وغياب الطالب مسبقا.']);
        } else {
            $student = Student::with(['group'])->find($id);
            if ($student) {
                StudentAttendance::updateOrCreate([
                    'student_id' => $id,
                    'teacher_id' => $student->group->teacher_id,
                    'datetime' => date('Y-m-d h:i:s'),
                    'status' => $status,
                ]);
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية اعتماد حضور وغياب الطالب بنجاح.']);
            }
        }
    }

    public function setMessage($message)
    {
        $this->catchError = $message;
    }

    public function messages()
    {
        return [
            'selectedType.required' => 'حقل النوع مطلوب',
            'selectedType.numeric' => 'يجب اختيار صالح لحقل النوع',
            'sura_from_id.required' => 'حقل سورة البداية مطلوب',
            'sura_from_id.numeric' => 'يجب اختيار صالح لحقل سورة البداية',
            'sura_to_id.required' => 'حقل سورة النهاية مطلوب',
            'sura_to_id.numeric' => 'يجب اختيار صالح لحقل سورة النهاية',
            'aya_from_id.required' => 'حقل رقم أية البداية مطلوب',
            'aya_from_id.numeric' => 'يجب اختيار صالح لحقل رقم أية البداية',
            'aya_to_id.required' => 'حقل رقم أية النهاية مطلوب',
            'aya_to_id.numeric' => 'يجب اختيار صالح لحقل رقم أية النهاية',
            'evaluation.required' => 'حقل التقييم مطلوب',
            'evaluation.numeric' => 'يجب اختيار صالح لحقل التقييم',
        ];
    }
}
