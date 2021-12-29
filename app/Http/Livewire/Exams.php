<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\ExamCustomQuestion;
use App\Models\ExamSettings;
use App\Models\ExamSuccessMark;
use App\Models\Grade;
use App\Models\Group;
use App\Models\LowerSupervisor;
use App\Models\QuranPart;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Teacher;
use App\Models\Tester;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\MessageBag;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class Exams extends Component
{
    use WithPagination;

    public $isAddExam = false, $isExamOfStart = false;
    public $grades, $groups, $students, $testers, $quran_parts, $grade_id, $group_id, $student_id;
    public $quran_part_id, $tester_id, $catchError, $success_mark, $exam_questions_count;
    public $marks_questions = [], $signs_questions = [], $exam_questions_min, $exam_questions_max, $exam_date;
    public $focus_id, $final_exam_score, $exam_mark = 100, $another_mark = 10, $exam_notes;
    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $search = '';
    public $searchGradeId, $searchGroupId, $searchStudentId;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $this->all_Groups();
        $this->all_Students();
        $this->checkLastExamStatus();
        $this->all_questions_exam();

        if ($this->isExamOfStart && $this->another_mark) {
            $this->calcAverage();
        }

        return view('livewire.exams', [
            'exams' => $this->all_Exam(),]);
    }

    public function mount()
    {
        $this->all_Grades();
        $this->all_Testers();
        $this->read_All_Exams();
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
            'grade_id' => 'required|numeric',
            'group_id' => 'required|numeric',
            'student_id' => 'required|numeric',
            'tester_id' => 'required|numeric',
            'quran_part_id' => 'required|numeric',
            'exam_questions_count' => 'required|numeric',
            'exam_date' => 'required||date|date_format:Y-m-d',
        ]);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function rules()
    {
        return [
            'grade_id' => 'required|numeric',
            'group_id' => 'required|numeric',
            'student_id' => 'required|numeric',
            'tester_id' => 'required|numeric',
            'quran_part_id' => 'required|numeric',
            'exam_questions_count' => 'required|numeric',
            'exam_date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function messages()
    {
        return [
            'grade_id.required' => 'حقل المرحلة مطلوب',
            'grade_id.numeric' => 'يجب اختيار صالح لحقل المرحلة',
            'group_id.required' => 'حقل المجموعة مطلوب',
            'group_id.numeric' => 'يجب اختيار صالح لحقل المجموعة',
            'student_id.required' => 'حقل الطالب مطلوب',
            'student_id.numeric' => 'يجب اختيار صالح لحقل الطالب',
            'tester_id.required' => 'حقل المختبر مطلوب',
            'tester_id.numeric' => 'يجب اختيار صالح لحقل المختبر',
            'quran_part_id.required' => 'حقل جزء الإختبار مطلوب',
            'quran_part_id.numeric' => 'يجب اختيار صالح لحقل الجزء',
            'exam_questions_count.required' => 'حقل عدد أسئلة الإختبار مطلوب',
            'exam_questions_count.numeric' => 'يجب اختيار صالح لحقل عدد أسئلة الإختبار',
            'exam_date.required' => 'حقل تاريخ الإختبار مطلوب',
            'exam_date.date' => 'حقل تاريخ الإختبار يجب أن يكون تاريخ',
            'exam_date.date_format' => 'حقل تاريخ الإختبار يجب أن يكون من نوع تاريخ',
        ];
    }

    public function addExam($isShow)
    {
        $this->isAddExam = $isShow;
    }

    public function all_Exam()
    {
        if (auth()->user()->current_role == 'محفظ') {
            if (empty($this->searchStudentId)) {
                return Exam::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) {
                        return $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Exam::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) {
                        return $q->where('group_id', '=', Group::where('teacher_id', auth()->id())->first()->id)
                            ->where('id', '=', $this->searchStudentId);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        } elseif (auth()->user()->current_role == 'مشرف') {
            return $this->getExamsByGrade(Supervisor::where('id', auth()->id())->first()->grade_id);
        } elseif (auth()->user()->current_role == 'مختبر') {
            return Exam::query()
                ->search($this->search)
                ->where('tester_id', auth()->id())
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } elseif (auth()->user()->current_role == 'اداري') {
            return $this->getExamsByGrade(LowerSupervisor::where('id', auth()->id())->first()->grade_id);
        } elseif (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            if (empty($this->searchGradeId)) {
                return Exam::query()
                    ->search($this->search)
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                if (empty($this->searchGroupId)) {
                    return Exam::query()
                        ->search($this->search)
                        ->whereHas('student', function ($q) {
                            return $q->where('grade_id', '=', $this->searchGradeId);
                        })
                        ->orderBy($this->sortBy, $this->sortDirection)
                        ->paginate($this->perPage);
                } else {
                    if (empty($this->searchStudentId)) {
                        return Exam::query()
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q->where('grade_id', '=', $this->searchGradeId)
                                    ->where('group_id', '=', $this->searchGroupId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    } else {
                        return Exam::query()
                            ->search($this->search)
                            ->whereHas('student', function ($q) {
                                return $q
                                    ->where('grade_id', '=', $this->searchGradeId)
                                    ->where('group_id', '=', $this->searchGroupId)
                                    ->where('id', '=', $this->searchStudentId);
                            })
                            ->orderBy($this->sortBy, $this->sortDirection)
                            ->paginate($this->perPage);
                    }
                }
            }
        }
        return [];
    }

    private function getExamsByGrade($grade_id)
    {
        if (empty($this->searchGroupId)) {
            return Exam::query()
                ->search($this->search)
                ->whereHas('student', function ($q) use ($grade_id) {
                    return $q->where('grade_id', '=', $grade_id);
                })
                ->orderBy($this->sortBy, $this->sortDirection)
                ->paginate($this->perPage);
        } else {
            if (empty($this->searchStudentId)) {
                return Exam::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) use ($grade_id) {
                        return $q->where('grade_id', '=', $grade_id)
                            ->where('group_id', '=', $this->searchGroupId);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            } else {
                return Exam::query()
                    ->search($this->search)
                    ->whereHas('student', function ($q) use ($grade_id) {
                        return $q
                            ->where('grade_id', '=', $grade_id)
                            ->where('group_id', '=', $this->searchGroupId)
                            ->where('id', '=', $this->searchStudentId);
                    })
                    ->orderBy($this->sortBy, $this->sortDirection)
                    ->paginate($this->perPage);
            }
        }
    }

    public function all_Grades()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            $this->grades = Grade::all();
        } else if (auth()->user()->current_role == 'مشرف') {
            $this->searchGradeId = Supervisor::where('id', auth()->id())->first()->grade_id;
        } else if (auth()->user()->current_role == 'اداري') {
            $this->searchGradeId = LowerSupervisor::where('id', auth()->id())->first()->grade_id;
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->searchGradeId = Teacher::where('id', auth()->id())->first()->grade_id;
        }
    }

    public function all_Groups()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            if ($this->grade_id) {
                $this->groups = Group::query()->where('grade_id', $this->grade_id)->get();
            } else if ($this->searchGradeId) {
                $this->groups = Group::query()->where('grade_id', $this->searchGradeId)->get();
            }
        } else if (auth()->user()->current_role == 'مشرف') {
            $this->groups = Group::query()
                ->where('grade_id', '=', Supervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if (auth()->user()->current_role == 'اداري') {
            $this->groups = Group::query()
                ->where('grade_id', '=', LowerSupervisor::where('id', auth()->id())->first()->grade_id)->get();
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->searchGroupId = Group::query()->where('teacher_id', auth()->id())->first()->id;
        }

    }

    public function all_Students()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            if ($this->group_id) {
                $this->students = Student::query()->where('group_id', $this->group_id)->get();
            } else if ($this->searchGroupId) {
                $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
            }
        } else if (auth()->user()->current_role == 'مشرف') {
            $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
        } else if (auth()->user()->current_role == 'اداري') {
            $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
        } else if (auth()->user()->current_role == 'محفظ') {
            $this->students = Student::query()->where('group_id', $this->searchGroupId)->get();
        }
    }

    public function all_Testers()
    {
        if (auth()->user()->current_role == 'أمير المركز' ||
            auth()->user()->current_role == 'مشرف الإختبارات') {
            $this->testers = Tester::all();
        }
    }


    public function all_Quran_Parst($id, $isSuccess)
    {
        if ($id != null) {
            $this->quran_parts =
                QuranPart::query()->orderBy('id')->find($isSuccess == true ? $id - 1 : $id)->toArray();
        } else {
            $this->quran_parts = QuranPart::query()->orderBy('id')->get();
        }
    }

    public function checkLastExamStatus()
    {
        if ($this->student_id != null) {
            $exam = Exam::where('student_id', $this->student_id)->orderBy('exam_date', 'desc')->first();
            if ($exam) {
                $sum = 0;
                for ($i = 1; $i <= count($exam->marks_questions); $i++) {
                    $sum += $exam->marks_questions[$i];
                }
                $exam_mark = round(100 - $sum) - (10 - $exam->another_mark);
                if ($exam_mark >= $exam->examSuccessMark->mark) {
                    $this->all_Quran_Parst($exam->quran_part_id, true);
                } else {
                    $to = Carbon::createFromFormat('Y-m-d', date('Y-m-d', Carbon::now()->timestamp));
                    $from = Carbon::createFromFormat('Y-m-d', $exam->exam_date);

                    $diff_in_days = $to->diffInDays($from);
                    $number_days_exam = ExamSettings::find(1)->number_days_exam;
                    $days = ($diff_in_days - $number_days_exam);
                    if ($days > 0) {
                        $this->all_Quran_Parst($exam->quran_part_id, false);
                    } else {
                        if (abs($days) == 0) {
                            $this->catchError = 'عذرا متبقي لهذا الطالب يوم حتى تتمكن من إضافة اختبار جديد';
                        } else if (abs($days) == 1) {
                            $this->catchError = 'عذرا متبقي لهذا الطالب يومان حتى تتمكن من إضافة اختبار جديد';
                        } else if (abs($days) == 2) {
                            $this->catchError = 'عذرا متبقي لهذا الطالب ثلاث أيام حتى تتمكن من إضافة اختبار جديد';
                        } else if (in_array(abs($days), range(3, 10))) {
                            $this->catchError = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' أيام حتى تتمكن من إضافة اختبار جديد';
                        } else if (in_array(abs($days), range(11, 15))) {
                            $this->catchError = 'عذرا متبقي لهذا الطالب ' . abs($days) . ' يوم حتى تتمكن من إضافة اختبار جديد';
                        }
                        $this->exam_questions_count = null;
                        $this->quran_part_id = null;
                    }
                }
            } else {
                $this->all_Quran_Parst(null, null);
            }
        }
    }

    private function read_All_Exams()
    {
        $exams = $this->all_Exam();

        if ($exams != null && !empty($exams)) {
            for ($i = 0; $i < count($exams); $i++) {
                if (auth()->user()->current_role == 'محفظ') {
                    if ($exams[$i]->readable['isReadableTeacher'] == false) {
                        $exam = Exam::find($exams[$i]->id);
                        $array = $exam->readable;
                        $array['isReadableTeacher'] = true;
                        $exam->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مشرف') {
                    if ($exams[$i]->readable['isReadableSupervisor'] == false) {
                        $exam = Exam::find($exams[$i]->id);
                        $array = $exam->readable;
                        $array['isReadableSupervisor'] = true;
                        $exam->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'اداري') {
                    if ($exams[$i]->readable['isReadableLowerSupervisor'] == false) {
                        $exam = Exam::find($exams[$i]->id);
                        $array = $exam->readable;
                        $array['isReadableLowerSupervisor'] = true;
                        $exam->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مختبر') {
                    if ($exams[$i]->readable['isReadableTester'] == false) {
                        $exam = Exam::find($exams[$i]->id);
                        $array = $exam->readable;
                        $array['isReadableTester'] = true;
                        $exam->update(['readable' => $array]);
                    }
                } else if (auth()->user()->current_role == 'مشرف الإختبارات') {
                    if ($exams[$i]->readable['isReadableSupervisorExams'] == false) {
                        $exam = Exam::find($exams[$i]->id);
                        $array = $exam->readable;
                        $array['isReadableSupervisorExams'] = true;
                        $exam->update(['readable' => $array]);
                    }
                }
            }
        }
    }


    public function all_questions_exam()
    {
        if ($this->quran_part_id != null) {
            $examCustomQuestion = ExamCustomQuestion::where('quran_part_id', $this->quran_part_id)->first();
            $examSettings = ExamSettings::find(1);
            $this->success_mark = $examSettings->exam_success_rate;
            if ($examCustomQuestion) {
                $this->exam_questions_min = $examCustomQuestion->exam_question_count;
                $this->exam_questions_max = $examCustomQuestion->exam_question_count;
            } else {
                if ($examSettings) {
                    $this->exam_questions_min = $examSettings->exam_questions_min;
                    $this->exam_questions_max = $examSettings->exam_questions_max;
                }
            }
        }
    }

    private function initializeExamStartInputs()
    {
        if ($this->exam_questions_count > 0) {
            for ($i = 1; $i <= $this->exam_questions_count; $i++) {
                $this->marks_questions[$i] = 0;
            }

            for ($i = 1; $i <= $this->exam_questions_count; $i++) {
                $this->signs_questions[$i] = '';
            }
        }
    }

    public function examInformationApproval()
    {
        $this->validate();
        $messageBag = new MessageBag;
        $messageBagExam = new MessageBag;
        $messageBag->add('tester_id', 'يجب أن لا يكون المختبر هو نفس المحفظ');
        $messageBagExam->add('exam_questions_count', 'يجب أن يكون هناك اختيار صالح في حقل عدد أسئلة الإختبار');
        if ($this->groups->firstWhere('id', $this->group_id)->teacher_id == $this->tester_id) {
            $this->setErrorBag($messageBag);
        } else {
            if ($this->exam_questions_count <= 0) {
                $this->setErrorBag($messageBagExam);
            } else {
                $this->isExamOfStart = true;
                $this->initializeExamStartInputs();
            }
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


    public function examApproval()
    {
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
                'teacher_id' => $this->groups->firstWhere('id', $this->group_id)->teacher_id,
                'tester_id' => $this->tester_id,
                'exam_success_mark_id' => $examSuccessMark->id,
                'exam_date' => $this->exam_date,
                'notes' => $this->exam_notes != null ? $this->exam_notes : null,
            ]);

            // push notifications
            $arr_external_user_ids = [];
            if (auth()->user()->current_role != 'مشرف الإختبارات') {
                $user_role_supervisor_exams = Role::where('name', 'مشرف الإختبارات')->first();
                if ($user_role_supervisor_exams != null && $user_role_supervisor_exams->users != null
                    && $user_role_supervisor_exams->users[0] != null) {
                    array_push($arr_external_user_ids, "" . $user_role_supervisor_exams->users[0]->id);
                }
            }

            array_push($arr_external_user_ids, "" . $this->groups->firstWhere('id', $this->group_id)->teacher_id);
            $quran_part_name = QuranPart::find($this->quran_part_id, ['name'])->name;
            $student_name = User::find($this->student_id, ['name'])->name;
            $message = "لقد تم اعتماد درجة: " . $this->exam_mark . "%" . " في اختبار: " . $quran_part_name . " للطالب: " . $student_name;
            $url = 'https://memorization-management-system.herokuapp.com/manage_exams';

            $this->push_notifications($arr_external_user_ids, $message, "حالة اختبار الطالب", $url);
            $this->emit('approval-exam');
            $this->dispatchBrowserEvent('alert',
                ['type' => 'success', 'message' => 'تمت عملية اعتماد اختبار الطالب بنجاح.']);
            DB::commit();
            $this->clearForm();
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('failure_message', $e->getMessage());
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

    public function clearForm()
    {
        $this->isAddExam = false;
        $this->isExamOfStart = false;
        $this->groups = null;
        $this->students = null;
        $this->quran_parts = null;
        $this->grade_id = null;
        $this->group_id = null;
        $this->student_id = null;
        $this->quran_part_id = null;
        $this->tester_id = null;
        $this->catchError = null;
        $this->success_mark = null;
        $this->exam_questions_count = null;
        $this->exam_questions_max = null;
        $this->exam_questions_min = null;
        $this->signs_questions = [];
        $this->marks_questions = [];
        $this->exam_date = null;
        $this->focus_id = null;
        $this->final_exam_score = null;
        $this->exam_mark = 100;
        $this->another_mark = 10;
        $this->exam_notes = null;
    }


}
