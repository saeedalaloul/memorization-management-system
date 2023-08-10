<?php

namespace App\Http\Livewire;

use App\Models\AyaDetails;
use App\Models\DailyMemorizationDetails;
use App\Models\Grade;
use App\Models\Group;
use App\Models\QuranPart;
use App\Models\QuranSuras;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentDailyMemorization;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;

class StudentsDailyMemorization extends HomeComponent
{
    public $groups = [], $grades = [], $suras_selected = [], $suras_custom_selected = [], $rows = [], $partsCombined = [], $parts = [], $suras;

    public $type_name, $evaluation_name, $sura_from_name, $sura_to_name, $aya_from_name, $aya_to_name;

    public $selectedGradeId, $selectedTeacherId, $selectedPartCombinedId, $count_parts_cumulative, $date, $status,
        $selectedPartId, $retStudent, $student_name, $dayOfWeek, $selectedType, $evaluation;
    public $ret_type, $daily_memorization, $i = 0, $selected_count = 0;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getLastDataModalByType' => 'getLastDataModalByType',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->all_Grades();
        if ($this->current_role === User::TEACHER_ROLE) {
            $this->perPage = 25;
        }
    }

    public function render()
    {
        $this->calcSelectedAll();
        if ($this->modalId === '') {
            if ($this->selectedType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                $this->getQuranSurasByPart();
            } else {
                $this->validateFromQuranSurasForStudent();
            }
        } else {
            $this->all_QuranSurasUpdated();
        }
        return view('livewire.students-daily-memorization', ['students' => $this->all_Students(),]);
    }

    private function calcSelectedAll()
    {
        if ($this->modalId === '') {
            $this->selected_count = 0;
            if (!empty($this->suras_selected)) {
                foreach ($this->suras as $index => $value) {
                    $arr = (array)$value;
                    if (isset($this->suras_selected[$arr['id']])) {
                        $this->selected_count++;
                    }
                }
            }
        }
    }

    public function getQuranSurasByPart()
    {
        $this->rows = [];
        $this->suras = $this->all_QuranSuras(StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE, [$this->selectedPartId], $this->retStudent->current_cumulative_revision_count, $this->count_parts_cumulative);
        foreach ($this->suras as $index => $value) {
            $this->addRow();
        }
    }

    public
    function all_QuranSuras($type, $current_part_id = null, $revision_count = 1, $cumulative_type = 1): Collection
    {
        return DB::table('quran_suras')
            ->select(['id', 'name', 'quran_part_id', 'total_number_aya', DB::raw('(select aya_to from students_daily_memorization
                   INNER JOIN daily_memorization_details ON students_daily_memorization.id = daily_memorization_details.id
                   WHERE student_id = ' . $this->retStudent->id . ' and type="' . $type . '" and cumulative_type="' . $cumulative_type . '"
                   and revision_count="' . $revision_count . '"  and sura_id = quran_suras.id ORDER by aya_to desc LIMIT 1 ) as aya_from')])
            ->when($current_part_id !== null, function ($q) use ($current_part_id) {
                $q->whereIn('quran_part_id', $current_part_id);
            })
            ->havingNull(DB::raw('aya_from'))
            ->orHaving(DB::raw('aya_from'), '<', DB::raw('total_number_aya'))
            ->orderByDesc('id')
            ->get();
    }

    public function addRow()
    {
        $messageBag = new MessageBag();
        if ($this->selectedType !== "" && $this->selectedType !== null) {
            if (count($this->rows) > 0) {
                if (count($this->suras) > count($this->rows)) {
                    $this->rows[] = '';
                    if ($this->selectedType !== StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                        $this->suras_selected[] = ['id' => null, 'aya_to' => null];
                    }
                }
            } else {
                $this->rows[] = '';
                if ($this->selectedType !== StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                    $this->suras_selected[] = ['id' => null, 'aya_to' => null];
                }
            }
        } else {
            $messageBag->add('selectedType', 'عذرا يجب اختيار نوع العملية.');
            $this->setErrorBag($messageBag);
            for ($i = count($this->rows) + 1; $i >= 0; $i--) {
                $this->removeRow($i);
            }
        }
    }

    public function removeRow($index)
    {
        if ($this->modalId === '') {
            unset($this->rows[$index]);
            unset($this->suras_selected[$index]);
            $this->dispatchBrowserEvent('deleteElement', ['index' => $index]);
        } else {
            $this->daily_memorization->daily_memorization_details()->where('sura_id', $this->suras_selected[$index]['id'])->delete();
            if ($this->daily_memorization->daily_memorization_details()->count() === 0) {
                $this->daily_memorization->delete();
                $this->modalId = '';
                $this->evaluation = null;
            }
            $this->i = 0;
            $this->rows = [];
            $this->suras_selected = [];
        }
    }

    public
    function delete(): void
    {
        if ($this->modalId !== '') {
            $this->daily_memorization->daily_memorization_details()->delete();
            $this->daily_memorization->delete();
            $this->emit('hideDialogAddDailyMemorization');
            if ($this->selectedType === StudentDailyMemorization::MEMORIZE_TYPE) {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية حذف متابعة حفظ الطالب لهذا اليوم بنجاح.']);
            } else if ($this->selectedType === StudentDailyMemorization::REVIEW_TYPE) {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية حذف متابعة مراجعة الطالب لهذا اليوم بنجاح.']);
            } else {
                $this->dispatchBrowserEvent('alert',
                    ['type' => 'success', 'message' => 'تمت عملية حذف متابعة مراجعة التجميعي للطالب لهذا اليوم بنجاح.']);
            }
            $this->modalFormReset();
        }
    }

    public function modalFormReset(): void
    {
        $this->resetValidation();
        $this->retStudent = null;
        $this->student_name = null;
        $this->dayOfWeek = null;
        $this->ret_type = null;
        $this->selectedType = null;
        $this->evaluation = null;
        $this->suras = [];
        $this->suras_selected = [];
        $this->suras_custom_selected = [];
        $this->rows = [];
        $this->catchError = '';
        $this->modalId = '';
        $this->daily_memorization = null;
        $this->i = 0;
        $this->selected_count = 0;
        $this->selectedPartCombinedId = null;
        $this->count_parts_cumulative = null;
        $this->selectedPartId = null;
        $this->date = null;
        $this->status = null;
    }

    public
    function validateFromQuranSurasForStudent()
    {
        if ($this->retStudent !== null) {
            $messageBag = new MessageBag();
            if ($this->selectedType === StudentDailyMemorization::MEMORIZE_TYPE) {
                if ($this->retStudent->current_part_id === null) {
                    $this->suras = $this->all_QuranSuras(StudentDailyMemorization::MEMORIZE_TYPE);
                } else {
                    $current_part_id = $this->retStudent->current_part_id === 16 || $this->retStudent->current_part_id === 17
                    || $this->retStudent->current_part_id === 18 ? [16, 17, 18] : [$this->retStudent->current_part_id];
                    $this->suras = $this->all_QuranSuras(StudentDailyMemorization::MEMORIZE_TYPE, $current_part_id);
                    if (count($this->suras) === 0) {
                        $current_part_name = $this->retStudent->current_part->name . ' ' . $this->retStudent->current_part->description;
                        $messageBag->add('selectedType', 'عذرا, الطالب انتهى من حفظ جزء ' . $current_part_name);
                        $this->setErrorBag($messageBag);
                    }
                }
            } else if ($this->selectedType === StudentDailyMemorization::REVIEW_TYPE) {
                if ($this->retStudent->current_part_id === null) {
                    $this->suras = [];
                    $messageBag->add('selectedType', 'عذرا, الطالب لم يرتبط بسجل حفظ لأي جزء حاليا.');
                    $this->setErrorBag($messageBag);
                } else {
                    $current_part_id = $this->retStudent->current_part_id === 16 || $this->retStudent->current_part_id === 17
                    || $this->retStudent->current_part_id === 18 ? [16, 17, 18] : [$this->retStudent->current_part_id];

                    if ($this->retStudent->current_part_id === 16 || $this->retStudent->current_part_id === 17
                        || $this->retStudent->current_part_id === 18) {
                        // فحص في حال كان جزء السور يتبع لسورة البقرة أو الفاتحة يتم فتح المراجعة بدون فحص حفظ السورة
                        $this->suras = $this->all_QuranSuras(StudentDailyMemorization::REVIEW_TYPE, $current_part_id, $this->retStudent->current_revision_count);
                    } else {
                        if ($this->all_QuranSuras(StudentDailyMemorization::MEMORIZE_TYPE, $current_part_id)->isNotEmpty()) {
                            $current_part_name = $this->retStudent->current_part->name . ' ' . $this->retStudent->current_part->description;
                            $messageBag->add('selectedType', 'عذرا, الطالب لم ينتهي من حفظ جزء ' . $current_part_name);
                            $this->setErrorBag($messageBag);
                            $this->suras = [];
                        } else {
                            $this->suras = $this->all_QuranSuras(StudentDailyMemorization::REVIEW_TYPE, $current_part_id, $this->retStudent->current_revision_count);
                            if (count($this->suras) === 0) {
                                $current_part_name = $this->retStudent->current_part->name . ' ' . $this->retStudent->current_part->description;
                                $messageBag->add('selectedType', 'عذرا, الطالب انتهى من مراجعة جزء ' . $current_part_name);
                                $this->setErrorBag($messageBag);
                            }
                        }
                    }
                }
            }
        }
    }

    public function all_QuranSurasUpdated()
    {
        if ($this->daily_memorization !== null) {
            $this->suras = [];
            $ret_suras = $this->daily_memorization->daily_memorization_details()->with('quranSura:id,name,total_number_aya')->get()->toArray();

            foreach ($ret_suras as $index => $value) {
                $this->suras[$index]['id'] = $value['sura_id'];
                $this->suras[$index]['aya_from'] = ($value['aya_from']);
                $this->suras[$index]['aya_to'] = ($value['aya_to']);
                $this->suras[$index]['name'] = $value['quran_sura']['name'];
                $this->suras[$index]['total_number_aya'] = $value['quran_sura']['total_number_aya'];
                $this->suras[$index] = (object)$this->suras[$index];
            }
            foreach ($this->suras as $index => $value) {
                $this->addRow();
                $this->suras_selected[$index]['id'] = $value->id;
                if ($this->i === 0) {
                    $this->suras_selected[$index]['aya_to'] = $value->aya_to;
                }
            }
            $this->i++;
        }
    }

    public
    function all_Students()
    {
        return Student::query()
            ->with(['user', 'grade', 'group.teacher.user', 'student_is_block', 'student_is_warning', 'attendance_today'])
            ->search($this->search)
            ->when($this->current_role === 'أمير المركز', function ($q, $v) {
                $q->when($this->selectedGradeId !== null, function ($q, $v) {
                    $q->where('grade_id', '=', $this->selectedGradeId);
                })->when($this->selectedTeacherId !== null, function ($q, $v) {
                    $q->where('group_id', '=', $this->selectedTeacherId);
                });
            })->when($this->current_role === 'مشرف', function ($q, $v) {
                $q->where('grade_id', '=', Supervisor::whereId(auth()->id())->first()->grade_id)
                    ->when($this->selectedTeacherId !== null, function ($q, $v) {
                        $q->where('group_id', '=', $this->selectedTeacherId);
                    });
            })->when($this->current_role === 'محفظ', function ($q, $v) {
                $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public
    function all_Grades()
    {
        if ($this->current_role === User::SUPERVISOR_ROLE) {
            $this->grades = Grade::query()->where('id', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === User::TEACHER_ROLE) {
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === User::ADMIN_ROLE) {
            $this->grades = Grade::all();
        }
    }

    public function rules()
    {
        if ($this->selectedType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
            return [
                'selectedType' => 'required|string',
                'evaluation' => 'required',
            ];
        } else {
            return [
                'selectedType' => 'required|string',
                'suras_selected.*.id' => 'required|numeric',
                'suras_selected.*.aya_to' => 'required|numeric',
                'evaluation' => 'required',
            ];
        }
    }

    public function updatedDate($date): void
    {
        $messageBag = new MessageBag;
        if ($date !== null) {
            if ($date !== date('Y-m-d')) {
                $this->resetValidation('date');
                $this->suras = [];
                $this->dayOfWeek = Carbon::now()->translatedFormat('l') . ' ' . $date;
                $attendance = StudentAttendance::query()
                    ->where('student_id', $this->retStudent->id)
                    ->whereDate('datetime', $date)->first();
                if ($attendance !== null) {
                    $this->status = $attendance->status;
                    $dailyMemorization = StudentDailyMemorization::query()
                        ->where('student_id', $this->retStudent->id)
                        ->whereDate('datetime', $date)->first();
                    if ($dailyMemorization !== null) {
                        $this->selectedType = $dailyMemorization->type;
                        $this->daily_memorization = $dailyMemorization;
                        $messageBag->add('selectedType', 'عذرا يوجد متابعة سابقة لهذا الطالب!');
                        $this->setErrorBag($messageBag);
                    } else {
                        $this->resetValidation();
                        $this->daily_memorization = null;
                    }
                } else {
                    $this->resetValidation();
                    $this->selectedType = null;
                    $this->daily_memorization = null;
                }
            } else {
                $messageBag->add('date', 'عذرا لا يمكنك متابعة حفظ الطالب لهذا اليوم من خلال هذه الشاشة!');
                $this->setErrorBag($messageBag);
                $this->date = null;
            }
        }
    }

    public function updatedSelectedType()
    {
        if ($this->date !== null && $this->date !== date('Y-m-d')) {
            if ($this->status === StudentAttendance::PRESENCE_STATUS || $this->status === StudentAttendance::LATE_STATUS) {
                if ($this->selectedType === StudentDailyMemorization::MEMORIZE_TYPE || $this->selectedType === StudentDailyMemorization::REVIEW_TYPE) {
                    $this->getLastDataModalByType();
                } elseif ($this->selectedType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                    $this->all_QuranPartsCombined();
                } else {
                    $this->rows = [];
                    $this->suras = [];
                    $this->daily_memorization = null;
                }
            }
        }
    }

    public function getLastDataModalByType()
    {
        $this->i = 0;
        $this->modalId = '';
        $this->rows = [];
        $this->suras_selected = [];
        $this->suras_custom_selected = [];
        $this->evaluation = null;

        if ($this->date === null) {
            $dailyMemorization = StudentDailyMemorization::query()
                ->where('student_id', $this->retStudent->id)
                ->where('type', $this->selectedType)
                ->whereDate('datetime', date('Y-m-d'))->first();


            if ($dailyMemorization !== null) {
                $this->modalId = $dailyMemorization->id;
                $this->daily_memorization = $dailyMemorization;
                $this->evaluation = $dailyMemorization->evaluation;
            }
        }

        if ($this->selectedType !== StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
            if (empty($this->rows)) {
                $this->addRow();
            }
        } else {
            $this->all_QuranPartsCombined();
        }
        $this->resetValidation();
    }

    private function all_QuranPartsCombined()
    {
        $this->partsCombined = QuranPart::query()->where('type', '=', QuranPart::DESERVED_TYPE)
            ->whereDoesntHave('exams', function ($q) {
                $q->whereHas('exam_success_mark', function ($q) {
                    $q->where(DB::raw('exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
                })->where('student_id', '=', $this->retStudent->id);
            })
            ->when($this->retStudent->current_part_cumulative_id !== null, function ($q) {
                $q->whereId($this->retStudent->current_part_cumulative_id);
            })
            ->orderByDesc('id')->get();
        if ($this->retStudent->current_part_cumulative_id !== null) {
            $this->selectedPartCombinedId = $this->retStudent->current_part_cumulative_id;
            $this->count_parts_cumulative = QuranPart::query()->where('id', $this->selectedPartCombinedId)->value('total_preservation_parts');
            $this->all_QuranParts();
        }
    }

    private function all_QuranParts()
    {
        $arr = explode('-', $this->partsCombined->where('id', $this->selectedPartCombinedId)->first()->name ?? null);
        if (count($arr) === 2) {
            $parts_ids = [];
            for ($i = $arr[1]; $i <= $arr[0]; $i++) {
                $parts_ids[] = (int)$i;
            }

            $this->parts = QuranPart::query()->where('type', '=', QuranPart::INDIVIDUAL_TYPE)
                ->whereIn('name', $parts_ids)
                ->orderByDesc('id')->get();
        } else {
            $this->parts = [];
            $this->selectedPartId = null;
        }
    }

    public function validatePreviousModal()
    {
        $messageBag = new MessageBag();
        if ($this->status !== null && $this->date !== null) {
            if ($this->date !== date('Y-m-d')) {
                if ($this->selectedType === null || $this->selectedType === '') {
                    $this->store_Attendance($this->retStudent->id, $this->status);
                } else {
                    if ($this->status === StudentAttendance::ABSENCE_STATUS || $this->status === StudentAttendance::AUTHORIZED_STATUS) {
                        $messageBag->add('selectedType', 'عذرا, يجب إلغاء تحديد هذه العملية.');
                    } else {
                        $this->validateModal();
                    }
                }
            }
        } else {
            if ($this->date === null) {
                $messageBag->add('date', 'عذرا, حقل التاريخ مطلوب.');
            } elseif ($this->status === null) {
                $messageBag->add('status', 'عذرا, حقل الحالة مطلوب.');
            }
        }
        $this->setErrorBag($messageBag);
    }

    public function store_Attendance($id, $status)
    {
        if ($status === StudentAttendance::ABSENCE_STATUS || $status === StudentAttendance::AUTHORIZED_STATUS) {
            $dailyMemorization = StudentDailyMemorization::query()
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
            $studentAttendance = StudentAttendance::where('student_id', $id)->whereDate('datetime', $this->date === null ? date('Y-m-d') : $this->date)->first();
            StudentAttendance::updateOrCreate(['id' => $studentAttendance->id ?? null], [
                'student_id' => $id,
                'teacher_id' => $this->current_role === User::TEACHER_ROLE ? auth()->id() : Student::where('id', $id)->first()->group->teacher_id,
                'datetime' => $this->date === null ? date('Y-m-d h:i:s') : $this->date . ' ' . date('h:i:s'),
                'status' => $status,
            ]);

            if ($this->selectedType === null || $this->selectedType === '') {
                $this->emit('hideDialogAddDailyMemorization');
                $this->modalFormReset();
            }

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد حضور وغياب الطالب بنجاح.']);
        }
    }

    public function validateModal()
    {
        $this->resetValidation();
        $this->validate();
        $isValidate = true;
        $messageBag = new MessageBag();

        if ($this->selectedType !== StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
            foreach ($this->suras_selected as $index => $sura) {
                $i = 0;
                foreach ($this->suras_selected as $index1 => $sura1) {
                    if ($sura['id'] == $sura1['id']) {
                        $i++;
                    }
                }

                if ($i >= 2) {
                    $isValidate = false;
                    $messageBag->add('suras_selected.' . $index . '.id', 'عذرا لا يمكنك اختيار نفس السورة أكثر من مرة.');
                }
            }

            if ($this->modalId === '') {
                $dailyMemorization = StudentDailyMemorization::query()
                    ->where('student_id', $this->retStudent->id)
                    ->where('type', $this->selectedType)
                    ->whereDate('datetime', $this->date === null ? date('Y-m-d') : $this->date)->first();

                if ($dailyMemorization !== null) {
                    $isValidate = false;
                    if ($dailyMemorization->type === StudentDailyMemorization::MEMORIZE_TYPE) {
                        $messageBag->add('selectedType', 'عذرا لقد تم إدخال متابعة حفظ الطالب مسبقا من خلال تاريخ اليوم');
                    } else {
                        $messageBag->add('selectedType', 'عذرا لقد تم إدخال متابعة مراجعة الطالب مسبقا من خلال تاريخ اليوم');
                    }
                }
            }
        }

        $this->setErrorBag($messageBag);

        if ($isValidate) {
            if ($this->date !== null) {
                $this->store_Attendance($this->retStudent->id, $this->status);
            }
            if ($this->selectedType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                $this->storeCumulativeReview();
            } else {
                $this->storeOrUpdate();
            }
        }
    }

    public function storeCumulativeReview()
    {
        if (!empty($this->suras_selected) || !empty($this->suras_custom_selected)) {
            $number_pages = 0;
            $ret_suras = DB::table('quran_suras')
                ->select(['id', 'name', 'total_number_aya', DB::raw('(select aya_to from  students_daily_memorization
                   INNER JOIN daily_memorization_details ON students_daily_memorization.id = daily_memorization_details.id
                   WHERE student_id = ' . $this->retStudent->id . ' and type="' . $this->selectedType . '" and cumulative_type="' . $this->count_parts_cumulative . '"
                   and revision_count="' . $this->retStudent->current_cumulative_revision_count . '"  and sura_id = quran_suras.id ORDER by aya_to desc LIMIT 1 ) as aya_to')])
                ->whereIn('id', array_merge(array_keys($this->suras_selected), array_keys($this->suras_custom_selected)))
                ->orderByDesc('id')
                ->get();

            foreach ($ret_suras as $index => $value) {
                if (isset($this->suras_custom_selected[$value->id])) {
                    $number_pages += round((AyaDetails::query()
                            ->where('sura_name', '=', $value->name)
                            ->whereBetween('aya_number', [$value->aya_to + 1, $this->suras_custom_selected[$value->id]])
                            ->sum('aya_percent')) / 15, 1);
                } else {
                    $number_pages += round((AyaDetails::query()
                            ->where('sura_name', '=', $value->name)
                            ->whereBetween('aya_number', [$value->aya_to + 1, $value->total_number_aya])
                            ->sum('aya_percent')) / 15, 1);
                }
            }


            $studentDailyMemorization = StudentDailyMemorization::create([
                'student_id' => $this->retStudent->id,
                'teacher_id' => $this->current_role === User::TEACHER_ROLE ? auth()->id() : Student::where('id', $this->retStudent->id)->first()->group->teacher_id,
                'type' => $this->selectedType,
                'number_pages' => $number_pages,
                'evaluation' => $this->evaluation,
                'revision_count' => $this->retStudent->current_cumulative_revision_count ?? 1,
                'cumulative_type' => (string)$this->count_parts_cumulative,
                'datetime' => $this->date === null ? date('Y-m-d h:i:s') : $this->date . ' ' . date('h:i:s'),
            ]);

            if ($studentDailyMemorization !== null && $studentDailyMemorization->id !== null) {
                foreach ($ret_suras as $index => $value) {
                    if (isset($this->suras_custom_selected[$value->id])) {
                        DailyMemorizationDetails::create([
                            'id' => $studentDailyMemorization->id,
                            'sura_id' => $value->id,
                            'aya_from' => $value->aya_to + 1,
                            'aya_to' => $this->suras_custom_selected[$value->id],
                        ]);
                    } else {
                        DailyMemorizationDetails::create([
                            'id' => $studentDailyMemorization->id,
                            'sura_id' => $value->id,
                            'aya_from' => $value->aya_to + 1,
                            'aya_to' => $value->total_number_aya,
                        ]);
                    }
                }
            }

            if ($this->retStudent->current_part_cumulative_id === null) {
                $this->retStudent->update(['current_part_cumulative_id' => $this->selectedPartCombinedId]);
            }

            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة مراجعة التجميعي للطالب بنجاح.']);
            $this->emit('hideDialogAddDailyMemorization');
            $this->modalFormReset();
        } else {
            $messageBag = new MessageBag();
            $messageBag->add('selectedType', 'عذرا لم تحدد أي سورة بعد.');
            $this->setErrorBag($messageBag);
        }
    }

    public function storeOrUpdate()
    {
        DB::beginTransaction();

        try {
            DB::commit();
            $number_pages = 0;
            $isCreatedOrUpdated = true;
            foreach ($this->suras_selected as $index => $sura) {
                $aya_from = 0;
                $sura_name = null;
                foreach ($this->suras as $i => $value) {
                    if ($sura['id'] == $value['id']) {
                        $sura_name = $value['name'];
                        $aya_from = $value['aya_from'] + ($this->modalId === '' ? 1 : 0);
                    }
                }

                if ($aya_from > 0 && $sura_name != null) {
                    $number_pages += round((AyaDetails::query()
                            ->where('sura_name', '=', $sura_name)
                            ->whereBetween('aya_number', [$aya_from, $sura['aya_to']])->sum('aya_percent')) / 15, 1);
                }
            }

            if ($number_pages > 0) {
                if ($this->selectedType === StudentDailyMemorization::REVIEW_TYPE) {
                    $messageBag = new MessageBag();
                    if ($this->retStudent->current_revision_count === 3 && $number_pages > 3.0) {
                        $isCreatedOrUpdated = false;
                        $messageBag->add('selectedType', 'عذرا لا يمكنك مراجعة أكثر من 3 صفحات لأنك في المراجعة ' . '(' . $this->retStudent->current_revision_count . ')' . ' للجزء.');
                    } elseif ($this->retStudent->current_revision_count >= 4 && $number_pages > 1.0) {
                        $isCreatedOrUpdated = false;
                        $messageBag->add('selectedType', 'عذرا لا يمكنك مراجعة أكثر من صفحة لأنك في المراجعة ' . '(' . $this->retStudent->current_revision_count . ')' . ' للجزء.');
                    }
                    $this->setErrorBag($messageBag);
                }

                if ($isCreatedOrUpdated) {
                    if ($this->modalId !== '') {
                        foreach ($this->suras_selected as $index => $value) {
                            $this->daily_memorization->daily_memorization_details()->where('sura_id', $value['id'])->update([
                                'aya_to' => $value['aya_to'],
                            ]);
                        }

                        $this->daily_memorization->update([
                            'evaluation' => $this->evaluation,
                            'number_pages' => $number_pages,
                        ]);

                        if ($this->selectedType === StudentDailyMemorization::MEMORIZE_TYPE) {
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تحديث متابعة حفظ الطالب بنجاح.']);
                        } else {
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية تحديث متابعة مراجعة الطالب بنجاح.']);
                        }
                    } else {
                        $studentDailyMemorization = StudentDailyMemorization::create([
                            'student_id' => $this->retStudent->id,
                            'teacher_id' => $this->current_role === User::TEACHER_ROLE ? auth()->id() : Student::where('id', $this->retStudent->id)->first()->group->teacher_id,
                            'type' => $this->selectedType,
                            'number_pages' => $number_pages,
                            'evaluation' => $this->evaluation,
                            'revision_count' => $this->selectedType === StudentDailyMemorization::MEMORIZE_TYPE ? 1 : $this->retStudent->current_revision_count ?? 1,
                            'datetime' => $this->date === null ? date('Y-m-d h:i:s') : $this->date . ' ' . date('h:i:s'),
                        ]);

                        if ($studentDailyMemorization !== null && $studentDailyMemorization->id !== null) {
                            foreach ($this->suras_selected as $index => $sura) {
                                $aya_from = 0;
                                foreach ($this->suras as $i => $value) {
                                    if ($sura['id'] == $value['id']) {
                                        $aya_from = $value['aya_from'] + 1;
                                    }
                                }

                                if ($aya_from > 0) {
                                    DailyMemorizationDetails::create([
                                        'id' => $studentDailyMemorization->id,
                                        'sura_id' => $sura['id'],
                                        'aya_from' => $aya_from,
                                        'aya_to' => $sura['aya_to'],
                                    ]);
                                }
                            }
                        }

                        if ($this->selectedType === 'memorize' && $this->retStudent->current_part_id === null) {
                            $this->retStudent->update(['current_part_id' => QuranSuras::whereId($this->suras_selected[0]['id'])->first()->quran_part_id]);
                        }


                        if ($this->selectedType === StudentDailyMemorization::MEMORIZE_TYPE) {
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة حفظ الطالب بنجاح.']);
                        } else {
                            $this->dispatchBrowserEvent('alert',
                                ['type' => 'success', 'message' => 'تمت عملية إضافة متابعة مراجعة الطالب بنجاح.']);
                        }
                    }
                    $this->emit('hideDialogAddDailyMemorization');
                    $this->modalFormReset();
                }
            }
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function loadModalData($id, $selectedType)
    {
        $this->i = 0;
        if ($this->retStudent === null) {
            $this->modalFormReset();
            $this->retStudent = Student::with(['user'])->where('id', $id)->first();
            $this->student_name = $this->retStudent->user->name;
        } elseif ($id !== $this->retStudent->id) {
            $this->modalFormReset();
            $this->retStudent = Student::with(['user'])->where('id', $id)->first();
            $this->student_name = $this->retStudent->user->name;
        }
        // هنا يتم تنفيذ عملية إضافة تسميع الطلاب
        if ($selectedType === -2) {
            $this->emit('showDialogAddPreviousDailyMemorization');
        } elseif ($selectedType === -1) {
            $this->dayOfWeek = Carbon::now()->translatedFormat('l');
            $dailyMemorization = StudentDailyMemorization::query()
                ->where('student_id', $this->retStudent->id)
                ->orderByDesc('datetime')->first();


            if ($dailyMemorization !== null) {
                $this->selectedType = $dailyMemorization->type;
                $this->ret_type = $dailyMemorization->type;
                if ($this->selectedType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                    $this->all_QuranPartsCombined();
                } else {
                    $this->addRow();
                }
                if (Carbon::parse($dailyMemorization->datetime)->format('Y-m-d') === date('Y-m-d')) {
                    $this->modalId = $dailyMemorization->id;
                    $this->daily_memorization = $dailyMemorization;
                    $this->evaluation = $dailyMemorization->evaluation;
                }
            }
            $this->emit('showDialogAddDailyMemorization');
        } else {
            // هنا يتم تنفيذ عملية عرض تسميع الطلاب
            $dailyMemorization = DB::table('students_daily_memorization')
                ->select(['students_daily_memorization.id', 'type', 'evaluation', 'datetime',
                    DB::raw("(select name from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id
                      where daily_memorization_details.id = students_daily_memorization.id
                      order by sura_id desc limit 1) as sura_from"),
                    DB::raw("(select aya_from from daily_memorization_details where daily_memorization_details.id = students_daily_memorization.id
                      order by sura_id desc limit 1) as aya_from"),
                    DB::raw("(select name from daily_memorization_details inner join quran_suras
                     on daily_memorization_details.sura_id = quran_suras.id
                     where daily_memorization_details.id = students_daily_memorization.id
                      order by sura_id asc limit 1) as sura_to"),
                    DB::raw("(select aya_to from daily_memorization_details where daily_memorization_details.id = students_daily_memorization.id
                      order by sura_id asc limit 1) as aya_to"),])
                ->where('student_id', $this->retStudent->id)
                ->where('type', $selectedType)
                ->orderByDesc('datetime')->first();

            if ($dailyMemorization !== null) {
                $this->dayOfWeek = Carbon::parse($dailyMemorization->datetime)
                        ->translatedFormat('l') . '  ' . Carbon::parse($dailyMemorization->datetime)->format('Y-m-d');
                $this->type_name = StudentDailyMemorization::types()[$dailyMemorization->type];
                $this->sura_from_name = $dailyMemorization->sura_from;
                $this->aya_from_name = $dailyMemorization->aya_from;
                $this->sura_to_name = $dailyMemorization->sura_to;
                $this->aya_to_name = $dailyMemorization->aya_to;
                $this->evaluation_name = StudentDailyMemorization::evaluations()[$dailyMemorization->evaluation];

                $this->emit('showDialogShowDailyMemorization');
            }

        }
    }

    public function updatedSelectedPartCombinedId()
    {
        if (!empty($this->selectedPartCombinedId)) {
            $this->count_parts_cumulative = QuranPart::query()->where('id', $this->selectedPartCombinedId)->value('total_preservation_parts');
            $this->all_QuranParts();
        }
    }

    public function updatedSelectedPartId()
    {
        $this->getQuranSurasByPart();
    }

    public function updatedSurasSelected()
    {
        if ($this->selectedType !== StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
            if (count($this->suras_selected) > 1) {
                foreach ($this->suras_selected as $key => $value) {
                    foreach ($this->suras as $index => $sura) {
                        if ($value != null && $sura['id'] == $value['id'] && isset($value['aya_to']) &&
                            $value['aya_to'] > $sura['total_number_aya']) {
                            $this->suras_selected[$key]['aya_to'] = null;
                        }
                    }
                }
            }
        } else {
            $this->clearSurasAndAyasIds();
        }
    }

    private
    function clearSurasAndAyasIds()
    {
        if ($this->modalId === '') {
            foreach ($this->suras_selected as $key => $value) {
                if ($this->selectedType === StudentDailyMemorization::CUMULATIVE_REVIEW_TYPE) {
                    if ($this->suras_selected[$key] && isset($this->suras_custom_selected[$key])) {
                        unset($this->suras_custom_selected[$key]);
                    }
                }
                if (!$this->suras_selected[$key]) {
                    unset($this->suras_selected[$key]);
                }
            }
        }
    }

    public
    function selectAll()
    {
        foreach ($this->suras as $index => $value) {
            if ($this->suras[$index]['quran_part_id'] === (int)$this->selectedPartId) {
                $this->suras_selected[$value['id']] = true;
            }
        }
        $this->clearSurasAndAyasIds();
    }

    public
    function undoSelectAll()
    {
        foreach ($this->suras as $index => $value) {
            if ($this->suras[$index]['quran_part_id'] === (int)$this->selectedPartId) {
                unset($this->suras_selected[$value['id']]);
            }
        }
        $this->clearSurasAndAyasIds();
    }

    public
    function getTeachersByGradeId()
    {
        $this->reset('groups', 'selectedTeacherId');

        if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::QURAN_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'أمير المركز') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])
                    ->where('type', Group::QURAN_TYPE)
                    ->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role === 'محفظ') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()
                    ->where('type', Group::QURAN_TYPE)
                    ->where('teacher_id', auth()->id())->get();
            }
        }
    }

    public
    function submitSearch()
    {
        $this->all_Students();
    }

    public
    function setMessage($message): void
    {
        $this->catchError = $message;
        $this->dispatchBrowserEvent('alert',
            ['type' => 'error', 'message' => $this->catchError]);
    }

    public
    function messages()
    {
        return [
            'selectedType.required' => 'حقل النوع مطلوب',
            'selectedType.numeric' => 'يجب اختيار صالح لحقل النوع',
            'suras_selected.*.id.required' => 'حقل السورة مطلوب',
            'suras_selected.*.id.numeric' => 'يجب اختيار صالح لحقل السورة',
            'suras_selected.*.aya_to.required' => 'حقل رقم الأية مطلوب',
            'suras_selected.*.aya_to.numeric' => 'يجب اختيار صالح لحقل رقم الأية',
            'evaluation.required' => 'حقل التقييم مطلوب',
            'evaluation.numeric' => 'يجب اختيار صالح لحقل التقييم',
        ];
    }

}
