<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\ExamCustomQuestion;
use App\Models\ExamOrder;
use App\Models\ExamSettings;
use App\Models\ExamSuccessMark;
use App\Models\Group;
use App\Models\Teacher;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class TodayExams extends Component
{
    use WithPagination;

    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    public $modalId, $notes, $student_name, $teacher_name, $tester_name, $quran_part, $numberOfReplays = 0;
    public $exam_date, $exam_mark = 100, $exam_questions_count, $focus_id, $success_mark, $another_mark = 10;
    public $final_exam_score, $exam_notes, $quran_part_id, $student_id, $teacher_id, $tester_id;
    public $isExamOfStart = false, $signs_questions = [], $marks_questions = [];
    public $exam_questions_min, $exam_questions_max, $examOrder;
    protected $paginationTheme = 'bootstrap';


    public function launchModal()
    {
        $this->emit('showModal');
    }

    public function render()
    {
        if ($this->isExamOfStart && $this->modalId && $this->another_mark) {
            $this->calcAverage();
        }
        return view('livewire.today-exams', [
            'exams_today' => $this->all_Exams_Today(),
        ]);
    }

    public function mount()
    {
        $this->read_All_Exams_Today_Orders();
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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'notes' => 'required|string|max:50',
            'another_mark' => 'required|numeric|between:5,10',
            'exam_mark' => 'required|numeric|between:60,100',
        ]);
    }

    public function rules()
    {
        return [
            'notes' => 'required|string|max:50',
        ];
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
        $this->validate();
        $examOrder = ExamOrder::where('id', $this->modalId)->first();
        $array = ["isReadableTeacher" => false, "isReadableSupervisor" => false,
            "isReadableTester" => false, "isReadableSupervisorExams" => false];
        if ($examOrder) {
            if ($examOrder->status == 2 && $examOrder->teacher_id != auth()->id()) {
                if (auth()->user()->current_role == 'مختبر') {
                    $examOrder->update([
                        'status' => -3,
                        'notes' => $this->notes,
                        'readable' => $array,
                    ]);
                    $this->emit('refusal-exam');
                    $this->dispatchBrowserEvent('alert',
                        ['type' => 'success', 'message' => 'تمت عملية اعتماد عدم إجراء الطالب الاختبار بنجاح.']);
                    $this->clearForm();

                    // push notifications
                    $arr_external_user_ids = [];
                    if (auth()->user()->current_role != 'مشرف الإختبارات') {
                        $user_role_supervisor_exams = Role::where('name', 'مشرف الإختبارات')->first();
                        if ($user_role_supervisor_exams != null && $user_role_supervisor_exams->users != null
                            && $user_role_supervisor_exams->users[0] != null) {
                            array_push($arr_external_user_ids, "" . $user_role_supervisor_exams->users[0]->id);
                        }
                    }

                    array_push($arr_external_user_ids, "" . $examOrder->teacher_id);

                    $message = "لقد قام المختبر: " . $examOrder->tester->name . " باعتماد عدم إجراء الطالب: " . $examOrder->student->user->name . " في اختبار: " . $examOrder->quranPart->name;
                    $url = 'https://memorization-management-system.herokuapp.com/manage_exams_orders';
                    $this->push_notifications($arr_external_user_ids, $message, 'حالة طلب الاختبار', $url);
                }
            }
        }
    }

    public function push_notifications($arr_external_user_ids, $message, $title, $url)
    {
        $fields = array(
            'app_id' => env("ONE_SIGNAL_APP_ID"),
            'include_external_user_ids' => $arr_external_user_ids,
            'channel_for_external_user_ids' => 'push',
            'data' => array("foo" => "bar"),
            'headings' => array(
                "en" => $title,
                "ar" => $title,
            ),
            'url' => $url,
            'contents' => array(
                "en" => $message,
                "ar" => $message,
            )
        );

        $fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic ' . env('ONE_SIGNAL_AUTHORIZE')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }


    public function examQuestionsNumberApproval()
    {
        $this->validate(['exam_questions_count' => 'required|numeric']);
        $this->initializeExamStartInputs(null);
        $this->emit('exam-question-count-select');
    }

    public function examOfStart($id)
    {
        $this->examOrder = ExamOrder::where('id', $id)->first();
        if ($this->examOrder) {
            if ($this->examOrder->status == 2 && $this->examOrder->teacher_id != auth()->id()) {
                if (auth()->user()->current_role == 'مختبر') {
                    $examSettings = ExamSettings::find(1);
                    $this->success_mark = $examSettings->exam_success_rate;
                    $examCustomQuestion = ExamCustomQuestion::where('quran_part_id', $this->examOrder->quran_part_id)->first();
                    if ($examCustomQuestion) {
                        $this->initializeExamStartInputs($examCustomQuestion->exam_question_count);
                    } else {
                        if ($examSettings) {
                            if ($examSettings->exam_questions_min == $examSettings->exam_questions_max) {
                                $this->initializeExamStartInputs($examSettings->exam_questions_min);
                            } else {
                                $this->exam_questions_min = $examSettings->exam_questions_min;
                                $this->exam_questions_max = $examSettings->exam_questions_max;
                                $this->launchModal();
                            }
                        }
                    }
                }
            }
        }
    }

    private function initializeExamStartInputs($count)
    {
        $this->isExamOfStart = true;
        $this->modalId = $this->examOrder->id;
        $this->student_name = $this->examOrder->student->user->name;
        $this->student_id = $this->examOrder->student->id;
        $this->teacher_name = $this->examOrder->teacher->user->name;
        $this->teacher_id = $this->examOrder->teacher->id;
        $this->tester_name = $this->examOrder->tester->user->name;
        $this->tester_id = $this->examOrder->tester->id;
        $this->quran_part = $this->examOrder->quranPart->name;
        $this->quran_part_id = $this->examOrder->quranPart->id;
        $this->exam_date = $this->examOrder->exam_date;
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
        $exams = Exam::query()->where('student_id', $this->student_id)
            ->where('quran_part_id', $this->quran_part_id)->get();
        if ($exams != null && count($exams) > 0) {
            for ($i = 0; $i < count($exams); $i++) {
                $sum = 0;
                for ($j = 1; $j <= count($exams[$i]->marks_questions); $j++) {
                    $sum += $exams[$i]->marks_questions[$j];
                }
                $exam_mark = round(100 - $sum) - (10 - $exams[$i]->another_mark);
                if ($exam_mark < $exams[$i]->examSuccessMark->mark) {
                    $this->numberOfReplays++;
                }
            }
        }
    }

    public function examApproval()
    {
        $this->validate(['another_mark' => 'required|numeric|between:5,10',
            'exam_mark' => 'required|numeric|between:60,100']);

        $array = ["isReadableTeacher" => false, "isReadableSupervisor" => false,
            "isReadableTester" => false, "isReadableLowerSupervisor" => false,
            "isReadableSupervisorExams" => false];
        DB::beginTransaction();
        try {
            $examSuccessMark = ExamSuccessMark::where('mark', $this->success_mark)->first();
            if (!$examSuccessMark) {
                $examSuccessMark = ExamSuccessMark::create(['mark' => $this->success_mark]);
            }
            Exam::create([
                'readable' => $array,
                'signs_questions' => $this->signs_questions,
                'marks_questions' => $this->marks_questions,
                'another_mark' => $this->another_mark,
                'quran_part_id' => $this->quran_part_id,
                'student_id' => $this->student_id,
                'teacher_id' => $this->teacher_id,
                'tester_id' => $this->tester_id,
                'exam_success_mark_id' => $examSuccessMark->id,
                'exam_date' => $this->exam_date,
                'notes' => $this->exam_notes != null ? $this->exam_notes : null,
            ]);

            $this->emit('approval-exam');
            DB::commit();
            $this->isExamOfStart = false;
            ExamOrder::find($this->modalId)->delete();
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد اختبار الطالب بنجاح.']);

            // push notifications
            $arr_external_user_ids = [];
            if (auth()->user()->current_role != 'مشرف الإختبارات') {
                $user_role_supervisor_exams = Role::where('name', 'مشرف الإختبارات')->first();
                if ($user_role_supervisor_exams != null && $user_role_supervisor_exams->users != null
                    && $user_role_supervisor_exams->users[0] != null) {
                    array_push($arr_external_user_ids, "" . $user_role_supervisor_exams->users[0]->id);
                }
            }

            array_push($arr_external_user_ids, "" . $this->teacher_id);

            $message = "لقد تم اعتماد درجة: " . $this->exam_mark . "%" . " في اختبار: " . $this->quran_part . " للطالب: " . $this->student_name;
            $url = 'https://memorization-management-system.herokuapp.com/manage_exams';

            $this->push_notifications($arr_external_user_ids, $message, "حالة اختبار الطالب", $url);

            $this->reset();
            $this->resetValidation();
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('failure_message', $e->getMessage());
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
            $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] + 1;
            $this->calcAverage();
        }
    }

    public function remove()
    {
        if ($this->focus_id != null && isset($this->signs_questions[$this->focus_id])) {
            $length = strlen($this->signs_questions[$this->focus_id]);
            if (isset($this->signs_questions[$this->focus_id][$length - 1])) {
                if ($this->signs_questions[$this->focus_id][$length - 1] == '/') {
                    $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] - 1;
                } else {
                    $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] - 0.5;
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
            $this->marks_questions[$this->focus_id] = $this->marks_questions[$this->focus_id] + 0.5;
            $this->calcAverage();
        }
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
        $examOrder = ExamOrder::where('id', $id)->first();
        if ($examOrder) {
            $this->modalId = $examOrder->id;
            $this->student_name = $examOrder->student->user->name;
            $this->quran_part = $examOrder->quranPart->name;
        }
    }

    public function clearForm()
    {
        $this->modalId = null;
        $this->student_name = null;
        $this->quran_part = null;
        $this->notes = null;
        $this->resetValidation();
    }

    public function all_Exams_Today()
    {
        if (auth()->user()->current_role == 'محفظ') {
            return ExamOrder::query()
                ->search($this->search)
                ->todayexams()
                ->whereHas('student', function ($q) {
                    return $q->where('grade_id', '=', Teacher::where('id', auth()->id())->first()->grade_id)
                        ->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else if (auth()->user()->current_role == 'مختبر') {
            return ExamOrder::query()
                ->search($this->search)
                ->todayexams()
                ->where('tester_id', auth()->id())
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            return ExamOrder::query()
                ->search($this->search)
                ->todayexams()
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        }
        return [];
    }

    private function read_All_Exams_Today_Orders()
    {
        $exams_orders = $this->all_Exams_Today();

        if ($exams_orders != null && !empty($exams_orders)) {
            for ($i = 0; $i < count($exams_orders); $i++) {
                if (auth()->user()->current_role == 'محفظ') {
                    if ($exams_orders[$i]->readable['isReadableTeacher'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableTeacher'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مشرف') {
                    if ($exams_orders[$i]->readable['isReadableSupervisor'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableSupervisor'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مختبر') {
                    if ($exams_orders[$i]->readable['isReadableTester'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableTester'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
                    if ($exams_orders[$i]->readable['isReadableSupervisorExams'] == false) {
                        $examOrder = ExamOrder::find($exams_orders[$i]->id);
                        $array = $examOrder->readable;
                        $array['isReadableSupervisorExams'] = true;
                        $examOrder->update(['readable' => $array]);
                    }
                }
            }
        }
    }

}
