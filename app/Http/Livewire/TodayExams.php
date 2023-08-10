<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\ExamCustomQuestion;
use App\Models\ExamOrder;
use App\Models\ExamSettings;
use App\Models\ExamSuccessMark;
use App\Models\Grade;
use App\Models\Group;
use App\Models\ImprovementExam;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\SunnahExam;
use App\Models\SunnahImprovementExam;
use App\Models\SunnahPart;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\User;
use App\Notifications\FailureExamOrderForTeacherNotify;
use App\Notifications\ImproveExamForTeacherNotify;
use App\Notifications\NewExamForTeacherNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TodayExams extends HomeComponent
{
    use NotificationTrait;

    public $grades = [], $groups = [], $students = [];
    public $notes, $numberOfReplays = 0;
    public $exam_mark = 100, $exam_questions_count, $focus_id, $success_mark, $another_mark = 10;
    public $final_exam_score, $exam_notes;
    public $isExamOfStart = false, $signs_questions = [], $marks_questions = [], $top_narrator_discounts = []
    , $bottom_narrator_discounts = [];
    public $exam_questions_min, $exam_questions_max, $examOrder;
    public $selectedGradeId, $selectedTeacherId, $selectedStudentId;

    protected $listeners = [
        'getTeachersByGradeId' => 'getTeachersByGradeId',
        'getStudentsByTeacherId' => 'getStudentsByTeacherId',
    ];

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
        $this->link = 'manage_exams/';
        $this->all_Grades();
    }

    public function render()
    {
        return view('livewire.today-exams', [
            'exams_today' => $this->all_Exams_Today(),
        ]);
    }

    public function messages()
    {
        $messages = [
            'notes.required' => 'حقل الملاحظات مطلوب',
            'notes.string' => 'حقل الملاحظات يجب أن يحتوي على نص',
            'notes.max' => 'حقل الملاحظات يجب أن لا يزيد عن 50 حرف',
            'exam_notes.max' => 'حقل الملاحظات يجب أن لا يزيد عن 50 حرف',
            'exam_questions_count.required' => 'حقل عدد أسئلة الإختبار مطلوب',
            'exam_questions_count.numeric' => 'حقل عدد أسئلة الإختبار يجب أن يحتوي على رقم',
            'exam_mark.required' => 'علامة الاختبار مطلوبة',
            'exam_mark.numeric' => 'يجب أن يكون رقم',
            'exam_mark.between' => 'يجب أن تكون علامة الاختبار بين 60 أو 100',
            'another_mark.numeric' => 'يجب أن يكون رقم',
        ];

        if ($this->examOrder->partable_type === 'App\Models\QuranPart') {
            // في حال كان نوع الاختبار قرآن
            $messages['another_mark.required'] = 'علامة أحكام الطالب مطلوبة';
            $messages['another_mark.between'] = 'يجب أن تكون علامة أحكام الطالب بين 4 أو 10';
        } else {
            // في حال كان نوع الاختبار سنة
            $messages['another_mark.required'] = 'علامة سؤال المعاني مطلوبة';
            $messages['another_mark.between'] = 'يجب أن تكون علامة سؤال المعاني بين 0 أو 3';
        }

        return $messages;
    }

    public function examOrderRefusal()
    {
        $this->validate([
            'notes' => 'required|string|max:50',
        ]);

        $examOrder = ExamOrder::where('id', $this->examOrder->id)->first();
        if ($examOrder && $examOrder->status === ExamOrder::ACCEPTABLE_STATUS && $examOrder->teacher_id !== auth()->id() && $this->current_role === 'مختبر') {
            $examOrder->update([
                'status' => ExamOrder::FAILURE_STATUS,
                'notes' => $this->notes,
            ]);

            $examOrder->teacher->user->notify(new FailureExamOrderForTeacherNotify($examOrder));
            $title = "طلب اختبار فشل إجراؤه";
            if ($this->examOrder->partable_type === 'App\Models\QuranPart') {
                // في حال كان نوع الاختبار قرآن
                $message = "لقد قام المختبر بتغيير حالة طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' ' . $examOrder->partable->description . ' إلى لم يختبر بسبب: ' . $examOrder->notes;
            } else {
                // في حال كان نوع الاختبار سنة
                $message = "لقد قام المختبر بتغيير حالة طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->partable->name . ' (' . $examOrder->partable->total_hadith_parts . ') حديث' . ' إلى لم يختبر بسبب: ' . $examOrder->notes;
            }
            $this->push_notification($message, $title, 'manage_exams_orders/' . $examOrder->id, [$examOrder->teacher->user->user_fcm_token->device_token ?? null]);
            Cache::forget('exam_order_id_' . $examOrder->id);

            $this->dispatchBrowserEvent('hideModal');
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد عدم إجراء الطالب الاختبار بنجاح.']);
            $this->clearForm();
        }
    }

    public function examQuestionsNumberApproval()
    {
        $this->validate(['exam_questions_count' => 'required|numeric']);
        $this->initializeExamStartInputs(null);
        $this->dispatchBrowserEvent('hideModal');
    }

    public function examOfStart($id)
    {
        $this->examOrder = ExamOrder::with(['student.user', 'teacher.user', 'tester.user', 'partable'])->where('id', $id)->first();
        if ($this->examOrder && $this->examOrder->status === ExamOrder::ACCEPTABLE_STATUS && $this->examOrder->teacher_id !== auth()->id() && $this->current_role === 'مختبر') {
            if (Cache::has('exam_order_id_' . $this->examOrder->id)) {
                // start exam based on cache
                $this->initializeExamStartInputs(0);
            } else {
                // start exam based on database
                $examSettings = ExamSettings::find(1);
                if ($this->examOrder->partable_type === SunnahPart::class) {
                    // في حالة كان نوع الاختبار سنة
                    $this->another_mark = 3;
                    $this->success_mark = $examSettings->exam_sunnah_success_rate;
                    if ($this->examOrder->partable->type === SunnahExam::INDIVIDUAL_TYPE) {
                        // في حالة كان نوع الاختبار منفرد
                        $this->initializeExamStartInputs($examSettings->exam_sunnah_questions);
                    } else {
                        // في حالة كان نوع الاختبار تجميعي
                        $this->initializeExamStartInputs($examSettings->exam_sunnah_questions_summative);
                    }
                } else if ($this->examOrder->partable_type === QuranPart::class) {
                    // في حالة كان نوع الاختبار منفرد
                    $this->success_mark = $examSettings->exam_success_rate;
                    $examCustomQuestion = ExamCustomQuestion::where('quran_part_id', $this->examOrder->partable_id)->first();
                    if ($examCustomQuestion) {
                        $this->initializeExamStartInputs($examCustomQuestion->question_count);
                    } else if ($examSettings) {
                        if ($examSettings->exam_questions_min === $examSettings->exam_questions_max) {
                            $this->initializeExamStartInputs($examSettings->exam_questions_min);
                        } else {
                            $this->exam_questions_min = $examSettings->exam_questions_min;
                            $this->exam_questions_max = $examSettings->exam_questions_max;
                            $this->dispatchBrowserEvent('showModal');
                        }
                    }
                } else {
                    // في حالة كان نوع الاختبار تجميعي
                    $this->success_mark = $examSettings->summative_exam_success_rate;
                    if ($this->examOrder->partable->total_preservation_parts === 3) {
                        // في حال كان اختبار التجميعي مكون من 3 أسئلة
                        $this->initializeExamStartInputs($examSettings->exam_questions_summative_three_part);
                    } else if ($this->examOrder->partable->total_preservation_parts === 5) {
                        // في حال كان اختبار التجميعي مكون من 5 أسئلة
                        $this->initializeExamStartInputs($examSettings->exam_questions_summative_five_part);
                    } else if ($this->examOrder->partable->total_preservation_parts === 10) {
                        // في حال كان اختبار التجميعي مكون من 10 أسئلة
                        $this->initializeExamStartInputs($examSettings->exam_questions_summative_ten_part);
                    }
                }
            }
        }
    }

    private function initializeExamStartInputs($count)
    {
        $this->isExamOfStart = true;

        // check cache
        if (!Cache::has('exam_order_id_' . $this->examOrder->id)) {
            if ($count !== null) {
                $this->exam_questions_count = $count;
            }

            for ($i = 1; $i <= $this->exam_questions_count; $i++) {
                $this->marks_questions[$i] = 0;
            }

            for ($i = 1; $i <= $this->exam_questions_count; $i++) {
                $this->signs_questions[$i] = '';
            }

            // The process of calculating the number of times the exam is repeated
            if ($this->examOrder->partable_type === QuranPart::class) {
                $this->numberOfReplays = Exam::query()->select(['id', 'student_id', 'quran_part_id'])->where('student_id', $this->examOrder->student_id)
                    ->where('quran_part_id', $this->examOrder->partable_id)->count();
            } else {
                for ($i = 1; $i <= $this->exam_questions_count; $i++) {
                    $this->top_narrator_discounts[$i] = false;
                }

                for ($i = 1; $i <= $this->exam_questions_count; $i++) {
                    $this->bottom_narrator_discounts[$i] = false;
                }

                $this->numberOfReplays = SunnahExam::query()->select(['id', 'student_id', 'sunnah_part_id'])->where('student_id', $this->examOrder->student_id)
                    ->where('sunnah_part_id', $this->examOrder->partable_id)->count();
            }
        } else {
            $info_exam = Cache::get('exam_order_id_' . $this->examOrder->id);
            $this->signs_questions = $info_exam['signs_questions'] ?? [];
            $this->marks_questions = $info_exam['marks_questions'] ?? [];
            $this->top_narrator_discounts = $info_exam['top_narrator_discounts'] ?? [];
            $this->bottom_narrator_discounts = $info_exam['bottom_narrator_discounts'] ?? [];
            $this->exam_questions_count = $info_exam['exam_questions_count'] ?? 0;
            $this->numberOfReplays = $info_exam['numberOfReplays'] ?? 0;
            $this->notes = $info_exam['notes'] ?? '';
            $this->another_mark = $info_exam['another_mark'] ?? 0;
            $this->success_mark = $info_exam['success_mark'] ?? 0;
            $this->calcAverage();
        }
    }

    public function examApproval()
    {
        if ($this->examOrder->partable_type === QuranPart::class) {
            // في حالة كان نوع الاختبار قرآن
            $this->validate(['another_mark' => 'required|numeric|between:4,10',
                'exam_mark' => 'required|numeric|between:60,100']);
        } else {
            // في حالة كان نوع الاختبار سنة
            $this->validate(['another_mark' => 'required|numeric|between:0,3',
                'exam_mark' => 'required|numeric|between:60,100']);
        }

        DB::beginTransaction();
        try {
            if ($this->examOrder->type === ExamOrder::NEW_TYPE) {
                $exam_success_mark = ExamSuccessMark::where('mark', $this->success_mark)->first();
                if (!$exam_success_mark) {
                    $exam_success_mark = ExamSuccessMark::create(['mark' => $this->success_mark]);
                }
                if ($this->examOrder->partable_type === QuranPart::class) {
                    // في حالة كان نوع الاختبار قرآن
                    $exam = Exam::create([
                        'mark' => $this->exam_mark,
                        'quran_part_id' => $this->examOrder->partable_id,
                        'student_id' => $this->examOrder->student_id,
                        'teacher_id' => $this->examOrder->teacher_id,
                        'tester_id' => $this->examOrder->tester_id,
                        'exam_success_mark_id' => $exam_success_mark->id,
                        'datetime' => Carbon::now(),
                        'notes' => $this->exam_notes,
                    ]);
                    $message = "لقد تم اعتماد درجة: " . $exam->mark . "%" . " في الجزء: " . $exam->quranPart->name . ' ' . $exam->quranPart->description . " للطالب: " . $exam->student->user->name;
                } else {
                    // في حالة كان نوع الاختبار سنة
                    $exam = SunnahExam::create([
                        'mark' => $this->exam_mark,
                        'sunnah_part_id' => $this->examOrder->partable_id,
                        'student_id' => $this->examOrder->student_id,
                        'teacher_id' => $this->examOrder->teacher_id,
                        'tester_id' => $this->examOrder->tester_id,
                        'exam_success_mark_id' => $exam_success_mark->id,
                        'datetime' => Carbon::now(),
                        'notes' => $this->exam_notes,
                    ]);
                    $message = "لقد تم اعتماد درجة: " . $exam->mark . "%" . " في الجزء: " . $exam->sunnahPart->name . ' (' . $exam->sunnahPart->total_hadith_parts . ') حديث' . " للطالب: " . $exam->student->user->name;
                }

                $exam->teacher->user->notify(new NewExamForTeacherNotify($exam));
                $title = "اختبار جديد معتمد";
                $this->push_notification($message, $title, $this->link . $exam->id, [$exam->teacher->user->user_fcm_token->device_token ?? null]);

            } else {
                if ($this->examOrder->partable_type === QuranPart::class) {
                    // في حالة كان نوع الاختبار قرآن
                    $exam_id = Exam::query()->select('id')->whereHas('exam_success_mark', function ($q) {
                        $q->where(DB::raw('exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
                    })->where('student_id', '=', $this->examOrder->student_id)
                        ->where('quran_part_id', '=', $this->examOrder->partable_id)
                        ->first()->id;
                } else {
                    // في حالة كان نوع الاختبار سنة
                    $exam_id = SunnahExam::query()->select('id')->whereHas('exam_success_mark', function ($q) {
                        $q->where(DB::raw('sunnah_exams.mark'), '>=', DB::raw('exam_success_mark.mark'));
                    })->where('student_id', '=', $this->examOrder->student_id)
                        ->where('sunnah_part_id', '=', $this->examOrder->partable_id)
                        ->first()->id;
                }

                if ($exam_id !== null) {
                    if ($this->examOrder->partable_type === QuranPart::class) {
                        // في حالة كان نوع الاختبار قرآن
                        $examImprovement = ImprovementExam::create([
                            'id' => $exam_id,
                            'mark' => $this->exam_mark,
                            'tester_id' => $this->examOrder->tester_id,
                            'datetime' => Carbon::now(),
                        ]);
                        $message = "لقد تم اعتماد تحسين درجة: " . $examImprovement->mark . "%" . " في الجزء: " . $examImprovement->exam->quranPart->name . ' ' . $examImprovement->exam->quranPart->description . " للطالب: " . $examImprovement->exam->student->user->name;
                    } else {
                        // في حالة كان نوع الاختبار سنة
                        $examImprovement = SunnahImprovementExam::create([
                            'id' => $exam_id,
                            'mark' => $this->exam_mark,
                            'tester_id' => $this->examOrder->tester_id,
                            'datetime' => Carbon::now(),
                        ]);
                        $message = "لقد تم اعتماد تحسين درجة: " . $examImprovement->mark . "%" . " في الجزء: " . $examImprovement->exam->sunnahPart->name . ' (' . $examImprovement->exam->sunnahPart->total_hadith_parts . ') حديث' . " للطالب: " . $examImprovement->exam->student->user->name;
                    }

                    if ($examImprovement !== null) {
                        $examImprovement->exam->teacher->user->notify(new ImproveExamForTeacherNotify($examImprovement));
                        $title = "اختبار تحسين درجة معتمد";
                        $this->push_notification($message, $title, $this->link . $exam_id, [$examImprovement->exam->teacher->user->user_fcm_token->device_token ?? null]);
                    }
                }
            }

            $this->dispatchBrowserEvent('hideModal');
            ExamOrder::find($this->examOrder->id)->delete();
            Cache::forget('exam_order_id_' . $this->examOrder->id);
            $this->isExamOfStart = false;
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد اختبار الطالب بنجاح.']);
            DB::commit();
            $this->clearForm();
        } catch (Exception $e) {
            DB::rollback();
            $this->catchError = $e->getMessage();
        }
    }

    public function exam_no_success()
    {
        if ($this->exam_mark < 90) {
            $this->exam_mark = 70;
            $this->examApproval();
        }
    }

    public function getFocusId($id)
    {
        if (in_array($id, range(1, $this->exam_questions_count), true)) {
            $this->focus_id = $id;
        }
    }

    public function minus_1()
    {
        if ($this->focus_id !== null) {
            if (isset($this->signs_questions[$this->focus_id])) {
                $this->signs_questions[$this->focus_id] .= '/';
            } else {
                $this->signs_questions[$this->focus_id] = '/';
            }
            ++$this->marks_questions[$this->focus_id];
            $this->calcAverage();
        }
    }

    public function remove()
    {
        if ($this->focus_id !== null && isset($this->signs_questions[$this->focus_id])) {
            $length = strlen($this->signs_questions[$this->focus_id]);
            if (isset($this->signs_questions[$this->focus_id][$length - 1])) {
                if ($this->signs_questions[$this->focus_id][$length - 1] === '/') {
                    --$this->marks_questions[$this->focus_id];
                } else {
                    // في حال كان نوع الاختبار سنة يتم معاملة المنفرد والتجميعي كما هو
                    $this->marks_questions[$this->focus_id] -= 0.5;
                }
                $this->signs_questions[$this->focus_id] = substr($this->signs_questions[$this->focus_id], 0, -1);
                $this->calcAverage();
            }
        }
    }

    public function minus_0_5()
    {
        if ($this->focus_id !== null) {
            if (isset($this->signs_questions[$this->focus_id])) {
                $this->signs_questions[$this->focus_id] .= '-';
            } else {
                $this->signs_questions[$this->focus_id] = '-';
            }

            $this->marks_questions[$this->focus_id] += 0.5;

            $this->calcAverage();
        }
    }

    public function minus_3_or_clear(): void
    {
        if ($this->focus_id !== null) {
            if (isset($this->top_narrator_discounts[$this->focus_id])) {
                $this->top_narrator_discounts[$this->focus_id] = !$this->top_narrator_discounts[$this->focus_id];
                if ($this->top_narrator_discounts[$this->focus_id] === true) {
                    $this->marks_questions[$this->focus_id] += 3;
                } else {
                    $this->marks_questions[$this->focus_id] -= 3;
                }
            }
            $this->calcAverage();
        }
    }

    public function minus_2_or_clear()
    {
        if ($this->focus_id !== null) {
            if (isset($this->bottom_narrator_discounts[$this->focus_id])) {
                $this->bottom_narrator_discounts[$this->focus_id] = !$this->bottom_narrator_discounts[$this->focus_id];
                if ($this->bottom_narrator_discounts[$this->focus_id] === true) {
                    $this->marks_questions[$this->focus_id] += 2;
                } else {
                    $this->marks_questions[$this->focus_id] -= 2;
                }
            }
            $this->calcAverage();
        }
    }

    public function updatedAnotherMark()
    {
        $this->calcAverage();
    }

    private function calcAverage()
    {
        $sum = 0;
        for ($i = 1; $i <= $this->exam_questions_count; $i++) {
            $sum += $this->marks_questions[$i];
        }

        if ($this->examOrder->partable_type === QuranPart::class) {
            // في حال كان نوع الاختبار قرآن
            $this->exam_mark = round(100 - $sum) - (10 - intval($this->another_mark));
        } else {
            // في حال كان نوع الاختبار سنة
            $this->exam_mark = round(100 - $sum) - (3 - intval($this->another_mark));
        }

        if ($this->exam_mark >= $this->success_mark) {
            $this->final_exam_score = 'درجة الطالب : (' . $this->exam_mark . ')' . ' اجتاز الطالب الإختبار بنجاح.';
        } else {
            $this->final_exam_score = 'درجة الطالب : (' . $this->exam_mark . ')' . ' لم يجتاز الطالب الإختبار بنجاح.';
        }

        if (Cache::has('exam_order_id_' . $this->examOrder->id)) {
            Cache::forget('exam_order_id_' . $this->examOrder->id);
        }

        Cache::remember('exam_order_id_' . $this->examOrder->id, now()->addDay(), function () {
            return [
                'signs_questions' => $this->signs_questions,
                'marks_questions' => $this->marks_questions,
                'top_narrator_discounts' => $this->top_narrator_discounts,
                'bottom_narrator_discounts' => $this->bottom_narrator_discounts,
                'exam_questions_count' => $this->exam_questions_count,
                'numberOfReplays' => $this->numberOfReplays,
                'notes' => $this->notes,
                'another_mark' => $this->another_mark,
                'success_mark' => $this->success_mark,
            ];
        });
    }


    public function getExamOrder($id)
    {
        $this->clearForm();
        $examOrder = ExamOrder::with(['student.user', 'partable'])->where('id', $id)->first();
        if ($examOrder) {
            $this->examOrder = $examOrder;
        }
    }

    public function clearForm()
    {
        $this->notes = null;
        $this->resetValidation();
    }

    public function all_Exams_Today()
    {
        return ExamOrder::query()
            ->with(['student.user', 'partable', 'teacher.user', 'tester.user'])
            ->search($this->search)
            ->todayexams()
            ->when($this->current_role === User::TEACHER_ROLE, function ($q, $v) {
                $group = Group::where('teacher_id', auth()->id())->first();
                $id = $group->id;
                $q->when($group->type === Group::QURAN_TYPE, function ($q, $v) use ($id) {
                    $q->whereHas('student', function ($q) use ($id) {
                        $q->where('group_id', '=', $id);
                    });
                })->when($group->type === Group::SUNNAH_TYPE, function ($q, $v) use ($id) {
                    $q->whereHas('student', function ($q) use ($id) {
                        $q->where('group_sunnah_id', '=', $id);
                    });
                });
            })->when($this->current_role === User::SUPERVISOR_ROLE, function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    $q->where('grade_id', '=', Supervisor::whereId(auth()->id())->first()->grade_id ?? null)
                        ->when($this->selectedStudentId != null, function ($q, $v) {
                            $q->where('id', '=', $this->selectedStudentId);
                        })
                        ->when($this->selectedTeacherId != null, function ($q, $v) {
                            $q->where('group_id', '=', $this->selectedTeacherId)
                                ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                        });
                });
            })
            ->when($this->current_role === User::TESTER_ROLE, function ($q, $v) {
                $q->where('tester_id', auth()->id())
                    ->when($this->selectedGradeId != null, function ($q, $v) {
                        $q->whereHas('student', function ($q) {
                            $q->where('grade_id', '=', $this->selectedGradeId)
                                ->when($this->selectedStudentId != null, function ($q, $v) {
                                    $q->where('id', '=', $this->selectedStudentId);
                                })
                                ->when($this->selectedTeacherId != null, function ($q, $v) {
                                    $q->where('group_id', '=', $this->selectedTeacherId)
                                        ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                                });
                        });
                    });;
            })->when($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات', function ($q, $v) {
                $q->when($this->selectedGradeId != null, function ($q, $v) {
                    $q->whereHas('student', function ($q) {
                        $q->where('grade_id', '=', $this->selectedGradeId)
                            ->when($this->selectedStudentId != null, function ($q, $v) {
                                $q->where('id', '=', $this->selectedStudentId);
                            })
                            ->when($this->selectedTeacherId != null, function ($q, $v) {
                                $q->where('group_id', '=', $this->selectedTeacherId)
                                    ->orWhere('group_sunnah_id', '=', $this->selectedTeacherId);
                            });
                    });
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch()
    {
        $this->all_Exams_Today();
    }

    public function all_Grades()
    {
        if ($this->current_role === User::SUPERVISOR_ROLE) {
            $this->selectedGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
            $this->grades = Grade::query()->where('id', $this->selectedGradeId)->get();
        } else if ($this->current_role === User::TEACHER_ROLE) {
            $this->selectedTeacherId = Group::where('teacher_id', auth()->id())->first()->id ?? null;
            $this->grades = Grade::query()->where('id', Teacher::where('id', auth()->id())->first()->grade_id)->get();
        } else if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === 'مختبر') {
            $this->grades = Grade::all();
        }
    }

    public function getTeachersByGradeId()
    {
        $this->reset('students', 'groups', 'selectedTeacherId', 'selectedStudentId');

        if ($this->current_role === 'مشرف') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } else if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === 'مختبر') {
            if ($this->selectedGradeId) {
                $this->groups = Group::query()->with(['teacher.user'])->where('grade_id', $this->selectedGradeId)->get();
            }
        } elseif ($this->current_role === 'محفظ') {
            $this->groups = Group::query()->where('teacher_id', auth()->id())->get();
        }
    }

    public function getStudentsByTeacherId()
    {
        $this->reset('students', 'selectedStudentId');

        if ($this->current_role === 'أمير المركز' || $this->current_role === 'مشرف الإختبارات' || $this->current_role === 'مختبر') {
            if ($this->selectedTeacherId) {
                $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)
                    ->orWhere('group_sunnah_id', $this->selectedTeacherId)->get();
            }
        } else if ($this->current_role === 'مشرف') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)
                ->orWhere('group_sunnah_id', $this->selectedTeacherId)->get();
        } else if ($this->current_role === 'محفظ') {
            $this->students = Student::query()->with(['user'])->where('group_id', $this->selectedTeacherId)
                ->orWhere('group_sunnah_id', $this->selectedTeacherId)->get();
        }
    }

}
