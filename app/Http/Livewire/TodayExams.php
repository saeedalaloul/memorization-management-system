<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\ExamCustomQuestion;
use App\Models\ExamImprovement;
use App\Models\ExamOrder;
use App\Models\ExamSettings;
use App\Models\ExamSuccessMark;
use App\Models\Group;
use App\Models\QuranPart;
use App\Models\Teacher;
use App\Notifications\FailureExamOrderForTeacherNotify;
use App\Notifications\ImproveExamForTeacherNotify;
use App\Notifications\NewExamForTeacherNotify;
use App\Traits\NotificationTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TodayExams extends HomeComponent
{
    use NotificationTrait;

    public $notes, $numberOfReplays = 0;
    public $exam_mark = 100, $exam_questions_count, $focus_id, $success_mark, $another_mark = 10;
    public $final_exam_score, $exam_notes;
    public $isExamOfStart = false, $signs_questions = [], $marks_questions = [];
    public $exam_questions_min, $exam_questions_max, $examOrder;

    public function mount()
    {
        $this->current_role = auth()->user()->current_role;
    }

    public function render()
    {
        return view('livewire.today-exams', [
            'exams_today' => $this->all_Exams_Today(),
        ]);
    }

    public function messages()
    {
        return [
            'notes.required' => 'حقل الملاحظات مطلوب',
            'notes.string' => 'حقل الملاحظات يجب أن يحتوي على نص',
            'notes.max' => 'حقل الملاحظات يجب أن لا يزيد عن 50 حرف',
            'exam_notes.max' => 'حقل الملاحظات يجب أن لا يزيد عن 50 حرف',
            'exam_questions_count.required' => 'حقل عدد أسئلة الإختبار مطلوب',
            'exam_questions_count.numeric' => 'حقل عدد أسئلة الإختبار يجب أن يحتوي على رقم',
            'another_mark.required' => 'علامة أحكام الطالب مطلوبة',
            'another_mark.numeric' => 'يجب أن يكون رقم',
            'another_mark.between' => 'يجب أن تكون علامة أحكام الطالب بين 5 أو 10',
            'exam_mark.required' => 'علامة الاختبار مطلوبة',
            'exam_mark.numeric' => 'يجب أن يكون رقم',
            'exam_mark.between' => 'يجب أن تكون علامة الاختبار بين 60 أو 100',
        ];
    }

    public function examOrderRefusal()
    {
        $this->validate([
            'notes' => 'required|string|max:50',
        ]);
        $examOrder = ExamOrder::where('id', $this->examOrder->id)->first();
        if ($examOrder) {
            if ($examOrder->status == ExamOrder::ACCEPTABLE_STATUS && $examOrder->teacher_id != auth()->id()) {
                if ($this->current_role == 'مختبر') {
                    $examOrder->update([
                        'status' => ExamOrder::FAILURE_STATUS,
                        'notes' => $this->notes,
                    ]);

                    $examOrder->teacher->user->notify(new FailureExamOrderForTeacherNotify($examOrder));
                    $title = "طلب اختبار فشل إجراؤه";
                    $message = "لقد قام المختبر بتغيير حالة طلب اختبار الطالب: " . $examOrder->student->user->name . " في الجزء: " . $examOrder->quranPart->name . ' ' . $examOrder->quranPart->description . ' إلى لم يختبر بسبب: ' . $examOrder->notes;
                    $this->push_notification($message, $title, [$examOrder->teacher->user->user_fcm_token->device_token]);

                    $this->dispatchBrowserEvent('hideModal');
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية اعتماد عدم إجراء الطالب الاختبار بنجاح.']);
                    $this->clearForm();
                }
            }
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
        $this->examOrder = ExamOrder::with(['student.user', 'teacher.user', 'tester.user', 'quranPart'])->where('id', $id)->first();
        if ($this->examOrder) {
            if ($this->examOrder->status == ExamOrder::ACCEPTABLE_STATUS && $this->examOrder->teacher_id != auth()->id()) {
                if ($this->current_role == 'مختبر') {
                    $examSettings = ExamSettings::find(1);
                    if ($this->examOrder->quranPart->type == QuranPart::INDIVIDUAL_TYPE) {
                        // في حالة كان نوع الاختبار منفرد
                        $this->success_mark = $examSettings->exam_success_rate;
                        $examCustomQuestion = ExamCustomQuestion::where('quran_part_id', $this->examOrder->quran_part_id)->first();
                        if ($examCustomQuestion) {
                            $this->initializeExamStartInputs($examCustomQuestion->question_count);
                        } else {
                            if ($examSettings) {
                                if ($examSettings->exam_questions_min == $examSettings->exam_questions_max) {
                                    $this->initializeExamStartInputs($examSettings->exam_questions_min);
                                } else {
                                    $this->exam_questions_min = $examSettings->exam_questions_min;
                                    $this->exam_questions_max = $examSettings->exam_questions_max;
                                    $this->dispatchBrowserEvent('showModal');
                                }
                            }
                        }
                    } else {
                        // في حالة كان نوع الاختبار تجميعي
                        $this->success_mark = $examSettings->summative_exam_success_rate;
                        if ($this->examOrder->quranPart->total_preservation_parts == 3) {
                            // في حال كان اختبار التجميعي مكون من 3 أسئلة
                            $this->initializeExamStartInputs($examSettings->exam_questions_summative_three_part);
                        } else if ($this->examOrder->quranPart->total_preservation_parts == 5) {
                            // في حال كان اختبار التجميعي مكون من 5 أسئلة
                            $this->initializeExamStartInputs($examSettings->exam_questions_summative_five_part);
                        } else if ($this->examOrder->quranPart->total_preservation_parts == 10) {
                            // في حال كان اختبار التجميعي مكون من 10 أسئلة
                            $this->initializeExamStartInputs($examSettings->exam_questions_summative_ten_part);
                        }
                    }
                }
            }
        }
    }

    private function initializeExamStartInputs($count)
    {
        $this->isExamOfStart = true;
        if ($count != null) {
            $this->exam_questions_count = $count;
        }
        for ($i = 1; $i <= $this->exam_questions_count; $i++) {
            $this->marks_questions[$i] = 0;
        }

        for ($i = 1; $i <= $this->exam_questions_count; $i++) {
            $this->signs_questions[$i] = '';
        }

        // The process of calculating the number of times the exam is repeated
        $this->numberOfReplays = Exam::query()->select(['id', 'student_id', 'quran_part_id'])->where('student_id', $this->examOrder->student_id)
            ->where('quran_part_id', $this->examOrder->quran_part_id)->count();
    }

    public function examApproval()
    {
        $this->validate(['another_mark' => 'required|numeric|between:5,10',
            'exam_mark' => 'required|numeric|between:60,100']);

        DB::beginTransaction();
        try {
            if ($this->examOrder->type == ExamOrder::NEW_TYPE) {
                $examSuccessMark = ExamSuccessMark::where('mark', $this->success_mark)->first();
                if (!$examSuccessMark) {
                    $examSuccessMark = ExamSuccessMark::create(['mark' => $this->success_mark]);
                }
                $exam = Exam::create([
                    'mark' => $this->exam_mark,
                    'quran_part_id' => $this->examOrder->quran_part_id,
                    'student_id' => $this->examOrder->student_id,
                    'teacher_id' => $this->examOrder->teacher_id,
                    'tester_id' => $this->examOrder->tester_id,
                    'exam_success_mark_id' => $examSuccessMark->id,
                    'datetime' => Carbon::now(),
                    'notes' => $this->exam_notes != null ? $this->exam_notes : null,
                ]);

                $exam->teacher->user->notify(new NewExamForTeacherNotify($exam));
                $title = "اختبار جديد معتمد";
                $message = "لقد تم اعتماد درجة: " . $exam->mark ."%" . " في الجزء: " . $exam->quranPart->name . ' ' . $exam->quranPart->description . " للطالب: ". $exam->student->user->name;
                $this->push_notification($message, $title, [$exam->teacher->user->user_fcm_token->device_token]);

            } else {
                $exam_id = null;
                $exams = Exam::query()->with(['examSuccessMark'])->where('student_id', '=', $this->examOrder->student_id)
                    ->where('quran_part_id', '=', $this->examOrder->quran_part_id)
                    ->get();

                foreach ($exams as $exam) {
                    if ($exam->mark >= $exam->examSuccessMark->mark) {
                        $exam_id = $exam->id;
                        break;
                    }
                }

                if ($exam_id != null) {
                   $examImprovement = ExamImprovement::create([
                        'id' => $exam_id,
                        'mark' => $this->exam_mark,
                        'tester_id' => $this->examOrder->tester_id,
                        'datetime' => Carbon::now(),
                    ]);

                    $examImprovement->exam->teacher->user->notify(new ImproveExamForTeacherNotify($examImprovement));
                    $title = "اختبار تحسين درجة معتمد";
                    $message = "لقد تم اعتماد تحسين درجة: " . $examImprovement->mark ."%" . " في الجزء: " . $examImprovement->exam->quranPart->name . ' ' . $examImprovement->exam->quranPart->description . " للطالب: ". $examImprovement->exam->student->user->name;
                    $this->push_notification($message, $title, [$examImprovement->exam->teacher->user->user_fcm_token->device_token]);
                }
            }

            $this->dispatchBrowserEvent('hideModal');
            ExamOrder::find($this->examOrder->id)->delete();
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

    public function getFocusId($id)
    {
        if (in_array($id, range(1, $this->exam_questions_count))) {
            $this->focus_id = $id;
        }
    }

    public function minus_1()
    {
        if ($this->focus_id != null) {
            if (isset($this->signs_questions[$this->focus_id])) {
                $this->signs_questions[$this->focus_id] = $this->signs_questions[$this->focus_id] . '/';
            } else {
                $this->signs_questions[$this->focus_id] = '/';
            }
            if ($this->examOrder->quranPart->type == QuranPart::INDIVIDUAL_TYPE) {
                // في حال كان نوع الاختبار منفرد
                $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] + 1;
            } else {
                // في حال كان نوع الاختبار تجميعي
                $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] + 2;
            }
            $this->calcAverage();
        }
    }

    public function remove()
    {
        if ($this->focus_id != null && isset($this->signs_questions[$this->focus_id])) {
            $length = strlen($this->signs_questions[$this->focus_id]);
            if (isset($this->signs_questions[$this->focus_id][$length - 1])) {
                if ($this->signs_questions[$this->focus_id][$length - 1] == '/') {
                    if ($this->examOrder->quranPart->type == QuranPart::INDIVIDUAL_TYPE) {
                        // في حال كان نوع الاختبار منفرد
                        $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] - 1;
                    } else {
                        // في حال كان نوع الاختبار تجميعي
                        $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] - 2;
                    }
                } else {
                    if ($this->examOrder->quranPart->type == QuranPart::INDIVIDUAL_TYPE) {
                        // في حال كان نوع الاختبار منفرد
                        $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] - 0.5;
                    } else {
                        // في حال كان نوع الاختبار تجميعي
                        $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] - 1;
                    }
                }
                $this->signs_questions[$this->focus_id] = substr($this->signs_questions[$this->focus_id], 0, -1);
                $this->calcAverage();
            }
        }
    }

    public function minus_0_5()
    {
        if ($this->focus_id != null) {
            if (isset($this->signs_questions[$this->focus_id])) {
                $this->signs_questions[$this->focus_id] = $this->signs_questions[$this->focus_id] . '-';
            } else {
                $this->signs_questions[$this->focus_id] = '-';
            }
            if ($this->examOrder->quranPart->type == QuranPart::INDIVIDUAL_TYPE) {
                // في حال كان نوع الاختبار منفرد
                $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] + 0.5;
            } else {
                // في حال كان نوع الاختبار تجميعي
                $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] + 1;
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
        $this->exam_mark = round(100 - $sum) - (10 - $this->another_mark);
        if ($this->exam_mark >= $this->success_mark) {
            $this->final_exam_score = 'درجة الطالب : (' . $this->exam_mark . ')' . ' اجتاز الطالب الإختبار بنجاح.';
        } else {
            $this->final_exam_score = 'درجة الطالب : (' . $this->exam_mark . ')' . ' لم يجتاز الطالب الإختبار بنجاح.';
        }
    }


    public function getExamOrder($id)
    {
        $this->clearForm();
        $examOrder = ExamOrder::with(['student.user', 'quranPart'])->where('id', $id)->first();
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
            ->with(['student.user', 'QuranPart', 'teacher.user', 'tester.user'])
            ->search($this->search)
            ->todayexams()
            ->when($this->current_role == 'محفظ', function ($q, $v) {
                $q->whereHas('student', function ($q) {
                    return $q->where('grade_id', '=', Teacher::where('id', auth()->id())->first()->grade_id)
                        ->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id ?? null);
                });
            })
            ->when($this->current_role == 'مختبر', function ($q, $v) {
                $q->where('tester_id', auth()->id());
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function submitSearch(){
        $this->all_Exams_Today();
    }
}
