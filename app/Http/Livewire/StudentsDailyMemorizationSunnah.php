<?php

namespace App\Http\Livewire;

use App\Models\Grade;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentSunnahAttendance;
use App\Models\StudentSunnahDailyMemorization;
use App\Models\SunnahBooks;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\MessageBag;

class StudentsDailyMemorizationSunnah extends HomeComponent
{
    public $groups = [], $grades = [], $books = [], $hadiths_from = [], $hadiths_to = [];

    public $type_name, $book_name, $hadith_from_name, $hadith_to_name, $evaluation_name;

    public $selectedGradeId, $selectedTeacherId, $retStudent, $student_name, $dayOfWeek,
        $book_id, $hadith_from_id, $hadith_to_id, $selectedType, $evaluation;
    public $ret_book_id, $ret_hadith_from_id, $ret_hadith_to_id, $ret_type, $isFoundModal = false;

    public function render()
    {
        return view('livewire.students-daily-memorization-sunnah', ['students' => $this->all_Students(),]);
    }

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getLastDataModalByType' => 'getLastDataModalByType',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
        if ($this->current_role === User::TEACHER_ROLE){
            $this->perPage = 25;
        }
    }

    public function rules()
    {
        return [
            'selectedType' => 'required|string',
            'book_id' => 'required|numeric',
            'hadith_from_id' => 'required|numeric',
            'hadith_to_id' => 'required|numeric',
            'evaluation' => 'required',
        ];
    }

    public function updateDailyMemorization(): void
    {
        $this->validate();
        $messageBag = new MessageBag;
        $this->continueValidateModal($messageBag);
    }

    public function validateModal()
    {
        $this->validate();
        $dailyMemorization = StudentSunnahDailyMemorization::query()
            ->where('student_id', $this->retStudent->id)
            ->where('type', $this->selectedType)
            ->whereDate('datetime', date('Y-m-d'))->first();

        $messageBag = new MessageBag;
        if ($dailyMemorization !== null) {
            if ($dailyMemorization->type === StudentSunnahDailyMemorization::MEMORIZE_TYPE) {
                $messageBag->add('selectedType', 'عذرا لقد تم إدخال متابعة حفظ الطالب مسبقا من خلال تاريخ اليوم');
            } else {
                $messageBag->add('selectedType', 'عذرا لقد تم إدخال متابعة مراجعة الطالب مسبقا من خلال تاريخ اليوم');
            }
            $this->setErrorBag($messageBag);
        } else if (!$this->isFoundModal) {
            // عملية تحقق المدخلات لتسميع الطالب الجديد
            if ($this->hadith_to_id >= $this->hadith_from_id) {
                $this->storeOrUpdate();
            } else {
                $messageBag->add('hadith_to_id', 'عذرا يجب أن يكون هنا اختيار صحيح');
                $this->setErrorBag($messageBag);
            }
        } else {
            $this->continueValidateModal($messageBag);
        }
    }

    public function storeOrUpdate(): void
    {
        if ($this->modalId !== '') {
            StudentSunnahDailyMemorization::whereId($this->modalId)->update([
                'hadith_from' => $this->hadith_from_id,
                'hadith_to' => $this->hadith_to_id,
                'evaluation' => $this->evaluation,
            ]);

            if ($this->selectedType === StudentSunnahDailyMemorization::MEMORIZE_TYPE) {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية تحديث متابعة حفظ الطالب بنجاح.']);
            } else {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية تحديث متابعة مراجعة الطالب بنجاح.']);
            }
        } else {
            StudentSunnahDailyMemorization::create([
                'student_id' => $this->retStudent->id,
                'teacher_id' => $this->retStudent->group->teacher_id,
                'type' => $this->selectedType,
                'book_id' => $this->book_id,
                'hadith_from' => $this->hadith_from_id,
                'hadith_to' => $this->hadith_to_id,
                'evaluation' => $this->evaluation,
                'datetime' => date('Y-m-d h:i:s'),
            ]);

            if ($this->selectedType === StudentSunnahDailyMemorization::MEMORIZE_TYPE) {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة حفظ الطالب بنجاح.']);
            } else {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة المراجعة للطالب بنجاح.']);
            }
        }

        $this->emit('hideDialogAddDailyMemorization');
        $this->modalFormReset();
    }


    public function loadModalData($id, $selectedType)
    {
        $this->modalFormReset();
        $student = Student::with(['user'])->where('id', $id)->first();
        $this->retStudent = $student;
        $this->student_name = $student->user->name;

        // هنا يتم تنفيذ عملية إضافة تسميع الطلاب
        if ($selectedType === -1) {
            $this->dayOfWeek = Carbon::now()->translatedFormat('l');
            $dailyMemorization = StudentSunnahDailyMemorization::query()
                ->with(['book'])
                ->where('student_id', $student->id)
                ->orderByDesc('datetime')->first();

            if ($dailyMemorization !== null) {
                $this->isFoundModal = true;
                $this->selectedType = $dailyMemorization->type;
                $this->ret_type = $dailyMemorization->type;
                $this->evaluation = StudentSunnahDailyMemorization::EXCELLENT_EVALUATION;
                if (Carbon::parse($dailyMemorization->datetime)->format('Y-m-d') === date('Y-m-d')) {
                    $this->modalId = $dailyMemorization->id;
                    $this->evaluation = $dailyMemorization->evaluation;
                }
                $this->hhhhh($dailyMemorization);
            } else {
                $this->isFoundModal = false;
                $this->modalId = '';
                $this->book_id = 1;
                $this->hadith_from_id = 1;
                $this->hadith_to_id = 1;
                $this->ret_hadith_to_id = 1;
                $this->ret_hadith_from_id = 1;
                $this->selectedType = StudentSunnahDailyMemorization::MEMORIZE_TYPE;
                $this->ret_type = StudentSunnahDailyMemorization::MEMORIZE_TYPE;
                $this->evaluation = StudentSunnahDailyMemorization::EXCELLENT_EVALUATION;
                $this->all_total_number_Hadith_From();
                $this->all_total_number_Hadith_To();
            }
            $this->all_SunnahBooks();
            $this->emit('showDialogAddDailyMemorization');
        } else {
            // هنا يتم تنفيذ عملية عرض تسميع الطلاب
            $dailyMemorization = StudentSunnahDailyMemorization::query()
                ->with(['book'])
                ->where('student_id', $student->id)
                ->where('type', $selectedType)
                ->orderByDesc('datetime')->first();
            if ($dailyMemorization !== null) {
                $this->dayOfWeek = Carbon::parse($dailyMemorization->datetime)
                        ->translatedFormat('l') . '  ' . Carbon::parse($dailyMemorization->datetime)->format('Y-m-d');
                $this->type_name = $dailyMemorization->TypeName();
                $this->book_name = $dailyMemorization->book->name;
                $this->hadith_from_name = $dailyMemorization->hadith_from;
                $this->hadith_to_name = $dailyMemorization->hadith_to;
                $this->evaluation_name = $dailyMemorization->evaluation();
                $this->emit('showDialogShowDailyMemorization');
            }
        }
    }


    public function getLastDataModalByType()
    {
        if ($this->retStudent !== null && $this->selectedType !== $this->ret_type) {
            $dailyMemorization = StudentSunnahDailyMemorization::query()
                ->with(['book'])
                ->where('student_id', $this->retStudent->id)
                ->where('type', $this->selectedType)
                ->orderByDesc('datetime')->first();
            // حتى لا يتم تكرار إرسال البيانات إلى الواجهة
            $this->ret_type = $this->selectedType;
            if ($dailyMemorization !== null) {
                $this->isFoundModal = true;
                if (Carbon::parse($dailyMemorization->datetime)->format('Y-m-d') === date('Y-m-d')) {
                    $this->modalId = $dailyMemorization->id;
                }
                $this->hhhhh($dailyMemorization);
            } else {
                $this->isFoundModal = false;
                $this->modalId = '';
                $this->evaluation = StudentSunnahDailyMemorization::EXCELLENT_EVALUATION;
                $this->book_id = 1;
                $this->hadith_from_id = 1;
                $this->hadith_to_id = 1;
                $this->ret_book_id = 1;
                $this->ret_hadith_from_id = 1;
                $this->ret_hadith_to_id = 1;
                $this->all_total_number_Hadith_From();
                $this->all_total_number_Hadith_To();
            }
            $this->all_SunnahBooks();
        }
    }


    public function modalFormReset(): void
    {
        $this->resetValidation();
        $this->retStudent = null;
        $this->books = null;
        $this->hadiths_from = null;
        $this->hadiths_to = null;
        $this->student_name = null;
        $this->dayOfWeek = null;
        $this->book_id = null;
        $this->hadith_from_id = null;
        $this->hadith_to_id = null;
        $this->ret_book_id = null;
        $this->ret_hadith_from_id = null;
        $this->ret_hadith_to_id = null;
        $this->ret_type = null;
        $this->selectedType = null;
        $this->evaluation = null;
        $this->isFoundModal = false;
        $this->modalId = '';
    }

    public function all_SunnahBooks(): void
    {
        if ($this->book_id === 1 && $this->hadith_from_id === 1) {
            $this->books = SunnahBooks::query()
                ->orderBy('id')->get();
        } else {
            $this->books = SunnahBooks::query()
                ->where('id', $this->book_id)->get();
        }
    }

    public function updatedBookId()
    {
        $this->all_total_number_Hadith_From();
    }

    public function all_total_number_Hadith_From(): void
    {
        if ($this->book_id !== null) {
            if ($this->book_id === $this->ret_book_id) {
                $total_number_hadith = SunnahBooks::find($this->book_id)->total_number_hadith;
                $this->hadiths_from = [];
                for ($i = $this->ret_hadith_to_id; $i <= $total_number_hadith; $i++) {
                    $this->hadiths_from[$i] = $i;
                }
            } else {
                $total_number_hadith = SunnahBooks::find($this->book_id)->total_number_hadith;
                $this->hadiths_from = [];
                for ($i = 1; $i <= $total_number_hadith; $i++) {
                    $this->hadiths_from[$i] = $i;
                }
            }
        }
    }

    public function all_total_number_Hadith_To(): void
    {
        if ($this->book_id !== null) {
            if ($this->modalId !== '') {
                $total_number_hadith = SunnahBooks::find($this->book_id)->total_number_hadith;
                $this->hadiths_to = [];
                for ($i = $this->ret_hadith_from_id; $i <= $total_number_hadith; $i++) {
                    $this->hadiths_to[$i] = $i;
                }
            }else if ($this->book_id === $this->ret_book_id) {
                $total_number_hadith = SunnahBooks::find($this->book_id)->total_number_hadith;
                $this->hadiths_to = [];
                for ($i = $this->ret_hadith_to_id; $i <= $total_number_hadith; $i++) {
                    $this->hadiths_to[$i] = $i;
                }
            } else {
                $total_number_hadith = SunnahBooks::find($this->book_id)->total_number_hadith;
                $this->hadiths_to = [];
                for ($i = 1; $i <= $total_number_hadith; $i++) {
                    $this->hadiths_to[$i] = $i;
                }
            }
        }
    }

    public function all_Grades(): void
    {
        if ($this->current_role === 'مشرف') {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();;
        } else if ($this->current_role === 'محفظ') {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'أمير المركز') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId(): void
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::SUNNAH_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'أمير المركز') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::SUNNAH_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role === 'محفظ') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()
                    ->where('type', Group::SUNNAH_TYPE)
                    ->where('teacher_id', auth()->id())->get();
            }
        }
    }


    public function all_Students()
    {
        return Student::query()
            ->with(['user', 'grade', 'group_sunnah.teacher.user', 'attendance_sunnah_today'])
            ->search($this->search)
            ->whereNotNull('group_sunnah_id')
            ->when($this->current_role === 'أمير المركز', function ($q, $v) {
                $q->when($this->selectedGradeId !== null, function ($q, $v) {
                    $q->where('grade_id', '=', $this->selectedGradeId);
                })->when($this->selectedTeacherId !== null, function ($q, $v) {
                    $q->where('group_sunnah_id', '=', $this->selectedTeacherId);
                });
            })->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->where('grade_id', '=', Supervisor::whereId(auth()->id())->first()->grade_id)
                    ->when($this->selectedTeacherId !== null, function ($q, $v) {
                        $q->where('group_sunnah_id', '=', $this->selectedTeacherId);
                    });
            })->when($this->current_role === 'محفظ', function ($q, $v) {
                $q->where('group_sunnah_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(): void
    {
        $this->all_Students();
    }


    public function hhhhh($dailyMemorization): void
    {
        if ($this->modalId !== '') {
            // هنا يتم فحص إذا هذه العملية عملية تحديث تسميع الطالب
            $this->book_name = $dailyMemorization->book->name;
            $this->book_id = $dailyMemorization->book_id;
            $this->ret_book_id = $dailyMemorization->book_id;
            $this->hadith_to_id = $dailyMemorization->hadith_to;
            $this->hadith_from_id = $dailyMemorization->hadith_from;
            $this->ret_hadith_from_id = $dailyMemorization->hadith_from;
            $this->ret_hadith_to_id = $dailyMemorization->hadith_to;
        } else if ($dailyMemorization->book->total_number_hadith === $dailyMemorization->hadith_to) {
            // هنا يتم فحص إذا الطالب أنجز حفظ الكتاب كاملة أو لا
            $this->book_id = $dailyMemorization->book_id + 1;
            $this->hadith_from_id = 1;
            $this->hadith_to_id = 1;
            $this->ret_book_id = $dailyMemorization->book_id + 1;
            $this->ret_hadith_to_id = 1;
            $this->ret_hadith_from_id = 1;
            $this->all_total_number_Hadith_From();
        } else {
            $this->book_id = $dailyMemorization->book_id;
            $this->hadith_from_id = $dailyMemorization->hadith_to + 1;
            $this->hadith_to_id = $dailyMemorization->hadith_to + 1;
            $this->ret_hadith_from_id = $dailyMemorization->hadith_to + 1;
            $this->ret_book_id = $dailyMemorization->book_id;
            $this->ret_hadith_to_id = $dailyMemorization->hadith_to + 1;
            $this->all_total_number_Hadith_From();
        }
        $this->all_total_number_Hadith_To();
    }

    public function store_Attendance($id, $status): void
    {
        if ($status === StudentSunnahAttendance::ABSENCE_STATUS || $status === StudentSunnahAttendance::AUTHORIZED_STATUS) {
            $dailyMemorization = StudentSunnahDailyMemorization::query()
                ->where('student_id', $id)
                ->whereDate('datetime', date('Y-m-d'))->first();
            if ($dailyMemorization !== null) {
                $isComplete = false;
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'error', 'message' => 'عذرا لا يمكنك تغيير حالة الطالب بسبب إدخال متابعة حفظ الطالب مسبقا من خلال تاريخ اليوم.']);
            } else {
                $isComplete = true;
            }
        } else {
            $isComplete = true;
        }

        if ($isComplete) {
            $studentAttendance = StudentSunnahAttendance::where('student_id', $id)->whereDate('datetime', date('Y-m-d'))->first();
            StudentSunnahAttendance::updateOrCreate(['id' => $studentAttendance->id ?? null], [
                'student_id' => $id,
                'teacher_id' => $this->current_role === User::TEACHER_ROLE ? auth()->id() : Student::where('id', $id)->first()->group_sunnah->teacher_id,
                'datetime' => date('Y-m-d h:i:s'),
                'status' => $status,
            ]);
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد حضور وغياب الطالب بنجاح.']);
        }
    }

    public
    function delete()
    {
        if ($this->modalId !== '') {
            StudentSunnahDailyMemorization::destroy($this->modalId);
            $this->emit('hideDialogAddDailyMemorization');
            if ($this->selectedType === StudentSunnahDailyMemorization::MEMORIZE_TYPE) {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية حذف متابعة حفظ الطالب لهذا اليوم بنجاح.']);
            } else {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية حذف متابعة مراجعة الطالب لهذا اليوم بنجاح.']);
            }

            $this->modalFormReset();
        }
    }

    public function setMessage($message): void
    {
        $this->catchError = $message;
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => $this->catchError]);
    }

    public function messages(): array
    {
        return [
            'selectedType.required' => 'حقل النوع مطلوب',
            'selectedType.numeric' => 'يجب اختيار صالح لحقل النوع',
            'book_id.required' => 'حقل كتاب البداية مطلوب',
            'book_id.numeric' => 'يجب اختيار صالح لحقل كتاب البداية',
            'hadith_from_id.required' => 'حقل رقم حديث البداية مطلوب',
            'hadith_from_id.numeric' => 'يجب اختيار صالح لحقل رقم حديث البداية',
            'hadith_to_id.required' => 'حقل رقم حديث النهاية مطلوب',
            'hadith_to_id.numeric' => 'يجب اختيار صالح لحقل رقم حديث النهاية',
            'evaluation.required' => 'حقل التقييم مطلوب',
            'evaluation.numeric' => 'يجب اختيار صالح لحقل التقييم',
        ];
    }

    public function continueValidateModal(MessageBag $messageBag): void
    {
        if ($this->hadith_from_id === $this->ret_hadith_from_id) {
            if ($this->hadith_to_id <= $this->ret_hadith_to_id) {
                if ($this->hadith_to_id >= $this->hadith_from_id) {
                    $this->storeOrUpdate();
                } else {
                    $messageBag->add('hadith_to_id', 'عذرا يجب أن يكون هنا اختيار صحيح');
                    $this->setErrorBag($messageBag);
                }
            } else {
                $this->storeOrUpdate();
            }
        } else {
            $messageBag->add('hadith_from_id', 'عذرا يجب أن يتم اختيار نفس حديث البداية');
            $this->setErrorBag($messageBag);
        }
    }
}
